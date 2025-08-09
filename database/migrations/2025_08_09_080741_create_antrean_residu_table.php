<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('antrean_residu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kode_limbah_id')->constrained('kode_limbah')->onDelete('cascade');
            $table->date('tanggal_masuk'); // dari tanggal input limbah diolah
            $table->decimal('berat_total', 10, 4); // total berat residu
            $table->timestamps();
            
            // Index untuk optimasi query
            $table->index(['kode_limbah_id', 'tanggal_masuk']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('antrean_residu');
    }
};