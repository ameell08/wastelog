<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SisaLimbah extends Model
{
    protected $table = 'sisa_limbah';

    protected $fillable = [
        'tanggal',
        'kode_limbah_id',
        'berat_kg',
    ];

    public function kodeLimbah()
    {
        return $this->belongsTo(KodeLimbah::class);
    }
}

