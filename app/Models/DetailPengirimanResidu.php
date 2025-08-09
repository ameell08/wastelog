<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPengirimanResidu extends Model
{
    use HasFactory;

    protected $table = 'detail_pengiriman_residu';

    protected $fillable = [
        'pengiriman_residu_id',
        'truk_id',
        'kode_limbah_id',
        'tanggal_masuk',
        'berat'
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'berat' => 'decimal:4'
    ];

    // Relasi ke pengiriman residu (header)
    public function pengirimanResidu()
    {
        return $this->belongsTo(PengirimanResidu::class);
    }

    // Relasi ke tabel truk
    public function truk()
    {
        return $this->belongsTo(Truk::class);
    }

    // Relasi ke kode limbah
    public function kodeLimbah()
    {
        return $this->belongsTo(KodeLimbah::class, 'kode_limbah_id');
    }

    // Relasi ke antrean residu
    public function antreanResidu()
    {
        return $this->belongsTo(AntreanResidu::class, 'kode_limbah_id', 'kode_limbah_id')
                    ->where('tanggal_masuk', $this->tanggal_masuk);
    }

    // Scope untuk filter berdasarkan periode
    public function scopePeriode($query, $tanggalMulai, $tanggalSelesai)
    {
        return $query->whereHas('pengirimanResidu', function($q) use ($tanggalMulai, $tanggalSelesai) {
            $q->whereBetween('tanggal_pengiriman', [$tanggalMulai, $tanggalSelesai]);
        });
    }

    // Scope untuk filter berdasarkan truk
    public function scopeByTruk($query, $trukId)
    {
        return $query->where('truk_id', $trukId);
    }

    // Static method untuk validasi stok tersedia
    public static function cekStokTersedia($kodeLimbahId, $tanggalMasuk, $beratDibutuhkan)
    {
        $antreanResidu = AntreanResidu::where('kode_limbah_id', $kodeLimbahId)
                                     ->where('tanggal_masuk', $tanggalMasuk)
                                     ->first();
        
        if (!$antreanResidu) {
            return false;
        }

        $totalDikirim = self::where('kode_limbah_id', $kodeLimbahId)
                           ->where('tanggal_masuk', $tanggalMasuk)
                           ->sum('berat');

        $sisaStok = $antreanResidu->berat_total - $totalDikirim;
        
        return $sisaStok >= $beratDibutuhkan;
    }
}
