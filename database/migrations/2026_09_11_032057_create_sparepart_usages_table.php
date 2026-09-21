<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sparepart_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_log_id')->constrained('work_logs')->cascadeOnDelete();
            $table->string('item_code', 50)->nullable();
            $table->string('item_name', 150);
            $table->unsignedInteger('quantity')->default(1);
            $table->string('unit', 20)->default('Pcs');
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sparepart_usages');
    }
};
