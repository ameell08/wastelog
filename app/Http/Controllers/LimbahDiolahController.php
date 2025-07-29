<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LimbahDiolah;
use App\Models\DetailLimbahDiolah;
use App\Models\Mesin;
use App\Models\KodeLimbah;
use App\Models\SisaLimbah;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LimbahDiolahController extends Controller
{
    public function index()
    {
        $mesin = Mesin::all();
        $kodeLimbah = KodeLimbah::all();

        // Data Diolah - Ambil data limbah yang sudah diolah
        $dataDiolah = DetailLimbahDiolah::with(['limbahDiolah.mesin', 'kodeLimbah'])
            ->orderBy('tanggal_input', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'no_mesin' => $item->limbahDiolah->mesin->no_mesin,
                    'kode_limbah' => $item->kodeLimbah->kode,
                    'deskripsi' => $item->kodeLimbah->deskripsi,
                    'berat_kg' => $item->berat_kg,
                ];
            });

        // Antrean Limbah - Ambil data sisa limbah yang belum diolah
        $antreanLimbah = SisaLimbah::with('kodeLimbah')
            ->where('berat_kg', '>', 0)
            ->orderBy('tanggal', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'kode' => $item->kodeLimbah->kode,
                    'deskripsi' => $item->kodeLimbah->deskripsi,
                    'sisa_berat' => $item->berat_kg,
                    'tanggal_masuk' => Carbon::parse($item->tanggal)->format('d/m/Y'),
                    'status' => $item->berat_kg > 0 ? 'Segera Diproses' : 'Menunggu Diproses',
                ];
            });

        $breadcrumb = (object)[
            'title' => 'Input Limbah Diolah',
            'list' => ['Login', 'Input Limbah Olah']
        ];
        return view('admin2.InputLimbahOlah', compact('mesin', 'kodeLimbah', 'dataDiolah', 'antreanLimbah', 'breadcrumb'))->with('activeMenu', 'limbahdiolah');
    }

    public function store(Request $request)
    {
        $request->validate([
            'detail.*.mesin_id' => 'required|exists:mesin,id',
            'detail.*.kode_limbah_id' => 'required|exists:kode_limbah,id',
            'detail.*.berat_kg' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->detail as $detail) {
                // Cek apakah ada sisa limbah yang cukup
                $sisaLimbah = SisaLimbah::where('kode_limbah_id', $detail['kode_limbah_id'])
                    ->where('berat_kg', '>=', $detail['berat_kg'])
                    ->orderBy('tanggal', 'asc')
                    ->first();

                if (!$sisaLimbah) {
                    throw new \Exception('Sisa limbah tidak mencukupi untuk kode limbah yang dipilih');
                }

                // Simpan ke limbah_diolah
                $limbahDiolah = LimbahDiolah::create([
                    'mesin_id' => $detail['mesin_id'],
                    'total_kg' => $detail['berat_kg'],
                ]);

                // Simpan ke detail_limbah_diolah
                DetailLimbahDiolah::create([
                    'limbah_diolah_id' => $limbahDiolah->id,
                    'kode_limbah_id' => $detail['kode_limbah_id'],
                    'berat_kg' => $detail['berat_kg'],
                    'tanggal_input' => now(),
                ]);

                // Update sisa_limbah
                $sisaLimbah->berat_kg -= $detail['berat_kg'];
                $sisaLimbah->save();

                // Hapus record sisa_limbah jika berat sudah 0
                if ($sisaLimbah->berat_kg <= 0) {
                    $sisaLimbah->delete();
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Data limbah berhasil diolah!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
    public function show(Request $request)
    {
        $mesinList = Mesin::all(); // Untuk dropdown filter

    // Query awal dengan relasi mesin
    $query = LimbahDiolah::with('mesin');

    // Filter berdasarkan no_mesin (relasi)
    if ($request->filled('no_mesin')) {
        $query->whereHas('mesin', function ($q) use ($request) {
            $q->where('no_mesin', $request->no_mesin);
        });
    }
        // Ambil data limbah yang sudah diolah
        $data = LimbahDiolah::with('mesin')
            ->orderBy('created_at', 'desc')
            ->get();

        // Breadcrumb untuk tampilan
        $breadcrumb = (object)[
            'title' => 'Data Limbah Diolah',
            'list' => ['Dashboard', 'Data Limbah Diolah']
        ];

        return view('admin2.DataLimbahOlah', compact('data', 'breadcrumb', 'mesinList'))
            ->with('activeMenu', 'datalimbaholah');
    }
    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|file|mimes:xlsx,xls',
        ]);

        DB::beginTransaction();
        try {
            $spreadsheet = IOFactory::load($request->file('file_excel'));
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Lewati baris header (baris 0)
            foreach (array_slice($rows, 1) as $row) {
                [$noMesin, $kodeLimbah, $beratKg] = $row;

                // Cari mesin dan kode limbah berdasarkan nilai dari Excel
                $mesin = Mesin::where('no_mesin', $noMesin)->first();
                $kode = KodeLimbah::where('kode', $kodeLimbah)->first();

                if (!$mesin || !$kode) {
                    throw new \Exception("Mesin atau Kode Limbah tidak ditemukan untuk: $noMesin / $kodeLimbah");
                }

                // Validasi berat
                if (!is_numeric($beratKg) || $beratKg <= 0) {
                    throw new \Exception("Berat tidak valid untuk kode limbah: $kodeLimbah");
                }

                // Cek sisa limbah
                $sisaLimbah = SisaLimbah::where('kode_limbah_id', $kode->id)
                    ->where('berat_kg', '>=', $beratKg)
                    ->orderBy('tanggal', 'asc')
                    ->first();

                if (!$sisaLimbah) {
                    throw new \Exception("Sisa limbah tidak mencukupi untuk kode: $kodeLimbah");
                }

                // Simpan limbah diolah
                $limbahDiolah = LimbahDiolah::create([
                    'mesin_id' => $mesin->id,
                    'total_kg' => $beratKg,
                ]);

                DetailLimbahDiolah::create([
                    'limbah_diolah_id' => $limbahDiolah->id,
                    'kode_limbah_id' => $kode->id,
                    'berat_kg' => $beratKg,
                    'tanggal_input' => now(),
                ]);

                // Update sisa limbah
                $sisaLimbah->berat_kg -= $beratKg;
                $sisaLimbah->save();

                if ($sisaLimbah->berat_kg <= 0) {
                    $sisaLimbah->delete();
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Data berhasil diimpor dari Excel!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $sheet->setCellValue('A1', 'no_mesin');
        $sheet->setCellValue('B1', 'kode_limbah');
        $sheet->setCellValue('C1', 'berat_kg');

        $writer = new Xlsx($spreadsheet);
        $filename = 'template_import_limbah.xlsx';

        // Download response
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set judul kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tanggal');
        $sheet->setCellValue('C1', 'Mesin');
        $sheet->setCellValue('D1', 'Kode Limbah (Deskripsi)');
        $sheet->setCellValue('E1', 'Berat (Kg)');

        $row = 2;
        $no = 1;

        // Ambil semua data limbah beserta relasi
        $limbahDiolahList = LimbahDiolah::with(['detailLimbahDiolah.kodeLimbah', 'mesin'])->get();

        foreach ($limbahDiolahList as $limbah) {
            foreach ($limbah->detailLimbahDiolah as $detail) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $detail->tanggal_input);
                $sheet->setCellValue('C' . $row, $limbah->mesin->nama ?? '-');

                // Gabungkan kode dan deskripsi kode limbah
                $kodeLimbah = $detail->kodeLimbah;
                $kodeDeskripsi = $kodeLimbah ? $kodeLimbah->kode . ' (' . $kodeLimbah->deskripsi . ')' : '-';
                $sheet->setCellValue('D' . $row, $kodeDeskripsi);

                $sheet->setCellValue('E' . $row, $detail->berat_kg);
                $row++;
            }
        }

        // Simpan dan download file
        $fileName = 'data_limbah_diolah.xlsx';
        $writer = new Xlsx($spreadsheet);

        // Atur header supaya langsung terdownload
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
