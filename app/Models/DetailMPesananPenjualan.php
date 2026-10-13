<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailMPesananPenjualan extends Model
{
    protected $table = 'detail_m_pesanan_penjualan';

    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'no_pesanan',
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

    public function pesananPenjualan(): BelongsTo
    {
        return $this->belongsTo(
            PesananPenjualan::class,
            'no_pesanan',
            'no_pesanan'
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