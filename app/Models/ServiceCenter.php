<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCenter extends Model
{
    protected $table = 'm_service_centers';

    protected $fillable = [
        'nama_service', 'no_telp', 'alamat',
        'cp', 'no_hp', 'keterangan', 'status',
    ];

    protected $casts = ['status' => 'boolean'];

    public function services(): HasMany
    {
        return $this->hasMany(MService::class, 'm_service_center_id');
    }
}
