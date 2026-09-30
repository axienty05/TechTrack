<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('computer_devices', function (Blueprint $table) {
            // Tambah FK ke m_pemakais (nullable agar data lama tidak error)
            $table->foreignId('m_pemakai_id')
                  ->nullable()
                  ->after('comp_name')
                  ->constrained('m_pemakais')
                  ->nullOnDelete();

            // Hapus kolom user_name yang dulu hanya string bebas
            $table->dropColumn('user_name');
        });
    }

    public function down(): void
    {
        Schema::table('computer_devices', function (Blueprint $table) {
            $table->dropForeign(['m_pemakai_id']);
            $table->dropColumn('m_pemakai_id');
            $table->string('user_name', 150)->nullable()->after('comp_name')->index();
        });
    }
};
