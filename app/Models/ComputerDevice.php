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
        'm_pemakai_id',
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

    public function pemakai(): BelongsTo
    {
        return $this->belongsTo(Pemakai::class, 'm_pemakai_id');
    }

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

    public function getUserNameAttribute(): ?string
    {
        return $this->pemakai?->nama;
    }

    public function setUserNameAttribute($value): void
    {
        if (!empty($value)) {
            $pemakai = \App\Models\Pemakai::firstOrCreate(
                ['nama' => $value],
                [
                    'comp_name' => $this->comp_name,
                    'department_id' => $this->department_id,
                    'status' => true,
                ]
            );
            $this->attributes['m_pemakai_id'] = $pemakai->id;
        }
    }

    public function scopeWhereUserName($query, string $name, string $operator = '=')
    {
        return $query->whereHas('pemakai', function ($q) use ($name, $operator) {
            $q->where('nama', $operator, $name);
        });
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
