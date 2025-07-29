<?php

namespace App\Http\Controllers;

use App\Models\KodeLimbah;
use Illuminate\Http\Request;

class KodeLimbahController extends Controller
{
    public function index()
    {
        $kodeLimbah = KodeLimbah::all();
        $breadcrumb = (object)[
            'title' => 'Data Kode Limbah',
            'list' => ['Dashboard', 'Data Kode Limbah']
        ];
         return view('superadmin.kodelimbah.index', compact('kodeLimbah', 'breadcrumb'))
            ->with('activeMenu', 'kode-limbah');
    }

    public function create()
    {
        return view('superadmin.kodelimbah.create');
    }

    // KodeLimbahController.php > store()
    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|unique:kode_limbah,kode',
            'deskripsi' => 'nullable|string'
        ]);

        $kodeLimbah = KodeLimbah::create($request->only('kode', 'deskripsi'));

        return response()->json($kodeLimbah);
    }


    public function edit($id)
    {
        $kodeLimbah = KodeLimbah::findOrFail($id);
        return view('superadmin.kode    limbah.edit', compact('kodeLimbah'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode' => 'required|unique:kode_limbah,kode,' . $id,
            'deskripsi' => 'nullable|string'
        ]);

        $kodeLimbah = KodeLimbah::findOrFail($id);
        $kodeLimbah->update($request->only('kode', 'deskripsi'));

        return redirect()->route('kode-limbah.index')
            ->with('success', 'Data berhasil diperbarui.');
    }

    public function delete($id)
    {
        KodeLimbah::findOrFail($id)->delete();

        return redirect()->route('kode-limbah.index')
            ->with('success', 'Data berhasil dihapus.');
    }
}
