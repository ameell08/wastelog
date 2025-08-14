<?php

namespace App\Http\Controllers;

use App\Models\LimbahDiolah;
use App\Models\LimbahMasuk;
use App\Models\SisaLimbah;
use App\Models\AntreanResidu;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;

class DashboardController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Dashboard',
            'list' => ['Home', 'Dashboard'],
        ];

        $page = (object) [
            'title' => 'Dashboard',
        ];

        $activeMenu = 'dashboard';

        // Data total hari ini (real-time)
        $today = now()->toDateString();

        $limbahmasuk = LimbahMasuk::whereDate('tanggal', $today)->sum('total_kg');
        $limbahdiolah = LimbahDiolah::whereDate('created_at', $today)->sum('total_kg');
        $sisalimbah = SisaLimbah::whereDate('tanggal', '<=', $today)->sum('berat_kg');

        // Hitung total residu limbah dari tabel antrean_residu (data yang sebenarnya tersimpan)
        $antreanResidu = AntreanResidu::with('kodeLimbah')->get();
        $totalResiduLimbah = $antreanResidu->sum('sisa_berat');

        // Data per bulan (chart)
        $bulan = [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];

        $limbahMasukBulanan = array_fill(0, 12, 0);
        $limbahDiolahBulanan = array_fill(0, 12, 0);
        $sisaLimbahBulanan = array_fill(0, 12, 0);

        $masuk = LimbahMasuk::selectRaw('MONTH(tanggal) as bulan, SUM(total_kg) as total')
            ->groupByRaw('MONTH(tanggal)')
            ->pluck('total', 'bulan');

        $diolah = LimbahDiolah::selectRaw('MONTH(created_at) as bulan, SUM(total_kg) as total')
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'bulan');
        
        $sisa = SisaLimbah::selectRaw('MONTH(tanggal) as bulan, SUM(berat_kg) as total')
            ->groupByRaw('MONTH(tanggal)')
            ->pluck('total', 'bulan');

        foreach ($sisa as $bulanIndex => $total) {
            $sisaLimbahBulanan[$bulanIndex - 1] = (float) $total;
        }

        foreach ($masuk as $bulanIndex => $total) {
            $limbahMasukBulanan[$bulanIndex - 1] = (float) $total;
        }

        foreach ($diolah as $bulanIndex => $total) {
            $limbahDiolahBulanan[$bulanIndex - 1] = (float) $total;
        }

        return view('dashboard.dashboard', compact(
            'breadcrumb',
            'page',
            'activeMenu',
            'limbahmasuk',
            'limbahdiolah',
            'sisalimbah',
            'totalResiduLimbah',
            'bulan',
            'limbahMasukBulanan',
            'limbahDiolahBulanan',
            'sisaLimbahBulanan'
        ))->with('activeMenu', 'dashboard');
    }

    public function exportLimbahMasukPdf($bulan)
    {
        $limbahMasuk = LimbahMasuk::whereMonth('tanggal', $bulan)->get();
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
        ][$bulan] ?? '-';

        $pdf = Pdf::loadView('dashboard.export_limbahmasuk_pdf', compact('limbahMasuk', 'namaBulan'));

        return $pdf->download('limbah_masuk_' . now()->format('d-m-y H:i:s') . '.pdf');
    }

    public function exportLimbahDiolahPdf($bulan)
{
     $limbahDiolah = LimbahDiolah::whereMonth('created_at', $bulan)->get();
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
        ][$bulan] ?? '-';
      

    // Proses perhitungan residu
    foreach ($limbahDiolah as $item) {
        foreach ($item->detailLimbahDiolah as $detail) {
            $berat = $detail->berat_kg;

            // Hitung residu
            $bottomAsh = $berat * 0.02;
            $flyAsh = $bottomAsh * 0.004;
            $flueGas = $flyAsh * 0.01;

            // Tambahkan ke detail sebagai properti tambahan (tidak disimpan ke DB, hanya untuk tampilan)
            $detail->bottom_ash = $bottomAsh == (int)$bottomAsh ? number_format($bottomAsh, 0) : rtrim(rtrim(number_format($bottomAsh, 4), '0'), '.');
            $detail->fly_ash = $flyAsh == (int)$flyAsh ? number_format($flyAsh, 0) : rtrim(rtrim(number_format($flyAsh, 4), '0'), '.') ;
            $detail->flue_gas = $flueGas == (int)$flueGas ? number_format($flueGas, 0) : rtrim(rtrim(number_format($flueGas, 4), '0'), '.') ;

            // Tambahkan persentase sebagai label tetap
            $detail->persen_bottom_ash = '2%' ;
            $detail->persen_fly_ash = '0.4%';
            $detail->persen_flue_gas = '1%';
        }
    }

    $pdf = Pdf::loadView('dashboard.export_limbahdiolah_pdf', compact('limbahDiolah', 'namaBulan'));

    return $pdf->download('limbah_diolah_' . now()->format('d-m-y H:i:s') . '.pdf');
}
    public function exportLimbahMasukExcel($bulan)
{
    $limbahMasuk = LimbahMasuk::with(['detailLimbahMasuk.truk', 'detailLimbahMasuk.kodeLimbah'])
        ->whereMonth('tanggal', $bulan)
        ->orderBy('tanggal', 'desc')
        ->get();

     $namaBulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ][$bulan] ?? '-';

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('A1', 'Bulan: ' . $namaBulan);
    $sheet->getStyle(('A1'))->getFont()->setBold(true);

    $headers = [
        'A2' => 'Tanggal',
        'B2' => 'Truk',
        'C2' => 'Kode Limbah',
        'D2' => 'Jumlah (kg)',
        'E2' => 'Kode Festronik'
    ];
    foreach ($headers as $cell => $text) {
        $sheet->setCellValue($cell, $text);
        $sheet->getStyle($cell)->getFont()->setBold(true);
    }
    $row = 3;

    foreach ($limbahMasuk as $item) {
        foreach ($item->detailLimbahMasuk as $detail) {
            $sheet->setCellValue('A' . $row, $item->tanggal);
            $sheet->setCellValue('B' . $row, $detail->truk->plat_nomor ?? '-');
            $sheet->setCellValue('C' . $row, $detail->kodeLimbah->kode ?? '-');
            $sheet->setCellValue('D' . $row, $detail->berat_kg);
            $sheet->setCellValue('E' . $row, $detail->kode_festronik ?? '-');
            $row++;
        }
    }

    $lastRow = $row - 1;
    $sheet->getStyle("A2:E{$lastRow}")
        ->getBorders()
        ->getAllBorders()
        ->setBorderStyle(Border::BORDER_THIN);

    $filename = 'limbah_masuk_' . $namaBulan . '_'  . now()->format('d-m-y H:i:s') . '.xlsx';
    $writer = new Xlsx($spreadsheet);

    // Output ke browser
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"{$filename}\"");
    $writer->save('php://output');
    exit;
}

public function exportLimbahDiolahExcel($bulan)
{
    $limbahDiolah = LimbahDiolah::with(['detailLimbahDiolah.kodeLimbah', 'mesin'])
        ->whereMonth('created_at', $bulan)
        ->orderBy('created_at', 'desc')
        ->get();

     $namaBulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ][$bulan] ?? '-';

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('A1', 'Bulan: ' . $namaBulan);
    $sheet->getStyle('A1')->getFont()->setBold(true);

    // Header kolom
     $headers = [
        'A2' => 'Tanggal',
        'B2' => 'Mesin',
        'C2' => 'Kode Limbah',
        'D2' => 'Jumlah (Kg)',
        'E2' => 'Bottom Ash (2%) Kg',
        'F2' => 'Fly Ash (0.4%) Kg',
        'G2' => 'Flue Gas (1%) Kg'
    ];
    foreach ($headers as $cell => $text) {
        $sheet->setCellValue($cell, $text);
        $sheet->getStyle($cell)->getFont()->setBold(true);
    }

    $row = 3;

    foreach ($limbahDiolah as $item) {
        foreach ($item->detailLimbahDiolah as $detail) {
            $berat = $detail->berat_kg;

            // Perhitungan residu
            $bottomAsh = $berat * 0.02;
            $flyAsh = $bottomAsh * 0.004;
            $flueGas = $flyAsh * 0.01;

            // Set nilai ke sheet
            $sheet->setCellValue('A' . $row, $item->created_at->format('Y-m-d'));
            $sheet->setCellValue('B' . $row, $item->mesin->no_mesin ?? '-');
            $sheet->setCellValue('C' . $row, $detail->kodeLimbah->kode ?? '-');
            $sheet->setCellValue('D' . $row, number_format($berat, 2));
            $sheet->setCellValue('E' . $row, number_format($bottomAsh, 4));
            $sheet->setCellValue('F' . $row, number_format($flyAsh, 4));
            $sheet->setCellValue('G' . $row, number_format($flueGas, 4));

            $row++;
        }
    }

    $lastRow = $row - 1;
    $sheet->getStyle("A2:G{$lastRow}")
        ->getBorders()
        ->getAllBorders()
        ->setBorderStyle(Border::BORDER_THIN);

    $filename = 'limbah_diolah_' . $namaBulan . '_' . now()->format('d-m-y H:i:s') . '.xlsx';
    $writer = new Xlsx($spreadsheet);

    // Output ke browser
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"{$filename}\"");
    $writer->save('php://output');
    exit;
}
public function exportNeracaExcel($bulan, $tahun)
{
    // Data Limbah Masuk
    $limbahMasuk = LimbahMasuk::with(['detailLimbahMasuk.kodeLimbah'])
        ->whereMonth('tanggal', $bulan)
        ->whereYear('tanggal', $tahun)
        ->get();

    // Data Limbah Diolah
    $limbahDiolah = LimbahDiolah::with(['detailLimbahDiolah.kodeLimbah'])
        ->whereMonth('created_at', $bulan)
        ->whereYear('created_at', $tahun)
        ->get();

    // Data Sisa Limbah
    $sisaLimbah = SisaLimbah::with(['kodeLimbah'])
        ->whereMonth('tanggal', $bulan)
        ->whereYear('tanggal', $tahun)
        ->get();

    // Data Pengiriman Residu
    $pengirimanResidu = \App\Models\PengirimanResidu::with(['detailPengirimanResidu.kodeLimbah'])
        ->whereMonth('tanggal_pengiriman', $bulan)
        ->whereYear('tanggal_pengiriman', $tahun)
        ->get();

    $namaBulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ][$bulan] ?? '-';

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header utama
    $sheet->setCellValue('A1', 'PENGELOLAAN LIMBAH BAHAN BERBAHAYA DAN BERACUN');
    $sheet->setCellValue('A2', 'PT PUTRA RESTU IBU ABADI');
    $sheet->setCellValue('A4', 'Bulan : ' . $namaBulan);
    $sheet->setCellValue('A5', 'Tahun : ' . $tahun);

    // Merge cells untuk header
    $sheet->mergeCells('A1:G1');
    $sheet->mergeCells('A2:G2');

    // Style header
    $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A4:A5')->getFont()->setBold(true);

    // Header tabel
    $headers = [
        'A7' => 'Tanggal',
        'B7' => 'Total Limbah Masuk (Kg)',
        'C7' => 'Total Limbah diolah (Kg)',
        'D7' => 'Sisa Limbah',
        'E7' => 'Total Residu',
        'F7' => 'Total pengiriman residu'
    ];

    foreach ($headers as $cell => $text) {
        $sheet->setCellValue($cell, $text);
        $sheet->getStyle($cell)->getFont()->setBold(true);
        $sheet->getStyle($cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E8E8E8');
    }

    // Ambil semua tanggal dalam bulan
    $startDate = \Carbon\Carbon::createFromDate($tahun, $bulan, 1);
    $endDate = $startDate->copy()->endOfMonth();
    $row = 8;

    // Variabel untuk menyimpan total kumulatif
    $totalKumulatifMasuk = 0;
    $totalKumulatifDiolah = 0;
    $totalKumulatifResidu = 0;
    $totalKumulatifPengiriman = 0;

    // Loop untuk setiap hari dalam bulan
    for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
        $tanggal = $date->format('Y-m-d');
        
        // Total limbah masuk per hari
        $totalMasukHari = $limbahMasuk->where('tanggal', $tanggal)
            ->sum(function($item) {
                return $item->detailLimbahMasuk->sum('berat_kg');
            });

        // Total limbah diolah per hari
        $totalDiolahHari = $limbahDiolah->filter(function($item) use ($tanggal) {
            return $item->created_at->format('Y-m-d') === $tanggal;
        })->sum(function($item) {
            return $item->detailLimbahDiolah->sum('berat_kg');
        });

        // Hitung total residu dari limbah diolah per hari
        $totalResiduHari = $limbahDiolah->filter(function($item) use ($tanggal) {
            return $item->created_at->format('Y-m-d') === $tanggal;
        })->sum(function($item) {
            return $item->detailLimbahDiolah->sum(function($detail) {
                $berat = $detail->berat_kg;
                $bottomAsh = $berat * 0.02;
                $flyAsh = $bottomAsh * 0.004;
                $flueGas = $flyAsh * 0.01;
                return $bottomAsh + $flyAsh + $flueGas;
            });
        });

        // Total pengiriman residu per hari
        $totalPengirimanHari = $pengirimanResidu->filter(function($item) use ($tanggal) {
            return $item->tanggal_pengiriman->format('Y-m-d') === $tanggal;
        })->sum(function($item) {
            return $item->detailPengirimanResidu->sum('berat');
        });

        // Update total kumulatif
        $totalKumulatifMasuk += $totalMasukHari;
        $totalKumulatifDiolah += $totalDiolahHari;
        $totalKumulatifResidu += $totalResiduHari;
        $totalKumulatifPengiriman += $totalPengirimanHari;

        // Hitung sisa limbah = Total Masuk Kumulatif - Total Diolah Kumulatif
        $sisaLimbah = $totalKumulatifMasuk - $totalKumulatifDiolah;

        // Hanya tampilkan baris jika ada data
        if ($totalMasukHari > 0 || $totalDiolahHari > 0 || $sisaLimbah > 0 || $totalResiduHari > 0 || $totalPengirimanHari > 0) {
            $sheet->setCellValue('A' . $row, $date->format('d'));
            $sheet->setCellValue('B' . $row, number_format($totalMasukHari, 2));
            $sheet->setCellValue('C' . $row, number_format($totalDiolahHari, 2));
            $sheet->setCellValue('D' . $row, number_format($sisaLimbah, 2));
            $sheet->setCellValue('E' . $row, number_format($totalKumulatifResidu, 2));
            $sheet->setCellValue('F' . $row, number_format($totalPengirimanHari, 2));
            $row++;
        }
    }

    // Auto-resize kolom
    foreach (range('A', 'F') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Border untuk tabel
    $lastRow = $row - 1;
    if ($lastRow >= 8) {
        $sheet->getStyle("A7:F{$lastRow}")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
    }

    $filename = 'neraca_' . $namaBulan . '_' . $tahun . '_' . now()->format('d-m-y_H-i-s') . '.xlsx';
    $writer = new Xlsx($spreadsheet);

    // Output ke browser
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"{$filename}\"");
    $writer->save('php://output');
    exit;
}


}
