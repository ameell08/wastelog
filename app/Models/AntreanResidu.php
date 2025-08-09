<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AntreanResidu extends Model
{
    use HasFactory;

    protected $table = 'antrean_residu';

    protected $fillable = [
        'kode_limbah_id',
        'tanggal_masuk',
        'berat_total'
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'berat_total' => 'decimal:4'
    ];

    // Relasi ke tabel kode_limbah
    public function kodeLimbah()
    {
        return $this->belongsTo(KodeLimbah::class, 'kode_limbah_id');
    }

    // Relasi ke detail pengiriman residu
    public function detailPengirimanResidu()
    {
        return $this->hasMany(DetailPengirimanResidu::class, 'kode_limbah_id', 'kode_limbah_id')
                    ->where('tanggal_masuk', $this->tanggal_masuk);
    }

    // Accessor untuk mendapatkan sisa berat yang belum dikirim
    public function getSisaBeratAttribute()
    {
        $totalDikirim = DetailPengirimanResidu::where('kode_limbah_id', $this->kode_limbah_id)
                                               ->where('tanggal_masuk', $this->tanggal_masuk)
                                               ->sum('berat');
        
        return $this->berat_total - $totalDikirim;
    }

    // Accessor untuk menghitung hari menunggu
    public function getHariMenungguAttribute()
    {
        return Carbon::parse($this->tanggal_masuk)->diffInDays(Carbon::now());
    }

    // Scope untuk residu yang masih ada sisa
    public function scopeAdaSisa($query)
    {
        return $query->whereRaw('berat_total > (
            SELECT COALESCE(SUM(berat), 0) 
            FROM detail_pengiriman_residu 
            WHERE kode_limbah_id = antrean_residu.kode_limbah_id 
            AND tanggal_masuk = antrean_residu.tanggal_masuk
        )');
    }

    // Scope untuk urutan FIFO
    public function scopeFifo($query)
    {
        return $query->orderBy('tanggal_masuk', 'asc')->orderBy('id', 'asc');
    }
}
