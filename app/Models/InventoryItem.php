<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'merk_model',
        'serial_number',
        'kategori',
        'lokasi',
        'department_id',
        'kondisi',
        'keterangan',
        'tgl_perolehan',
        'harga_perolehan',
    ];

    protected $casts = [
        'tgl_perolehan' => 'date',
        'harga_perolehan' => 'decimal:2',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function histories()
    {
        return $this->hasMany(InventoryHistory::class)->orderBy('action_date', 'desc')->orderBy('id', 'desc');
    }
}
