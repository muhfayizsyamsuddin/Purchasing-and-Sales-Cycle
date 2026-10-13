<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailMFakturPenjualan extends Model
{
    protected $table = 'detail_m_faktur_penjualan';

    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'no_faktur',
        'sku',
        'kuantitas',
        'harga_unit',
        'diskon',
        'total_harga',
    ];

    protected $casts = [
        'kuantitas' => 'integer',
        'harga_unit' => 'decimal:2',
        'diskon' => 'decimal:2',
        'total_harga' => 'decimal:2',
    ];

    public function fakturPenjualan(): BelongsTo
    {
        return $this->belongsTo(
            FakturPenjualan::class,
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