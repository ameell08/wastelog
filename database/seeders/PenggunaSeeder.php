<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class PenggunaSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk tabel pengguna.
     */
    public function run(): void
    {
        DB::table('pengguna')->insert([
            [
                'nama' => 'Super Admin',
                'email' => 'superadmin@pria.com',
                'password' => Hash::make('123'),
                'id_jenis_pengguna' => 1, 
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama' => 'Admin 1',
                'email' => 'admin1@pria.com',
                'password' => Hash::make('123'),
                'id_jenis_pengguna' => 2, 
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama' => 'Admin 2',
                'email' => 'admin2@pria.com',
                'password' => Hash::make('123'),
                'id_jenis_pengguna' => 3, 
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama' => 'Pimpinan',
                'email' => 'pimpinan@pria.com',
                'password' => Hash::make('123'),
                'id_jenis_pengguna' => 4, 
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
