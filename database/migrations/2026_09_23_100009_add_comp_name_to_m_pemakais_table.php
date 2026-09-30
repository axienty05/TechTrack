<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('m_pemakais', function (Blueprint $table) {
            $table->string('comp_name', 100)->nullable()->after('nama')->index();
        });
    }

    public function down(): void
    {
        Schema::table('m_pemakais', function (Blueprint $table) {
            $table->dropColumn('comp_name');
        });
    }
};
