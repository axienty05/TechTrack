<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('m_pemakai_id')->nullable()->constrained('m_pemakais')->nullOnDelete();
            $table->string('kode_barang', 10)->unique();     // Format: B/XXXXXX
            $table->string('nama_barang', 150);
            $table->string('serial_number', 50)->nullable()->unique();
            $table->enum('kategori', [
                'komputer', 'laptop', 'ups', 'printer', 'monitor',
                'mouse', 'keyboard', 'scanner', 'stavolt', 'memory',
                'storage', 'license', 'sparepart', 'cctv', 'lain_lain',
            ]);
            $table->text('keterangan')->nullable();
            $table->enum('status', ['aktif', 'tidak_aktif', 'sedang_service', 'rusak', 'dijual'])
                  ->default('aktif');
            $table->timestamps();

            $table->index(['kategori', 'status']);
            $table->index('m_pemakai_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_barangs');
    }
};
