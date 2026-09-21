<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'code',
        'default_sla_minutes',
        'icon',
        'color_hex',
    ];

    public function workLogs(): HasMany
    {
        return $this->hasMany(WorkLog::class);
    }

    public function routineSchedules(): HasMany
    {
        return $this->hasMany(RoutineSchedule::class);
    }
}
