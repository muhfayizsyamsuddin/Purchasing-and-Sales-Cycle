<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailMPenerimaanBarang extends Model
{
    protected $table = 'detail_m_penerimaan_barang';

    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'no_penerimaan',
        'sku',
        'kuantitas',
        'kondisi_barang',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'kuantitas' => 'integer',
        ];
    }

    public function penerimaan(): BelongsTo
    {
        return $this->belongsTo(
            PenerimaanBarang::class,
            'no_penerimaan',
            'no_penerimaan'
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