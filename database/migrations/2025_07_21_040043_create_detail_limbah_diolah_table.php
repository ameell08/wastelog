<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('detail_limbah_diolah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('limbah_diolah_id')->constrained('limbah_diolah')->onDelete('cascade');
            $table->foreignId('kode_limbah_id')->constrained('kode_limbah')->onDelete('cascade');
            $table->integer('berat_kg');      // berat kode limbah yang diolah oleh mesin header
            $table->date('tanggal_input');    // kapan admin2 input baris ini
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_limbah_diolah');
    }
};
