<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturPenjualan extends Model
{
    protected $table = 'retur_penjualan';

    protected $primaryKey = 'no_retur';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'no_retur',
        'id_pengiriman_pesanan',
        'tanggal',
        'nama_pembeli',
        'alamat_pembeli',
        'nama_penjual',
        'alamat_penjual',
        'jumlah',
        'ppn',
        'total_harga',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2',
        'ppn' => 'decimal:2',
        'total_harga' => 'decimal:2',
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
            DetailMReturPenjualan::class,
            'no_retur',
            'no_retur'
        );
    }
}