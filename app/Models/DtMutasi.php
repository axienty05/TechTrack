<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DtMutasi extends Model
{
    protected $table = 'dt_mutasis';

    protected $fillable = [
        'mt_mutasi_id', 'pemakai_lama', 'm_barang_id', 'pemakai_baru', 'harga',
    ];

    public function mtMutasi(): BelongsTo
    {
        return $this->belongsTo(MtMutasi::class, 'mt_mutasi_id');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'm_barang_id');
    }

    // Resolve pemakai_lama ke nama
    public function getPemakaiLamaNameAttribute(): string
    {
        if (!$this->pemakai_lama) return '-';
        return Pemakai::find($this->pemakai_lama)?->nama ?? '-';
    }

    // Resolve pemakai_baru ke nama
    public function getPemakaiBaruNameAttribute(): string
    {
        if (!$this->pemakai_baru) return '-';
        return Pemakai::find($this->pemakai_baru)?->nama ?? '-';
    }
}
