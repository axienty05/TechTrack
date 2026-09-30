<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('m_pemakais', function (Blueprint $table) {
            // Flag untuk mengecualikan user dari daftar PC Maintenance
            // true = dikecualikan (pakai laptop, dll), false = tampil normal
            $table->boolean('exclude_pc_maintenance')->default(false)->after('comp_name');
        });
    }

    public function down(): void
    {
        Schema::table('m_pemakais', function (Blueprint $table) {
            $table->dropColumn('exclude_pc_maintenance');
        });
    }
};
