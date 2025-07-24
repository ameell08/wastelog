<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Truk extends Model
{
    protected $table = 'truk';

    protected $fillable = [
        'plat_nomor',
        'nama_sopir',
    ];

    public function detailLimbahMasuk()
    {
        return $this->hasMany(DetailLimbahMasuk::class);
    }
}
