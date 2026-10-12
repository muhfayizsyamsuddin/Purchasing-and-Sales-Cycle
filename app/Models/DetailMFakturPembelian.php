<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailMFakturPembelian extends Model
{
    protected $table = 'detail_m_faktur_pembelian';

    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'no_faktur',
        'sku',
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

    public function faktur(): BelongsTo
    {
        return $this->belongsTo(
            FakturPembelian::class,
            'no_faktur',
            'no_faktur'
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