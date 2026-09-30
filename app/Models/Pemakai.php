<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pemakai extends Model
{
    protected $table = 'm_pemakais';

    protected $fillable = ['nama', 'comp_name', 'department_id', 'status', 'exclude_pc_maintenance'];

    protected $casts = [
        'status'                 => 'boolean',
        'exclude_pc_maintenance' => 'boolean',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function barangs(): HasMany
    {
        return $this->hasMany(Barang::class, 'm_pemakai_id');
    }

    public function computerDevices(): HasMany
    {
        return $this->hasMany(ComputerDevice::class, 'm_pemakai_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(MService::class, 'm_pemakai_id');
    }

    public function serviceInternals(): HasMany
    {
        return $this->hasMany(MServiceInternal::class, 'm_pemakai_id');
    }

    // Ambil nama komputer yang dipakai
    public function getCompNamesAttribute(): string
    {
        if (!empty($this->attributes['comp_name'])) {
            return $this->attributes['comp_name'];
        }
        return $this->computerDevices->pluck('comp_name')->implode(', ');
    }
}
