<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routine_checklist_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_log_id')->constrained('work_logs')->cascadeOnDelete();
            $table->string('item_name');
            $table->boolean('is_passed')->default(true);
            $table->text('notes')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routine_checklist_results');
    }
};
