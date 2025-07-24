<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KodeLimbah extends Model
{
    protected $table = 'kode_limbah';

    protected $fillable = [
        'kode',
        'deskripsi',
    ];

    public function detailLimbahMasuk()
    {
        return $this->hasMany(DetailLimbahMasuk::class);
    }

    public function detailLimbahDiolah()
    {
        return $this->hasMany(DetailLimbahDiolah::class);
    }

    public function sisaLimbah()
    {
        return $this->hasMany(SisaLimbah::class);
    }
}

