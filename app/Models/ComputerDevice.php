<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ComputerDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'comp_name',
        'user_name',
        'department_id',
        'location',
        'device_type',
        'ip_address',
        'operating_system',
        'specs',
        'status',
        'notes',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(PcMaintenanceRecord::class)->orderBy('maintenance_date', 'desc');
    }

    public function latestMaintenance(): HasOne
    {
        return $this->hasOne(PcMaintenanceRecord::class)->latestOfMany('maintenance_date');
    }

    public function scopeKantor($query)
    {
        return $query->where('location', 'kantor');
    }

    public function scopePabrik($query)
    {
        return $query->where('location', 'pabrik');
    }
}
