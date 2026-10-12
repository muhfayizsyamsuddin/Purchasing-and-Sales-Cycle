<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TPermintaanPembelian extends Model
{
    protected $table = 'permintaan_pembelian';

    protected $primaryKey = 'no_faktur';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'no_faktur',
        'tanggal',
        'tanggal_diperlukan',
        'status',
        'description',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(
            DetailTPermintaanPembelian::class,
            'no_faktur',
            'no_faktur'
        );
    }
}
