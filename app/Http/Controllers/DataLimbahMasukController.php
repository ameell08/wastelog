<?php

namespace App\Http\Controllers;

use App\Models\LimbahMasuk;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class DataLimbahMasukController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal');
        $query = LimbahMasuk::query();

        if ($tanggal) {
            $query->whereDate('tanggal', $tanggal);
        }

        $limbahMasuk = $query->selectRaw('DATE(tanggal) as tanggal, SUM(total_kg) as total_kg')
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'desc')
            ->paginate(10);

        $breadcrumb = (object)[
            'title' => 'Data Limbah Masuk',
            'list' => ['Input Limbah Masuk', 'Data Limbah Masuk']
        ];


        return view('admin1.DataLimbahMasuk', compact('limbahMasuk', 'tanggal', 'breadcrumb'))->with('activeMenu', 'datalimbahmasuk');
    }

    public function show($id)
    {
        $limbahMasuk = LimbahMasuk::findOrFail($id);
        $detail = $limbahMasuk->detailLimbahMasuk()->with(['truk', 'kodeLimbah'])->get(); // FIXED

        return response()->json([
            'tanggal' => $limbahMasuk->tanggal,
            'data' => $detail,
        ]);
    }

    public function export_excel()
    {
        try {
            $limbahMasuk = LimbahMasuk::with(['detailLimbahMasuk.truk', 'detailLimbahMasuk.kodeLimbah'])
                ->orderBy('tanggal', 'desc')
                ->get();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'No');
            $sheet->setCellValue('B1', 'Tanggal');
            $sheet->setCellValue('C1', 'Plat Nomor Truk');
            $sheet->setCellValue('D1', 'Kode Limbah');
            $sheet->setCellValue('E1', 'Berat (Kg)');

            $no = 1;
            $row = 2; // Mulai dari baris kedua
            foreach ($limbahMasuk as $item) {
                foreach ($item->detailLimbahMasuk as $detail) {
                    $sheet->setCellValue('A' . $row, $no);
                    $sheet->setCellValue('B' . $row, $item->tanggal);
                    $sheet->setCellValue('C' . $row, $detail->truk->plat_nomor);
                    $sheet->setCellValue('D' . $row, $detail->kodeLimbah->kode);
                    $sheet->setCellValue('E' . $row, $detail->berat_kg);
                    $row++;
                    $no++;
                }
            }
            foreach (range('A', 'E') as $columID) {
                $sheet->getColumnDimension($columID)->setAutoSize(true); //set auto size kolom
            }

            $sheet->setTitle('Data Limbah Masuk');
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $filename = 'Data Limbah Masuk ' . date('d-m-y H:i:s') . '.xlsx';
            // Set header untuk download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');

            $writer->save('php://output');
            // Simpan ke output untuk download
            // \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx')->save('php://output');
            exit;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export: ' . $e->getMessage());
        }
    }
    public function showByTanggal($tanggal)
    {
        $parsedTanggal = Carbon::parse($tanggal)->toDateString();

        $limbahMasuk = LimbahMasuk::whereDate('tanggal', $parsedTanggal)
            ->with(['detailLimbahMasuk.truk', 'detailLimbahMasuk.kodeLimbah'])
            ->get();

        $detail = [];

        foreach ($limbahMasuk as $item) {
            foreach ($item->detailLimbahMasuk as $d) {
                $detail[] = [
                    'plat_nomor' => $d->truk->plat_nomor,
                    'kode_limbah' => [
                        'kode' => $d->kodeLimbah->kode,
                        'deskripsi' => $d->kodeLimbah->deskripsi ?? '-',
                    ],
                    'berat_kg' => $d->berat_kg,
                ];
            }
        }

        return response()->json([
            'tanggal' => $parsedTanggal,
            'data' => $detail,
        ]);
    }

    public function detailexportexcel($tanggal)
    {
        try {
            $parsedTanggal = Carbon::parse($tanggal)->toDateString();

            $limbahMasukList = LimbahMasuk::whereDate('tanggal', $parsedTanggal)
                ->with(['detailLimbahMasuk.truk', 'detailLimbahMasuk.kodeLimbah'])
                ->get();

            if ($limbahMasukList->isEmpty()) {
                return redirect()->back()->with('error', 'Data tidak ditemukan untuk tanggal tersebut.');
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'Plat Nomor');
            $sheet->setCellValue('B1', 'Kode Limbah (Deskripsi)');
            $sheet->setCellValue('C1', 'Berat (Kg)');

            $row = 2;
            foreach ($limbahMasukList as $limbahMasuk) {
                foreach ($limbahMasuk->detailLimbahMasuk as $detail) {
                    $platNomor = $detail->truk->plat_nomor ?? '-';
                    $kode = $detail->kodeLimbah->kode ?? '-';
                    $deskripsi = $detail->kodeLimbah->deskripsi ?? '-';
                    $berat = $detail->berat_kg ?? 0;

                    $sheet->setCellValue('A' . $row, $platNomor);
                    $sheet->setCellValue('B' . $row, $kode . ' (' . $deskripsi . ')');
                    $sheet->setCellValue('C' . $row, $berat);
                    $row++;
                }
            }

            foreach (range('A', 'C') as $columID) {
                $sheet->getColumnDimension($columID)->setAutoSize(true);
            }

            $tanggalFormatted = Carbon::parse($parsedTanggal)->format('d-m-Y');
            $sheet->setTitle('Detail Limbah Masuk ' . $tanggalFormatted);

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $filename = 'Detail Limbah Masuk ' . $tanggalFormatted . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');

            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export: ' . $e->getMessage());
        }
    }
}
