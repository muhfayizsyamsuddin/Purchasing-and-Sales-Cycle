<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembayaranPembelian extends Model
{
    protected $table = 'pembayaran_pembelian';

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

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jumlah' => 'decimal:2',
        ];
    }

    public function faktur(): BelongsTo
    {
        return $this->belongsTo(
            FakturPembelian::class,
            'no_faktur',
            'no_faktur'
        );
    }
}