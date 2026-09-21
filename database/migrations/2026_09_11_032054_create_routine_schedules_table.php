<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routine_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->enum('frequency', ['daily', 'weekly', 'monthly'])->default('daily');
            $table->string('target_day', 20)->nullable();
            $table->json('checklist_template');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routine_schedules');
    }
};
