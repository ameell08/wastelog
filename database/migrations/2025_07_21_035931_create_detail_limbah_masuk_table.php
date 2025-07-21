<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('detail_limbah_masuk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('limbah_masuk_id')->constrained('limbah_masuk')->onDelete('cascade');
            $table->foreignId('truk_id')->constrained('truk')->onDelete('cascade');
            $table->foreignId('kode_limbah_id')->constrained('kode_limbah')->onDelete('cascade');
            $table->integer('berat_kg'); // berat per kode limbah dari truk
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_limbah_masuk');
    }
};
