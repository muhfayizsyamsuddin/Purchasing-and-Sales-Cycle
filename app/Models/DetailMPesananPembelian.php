<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailMPesananPembelian extends Model
{
    protected $table = 'detail_m_pesanan_pembelian';

    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'id_detail',
        'sku',
        'no_order_pembelian',
        'kuantitas',
        'harga_unit',
        'diskon',
        'total_harga',
    ];

    protected function casts(): array
    {
        return [
            'kuantitas' => 'integer',
            'harga_unit' => 'decimal:2',
            'diskon' => 'decimal:2',
            'total_harga' => 'decimal:2',
        ];
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(
            PesananPembelian::class,
            'no_order_pembelian',
            'no_order_pembelian'
        );
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(
            MBarang::class,
            'sku',
            'sku'
        );
    }
}