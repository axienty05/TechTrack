<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dt_mutasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mt_mutasi_id')->constrained('mt_mutasis')->cascadeOnDelete();
            $table->unsignedBigInteger('pemakai_lama');      // ID pemakai asal (bisa 0 jika belum ada pemakai)
            $table->foreignId('m_barang_id')->constrained('m_barangs');
            $table->unsignedBigInteger('pemakai_baru')->nullable(); // ID pemakai tujuan (null untuk penjualan)
            $table->bigInteger('harga')->unsigned()->nullable()->default(0);
            $table->timestamps();

            $table->index('mt_mutasi_id');
            $table->index('m_barang_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dt_mutasis');
    }
};
