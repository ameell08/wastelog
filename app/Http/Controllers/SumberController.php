<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sumber;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

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

    public function import_ajax(Request $request)
    {
        $request->validate([
            'file_sumber' => 'required|file|mimes:xlsx,xls',
        ]);

        DB::beginTransaction();
        try {
            $spreadsheet = IOFactory::load($request->file('file_sumber'));
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            $importedCount = 0;

            // Lewati baris header (baris 0)
            foreach (array_slice($rows, 1) as $row) {
                // Skip baris kosong
                if (empty(array_filter($row))) {
                    continue;
                }

                [$namaSumber, $kategori] = $row;

                // Validasi data
                if (empty($namaSumber) || empty($kategori)) {
                    throw new \Exception("Nama sumber dan kategori tidak boleh kosong.");
                }

                // Cek apakah nama sumber sudah ada
                $existingSumber = Sumber::where('nama_sumber', $namaSumber)->first();
                if ($existingSumber) {
                    throw new \Exception("Nama sumber '$namaSumber' sudah ada dalam database.");
                }

                // Simpan data sumber
                Sumber::create([
                    'nama_sumber' => $namaSumber,
                    'kategori' => $kategori,
                ]);

                $importedCount++;
            }

            DB::commit();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Berhasil mengimpor {$importedCount} data sumber limbah dari Excel!",
                    'imported_count' => $importedCount
                ]);
            }
            
            return redirect()->back()->with('success', "Berhasil mengimpor {$importedCount} data sumber limbah dari Excel!");
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }
            
            return redirect()->back()->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }
}
