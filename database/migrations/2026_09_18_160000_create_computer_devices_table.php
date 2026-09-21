<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('computer_devices', function (Blueprint $table) {
            $table->id();
            $table->string('comp_name', 100)->index();
            $table->string('user_name', 150)->index();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->enum('location', ['kantor', 'pabrik'])->default('kantor')->index();
            $table->string('device_type', 50)->default('PC Desktop');
            $table->string('ip_address', 45)->nullable();
            $table->string('operating_system', 100)->nullable();
            $table->text('specs')->nullable();
            $table->string('status', 30)->default('active'); // active, maintenance, broken, inactive
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('computer_devices');
    }
};
