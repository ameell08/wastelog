<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LimbahMasuk;
use App\Models\DetailLimbahMasuk;
use App\Models\Truk;
use App\Models\KodeLimbah;
use Illuminate\Support\Facades\DB;
use App\Imports\LimbahMasukImport;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;

class LimbahMasukController extends Controller
{
    public function index()
    {
        $truks = Truk::all();
        $kodeLimbahs = KodeLimbah::all();
        $breadcrumb = (object)[
            'title' => 'Input Limbah Masuk',
            'list' => ['Login', 'Input Limbah Masuk']
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

    public function import()
    {
        return view('admin1.import');
    }
    public function import_ajax(Request $request)
    {
        $request->validate([
            'file_limbah_masuk' => 'required|mimes:xlsx,xls',
        ]);

        try {
            $spreadsheet = IOFactory::load($request->file('file_limbah_masuk'));
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            // Asumsi baris 1 adalah header
            unset($rows[1]);

            DB::beginTransaction();

            foreach ($rows as $row) {
                $tanggal = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['A'])->format('Y-m-d');
                $platNomor = $row['B'];
                $kodeLimbah = $row['C'];
                $beratKg = (float) $row['D'];

                // Ambil truk_id dari plat_nomor
                $truk = \App\Models\Truk::where('plat_nomor', $platNomor)->first();
                if (!$truk) throw new \Exception("Plat nomor {$platNomor} tidak ditemukan.");

                // Ambil kode_limbah_id dari kode
                $kode = \App\Models\KodeLimbah::where('kode', $kodeLimbah)->first();
                if (!$kode) throw new \Exception("Kode limbah {$kodeLimbah} tidak ditemukan.");

                // Cek apakah sudah ada LimbahMasuk di tanggal tsb
                $limbahMasuk = LimbahMasuk::firstOrCreate(
                    ['tanggal' => $tanggal],
                    ['total_kg' => 0]
                );

                // Tambah detail
                $limbahMasuk->detailLimbahMasuk()->create([
                    'truk_id' => $truk->id,
                    'kode_limbah_id' => $kode->id,
                    'berat_kg' => $beratKg,
                ]);

                // Update total_kg
                $limbahMasuk->total_kg += $beratKg;
                $limbahMasuk->save();
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil diimpor.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Gagal import: ' . $e->getMessage(),
                'msgField' => []
            ]);
        }
    }
}
