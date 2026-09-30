<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    protected $table = 'm_barangs';

    protected $fillable = [
        'm_pemakai_id', 'kode_barang', 'nama_barang',
        'serial_number', 'kategori', 'keterangan', 'status',
    ];

    public function pemakai(): BelongsTo
    {
        return $this->belongsTo(Pemakai::class, 'm_pemakai_id');
    }

    public function dtMutasis(): HasMany
    {
        return $this->hasMany(DtMutasi::class, 'm_barang_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(MService::class, 'm_barang_id');
    }

    public function serviceInternals(): HasMany
    {
        return $this->hasMany(MServiceInternal::class, 'm_barang_id');
    }

    public function pcMaintenances(): HasMany
    {
        return $this->hasMany(PcMaintenanceRecord::class, 'm_barang_id');
    }

    // Auto-generate kode_barang (format: B/000001)
    public static function generateKode(): string
    {
        $last = static::orderByDesc('id')->value('kode_barang');
        if (!$last) return 'B/000001';
        $num = (int) substr($last, 2) + 1;
        return 'B/' . str_pad($num, 6, '0', STR_PAD_LEFT);
    }
}
