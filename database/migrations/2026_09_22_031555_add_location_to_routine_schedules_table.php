<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambah kolom pc_location ke routine_schedules:
     *   'kantor' = hanya berlaku untuk PC Kantor
     *   'pabrik' = hanya berlaku untuk PC Pabrik
     *   'semua'  = berlaku untuk semua lokasi
     */
    public function up(): void
    {
        Schema::table('routine_schedules', function (Blueprint $table) {
            $table->string('pc_location', 20)->default('semua')->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('routine_schedules', function (Blueprint $table) {
            $table->dropColumn('pc_location');
        });
    }
};
