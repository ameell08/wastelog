<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Jalankan migration untuk tabel pengguna
     */
    public function up(): void
    {
        Schema::create('pengguna', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('password');
            $table->unsignedBigInteger('id_jenis_pengguna');  // Kolom FK

            $table->foreign('id_jenis_pengguna')
                ->references('id_jenis_pengguna')
                ->on('jenis_pengguna')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};
