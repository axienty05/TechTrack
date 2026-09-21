<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_log_id',
        'file_path',
        'original_name',
        'mime_type',
        'attachment_type',
        'caption',
    ];

    public function workLog(): BelongsTo
    {
        return $this->belongsTo(WorkLog::class);
    }
}
