<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FakturPembelian extends Model
{
    protected $table = 'faktur_pembelian';

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
        'ppn',
        'total_harga',
        'no_penerimaan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jumlah' => 'decimal:2',
            'ppn' => 'decimal:2',
            'total_harga' => 'decimal:2',
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

    public function details(): HasMany
    {
        return $this->hasMany(
            DetailMFakturPembelian::class,
            'no_faktur',
            'no_faktur'
        );
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(
            PembayaranPembelian::class,
            'no_faktur',
            'no_faktur'
        );
    }
}