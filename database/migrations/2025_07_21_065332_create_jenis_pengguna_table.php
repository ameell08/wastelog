<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Jalankan migration untuk tabel jenis_pengguna
     */
    public function up(): void
    {
        Schema::create('jenis_pengguna', function (Blueprint $table) {
            $table->id('id_jenis_pengguna');      // Primary Key
            $table->string('nama_jenis_pengguna'); // Contoh: Super Admin
            $table->string('kode_jenis_pengguna')->unique(); // Contoh: SDM
            $table->timestamps();
        });
    }

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_pengguna');
    }
};
