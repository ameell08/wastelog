<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mesin;

class MesinController extends Controller
{
    public function index()
    {
        $dataMesin = Mesin::all();
        $breadcrumb = (object) [
            'title' => 'Data Mesin',
            'list' => ['Dashboard', 'Data Mesin']
        ];
        return view('superadmin.datamesin.index', compact('dataMesin', 'breadcrumb'))
            ->with('activeMenu', 'datamesin');
    }

    public function create()
    {
        return view('superadmin.datamesin.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_mesin' => 'required|unique:mesin,no_mesin',
            'status' => 'required|in:on,off',
            'keterangan' => 'nullable|string',
        ]);

        $mesin = Mesin::create($request->only('no_mesin', 'status', 'keterangan'));

        return response()->json($mesin);
    }

    public function edit($id)
    {
        $mesin = Mesin::findOrFail($id);
        return view('superadmin.datamesin.update', compact('mesin'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'no_mesin' => 'required|unique:mesin,no_mesin,' . $id,
            'status' => 'required|in:on,off',
            'keterangan' => 'nullable|string',
        ]);

        $mesin = Mesin::findOrFail($id);
        $mesin->update($request->only('no_mesin', 'status', 'keterangan'));

        return response()->json($mesin);
    }

    public function destroy($id)
    {
        Mesin::findOrFail($id)->delete();
        return response()->json(['message' => 'Data mesin berhasil dihapus.']);
    }
}
