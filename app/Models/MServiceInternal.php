<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MServiceInternal extends Model
{
    protected $table = 'm_service_internals';

    protected $fillable = [
        'tgl_service', 'tgl_selesai', 'm_pemakai_id', 'm_barang_id', 'kerusakan',
    ];

    protected $casts = [
        'tgl_service' => 'date',
        'tgl_selesai' => 'date',
    ];

    public function pemakai(): BelongsTo
    {
        return $this->belongsTo(Pemakai::class, 'm_pemakai_id');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'm_barang_id');
    }
}
