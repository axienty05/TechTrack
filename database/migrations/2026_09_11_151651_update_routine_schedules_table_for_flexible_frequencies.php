<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('routine_schedules', function (Blueprint $table) {
            $table->string('frequency', 50)->default('monthly')->change();
            $table->string('target_day', 100)->nullable()->change();
            $table->json('checklist_template')->nullable()->change();
            $table->text('description')->nullable()->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('routine_schedules', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
