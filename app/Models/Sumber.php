<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sumber extends Model
{
    protected $table = 'sumber';

    protected $fillable = [
        'nama_sumber',
        'kategori',
    ];

    public function detailLimbahMasuk()
    {
        return $this->hasMany(DetailLimbahMasuk::class);
    }

    public function detailLimbahDiolah()
    {
        return $this->hasMany(DetailLimbahDiolah::class);
    }
}
