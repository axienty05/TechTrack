<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_log_id')->constrained('work_logs')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->enum('attachment_type', ['before', 'after', 'scan_document', 'error_screenshot'])->default('after');
            $table->string('caption', 200)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_attachments');
    }
};
