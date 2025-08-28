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
        
        // Menggunakan tanggal_input dari detail_limbah_diolah
        $limbahdiolah = DB::table('detail_limbah_diolah')
            ->whereDate('tanggal_input', $today)
            ->sum('berat_kg');
            
        $sisalimbah = SisaLimbah::whereDate('tanggal', '<=', $today)->sum('berat_kg');

        // Hitung total residu limbah dari tabel antrean_residu (data yang sebenarnya tersimpan)
        $antreanResidu = AntreanResidu::with('kodeLimbah')->get();
        $totalResiduLimbah = $antreanResidu->sum('sisa_berat');

        // Data sumber untuk pie chart
        $dataSumberDetail = \App\Models\DetailLimbahMasuk::with(['sumber'])
            ->whereHas('sumber')
            ->get()
            ->groupBy('sumber.nama_sumber')
            ->map(function ($details, $namaSumber) {
                $sumber = $details->first()->sumber;
                $totalBerat = $details->sum('berat_kg');
                return [
                    'nama_sumber' => $namaSumber,
                    'kategori' => $sumber->kategori ?? 'Tidak Ada Kategori',
                    'total_berat' => $totalBerat
                ];
            })
            ->filter(function ($item) {
                return $item['total_berat'] > 0;
            })
            ->values();

        // Data sumber berdasarkan kategori (untuk tampilan awal)
        $dataSumber = $dataSumberDetail->groupBy('kategori')
            ->map(function ($items, $kategori) {
                $totalBerat = 0;
                $detailSumber = [];
                
                foreach ($items as $item) {
                    $totalBerat += $item['total_berat'];
                    $detailSumber[] = $item;
                }
                
                return [
                    'nama' => $kategori,
                    'total_berat' => $totalBerat,
                    'kategori' => $kategori,
                    'detail_sumber' => $detailSumber
                ];
            })
            ->values();

        // Ambil kategori unik untuk filter
        $kategoriSumber = $dataSumber->pluck('kategori')->unique()->values();

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

        // Menggunakan tanggal_input dari detail_limbah_diolah
        $diolah = DB::table('detail_limbah_diolah')
            ->selectRaw('MONTH(tanggal_input) as bulan, SUM(berat_kg) as total')
            ->groupByRaw('MONTH(tanggal_input)')
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
            'sisaLimbahBulanan',
            'dataSumber',
            'dataSumberDetail',
            'kategoriSumber'
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
        // Menggunakan tanggal_input dari detail_limbah_diolah
        $limbahDiolah = LimbahDiolah::with(['detailLimbahDiolah.kodeLimbah', 'mesin'])
            ->whereHas('detailLimbahDiolah', function($query) use ($bulan) {
                $query->whereMonth('tanggal_input', $bulan);
            })
            ->get();
            
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
                $detail->fly_ash = $flyAsh == (int)$flyAsh ? number_format($flyAsh, 0) : rtrim(rtrim(number_format($flyAsh, 4), '0'), '.');
                $detail->flue_gas = $flueGas == (int)$flueGas ? number_format($flueGas, 0) : rtrim(rtrim(number_format($flueGas, 4), '0'), '.');

                // Tambahkan persentase sebagai label tetap
                $detail->persen_bottom_ash = '2%';
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

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Bulan: ' . $namaBulan);
        $sheet->getStyle(('A1'))->getFont()->setBold(true);

        $headers = [
            'A2' => 'Tanggal',
            'B2' => 'Truk',
            'C2' => 'Kode Limbah',
            'D2' => 'Sumber',
            'E2' => 'Jumlah (kg)',
            'F2' => 'Kode Festronik'
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
                $sheet->setCellValue('D' . $row, $detail->sumber->nama_sumber ?? '-');
                $sheet->setCellValue('E' . $row, $detail->berat_kg);
                $sheet->setCellValue('F' . $row, $detail->kode_festronik ?? '-');
                $row++;
            }
        }

        $lastRow = $row - 1;
        $sheet->getStyle("A2:F{$lastRow}")
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
        // Menggunakan tanggal_input dari detail_limbah_diolah
        $limbahDiolah = LimbahDiolah::with(['detailLimbahDiolah.kodeLimbah', 'mesin'])
            ->whereHas('detailLimbahDiolah', function($query) use ($bulan) {
                $query->whereMonth('tanggal_input', $bulan);
            })
            ->get();

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
                $sheet->setCellValue('A' . $row, \Carbon\Carbon::parse($detail->tanggal_input)->format('Y-m-d'));
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

        // Data Limbah Diolah - menggunakan tanggal_input dari detail_limbah_diolah
        $limbahDiolah = LimbahDiolah::with(['detailLimbahDiolah.kodeLimbah'])
            ->whereHas('detailLimbahDiolah', function($query) use ($bulan, $tahun) {
                $query->whereMonth('tanggal_input', $bulan)
                      ->whereYear('tanggal_input', $tahun);
            })
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

        // Hitung sisa limbah dari bulan sebelumnya
        $bulanSebelumnya = $bulan - 1;
        $tahunSebelumnya = $tahun;
        
        if ($bulanSebelumnya <= 0) {
            $bulanSebelumnya = 12;
            $tahunSebelumnya = $tahun - 1;
        }

        // Total limbah masuk bulan sebelumnya
        $totalMasukBulanSebelumnya = LimbahMasuk::with(['detailLimbahMasuk'])
            ->whereMonth('tanggal', $bulanSebelumnya)
            ->whereYear('tanggal', $tahunSebelumnya)
            ->get()
            ->sum(function ($item) {
                return $item->detailLimbahMasuk->sum('berat_kg');
            });

        // Total limbah diolah bulan sebelumnya
        $totalDiolahBulanSebelumnya = LimbahDiolah::with(['detailLimbahDiolah'])
            ->whereHas('detailLimbahDiolah', function($query) use ($bulanSebelumnya, $tahunSebelumnya) {
                $query->whereMonth('tanggal_input', $bulanSebelumnya)
                      ->whereYear('tanggal_input', $tahunSebelumnya);
            })
            ->get()
            ->sum(function ($item) {
                return $item->detailLimbahDiolah->sum('berat_kg');
            });

        // Sisa limbah dari bulan sebelumnya
        $sisaLimbahBulanSebelumnya = $totalMasukBulanSebelumnya - $totalDiolahBulanSebelumnya;

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

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header utama
        $sheet->setCellValue('A1', 'PENGOLAHAN LIMBAH BAHAN BERBAHAYA DAN BERACUN');
        $sheet->setCellValue('A2', 'PT PUTRA RESTU IBU ABADI');
        $sheet->setCellValue('A3', 'Kegiatan : Pengolahan dengan Insinerator');
        $sheet->setCellValue('A4', 'Bulan : ' . $namaBulan);
        $sheet->setCellValue('A5', 'Tahun : ' . $tahun);

        // Merge cells untuk header
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');

        // Style header
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:A5')->getFont()->setBold(true);

        // Header tabel
        $headers = [
            'A7' => 'Tanggal',
            'B7' => 'Total Limbah Masuk (Kg)',
            'C7' => 'Total Limbah diolah (Kg)',
            'D7' => 'Sisa Limbah',
            'E7' => 'Total Residu',
            'F7' => 'Total pengiriman residu',
            'G7' => 'Sisa Residu'
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
                ->sum(function ($item) {
                    return $item->detailLimbahMasuk->sum('berat_kg');
                });

            // Total limbah diolah per hari - menggunakan tanggal_input
            $totalDiolahHari = $limbahDiolah->sum(function ($item) use ($tanggal) {
                return $item->detailLimbahDiolah->filter(function ($detail) use ($tanggal) {
                    return \Carbon\Carbon::parse($detail->tanggal_input)->format('Y-m-d') === $tanggal;
                })->sum('berat_kg');
            });

            // Hitung total residu dari limbah diolah per hari - menggunakan tanggal_input
            $totalResiduHari = $limbahDiolah->sum(function ($item) use ($tanggal) {
                return $item->detailLimbahDiolah->filter(function ($detail) use ($tanggal) {
                    return \Carbon\Carbon::parse($detail->tanggal_input)->format('Y-m-d') === $tanggal;
                })->sum(function ($detail) {
                    $berat = $detail->berat_kg;
                    $bottomAsh = $berat * 0.02;
                    $flyAsh = $bottomAsh * 0.004;
                    $flueGas = $flyAsh * 0.01;
                    return $bottomAsh + $flyAsh + $flueGas;
                });
            });

            // Total pengiriman residu per hari
            $totalPengirimanHari = $pengirimanResidu->filter(function ($item) use ($tanggal) {
                return $item->tanggal_pengiriman->format('Y-m-d') === $tanggal;
            })->sum(function ($item) {
                return $item->detailPengirimanResidu->sum('berat');
            });

            // Update total kumulatif
            $totalKumulatifMasuk += $totalMasukHari;
            $totalKumulatifDiolah += $totalDiolahHari;
            $totalKumulatifResidu += $totalResiduHari;
            $totalKumulatifPengiriman += $totalPengirimanHari;

            // Hitung sisa limbah = (Sisa Bulan Sebelumnya + Total Masuk Kumulatif) - Total Diolah Kumulatif
            $sisaLimbah = $sisaLimbahBulanSebelumnya + $totalKumulatifMasuk - $totalKumulatifDiolah;
            $sisaResidu = $totalKumulatifResidu - $totalKumulatifPengiriman;
            // Hanya tampilkan baris jika ada data
            if ($totalMasukHari > 0 || $totalDiolahHari > 0 || $sisaLimbah > 0 || $totalResiduHari > 0 || $totalPengirimanHari > 0 || $sisaResidu > 0) {
                $sheet->setCellValue('A' . $row, $date->format('d'));
                $sheet->setCellValue('B' . $row, number_format($totalMasukHari, 2));
                $sheet->setCellValue('C' . $row, number_format($totalDiolahHari, 2));
                $sheet->setCellValue('D' . $row, number_format($sisaLimbah, 2));
                $sheet->setCellValue('E' . $row, number_format($totalKumulatifResidu, 2));
                $sheet->setCellValue('F' . $row, number_format($totalPengirimanHari, 2));
                $sheet->setCellValue('G' . $row, number_format($sisaResidu, 2));
                $sheet->getStyle('A' . $row . ':G' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $row++;
            }
        }

        // Auto-resize kolom
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Border untuk tabel
        $lastRow = $row - 1;
        if ($lastRow >= 8) {
            $sheet->getStyle("A7:G{$lastRow}")
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
        }
        
        $sheet->setCellValue('G' . ($lastRow + 2), 'Dicetak pada: ' . now()->format('d/m/Y H:i'));
        $sheet->setCellValue('G' . ($lastRow + 3), 'Dicetak oleh: ' . auth()->user()->nama);
        $sheet->getStyle('G' . ($lastRow + 2) . ':G' . ($lastRow + 3))->getFont()->setSize(10)->setItalic(true);
        $sheet->getStyle('G' . ($lastRow + 2) . ':G' . ($lastRow + 3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        $filename = 'neraca_' . $namaBulan . '_' . $tahun . '_' . now()->format('d-m-y_H-i-s') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        // Output ke browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        $writer->save('php://output');
        exit;
    }

    public function exportNeracaPdf($bulan, $tahun)
    {
        $limbahMasuk = LimbahMasuk::with(['detailLimbahMasuk'])
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        // Menggunakan tanggal_input dari detail_limbah_diolah
        $limbahDiolah = LimbahDiolah::with(['detailLimbahDiolah'])
            ->whereHas('detailLimbahDiolah', function($query) use ($bulan, $tahun) {
                $query->whereMonth('tanggal_input', $bulan)
                      ->whereYear('tanggal_input', $tahun);
            })
            ->get();

        $pengirimanResidu = \App\Models\PengirimanResidu::with(['detailPengirimanResidu'])
            ->whereMonth('tanggal_pengiriman', $bulan)
            ->whereYear('tanggal_pengiriman', $tahun)
            ->get();

        // Hitung sisa limbah dari bulan sebelumnya
        $bulanSebelumnya = $bulan - 1;
        $tahunSebelumnya = $tahun;
        
        if ($bulanSebelumnya <= 0) {
            $bulanSebelumnya = 12;
            $tahunSebelumnya = $tahun - 1;
        }

        // Total limbah masuk bulan sebelumnya
        $totalMasukBulanSebelumnya = LimbahMasuk::with(['detailLimbahMasuk'])
            ->whereMonth('tanggal', $bulanSebelumnya)
            ->whereYear('tanggal', $tahunSebelumnya)
            ->get()
            ->sum(function ($item) {
                return $item->detailLimbahMasuk->sum('berat_kg');
            });

        // Total limbah diolah bulan sebelumnya
        $totalDiolahBulanSebelumnya = LimbahDiolah::with(['detailLimbahDiolah'])
            ->whereHas('detailLimbahDiolah', function($query) use ($bulanSebelumnya, $tahunSebelumnya) {
                $query->whereMonth('tanggal_input', $bulanSebelumnya)
                      ->whereYear('tanggal_input', $tahunSebelumnya);
            })
            ->get()
            ->sum(function ($item) {
                return $item->detailLimbahDiolah->sum('berat_kg');
            });

        // Sisa limbah dari bulan sebelumnya
        $sisaLimbahBulanSebelumnya = $totalMasukBulanSebelumnya - $totalDiolahBulanSebelumnya;

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

        $start = \Carbon\Carbon::createFromDate($tahun, $bulan, 1);
        $end   = $start->copy()->endOfMonth();

        $kumMasuk = 0;
        $kumDiolah = 0;
        $kumResidu = 0;
        $rows = [];

        $firstDayWithData = null;

        for ($d = $start->copy(); $d <= $end; $d->addDay()) {
            $tglStr = $d->format('Y-m-d');

            $masukHarian = $limbahMasuk->where('tanggal', $tglStr)->sum(function ($lm) {
                return $lm->detailLimbahMasuk->sum('berat_kg');
            });

            $diolahHarian = $limbahDiolah->sum(function ($ld) use ($tglStr) {
                return $ld->detailLimbahDiolah->filter(function ($det) use ($tglStr) {
                    return \Carbon\Carbon::parse($det->tanggal_input)->format('Y-m-d') === $tglStr;
                })->sum('berat_kg');
            });

            $residuHarian = $limbahDiolah->sum(function ($ld) use ($tglStr) {
                return $ld->detailLimbahDiolah->filter(function ($det) use ($tglStr) {
                    return \Carbon\Carbon::parse($det->tanggal_input)->format('Y-m-d') === $tglStr;
                })->sum(function ($det) {
                    $berat = (float) $det->berat_kg;
                    $bottom = $berat * 0.02;
                    $fly    = $bottom * 0.004;
                    $flue   = $fly * 0.01;
                    return $bottom + $fly + $flue;
                });
            });

            $kirimHarian = $pengirimanResidu
                ->filter(fn($pr) => $pr->tanggal_pengiriman->format('Y-m-d') === $tglStr)
                ->sum(fn($pr) => $pr->detailPengirimanResidu->sum('berat'));

            //  hari pertama yang punya data
            if ($firstDayWithData === null && ($masukHarian > 0 || $diolahHarian > 0 || $residuHarian > 0 || $kirimHarian > 0)) {
                $firstDayWithData = $d->copy();
            }

            // kumulatif dihitung terus, tapi baris hanya disimpan mulai firstDayWithData
            $kumMasuk  += $masukHarian;
            $kumDiolah += $diolahHarian;
            $kumResidu += $residuHarian;
            $sisa       = $sisaLimbahBulanSebelumnya + $kumMasuk - $kumDiolah;

            if ($firstDayWithData !== null && $d->greaterThanOrEqualTo($firstDayWithData)) {
                $rows[] = [
                    'hari'   => $d->format('j'),
                    'masuk'  => $masukHarian > 0 ? number_format($masukHarian, 2) : '0',
                    'diolah' => $diolahHarian > 0 ? number_format($diolahHarian, 2) : '0',
                    'sisa'   => $sisa > 0 ? number_format($sisa, 2) : '0',
                    'residu' => $kumResidu > 0 ? number_format($kumResidu, 2) : '0',
                    'kirim'  => $kirimHarian > 0 ? number_format($kirimHarian, 2) : '0',
                ];
            }
        }

        if (empty($rows)) {
            $rows[] = [
                'hari'   => '-',
                'masuk'  => '0',
                'diolah' => '0',
                'sisa'   => '0',
                'residu' => '0',
                'kirim'  => '0',
            ];
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'dashboard.export_neraca_pdf',
            [
                'rows'      => $rows,
                'namaBulan' => $namaBulan,
                'tahun'     => (int) $tahun,
            ]
        )->setPaper('a4', 'portrait');

        return $pdf->download('neraca_' . $namaBulan . '_' . $tahun . '_' . now()->format('d-m-y_H-i-s') . '.pdf');
    }
}
