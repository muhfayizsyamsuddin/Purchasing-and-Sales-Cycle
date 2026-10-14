<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PenerimaanBarang extends Model
{
    protected $table = 'penerimaan_barang';

    protected $primaryKey = 'no_penerimaan';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'no_penerimaan',
        'tanggal_penerimaan',
        'no_order_pembelian',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_penerimaan' => 'date',
        ];
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(
            PesananPembelian::class,
            'no_order_pembelian',
            'no_order_pembelian'
        );
    }

    public function details(): HasMany
    {
        return $this->hasMany(
            DetailMPenerimaanBarang::class,
            'no_penerimaan',
            'no_penerimaan'
        );
    }

    public function returPembelian(): HasMany
    {
        return $this->hasMany(
            ReturPembelian::class,
            'no_penerimaan',
            'no_penerimaan'
        );
    }
}