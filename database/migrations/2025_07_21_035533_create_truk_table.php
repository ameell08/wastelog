<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('truk', function (Blueprint $table) {
            $table->id();
            $table->string('plat_nomor')->unique();
            $table->string('nama_sopir');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('truk');
    }
};
