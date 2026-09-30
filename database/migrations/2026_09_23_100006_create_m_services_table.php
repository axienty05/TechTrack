<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_services', function (Blueprint $table) {
            $table->id();
            $table->string('no_sj')->nullable();             // Nomor surat jalan
            $table->foreignId('m_pemakai_id')->constrained('m_pemakais');
            $table->foreignId('m_barang_id')->constrained('m_barangs');
            $table->foreignId('m_service_center_id')->constrained('m_service_centers');
            $table->date('tgl_service');
            $table->date('tgl_selesai')->nullable();
            $table->bigInteger('biaya')->unsigned()->nullable()->default(0);
            $table->text('kerusakan')->nullable();
            $table->text('analisa')->nullable();
            $table->text('solusi')->nullable();
            $table->timestamps();

            $table->index(['m_barang_id', 'tgl_service']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_services');
    }
};
