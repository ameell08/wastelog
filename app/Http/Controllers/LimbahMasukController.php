<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LimbahMasuk;
use App\Models\DetailLimbahMasuk;
use App\Models\Truk;
use App\Models\KodeLimbah;
use App\Models\SisaLimbah;
use Illuminate\Support\Facades\DB;
use App\Imports\LimbahMasukImport;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use DateTime;

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
            'detail.*.kode_festronik' => 'required|string|distinct:ignore_case'
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
                    'kode_festronik'  => $item['kode_festronik'] ?? null,
                ]);

                // Tambahkan ke sisa limbah untuk sistem FIFO
                SisaLimbah::create([
                    'tanggal' => $request->tanggal,
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
                $tanggalRaw = trim($row['A']);

                if (is_numeric($tanggalRaw)) {
                    $tanggal = ExcelDate::excelToDateTimeObject($tanggalRaw)->format('Y-m-d');
                } elseif (DateTime::createFromFormat('d-m-Y', $tanggalRaw)) {
                    $tanggal = Carbon::createFromFormat('d-m-Y', $tanggalRaw)->format('Y-m-d');
                } elseif (DateTime::createFromFormat('d/m/Y', $tanggalRaw)) {
                    $tanggal = Carbon::createFromFormat('d/m/Y', $tanggalRaw)->format('Y-m-d');
                } elseif (DateTime::createFromFormat('Y-m-d', $tanggalRaw)) {
                    $tanggal = Carbon::createFromFormat('Y-m-d', $tanggalRaw)->format('Y-m-d');
                } else {
                    throw new \Exception("Format tanggal tidak dikenali: {$tanggalRaw}");
                }

                $platNomor = $row['B'];
                $kodeLimbah = $row['C'];
                $beratKg = (float) $row['D'];
                $kodeFestronik = $row['E'];

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
                    'kode_festronik' => $kodeFestronik,
                ]);

                // Tambahkan ke sisa limbah untuk sistem FIFO
                SisaLimbah::create([
                    'tanggal' => $tanggal,
                    'kode_limbah_id' => $kode->id,
                    'berat_kg' => $beratKg,
                ]);

                // Update total_kg
                $limbahMasuk->total_kg += $beratKg;
                $limbahMasuk->save();
            }

            DB::commit();

            return redirect()->back()->with('success', 'Data berhasil diimpor dari Excel!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }
}
