<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sumber;

class SumberController extends Controller
{
    public function index()
    {
        $dataSumber = Sumber::all();
        $breadcrumb = (object)[
            'title' => 'Data Sumber Limbah',
            'list' => ['Dashboard', 'Data Sumber Limbah']
        ];
        return view('superadmin.datasumber.index', compact('dataSumber', 'breadcrumb'))
            ->with('activeMenu', 'datasumber');
    }

    public function create()
    {
        return view('superadmin.datasumber.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_sumber' => 'required|unique:sumber,nama_sumber',
            'kategori' => 'required|string',
        ]);

        $sumber = Sumber::create($request->only('nama_sumber', 'kategori'));

        return response()->json($sumber);
    }

    public function edit($id)
    {
        $sumber = Sumber::findOrFail($id);
        return view('superadmin.datasumber.edit', compact('sumber'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_sumber' => 'required|unique:sumber,nama_sumber,' . $id,
            'kategori' => 'required|string',
        ]);

        $sumber = Sumber::findOrFail($id);
        $sumber->update($request->only('nama_sumber', 'kategori'));

        return response()->json($sumber);
    }

    public function destroy($id)
    {
        Sumber::findOrFail($id)->delete();

        return redirect()->route('sumber.index')->with('success', 'Data Sumber limbah berhasil dihapus.');
    }
}
