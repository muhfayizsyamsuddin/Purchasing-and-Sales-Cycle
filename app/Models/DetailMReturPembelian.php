<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailMReturPembelian extends Model
{
    protected $table = 'detail_m_retur_pembelian';

    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'no_retur',
        'sku',
        'jumlah',
        'harga_unit',
        'diskon',
        'total_harga',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'harga_unit' => 'decimal:2',
            'diskon' => 'decimal:2',
            'total_harga' => 'decimal:2',
        ];
    }

    public function retur(): BelongsTo
    {
        return $this->belongsTo(
            ReturPembelian::class,
            'no_retur',
            'no_retur'
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