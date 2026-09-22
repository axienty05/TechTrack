<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class RoutineSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category_id',
        'frequency',
        'target_day',
        'checklist_template',
        'is_active',
        'pc_location', // 'kantor' | 'pabrik' | 'semua'
    ];

    protected $casts = [
        'checklist_template' => 'array',
        'is_active'          => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function workLogs(): HasMany
    {
        return $this->hasMany(WorkLog::class);
    }

    /**
     * Hitung tanggal maintenance berikutnya berdasarkan frekuensi dan
     * tanggal maintenance terakhir. Jika belum pernah maintenance,
     * anggap sudah jatuh tempo (kembalikan kemarin).
     */
    public function nextDueDate(?Carbon $lastMaintenanceDate): Carbon
    {
        if ($lastMaintenanceDate === null) {
            // Belum pernah maintenance → langsung overdue
            return Carbon::yesterday();
        }

        return match ($this->frequency) {
            'daily'                     => $lastMaintenanceDate->copy()->addDay(),
            'weekly'                    => $lastMaintenanceDate->copy()->addWeek(),
            'monthly'                   => $lastMaintenanceDate->copy()->addMonth(),
            'quarterly'                 => $lastMaintenanceDate->copy()->addMonths(3),
            'semester', 'semi_annually' => $lastMaintenanceDate->copy()->addMonths(6),
            'yearly'                    => $lastMaintenanceDate->copy()->addYear(),
            default                     => $lastMaintenanceDate->copy()->addMonth(),
        };
    }

    /**
     * Apakah perangkat ini sudah jatuh tempo maintenance berdasarkan jadwal?
     */
    public function isDueForDevice(?Carbon $lastMaintenanceDate): bool
    {
        return $this->nextDueDate($lastMaintenanceDate)->isPast();
    }

    /**
     * Label ringkas untuk frekuensi dalam Bahasa Indonesia.
     */
    public function getFrequencyLabelAttribute(): string
    {
        return match ($this->frequency) {
            'daily'                     => 'Harian',
            'weekly'                    => 'Mingguan',
            'monthly'                   => 'Bulanan',
            'quarterly'                 => 'Triwulan (3 Bulan)',
            'semester', 'semi_annually' => 'Semester (6 Bulan)',
            'yearly'                    => 'Tahunan',
            default                     => ucfirst($this->frequency),
        };
    }

    /**
     * Label ringkas untuk pc_location.
     */
    public function getPcLocationLabelAttribute(): string
    {
        return match ($this->pc_location) {
            'kantor' => 'Kantor',
            'pabrik' => 'Pabrik',
            default  => 'Semua Lokasi',
        };
    }
}
