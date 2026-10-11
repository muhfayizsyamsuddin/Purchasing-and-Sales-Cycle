<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TPermintaanPembelian extends Model
{
    protected $table = 't_permintaan_pembelian';

    protected $primaryKey = 'id_permintaan';

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
            'id_permintaan',
            'id_permintaan'
        );
    }
}
