<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kode_limbah', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique(); // contoh: B3-100
            $table->string('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kode_limbah');
    }
};
