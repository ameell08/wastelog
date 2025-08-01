<?php

namespace App\Http\Controllers;

use App\Models\LimbahDiolah;
use App\Models\LimbahMasuk;
use App\Models\SisaLimbah;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Carbon;

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
        $sisalimbah = SisaLimbah::whereDate('tanggal','<=', $today)->sum('berat_kg');

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
}
