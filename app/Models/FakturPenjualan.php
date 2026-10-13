<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FakturPenjualan extends Model
{
    protected $table = 'faktur_penjualan';

    protected $primaryKey = 'no_faktur';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'no_faktur',
        'nama',
        'no_telp',
        'alamat',
        'tanggal',
        'jangka_waktu_pembayaran',
        'jumlah',
        'PPN',
        'total_harga',
        'id_pengiriman_pesanan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2',
        'PPN' => 'decimal:2',
        'total_harga' => 'decimal:2',
        'id_pengiriman_pesanan' => 'integer',
    ];

    public function pengirimanPesanan(): BelongsTo
    {
        return $this->belongsTo(
            PengirimanPesanan::class,
            'id_pengiriman_pesanan',
            'id_pengiriman_pesanan'
        );
    }

    public function details(): HasMany
    {
        return $this->hasMany(
            DetailMFakturPenjualan::class,
            'no_faktur',
            'no_faktur'
        );
    }
}