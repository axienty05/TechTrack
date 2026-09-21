<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->string('code', 10)->unique();
            $table->unsignedInteger('default_sla_minutes')->default(60);
            $table->string('icon', 50)->nullable()->default('heroicon-o-wrench');
            $table->string('color_hex', 10)->nullable()->default('#3B82F6');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
