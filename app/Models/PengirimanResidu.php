<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengirimanResidu extends Model
{
    use HasFactory;

    protected $table = 'pengiriman_residu';

    protected $fillable = [
        'tanggal_pengiriman',
        'total_berat'
    ];

    protected $casts = [
        'tanggal_pengiriman' => 'date',
        'total_berat' => 'decimal:4'
    ];

    // Relasi ke detail pengiriman residu
    public function detailPengirimanResidu()
    {
        return $this->hasMany(DetailPengirimanResidu::class);
    }

    // Accessor untuk mendapatkan jumlah item detail
    public function getJumlahItemAttribute()
    {
        return $this->detailPengirimanResidu()->count();
    }

    // Accessor untuk mendapatkan daftar truk yang digunakan
    public function getTrukDigunakanAttribute()
    {
        return $this->detailPengirimanResidu()
                    ->with('truk')
                    ->get()
                    ->pluck('truk.plat_nomor')
                    ->unique()
                    ->values();
    }

    // Static method untuk membuat pengiriman baru dengan detail
    public static function buatPengiriman($tanggalPengiriman, $detailResidu)
    {
        $totalBerat = collect($detailResidu)->sum('berat');
        
        $pengiriman = self::create([
            'tanggal_pengiriman' => $tanggalPengiriman,
            'total_berat' => $totalBerat
        ]);

        foreach ($detailResidu as $detail) {
            $pengiriman->detailPengirimanResidu()->create($detail);
        }

        return $pengiriman;
    }
}
