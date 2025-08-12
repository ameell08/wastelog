<?php

namespace App\Http\Controllers;

use App\Models\PengirimanResidu;
use App\Models\DetailPengirimanResidu;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;

class DataPengirimanResiduController extends Controller
{
    /**
     * Format angka untuk menghilangkan trailing zeros
     */
    private function formatNumber($number, $decimals = 4)
    {
        if ($number == (int)$number) {
            return number_format($number, 0);
        }
        return rtrim(rtrim(number_format($number, $decimals), '0'), '.');
    }
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal');
        $query = PengirimanResidu::query();

        if ($tanggal) {
            $query->whereDate('tanggal_pengiriman', $tanggal);
        }

        $pengirimanResidu = $query->selectRaw('DATE(tanggal_pengiriman) as tanggal_pengiriman, SUM(total_berat) as total_berat')
            ->groupBy('tanggal_pengiriman')
            ->orderBy('tanggal_pengiriman', 'desc')
            ->paginate(10);

        $breadcrumb = (object)[
            'title' => 'Data Pengiriman Residu',
            'list' => ['Input Pengiriman Residu', 'Data Pengiriman Residu']
        ];

        return view('admin2.DataPengirimanResidu', compact('pengirimanResidu', 'tanggal', 'breadcrumb'))
               ->with('activeMenu', 'datapengirimanresidu');
    }

    public function showByTanggal($tanggal)
    {
        try {
            // Konversi format tanggal dari d-m-Y ke Y-m-d
            $tanggalFormatted = Carbon::createFromFormat('d-m-Y', $tanggal)->format('Y-m-d');
            
            $pengirimanResidu = PengirimanResidu::whereDate('tanggal_pengiriman', $tanggalFormatted)
                ->with(['detailPengirimanResidu.truk', 'detailPengirimanResidu.kodeLimbah'])
                ->get();

            $detail = [];
            foreach ($pengirimanResidu as $pengiriman) {
                foreach ($pengiriman->detailPengirimanResidu as $detailItem) {
                    $detail[] = [
                        'id' => $detailItem->id,
                        'pengiriman_id' => $pengiriman->id,
                        'tanggal_pengiriman' => $pengiriman->tanggal_pengiriman->format('d/m/Y'),
                        'plat_nomor' => $detailItem->truk->plat_nomor,
                        'kode_limbah' => [
                            'kode' => $detailItem->kodeLimbah->kode,
                            'deskripsi' => $detailItem->kodeLimbah->deskripsi
                        ],
                        'tanggal_masuk' => Carbon::parse($detailItem->tanggal_masuk)->format('d/m/Y'),
                        'berat' => $this->formatNumber($detailItem->berat)
                    ];
                }
            }

            return response()->json([
                'tanggal' => $tanggal,
                'data' => $detail,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Data tidak ditemukan atau format tanggal salah'
            ], 404);
        }
    }

    public function export_excel()
    {
        try {
            $pengirimanResidu = PengirimanResidu::with(['detailPengirimanResidu.truk', 'detailPengirimanResidu.kodeLimbah'])
                ->orderBy('tanggal_pengiriman', 'desc')
                ->get();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('E1', 'DATA KESELURUHAN PENGIRIMAN RESIDU');
            $sheet->getStyle(('E1'))->getFont()->setBold(true);

            $headers = [
                'A3' => 'No',
                'B3' => 'Tanggal Pengiriman',
                'C3' => 'Plat Nomor',
                'D3' => 'Kode Limbah',
                'E3' => 'Deskripsi Limbah',
                'F3' => 'Tanggal Masuk',
                'G3' => 'Berat (Kg)',
            ];
            foreach ($headers as $cell => $text) {
                $sheet->setCellValue($cell, $text);
                $sheet->getStyle($cell)->getFont()->setBold(true);
            }

            $row = 4;
            $no = 1;

            foreach ($pengirimanResidu as $pengiriman) {
                foreach ($pengiriman->detailPengirimanResidu as $detail) {
                    $sheet->setCellValue('A' . $row, $no);
                    $sheet->setCellValue('B' . $row, $pengiriman->tanggal_pengiriman->format('d/m/Y'));
                    $sheet->setCellValue('C' . $row, $detail->truk->plat_nomor);
                    $sheet->setCellValue('D' . $row, $detail->kodeLimbah->kode);
                    $sheet->setCellValue('E' . $row, $detail->kodeLimbah->deskripsi);
                    $sheet->setCellValue('F' . $row, Carbon::parse($detail->tanggal_masuk)->format('d/m/Y'));
                    $sheet->setCellValue('G' . $row, $detail->berat);

                    $row++;
                    $no++;
                }
            }

            $lastRow = $row - 1;
            $sheet->getStyle('A3:G' . $lastRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ]);
            // Auto width
            foreach (range('A', 'G') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $filename = 'Data_Pengiriman_Residu_' . date('Y-m-d_H-i-s') . '.xlsx';

            // Set headers untuk download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengexport data: ' . $e->getMessage());
        }
    }

    public function detailExportExcel($tanggal)
    {
        try {
            // Konversi format tanggal dari d-m-Y ke Y-m-d
            $tanggalFormatted = Carbon::createFromFormat('d-m-Y', $tanggal)->format('Y-m-d');
            
            $pengirimanResidu = PengirimanResidu::whereDate('tanggal_pengiriman', $tanggalFormatted)
                ->with(['detailPengirimanResidu.truk', 'detailPengirimanResidu.kodeLimbah'])
                ->get();

            if ($pengirimanResidu->isEmpty()) {
                return redirect()->back()->with('error', 'Data tidak ditemukan untuk tanggal tersebut');
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('D1', 'DATA PENGIRIMAN RESIDU' . ' - ' . Carbon::createFromFormat('d-m-Y', $tanggal)->format('d/m/Y'));
            $sheet->getStyle(('D1'))->getFont()->setBold(true);

            $headers = [
                'A3' => 'No',
                'B3' => 'Plat Nomor',
                'C3' => 'Kode Limbah',
                'D3' => 'Deskripsi Limbah',
                'E3' => 'Tanggal Masuk',
                'F3' => 'Berat (Kg)',
            ];
            foreach ($headers as $cell => $text) {
                $sheet->setCellValue($cell, $text);
                $sheet->getStyle($cell)->getFont()->setBold(true);
            }
            $row = 4;
            $no = 1;
            $totalBerat = 0;

            foreach ($pengirimanResidu as $pengiriman) {
                foreach ($pengiriman->detailPengirimanResidu as $detail) {
                    $sheet->setCellValue('A' . $row, $no);
                    $sheet->setCellValue('B' . $row, $detail->truk->plat_nomor);
                    $sheet->setCellValue('C' . $row, $detail->kodeLimbah->kode);
                    $sheet->setCellValue('D' . $row, $detail->kodeLimbah->deskripsi);
                    $sheet->setCellValue('E' . $row, Carbon::parse($detail->tanggal_masuk)->format('d/m/Y'));
                    $sheet->setCellValue('F' . $row, $detail->berat);

                    $totalBerat += $detail->berat;

                    $row++;
                    $no++;
                }
            }

            // Total row
            $sheet->setCellValue('E' . $row, 'Total Berat:');
            $sheet->setCellValue('F' . $row, $totalBerat);
            $sheet->getStyle('E' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A3:F' . $row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ]);

            // Auto width
            foreach (range('A', 'G') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $tanggalFilename = Carbon::createFromFormat('d-m-Y', $tanggal)->format('Y-m-d');
            $filename = 'Detail_Pengiriman_Residu_' . $tanggalFilename . '.xlsx';

            // Set headers untuk download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengexport data: ' . $e->getMessage());
        }
    }
}
