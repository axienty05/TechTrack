<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoutineChecklistResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_log_id',
        'item_name',
        'is_passed',
        'notes',
        'checked_at',
    ];

    protected $casts = [
        'is_passed' => 'boolean',
        'checked_at' => 'datetime',
    ];

    public function workLog(): BelongsTo
    {
        return $this->belongsTo(WorkLog::class);
    }
}
