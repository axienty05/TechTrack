<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MtMutasi extends Model
{
    protected $table = 'mt_mutasis';

    protected $fillable = [
        'no_mutasi', 'm_supplier_id', 'jenis_mutasi', 'tgl_mutasi', 'keterangan',
    ];

    protected $casts = ['tgl_mutasi' => 'date'];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'm_supplier_id');
    }

    public function dtMutasis(): HasMany
    {
        return $this->hasMany(DtMutasi::class, 'mt_mutasi_id');
    }

    // Auto-generate nomor mutasi sesuai jenis
    public static function generateNo(string $jenis): string
    {
        $prefix = match ($jenis) {
            'pembelian'   => 'MB',
            'penjualan'   => 'MJ',
            'perpindahan' => 'MP',
            default       => 'MX',
        };

        $last = static::where('jenis_mutasi', $jenis)->orderByDesc('id')->value('no_mutasi');
        $num = $last ? (int) substr($last, 3) + 1 : 1;
        return $prefix . '/' . str_pad($num, 5, '0', STR_PAD_LEFT);
    }
}
