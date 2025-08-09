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
        Schema::create('detail_pengiriman_residu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengiriman_residu_id')->constrained('pengiriman_residu')->onDelete('cascade');
            $table->foreignId('truk_id')->constrained('truk')->onDelete('cascade');
            $table->foreignId('kode_limbah_id')->constrained('kode_limbah')->onDelete('cascade');
            $table->date('tanggal_masuk'); // tanggal masuk residu dari limbah diolah
            $table->decimal('berat', 10, 4);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pengiriman_residu');
    }
};