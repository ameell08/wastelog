<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\JenisPengguna;

class Pengguna extends Authenticatable
{
    use Notifiable;

    protected $table = 'pengguna';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'nama', 'email', 'password', 'id_jenis_pengguna'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function jenisPengguna()
    {
        return $this->belongsTo(JenisPengguna::class, 'id_jenis_pengguna');
    }

    // method untuk ambil role
    public function getRole()
    {
          return $this->jenisPengguna ? $this->jenisPengguna->kode_jenis_pengguna : null;
    }
    
}
