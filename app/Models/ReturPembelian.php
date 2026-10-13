<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturPembelian extends Model
{
    protected $table = 'retur_pembelian';

    protected $primaryKey = 'no_retur';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'no_retur',
        'no_penerimaan',
        'tanggal',
        'nama_pembeli',
        'alamat_pembeli',
        'nama_penjual',
        'alamat_penjual',
        'jumlah',
        'ppn',
        'total_harga',
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
            DetailMReturPembelian::class,
            'no_retur',
            'no_retur'
        );
    }
}