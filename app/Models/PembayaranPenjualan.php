<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembayaranPenjualan extends Model
{
    protected $table = 'pembayaran_penjualan';

    protected $primaryKey = 'no_pembayaran';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'no_pembayaran',
        'tanggal',
        'terima_dari',
        'terbilang',
        'untuk_pembayaran',
        'jumlah',
        'no_faktur',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2',
    ];

    public function fakturPenjualan(): BelongsTo
    {
        return $this->belongsTo(
            FakturPenjualan::class,
            'no_faktur',
            'no_faktur'
        );
    }
}