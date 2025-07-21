<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sisa_limbah', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal'); // hari rekap
            $table->foreignId('kode_limbah_id')->constrained('kode_limbah')->onDelete('cascade');
            $table->integer('berat_kg'); // sisa = masuk - diolah
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sisa_limbah');
    }
};
