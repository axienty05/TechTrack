<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MService extends Model
{
    protected $table = 'm_services';

    protected $fillable = [
        'no_sj', 'm_pemakai_id', 'm_barang_id', 'm_service_center_id',
        'tgl_service', 'tgl_selesai', 'biaya', 'kerusakan', 'analisa', 'solusi',
    ];

    protected $casts = [
        'tgl_service'  => 'date',
        'tgl_selesai'  => 'date',
    ];

    public function pemakai(): BelongsTo
    {
        return $this->belongsTo(Pemakai::class, 'm_pemakai_id');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'm_barang_id');
    }

    public function serviceCenter(): BelongsTo
    {
        return $this->belongsTo(ServiceCenter::class, 'm_service_center_id');
    }
}
