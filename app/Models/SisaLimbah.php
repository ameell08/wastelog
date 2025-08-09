<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SisaLimbah extends Model
{
    protected $table = 'sisa_limbah';

    protected $fillable = [
        'tanggal',
        'kode_limbah_id',
        'berat_kg',
    ];

    protected $dates = ['tanggal'];

    public function kodeLimbah()
    {
        return $this->belongsTo(KodeLimbah::class);
    }

    /**
     * Ambil sisa limbah untuk kode tertentu dengan urutan FIFO
     * 
     * @param int $kodeLimbahId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getFifoQueue($kodeLimbahId)
    {
        return self::where('kode_limbah_id', $kodeLimbahId)
            ->where('berat_kg', '>', 0)
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * Cek apakah ada stok yang cukup untuk kode limbah tertentu
     * 
     * @param int $kodeLimbahId
     * @param float $beratDibutuhkan
     * @return bool
     */
    public static function checkAvailableStock($kodeLimbahId, $beratDibutuhkan)
    {
        $totalStock = self::where('kode_limbah_id', $kodeLimbahId)
            ->where('berat_kg', '>', 0)
            ->sum('berat_kg');
            
        return $totalStock >= $beratDibutuhkan;
    }

    /**
     * Proses pengambilan limbah dengan sistem FIFO
     * 
     * @param int $kodeLimbahId
     * @param float $beratDiambil
     * @return bool
     */
    public static function processFifoConsumption($kodeLimbahId, $beratDiambil)
    {
        $sisaLimbahList = self::getFifoQueue($kodeLimbahId);
        $sisaBerat = $beratDiambil;

        foreach ($sisaLimbahList as $sisaLimbah) {
            if ($sisaBerat <= 0) break;
            
            if ($sisaLimbah->berat_kg >= $sisaBerat) {
                // Jika sisa limbah ini cukup untuk memenuhi kebutuhan
                $sisaLimbah->berat_kg -= $sisaBerat;
                $sisaBerat = 0;
                
                if ($sisaLimbah->berat_kg <= 0) {
                    $sisaLimbah->delete();
                } else {
                    $sisaLimbah->save();
                }
            } else {
                // Jika sisa limbah ini tidak cukup, ambil semua dan lanjut ke yang berikutnya
                $sisaBerat -= $sisaLimbah->berat_kg;
                $sisaLimbah->delete();
            }
        }

        return $sisaBerat <= 0; // Return true jika berhasil mengambil semua
    }

    /**
     * Hitung hari menunggu sejak tanggal masuk
     * 
     * @return int
     */
    public function getHariMenungguAttribute()
    {
        return Carbon::parse($this->tanggal)->diffInDays(now());
    }

    /**
     * Dapatkan status berdasarkan lama menunggu
     * 
     * @return string
     */
    public function getStatusAttribute()
    {
        $hariMenunggu = $this->hari_menunggu;
        
        if ($hariMenunggu >= 2) {
            return 'Coolstorage (Prioritas)';
        } elseif ($hariMenunggu >= 1) {
            return 'Coolstorage (Segera Diproses)';
        }
        
        return 'Menunggu Diproses';
    }
}

