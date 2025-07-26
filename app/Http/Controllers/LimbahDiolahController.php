<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LimbahDiolah;
use App\Models\DetailLimbahDiolah;
use App\Models\Mesin;
use App\Models\KodeLimbah;
use App\Models\SisaLimbah;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LimbahDiolahController extends Controller
{
    public function index()
    {
        $mesin = Mesin::all();
        $kodeLimbah = KodeLimbah::all();

        // Data Diolah - Ambil data limbah yang sudah diolah
        $dataDiolah = DetailLimbahDiolah::with(['limbahDiolah.mesin', 'kodeLimbah'])
            ->orderBy('tanggal_input', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'no_mesin' => $item->limbahDiolah->mesin->no_mesin,
                    'kode_limbah' => $item->kodeLimbah->kode,
                    'deskripsi' => $item->kodeLimbah->deskripsi,
                    'berat_kg' => $item->berat_kg,
                ];
            });

        // Antrean Limbah - Ambil data sisa limbah yang belum diolah
        $antreanLimbah = SisaLimbah::with('kodeLimbah')
            ->where('berat_kg', '>', 0)
            ->orderBy('tanggal', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'kode' => $item->kodeLimbah->kode,
                    'deskripsi' => $item->kodeLimbah->deskripsi,
                    'sisa_berat' => $item->berat_kg,
                    'tanggal_masuk' => Carbon::parse($item->tanggal)->format('d/m/Y'),
                    'status' => $item->berat_kg > 0 ? 'Segera Diproses' : 'Menunggu Diproses',
                ];
            });

        $breadcrumb = (object)[
            'title' => 'Input Limbah Diolah',
            'list' => ['Login', 'Input Limbah Olah']
        ];
        return view('admin2.InputLimbahOlah', compact('mesin', 'kodeLimbah', 'dataDiolah', 'antreanLimbah', 'breadcrumb'))->with('activeMenu', 'limbahdiolah');
    }

    public function store(Request $request)
    {
        $request->validate([
            'detail.*.mesin_id' => 'required|exists:mesin,id',
            'detail.*.kode_limbah_id' => 'required|exists:kode_limbah,id',
            'detail.*.berat_kg' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->detail as $detail) {
                // Cek apakah ada sisa limbah yang cukup
                $sisaLimbah = SisaLimbah::where('kode_limbah_id', $detail['kode_limbah_id'])
                    ->where('berat_kg', '>=', $detail['berat_kg'])
                    ->orderBy('tanggal', 'asc')
                    ->first();

                if (!$sisaLimbah) {
                    throw new \Exception('Sisa limbah tidak mencukupi untuk kode limbah yang dipilih');
                }

                // Simpan ke limbah_diolah
                $limbahDiolah = LimbahDiolah::create([
                    'mesin_id' => $detail['mesin_id'],
                    'total_kg' => $detail['berat_kg'],
                ]);

                // Simpan ke detail_limbah_diolah
                DetailLimbahDiolah::create([
                    'limbah_diolah_id' => $limbahDiolah->id,
                    'kode_limbah_id' => $detail['kode_limbah_id'],
                    'berat_kg' => $detail['berat_kg'],
                    'tanggal_input' => now(),
                ]);

                // Update sisa_limbah
                $sisaLimbah->berat_kg -= $detail['berat_kg'];
                $sisaLimbah->save();

                // Hapus record sisa_limbah jika berat sudah 0
                if ($sisaLimbah->berat_kg <= 0) {
                    $sisaLimbah->delete();
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Data limbah berhasil diolah!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
    public function show()
    {
        // Ambil data limbah yang sudah diolah
        $data = LimbahDiolah::with('mesin')
            ->orderBy('created_at', 'desc')
            ->get();

        // Breadcrumb untuk tampilan
        $breadcrumb = (object)[
            'title' => 'Data Limbah Diolah',
            'list' => ['Dashboard', 'Data Limbah Diolah']
        ];

        return view('admin2.DataLimbahOlah', compact('data', 'breadcrumb'))
            ->with('activeMenu', 'datalimbaholah');
    }
}
