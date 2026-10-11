<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailTPermintaanPembelian extends Model
{
    protected $table = 'detail_t_permintaan_pembelian';

    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'id_permintaan',
        'sku',
        'kuantitas',
        'keterangan',
    ];

    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(
            TPermintaanPembelian::class,
            'id_permintaan',
            'id_permintaan'
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
