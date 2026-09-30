<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mt_mutasis', function (Blueprint $table) {
            $table->id();
            $table->string('no_mutasi', 12)->unique();       // Format: MB/XXXXX | MJ/XXXXX | MP/XXXXX
            $table->foreignId('m_supplier_id')->nullable()->constrained('m_suppliers')->nullOnDelete();
            $table->enum('jenis_mutasi', ['pembelian', 'penjualan', 'perpindahan'])->default('pembelian');
            $table->date('tgl_mutasi');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['jenis_mutasi', 'tgl_mutasi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mt_mutasis');
    }
};
