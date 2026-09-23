<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang', 15)->unique();           // e.g. BRG-001
            $table->string('nama_barang', 150);
            $table->string('merk_model', 100)->nullable();         // Brand/model
            $table->string('serial_number', 80)->nullable()->unique();
            $table->string('kategori', 50);                        // komputer, laptop, monitor, printer, sparepart, jaringan, ups, lain_lain
            $table->string('lokasi', 80)->nullable();              // Tempat penyimpanan, e.g. "Gudang IT", "Meja Admin"
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->string('kondisi', 30)->default('aktif');       // aktif, cadangan, rusak, dijual, sedang_servis
            $table->text('keterangan')->nullable();
            $table->date('tgl_perolehan')->nullable();             // Tanggal beli/terima
            $table->decimal('harga_perolehan', 15, 0)->nullable(); // Harga beli (Rupiah)
            $table->timestamps();

            $table->index(['kategori', 'kondisi']);
            $table->index('department_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
