<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailLimbahMasuk extends Model
{
    protected $table = 'detail_limbah_masuk';

    protected $fillable = [
        'limbah_masuk_id',
        'truk_id',
        'kode_limbah_id',
        'sumber_id',
        'berat_kg',
        'kode_festronik'
    ];

    public function limbahMasuk()
    {
        return $this->belongsTo(LimbahMasuk::class);
    }

    public function truk()
    {
        return $this->belongsTo(Truk::class);
    }

    public function kodeLimbah()
    {
        return $this->belongsTo(KodeLimbah::class);
    }

    public function sumber()
    {
        return $this->belongsTo(Sumber::class);
    }
}

