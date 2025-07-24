<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LimbahDiolah extends Model
{
    protected $table = 'limbah_diolah';

    protected $fillable = [
        'mesin_id',
        'total_kg',
    ];

    public function mesin()
    {
        return $this->belongsTo(Mesin::class);
    }

    public function detailLimbahDiolah()
    {
        return $this->hasMany(DetailLimbahDiolah::class);
    }
}

