<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('limbah_masuk', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');     // tanggal kedatangan
            $table->integer('total_kg'); // sum dari detail
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('limbah_masuk');
    }
};
