<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mesin extends Model
{
    protected $table = 'mesin';

    protected $fillable = [
        'no_mesin',
        'status',
        'keterangan',
    ];

    public function limbahDiolah()
    {
        return $this->hasMany(LimbahDiolah::class);
    }
}

