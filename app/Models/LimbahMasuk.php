<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LimbahMasuk extends Model
{
    protected $table = 'limbah_masuk';

    protected $fillable = [
        'tanggal',
        'total_kg',
    ];

    public function detailLimbahMasuk()
    {
        return $this->hasMany(DetailLimbahMasuk::class);
    }
}

