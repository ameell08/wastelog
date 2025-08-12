<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_limbah_masuk', function (Blueprint $table) {
            $table->string('kode_festronik', 100)->nullable()->after('berat_kg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_limbah_masuk', function (Blueprint $table) {
            $table->dropColumn('kode_festronik');
        });
    }
};
