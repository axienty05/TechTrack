<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PcMaintenanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'computer_device_id',
        'technician_id',
        'maintenance_date',
        'period',
        'checklist_items',
        'notes',
        'user_sign_name',
        'is_user_signed',
        'overall_condition',
        'work_log_id',
    ];

    protected $casts = [
        'checklist_items' => 'array',
        'maintenance_date' => 'date',
        'is_user_signed' => 'boolean',
    ];

    public function computerDevice(): BelongsTo
    {
        return $this->belongsTo(ComputerDevice::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function workLog(): BelongsTo
    {
        return $this->belongsTo(WorkLog::class);
    }
}
