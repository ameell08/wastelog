<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailLimbahDiolah extends Model
{
    protected $table = 'detail_limbah_diolah';

    protected $fillable = [
        'limbah_diolah_id',
        'kode_limbah_id',
        'berat_kg',
        'tanggal_input',
    ];

    public function limbahDiolah()
    {
        return $this->belongsTo(LimbahDiolah::class);
    }

    public function kodeLimbah()
    {
        return $this->belongsTo(KodeLimbah::class);
    }
}

