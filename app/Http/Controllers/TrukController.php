<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Truk;

class TrukController extends Controller
{
    public function index()
    {
        $dataTruk = Truk::all();
        $breadcrumb = (object)[
            'title' => 'Data Truk',
            'list' => ['Dashboard', 'Data Truk']
        ];
        return view('superadmin.datatruk.index', compact('dataTruk', 'breadcrumb'))
            ->with('activeMenu', 'datatruk');
    }

    public function create()
    {
        return view('superadmin.datatruk.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'plat_nomor' => 'required|unique:truk,plat_nomor',
            'nama_sopir' => 'required|string',
        ]);

        $truk = Truk::create($request->only('plat_nomor', 'nama_sopir'));

        return response()->json($truk);
    }

    public function edit($id)
    {
        $truk = Truk::findOrFail($id);
        return view('superadmin.datatruk.edit', compact('truk'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'plat_nomor' => 'required|unique:truk,plat_nomor,' . $id,
            'nama_sopir' => 'required|string',
        ]);

        $truk = Truk::findOrFail($id);
        $truk->update($request->only('plat_nomor', 'nama_sopir'));

        return response()->json($truk);
    }

    public function destroy($id)
    {
        Truk::findOrFail($id)->delete();

        return redirect()->route('truk.index')->with('success', 'Data truk berhasil dihapus.');
    }
}
