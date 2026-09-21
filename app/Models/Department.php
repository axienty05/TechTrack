<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'floor_location',
    ];

    public function workLogs(): HasMany
    {
        return $this->hasMany(WorkLog::class);
    }
}
