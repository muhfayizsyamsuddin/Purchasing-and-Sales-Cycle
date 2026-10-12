<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PesananPembelian extends Model
{
    protected $table = 'pesanan_pembelian';

    protected $primaryKey = 'no_order_pembelian';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'no_order_pembelian',
        'tanggal',
        'tanggal_dibutuhkan',
        'jumlah',
        'ppn',
        'total_harga',
        'id_vendor',
        'no_faktur',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'tanggal_dibutuhkan' => 'date',
            'jumlah' => 'decimal:2',
            'ppn' => 'decimal:2',
            'total_harga' => 'decimal:2',
        ];
    }

    public function details(): HasMany
    {
        return $this->hasMany(
            DetailMPesananPembelian::class,
            'no_order_pembelian',
            'no_order_pembelian'
        );
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(
            MVendor::class,
            'id_vendor',
            'id_vendor'
        );
    }

    public function penerimaans(): HasMany
    {
        return $this->hasMany(
            PenerimaanBarang::class,
            'no_order_pembelian',
            'no_order_pembelian'
        );
    }
}