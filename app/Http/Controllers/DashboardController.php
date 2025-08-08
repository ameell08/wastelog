<?php

namespace App\Http\Controllers;

use App\Models\LimbahDiolah;
use App\Models\LimbahMasuk;
use App\Models\SisaLimbah;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


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

        $masuk = LimbahMasuk::selectRaw('MONTH(tanggal) as bulan, SUM(total_kg) as total')
            ->groupByRaw('MONTH(tanggal)')
            ->pluck('total', 'bulan');

        $diolah = LimbahDiolah::selectRaw('MONTH(created_at) as bulan, SUM(total_kg) as total')
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'bulan');

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
            'bulan',
            'limbahMasukBulanan',
            'limbahDiolahBulanan'
        ))->with('activeMenu', 'dashboard');
    }

    public function exportLimbahMasukPdf()
    {
        $limbahMasuk = LimbahMasuk::with(['detailLimbahMasuk.truk', 'detailLimbahMasuk.kodeLimbah'])
            ->orderBy('tanggal', 'desc')
            ->get();

        $pdf = Pdf::loadView('dashboard.export_limbahmasuk_pdf', compact('limbahMasuk'));

        return $pdf->download('limbah_masuk_' . now()->format('d-m-y H:i:s') . '.pdf');
    }

    public function exportLimbahDiolahPdf()
{
    $limbahDiolah = LimbahDiolah::with(['detailLimbahDiolah.kodeLimbah', 'mesin'])
        ->orderBy('created_at', 'desc')
        ->get();

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

    $pdf = Pdf::loadView('dashboard.export_limbahdiolah_pdf', compact('limbahDiolah'));

    return $pdf->download('limbah_diolah_' . now()->format('d-m-y H:i:s') . '.pdf');
}
    public function exportLimbahMasukExcel()
{
    $limbahMasuk = LimbahMasuk::with(['detailLimbahMasuk.truk', 'detailLimbahMasuk.kodeLimbah'])
        ->orderBy('tanggal', 'desc')
        ->get();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header
    $sheet->setCellValue('A1', 'Tanggal');
    $sheet->setCellValue('B1', 'Truk');
    $sheet->setCellValue('C1', 'Kode Limbah');
    $sheet->setCellValue('D1', 'Jumlah (kg)');

    $row = 2;

    foreach ($limbahMasuk as $item) {
        foreach ($item->detailLimbahMasuk as $detail) {
            $sheet->setCellValue('A' . $row, $item->tanggal);
            $sheet->setCellValue('B' . $row, $detail->truk->plat_nomor ?? '-');
            $sheet->setCellValue('C' . $row, $detail->kodeLimbah->kode ?? '-');
            $sheet->setCellValue('D' . $row, $detail->berat_kg);
            $row++;
        }
    }

    $filename = 'limbah_masuk_' . now()->format('d-m-y H:i:s') . '.xlsx';
    $writer = new Xlsx($spreadsheet);

    // Output ke browser
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"{$filename}\"");
    $writer->save('php://output');
    exit;
}

public function exportLimbahDiolahExcel()
{
    $limbahDiolah = LimbahDiolah::with(['detailLimbahDiolah.kodeLimbah', 'mesin'])
        ->orderBy('created_at', 'desc')
        ->get();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header kolom
    $sheet->setCellValue('A1', 'Tanggal');
    $sheet->setCellValue('B1', 'Mesin');
    $sheet->setCellValue('C1', 'Kode Limbah');
    $sheet->setCellValue('D1', 'Jumlah (Kg)');
    $sheet->setCellValue('E1', 'Bottom Ash (2%) Kg');
    $sheet->setCellValue('F1', 'Fly Ash (0.4%) Kg');
    $sheet->setCellValue('G1', 'Flue Gas (1%) Kg');

    $row = 2;

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

    $filename = 'limbah_diolah_' . now()->format('d-m-y H:i:s') . '.xlsx';
    $writer = new Xlsx($spreadsheet);

    // Output ke browser
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"{$filename}\"");
    $writer->save('php://output');
    exit;
}


}
