<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisPenggunaSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk tabel jenis_pengguna.
     */
    public function run(): void
    {
        DB::table('jenis_pengguna')->insert([
            [
                'nama_jenis_pengguna' => 'Super Admin',
                'kode_jenis_pengguna' => 'SDM',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_jenis_pengguna' => 'Admin 1',
                'kode_jenis_pengguna' => 'ADM1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_jenis_pengguna' => 'Admin 2',
                'kode_jenis_pengguna' => 'ADM2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_jenis_pengguna' => 'Pimpinan',
                'kode_jenis_pengguna' => 'PMP',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
