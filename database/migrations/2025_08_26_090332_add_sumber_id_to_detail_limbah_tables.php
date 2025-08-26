<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('detail_limbah_masuk', function (Blueprint $table) {
            $table->foreignId('sumber_id')->after('kode_limbah_id')->constrained('sumber')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('detail_limbah_masuk', function (Blueprint $table) {
            $table->dropForeign(['sumber_id']);
            $table->dropColumn('sumber_id');
        });
    }
};