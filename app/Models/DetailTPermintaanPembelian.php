<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailTPermintaanPembelian extends Model
{
    protected $table = 'detail_m_permintaan_pembelian';

    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'no_faktur',
        'sku',
        'kuantitas',
        'keterangan',
    ];

    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(
            TPermintaanPembelian::class,
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
