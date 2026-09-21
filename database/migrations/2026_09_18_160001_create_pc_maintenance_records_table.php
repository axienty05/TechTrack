<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pc_maintenance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('computer_device_id')->constrained('computer_devices')->cascadeOnDelete();
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('maintenance_date')->index();
            $table->string('period', 50)->nullable()->index(); // e.g. "2026-09", "Q3 2026", "September 2026"
            $table->json('checklist_items')->nullable();
            $table->text('notes')->nullable();
            $table->string('user_sign_name', 150)->nullable();
            $table->boolean('is_user_signed')->default(false);
            $table->string('overall_condition', 30)->default('good'); // good, needs_attention, critical
            $table->foreignId('work_log_id')->nullable()->constrained('work_logs')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pc_maintenance_records');
    }
};
