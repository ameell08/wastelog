<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengirimanResidu;
use App\Models\DetailPengirimanResidu;
use App\Models\AntreanResidu;
use App\Models\Truk;
use App\Models\KodeLimbah;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PengirimanResiduController extends Controller
{
    public function index()
    {
        // Ambil semua data truk (tanpa filter status)
        $truk = Truk::all();
        
        // Ambil data antrean residu yang masih ada sisa dengan urutan FIFO
        $antreanResidu = AntreanResidu::with('kodeLimbah')
            ->adaSisa()
            ->fifo()
            ->get()
            ->map(function ($item) {
                // Format sisa berat: hilangkan trailing zero
                $sisaBerat = $item->sisa_berat;
                $sisaBeratFormatted = $sisaBerat == (int)$sisaBerat ? 
                    number_format($sisaBerat, 0) : 
                    rtrim(rtrim(number_format($sisaBerat, 4), '0'), '.');
                
                return [
                    'id' => $item->id,
                    'kode_limbah' => $item->kodeLimbah->kode . ' - ' . $item->kodeLimbah->deskripsi,
                    'kode_limbah_id' => $item->kode_limbah_id,
                    'tanggal_masuk' => $item->tanggal_masuk->format('d/m/Y'),
                    'tanggal_masuk_raw' => $item->tanggal_masuk,
                    'sisa_berat' => $sisaBeratFormatted,
                    'sisa_berat_raw' => $sisaBerat,
                    'hari_menunggu' => $item->hari_menunggu,
                    'status' => $item->hari_menunggu >= 7 ? 'Prioritas' : 'Normal'
                ];
            });

        $breadcrumb = (object)[
            'title' => 'Input Pengiriman Residu',
            'list' => ['Login', 'Input Pengiriman Residu']
        ];

        return view('admin2.InputPengirimanResidu', compact('truk', 'antreanResidu', 'breadcrumb'))
               ->with('activeMenu', 'inputpengirimanresidu');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_pengiriman' => 'required|date',
            'detail.*.truk_id' => 'required|exists:truk,id',
            'detail.*.kode_limbah_id' => 'required|exists:kode_limbah,id',
            'detail.*.tanggal_masuk' => 'required|date',
            'detail.*.berat' => 'required|numeric|min:0.0001',
        ]);

        DB::beginTransaction();
        try {
            // Hitung total berat
            $totalBerat = collect($request->detail)->sum('berat');

            // Buat header pengiriman
            $pengiriman = PengirimanResidu::create([
                'tanggal_pengiriman' => $request->tanggal_pengiriman,
                'total_berat' => $totalBerat
            ]);

            // Proses setiap detail dengan sistem FIFO
            foreach ($request->detail as $detail) {
                $beratDiminta = $detail['berat'];
                $kodeLimbahId = $detail['kode_limbah_id'];
                
                // Ambil semua antrean residu yang masih ada sisa, urutkan FIFO
                $antreanList = AntreanResidu::where('kode_limbah_id', $kodeLimbahId)
                    ->adaSisa()
                    ->fifo()
                    ->get();
                
                // Hitung total stok tersedia
                $totalStokTersedia = $antreanList->sum('sisa_berat');
                
                if ($beratDiminta > $totalStokTersedia) {
                    $kodeLimbah = KodeLimbah::find($kodeLimbahId);
                    throw new \Exception("Stok residu tidak mencukupi untuk {$kodeLimbah->kode}. Diminta: {$beratDiminta} kg, Tersedia: {$totalStokTersedia} kg");
                }

                // Proses pengiriman berdasarkan FIFO (bisa dari beberapa antrean)
                $sisaBerat = $beratDiminta;
                foreach ($antreanList as $antrean) {
                    if ($sisaBerat <= 0) break;
                    
                    $sisaStok = $antrean->sisa_berat;
                    $beratAmbil = min($sisaBerat, $sisaStok);
                    
                    if ($beratAmbil > 0) {
                        DetailPengirimanResidu::create([
                            'pengiriman_residu_id' => $pengiriman->id,
                            'truk_id' => $detail['truk_id'],
                            'kode_limbah_id' => $kodeLimbahId,
                            'tanggal_masuk' => $antrean->tanggal_masuk, // Gunakan tanggal dari antrean yang dipilih
                            'berat' => $beratAmbil
                        ]);
                        
                        $sisaBerat -= $beratAmbil;
                    }
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Data pengiriman residu berhasil disimpan!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function getStokTersedia($kodeLimbahId, $tanggalMasuk)
    {
        $antreanResidu = AntreanResidu::where('kode_limbah_id', $kodeLimbahId)
                                     ->where('tanggal_masuk', $tanggalMasuk)
                                     ->first();
        
        if (!$antreanResidu) {
            return response()->json(['stok' => 0]);
        }

        $sisaBerat = $antreanResidu->sisa_berat;
        return response()->json(['stok' => $sisaBerat]);
    }
}
