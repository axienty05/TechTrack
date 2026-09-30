<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_service_internals', function (Blueprint $table) {
            $table->id();
            $table->date('tgl_service');
            $table->date('tgl_selesai')->nullable();
            $table->foreignId('m_pemakai_id')->constrained('m_pemakais');
            $table->foreignId('m_barang_id')->constrained('m_barangs');
            $table->text('kerusakan');
            $table->timestamps();

            $table->index(['m_barang_id', 'tgl_service']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_service_internals');
    }
};
