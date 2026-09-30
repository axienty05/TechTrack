<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('nama_supplier', 100);
            $table->string('alamat', 150)->nullable();
            $table->string('no_telp', 20)->nullable()->unique();
            $table->string('email', 100)->nullable()->unique();
            $table->string('cp', 100)->nullable();          // Contact Person name
            $table->string('no_hp', 20)->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('status')->default(true);       // true = aktif
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_suppliers');
    }
};
