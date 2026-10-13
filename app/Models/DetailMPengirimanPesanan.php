<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailMPengirimanPesanan extends Model
{
    protected $table = 'detail_m_pengiriman_pesanan';

    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'id_pengiriman_pesanan',
        'sku',
        'keterangan',
        'total',
    ];

    protected $casts = [
        'id_pengiriman_pesanan' => 'integer',
        'total' => 'integer',
    ];

    public function pengirimanPesanan(): BelongsTo
    {
        return $this->belongsTo(
            PengirimanPesanan::class,
            'id_pengiriman_pesanan',
            'id_pengiriman_pesanan'
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