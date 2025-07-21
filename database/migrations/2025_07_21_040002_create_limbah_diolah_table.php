<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('limbah_diolah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mesin_id')->constrained('mesin')->onDelete('cascade'); // 1 record = 1 mesin proses
            $table->integer('total_kg'); // akumulasi dari detail_limbah_diolah
            $table->timestamps();        // created_at dapat dipakai sebagai tanggal proses
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('limbah_diolah');
    }
};
