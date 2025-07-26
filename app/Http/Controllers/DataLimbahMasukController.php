<?php

namespace App\Http\Controllers;

use App\Models\LimbahMasuk;
use App\Models\DetailLimbahMasuk;
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

        $limbahMasuk = $query->paginate(10);
        $breadcrumb = (object)[
            'title' => 'Data Limbah Masuk',
            'list' => ['Input Limbah Masuk', 'Data Limbah Masuk']
        ];


        return view('admin1.DataLimbahMasuk', compact('limbahMasuk', 'tanggal', 'breadcrumb'))->with('activeMenu', 'datalimbahmasuk');
    }

    public function show($id)
    {
        $limbahMasuk = LimbahMasuk::findOrFail($id);
        $detail = $limbahMasuk->DetailLimbahMasuk()->with(['truk', 'kodeLimbah'])->get();

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
            $filename = 'Data Limbah Masuk ' . date('Y-m-d H:i:s') . '.xlsx';
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
}
