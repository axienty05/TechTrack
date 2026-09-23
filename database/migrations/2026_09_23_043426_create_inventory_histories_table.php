<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Siapa yang mencatat
            $table->string('action', 50); // e.g., 'created', 'updated', 'maintenance', 'location_change', 'status_change'
            $table->text('description'); // Penjelasan aksi atau riwayat perbaikan
            $table->date('action_date'); // Kapan kejadian tersebut (bisa diisi manual, tidak selalu now())
            $table->string('attachment')->nullable(); // Lampiran foto jika ada perbaikan/mutasi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_histories');
    }
};
