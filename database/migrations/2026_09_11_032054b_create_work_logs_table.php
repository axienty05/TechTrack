<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 30)->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('routine_schedule_id')->nullable()->constrained('routine_schedules')->nullOnDelete();
            $table->enum('task_type', ['reactive', 'preventive', 'administrative'])->default('reactive');
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->text('action_taken')->nullable();
            $table->string('requester_name', 100)->nullable()->default('Internal IT');
            $table->string('device_identifier', 100)->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['pending', 'in_progress', 'waiting_sparepart', 'escalated', 'completed', 'cancelled'])->default('in_progress');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->unsignedInteger('duration_minutes')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'status', 'started_at']);
            $table->index(['category_id', 'started_at']);
            $table->index('device_identifier');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_logs');
    }
};
