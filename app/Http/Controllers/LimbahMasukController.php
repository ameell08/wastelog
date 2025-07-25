<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LimbahMasuk;
use App\Models\DetailLimbahMasuk;
use App\Models\Truk;
use App\Models\KodeLimbah;
use Illuminate\Support\Facades\DB;

class LimbahMasukController extends Controller
{
    public function index()
    {
        $truks = Truk::all();
        $kodeLimbahs = KodeLimbah::all();
        $breadcrumb = (object)[
        'title' => 'Input Limbah Masuk',
        'list' => ['Dashboard', 'Input Limbah Masuk']
    ];

    return view('admin1.InputLimbahMasuk', compact('truks', 'kodeLimbahs', 'breadcrumb'))->with('activeMenu', 'inputlimbahmasuk');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'detail.*.truk_id' => 'required|exists:truk,id',
            'detail.*.kode_limbah_id' => 'required|exists:kode_limbah,id',
            'detail.*.berat_kg' => 'required|numeric|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $totalKg = collect($request->detail)->sum('berat_kg');

            $limbahMasuk = LimbahMasuk::create([
                'tanggal' => $request->tanggal,
                'total_kg' => $totalKg,
            ]);

            foreach ($request->detail as $item) {
                $limbahMasuk->detailLimbahMasuk()->create([
                    'truk_id' => $item['truk_id'],
                    'kode_limbah_id' => $item['kode_limbah_id'],
                    'berat_kg' => $item['berat_kg'],
                ]);
            }
        });

        return redirect()->back()->with('success', 'Limbah berhasil disimpan.');
    }
}
