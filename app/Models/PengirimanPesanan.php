<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PengirimanPesanan extends Model
{
    protected $table = 'pengiriman_pesanan';

    protected $primaryKey = 'id_pengiriman_pesanan';

    protected $fillable = [
        'no_pesanan',
        'tanggal',
        'nama_pengirim',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function pesananPenjualan(): BelongsTo
    {
        return $this->belongsTo(
            PesananPenjualan::class,
            'no_pesanan',
            'no_pesanan'
        );
    }

    public function details(): HasMany
    {
        return $this->hasMany(
            DetailMPengirimanPesanan::class,
            'id_pengiriman_pesanan',
            'id_pengiriman_pesanan'
        );
    }

    public function fakturPenjualan(): HasOne
    {
        return $this->hasOne(
            FakturPenjualan::class,
            'id_pengiriman_pesanan',
            'id_pengiriman_pesanan'
        );
    }
}