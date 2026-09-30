<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $table = 'm_suppliers';

    protected $fillable = [
        'nama_supplier', 'alamat', 'no_telp',
        'email', 'cp', 'no_hp', 'keterangan', 'status',
    ];

    protected $casts = ['status' => 'boolean'];

    public function mtMutasis(): HasMany
    {
        return $this->hasMany(MtMutasi::class, 'm_supplier_id');
    }
}
