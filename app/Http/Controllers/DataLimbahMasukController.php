<?php

namespace App\Http\Controllers;

use App\Models\LimbahMasuk;
use App\Models\DetailLimbahMasuk;
use Illuminate\Http\Request;

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
            'tanggal' => $limbahMasuk->tanggal_masuk,
            'data' => $detail,
        ]);
    }
}
