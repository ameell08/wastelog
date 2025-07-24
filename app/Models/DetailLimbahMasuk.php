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
        'berat_kg',
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
}

