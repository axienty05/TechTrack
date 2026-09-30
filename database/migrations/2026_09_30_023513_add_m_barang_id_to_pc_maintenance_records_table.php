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
        Schema::table('pc_maintenance_records', function (Blueprint $table) {
            $table->foreignId('m_barang_id')
                ->nullable()
                ->after('id')
                ->constrained('m_barangs')
                ->nullOnDelete();

            $table->unsignedBigInteger('computer_device_id')->nullable()->change();
            $table->index(['m_barang_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::table('pc_maintenance_records', function (Blueprint $table) {
            $table->dropForeign(['m_barang_id']);
            $table->dropIndex(['m_barang_id', 'period']);
            $table->dropColumn('m_barang_id');
        });
    }
};
