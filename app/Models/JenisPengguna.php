<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPengguna extends Model
{
    protected $table = 'jenis_pengguna';
    protected $primaryKey = 'id_jenis_pengguna';

    protected $fillable = ['nama_jenis_pengguna', 'kode_jenis_pengguna'];

    public function pengguna()
    {
        return $this->hasMany(Pengguna::class, 'id_jenis_pengguna', 'id_jenis_pengguna');
    }
}
