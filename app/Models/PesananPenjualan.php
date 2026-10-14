<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PesananPenjualan extends Model
{
    protected $table = 'pesanan_penjualan';

    protected $primaryKey = 'no_pesanan';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'no_pesanan',
        'tanggal',
        'nama_pembeli',
        'alamat',
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

    public function details(): HasMany
    {
        return $this->hasMany(
            DetailMPesananPenjualan::class,
            'no_pesanan',
            'no_pesanan'
        );
    }
}