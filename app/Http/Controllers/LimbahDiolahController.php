<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LimbahDiolah;
use App\Models\DetailLimbahDiolah;
use App\Models\Mesin;
use App\Models\KodeLimbah;
use App\Models\SisaLimbah;
use App\Models\AntreanResidu;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LimbahDiolahController extends Controller
{
    public function index()
    {
        $mesin = Mesin::where('status', 'on')->get();
        $mesinOff = Mesin::where('status', 'off')->get();
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

        // Antrean Limbah - Ambil data sisa limbah yang belum diolah dengan urutan FIFO
        $antreanLimbahRaw = SisaLimbah::with('kodeLimbah')
            ->where('berat_kg', '>', 0)
            ->orderBy('tanggal', 'asc') // FIFO: tampilkan berdasarkan tanggal masuk paling awal
            ->orderBy('id', 'asc')
            ->get();

        // Gabungkan item dengan kode limbah dan tanggal yang sama
        $antreanLimbahGrouped = $antreanLimbahRaw->groupBy(function ($item) {
            return $item->kodeLimbah->kode . '|' . \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d');
        });

        $antreanLimbah = $antreanLimbahGrouped->map(function ($group) {
            $firstItem = $group->first();
            $totalBerat = $group->sum('berat_kg');

            return [
                'kode' => $firstItem->kodeLimbah->kode,
                'deskripsi' => $firstItem->kodeLimbah->deskripsi,
                'sisa_berat' => $totalBerat,
                'tanggal_masuk' => \Carbon\Carbon::parse($firstItem->tanggal)->format('d/m/Y'),
                'hari_menunggu' => $firstItem->hari_menunggu,
                'status' => $firstItem->status,
            ];
        })->values(); // values() untuk reset index array

        $breadcrumb = (object)[
            'title' => 'Input Limbah Diolah',
            'list' => ['Login', 'Input Limbah Olah']
        ];
        return view('admin2.InputLimbahOlah', compact('mesin', 'mesinOff', 'kodeLimbah', 'dataDiolah', 'antreanLimbah', 'breadcrumb'))->with('activeMenu', 'limbahdiolah');
    }

    public function store(Request $request)
    {
        $request->validate([
            'detail.*.mesin_id' => [
                'required',
                'exists:mesin,id',
                function ($attribute, $value, $fail) {
                    $mesin = Mesin::find($value);
                    if (!$mesin || $mesin->status !== 'on') {
                        $fail('Mesin yang dipilih sedang tidak aktif.');
                    }
                }
            ],
            'detail.*.kode_limbah_id' => 'required|exists:kode_limbah,id',
            'detail.*.berat_kg' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->detail as $detail) {
                $kodeLimbahId = $detail['kode_limbah_id'];
                $beratDibutuhkan = $detail['berat_kg'];
                $mesinId = $detail['mesin_id'];

                // Cek apakah mesin dalam status 'on'
                $mesin = Mesin::find($mesinId);
                if (!$mesin || $mesin->status !== 'on') {
                    throw new \Exception('Mesin yang dipilih sedang tidak aktif atau tidak tersedia.');
                }

                // Cek apakah stok mencukupi
                if (!SisaLimbah::checkAvailableStock($kodeLimbahId, $beratDibutuhkan)) {
                    $availableStock = SisaLimbah::where('kode_limbah_id', $kodeLimbahId)
                        ->where('berat_kg', '>', 0)
                        ->sum('berat_kg');
                    throw new \Exception('Sisa limbah tidak mencukupi untuk kode limbah yang dipilih. Tersedia: ' . $availableStock . ' Kg, Diminta: ' . $beratDibutuhkan . ' Kg');
                }

                // Simpan ke limbah_diolah
                $limbahDiolah = LimbahDiolah::create([
                    'mesin_id' => $mesinId,
                    'total_kg' => $beratDibutuhkan,
                ]);

                // Simpan ke detail_limbah_diolah
                DetailLimbahDiolah::create([
                    'limbah_diolah_id' => $limbahDiolah->id,
                    'kode_limbah_id' => $kodeLimbahId,
                    'berat_kg' => $beratDibutuhkan,
                    'tanggal_input' => now(),
                ]);

                // Proses pengurangan sisa limbah dengan sistem FIFO
                SisaLimbah::processFifoConsumption($kodeLimbahId, $beratDibutuhkan);

                // Tambahkan residu otomatis ke antrean_residu
                $this->createResiduFromLimbahDiolah($beratDibutuhkan, $request->tanggal);
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

        // Ambil data limbah yang sudah diolah menggunakan query yang sudah difilter
        // Gabungkan data berdasarkan mesin_id
        $data = $query->select('mesin_id', DB::raw('SUM(total_kg) as total_kg'))
            ->groupBy('mesin_id')
            ->with('mesin')
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
            'file_limbah_olah' => 'required|file|mimes:xlsx,xls',
        ]);

        DB::beginTransaction();
        try {
            $spreadsheet = IOFactory::load($request->file('file_limbah_olah'));
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Lewati baris header (baris 0)
            foreach (array_slice($rows, 1) as $row) {
                [$noMesin, $kodeLimbah, $beratKg] = $row;

                // Cari mesin dan kode limbah berdasarkan nilai dari Excel
                $mesin = Mesin::where('no_mesin', $noMesin)->where('status', 'on')->first();
                $kode = KodeLimbah::where('kode', $kodeLimbah)->first();

                if (!$mesin) {
                    throw new \Exception("Mesin $noMesin tidak ditemukan atau sedang tidak aktif.");
                }

                if (!$kode) {
                    throw new \Exception("Kode Limbah $kodeLimbah tidak ditemukan.");
                }

                // Validasi berat
                if (!is_numeric($beratKg) || $beratKg <= 0) {
                    throw new \Exception("Berat tidak valid untuk kode limbah: $kodeLimbah");
                }

                // Cek sisa limbah dengan sistem FIFO
                if (!SisaLimbah::checkAvailableStock($kode->id, $beratKg)) {
                    $availableStock = SisaLimbah::where('kode_limbah_id', $kode->id)
                        ->where('berat_kg', '>', 0)
                        ->sum('berat_kg');
                    throw new \Exception("Sisa limbah tidak mencukupi untuk kode: $kodeLimbah. Tersedia: $availableStock Kg, Diminta: $beratKg Kg");
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

                // Proses pengurangan sisa limbah dengan sistem FIFO
                SisaLimbah::processFifoConsumption($kode->id, $beratKg);
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

        $sheet->setCellValue('C1', 'DATA LIMBAH DIOLAH');
        $sheet->getStyle(('C1'))->getFont()->setBold(true);

        $headers = [
            'A3' => 'No',
            'B3' => 'Tanggal',
            'C3' => 'Mesin',
            'D3' => 'Kode Limbah (Deskripsi)',
            'E3' => 'Berat (Kg)',
        ];
        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
            $sheet->getStyle($cell)->getFont()->setBold(true);
        }

        $row = 4;
        $no = 1;

        // Ambil semua data limbah beserta relasi
        $limbahDiolahList = LimbahDiolah::with(['detailLimbahDiolah.kodeLimbah', 'mesin'])->get();

        foreach ($limbahDiolahList as $limbah) {
            foreach ($limbah->detailLimbahDiolah as $detail) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $detail->tanggal_input);
                $sheet->setCellValue('C' . $row, $limbah->mesin->no_mesin ?? '-');

                // Gabungkan kode dan deskripsi kode limbah
                $kodeLimbah = $detail->kodeLimbah;
                $kodeDeskripsi = $kodeLimbah ? $kodeLimbah->kode . ' (' . $kodeLimbah->deskripsi . ')' : '-';
                $sheet->setCellValue('D' . $row, $kodeDeskripsi);

                $sheet->setCellValue('E' . $row, $detail->berat_kg);
                $row++;
            }

            $lastRow = $row - 1;
            $sheet->getStyle("A3:E{$lastRow}")
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
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
    public function getDetailByMesin($mesin_id)
    {
        $details = DetailLimbahDiolah::with(['kodeLimbah', 'limbahDiolah'])
            ->whereHas('limbahDiolah', function ($q) use ($mesin_id) {
                $q->where('mesin_id', $mesin_id);
            })
            ->get()
            ->map(function ($item) {
                $bottomAsh = 0;
                $flyAsh = 0;
                $flueGas = 0;

                $kode = optional($item->kodeLimbah)->kode;
                $deskripsi = optional($item->kodeLimbah)->deskripsi;
                $berat = $item->berat_kg;

                // Bottom Ash: 2% dari berat limbah yang dibakar
                $bottomAsh = $berat * 0.02; // 2% dari berat input

                // Fly Ash: 0.4% dari berat Bottom Ash
                $flyAsh = $bottomAsh * 0.004; // 0.4% dari berat Bottom Ash

                // Flue Gas: 1% dari berat Fly Ash
                $flueGas = $flyAsh * 0.01; // 1% dari berat Fly Ash

                return [
                    'limbah_diolah_id' => $item->limbah_diolah_id,
                    'kode_limbah' => [
                        'kode' => $kode ?? '-',
                        'deskripsi' => $deskripsi ?? '-',
                    ],
                    'berat_kg' => number_format($berat, 2),
                    'tanggal_input' => Carbon::parse($item->created_at)->format('d-m-Y H:i'),
                    'bottom_ash' => $bottomAsh == (int)$bottomAsh ? number_format($bottomAsh, 0) : rtrim(rtrim(number_format($bottomAsh, 4), '0'), '.'),
                    'fly_ash' => $flyAsh == (int)$flyAsh ? number_format($flyAsh, 0) : rtrim(rtrim(number_format($flyAsh, 4), '0'), '.'),
                    'flue_gas' => $flueGas == (int)$flueGas ? number_format($flueGas, 0) : rtrim(rtrim(number_format($flueGas, 4), '0'), '.'),
                    // Semua limbah menghasilkan residu dengan persentase yang sama
                    'persen_bottom_ash' => '2%', // Total berat input limbah menghasilkan 2% bottom ash
                    'persen_fly_ash' => '0.4%', // Menghasilkan 0.4% fly ash dari bottom ash
                    'persen_flue_gas' => '1%', // Menghasilkan 1% flue gas dari fly ash
                ];
            });

        return response()->json($details);
    }
    public function exportByMonth($mesin_id, $bulan)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        $sheet->setCellValue('D1', 'DETAIL LIMBAH DIOLAH ');
        $sheet->setCellValue('D2', 'Bulan : ' . ($namaBulan[(int)$bulan] ?? $bulan));
        $sheet->getStyle(('D1'))->getFont()->setBold(true);
        $sheet->getStyle(('D2'))->getFont()->setBold(true);

        $headers = [
            'A4' => 'No',
            'B4' => 'Tanggal',
            'C4' => 'Mesin',
            'D4' => 'Kode Limbah (Deskripsi)',
            'E4' => 'Berat Input (Kg)',
            'F4' => 'Bottom Ash (2%)',
            'G4' => 'Fly Ash (0.4%)',
            'H4' => 'Flue Gas (1%)'
        ];
        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
            $sheet->getStyle($cell)->getFont()->setBold(true);
        }

        $row = 5;
        $no = 1;

        // Ambil data yang sudah difilter berdasarkan mesin_id dan bulan
        $details = DetailLimbahDiolah::with(['kodeLimbah', 'limbahDiolah.mesin'])
            ->whereHas('limbahDiolah', function ($q) use ($mesin_id) {
                $q->where('mesin_id', $mesin_id);
            })
            ->whereMonth('tanggal_input', $bulan)
            ->get();

        foreach ($details as $detail) {
            $berat = $detail->berat_kg;

            // Hitung residu sesuai dengan logic di getDetailByMesin
            $bottomAsh = $berat * 0.02; // 2% dari berat input
            $flyAsh = $bottomAsh * 0.004; // 0.4% dari berat Bottom Ash
            $flueGas = $flyAsh * 0.01; // 1% dari berat Fly Ash

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, Carbon::parse($detail->created_at)->format('d/m/Y H:i'));
            $sheet->setCellValue('C' . $row, $detail->limbahDiolah->mesin->no_mesin ?? '-');

            $kodeLimbah = $detail->kodeLimbah;
            $kodeDeskripsi = $kodeLimbah ? $kodeLimbah->kode . ' (' . $kodeLimbah->deskripsi . ')' : '-';
            $sheet->setCellValue('D' . $row, $kodeDeskripsi);

            $sheet->setCellValue('E' . $row, number_format($detail->berat_kg, 2) . ' Kg');
            $sheet->setCellValue('F' . $row, ($bottomAsh == (int)$bottomAsh ? number_format($bottomAsh, 0) : rtrim(rtrim(number_format($bottomAsh, 4), '0'), '.')) . ' Kg');
            $sheet->setCellValue('G' . $row, ($flyAsh == (int)$flyAsh ? number_format($flyAsh, 0) : rtrim(rtrim(number_format($flyAsh, 4), '0'), '.')) . ' Kg');
            $sheet->setCellValue('H' . $row, ($flueGas == (int)$flueGas ? number_format($flueGas, 0) : rtrim(rtrim(number_format($flueGas, 4), '0'), '.')) . ' Kg');

            $row++;
        }
        $lastRow = $row - 1;
        $sheet->getStyle("A4:H{$lastRow}")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Ambil nama mesin untuk nama file
        $mesin = Mesin::find($mesin_id);
        $namaFile = 'detail_limbah_diolah_' . ($mesin ? $mesin->no_mesin : 'mesin') . '_bulan_' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        // Header response
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$namaFile\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Membuat residu otomatis setiap input limbah diolah
     * Berdasarkan perhitungan residu yang sudah ada di aplikasi
     */
    private function createResiduFromLimbahDiolah($beratLimbah, $tanggalMasuk)
    {
        // Perhitungan residu berdasarkan logika yang sudah ada
        $bottomAsh = $beratLimbah * 0.02; // 2% dari berat limbah
        $flyAsh = $bottomAsh * 0.004; // 0.4% dari bottom ash
        $flueGas = $flyAsh * 0.01; // 1% dari fly ash

        // Cari kode limbah untuk masing-masing residu
        $kodeLimbahBottomAsh = KodeLimbah::where('kode', 'A347-2')->first(); // Bottom ash Insenerator
        $kodeLimbahFlyAsh = KodeLimbah::where('kode', 'A347-1')->first(); // Fly ash Insenerator  
        $kodeLimbahFlueGas = KodeLimbah::where('kode', 'B347-1')->first(); // Residu pengolahan Flue gas

        if (!$kodeLimbahBottomAsh || !$kodeLimbahFlyAsh || !$kodeLimbahFlueGas) {
            throw new \Exception('Kode limbah untuk residu belum tersedia. Pastikan kode A347-2, A347-1, dan B347-1 sudah dibuat.');
        }

        // Simpan ke antrean_residu
        $residuData = [
            [
                'kode_limbah_id' => $kodeLimbahBottomAsh->id,
                'tanggal_masuk' => $tanggalMasuk,
                'berat_total' => $bottomAsh,
            ],
            [
                'kode_limbah_id' => $kodeLimbahFlyAsh->id,
                'tanggal_masuk' => $tanggalMasuk,
                'berat_total' => $flyAsh,
            ],
            [
                'kode_limbah_id' => $kodeLimbahFlueGas->id,
                'tanggal_masuk' => $tanggalMasuk,
                'berat_total' => $flueGas,
            ]
        ];

        foreach ($residuData as $residu) {
            // Cek apakah sudah ada residu dengan kode limbah dan tanggal yang sama
            $existingResidu = AntreanResidu::where('kode_limbah_id', $residu['kode_limbah_id'])
                ->where('tanggal_masuk', $residu['tanggal_masuk'])
                ->first();

            if ($existingResidu) {
                // Jika sudah ada, tambahkan beratnya
                $existingResidu->berat_total += $residu['berat_total'];
                $existingResidu->save();
            } else {
                // Jika belum ada, buat baru
                AntreanResidu::create($residu);
            }
        }
    }
}
