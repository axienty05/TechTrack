<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'category_id',
        'department_id',
        'routine_schedule_id',
        'task_type',
        'title',
        'description',
        'action_taken',
        'requester_name',
        'device_identifier',
        'priority',
        'status',
        'started_at',
        'completed_at',
        'duration_minutes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'duration_minutes' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function ($workLog) {
            if (empty($workLog->ticket_number)) {
                $datePrefix = date('Ymd');
                do {
                    $suffix = str_pad((string) random_int(1, 999), 3, '0', STR_PAD_LEFT);
                    $ticketNumber = "IT-{$datePrefix}-{$suffix}";
                } while (static::where('ticket_number', $ticketNumber)->exists());

                $workLog->ticket_number = $ticketNumber;
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function routineSchedule(): BelongsTo
    {
        return $this->belongsTo(RoutineSchedule::class);
    }

    public function checklistResults(): HasMany
    {
        return $this->hasMany(RoutineChecklistResult::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(LogAttachment::class);
    }

    public function sparepartUsages(): HasMany
    {
        return $this->hasMany(SparepartUsage::class);
    }
}
