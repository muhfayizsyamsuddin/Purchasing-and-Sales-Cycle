<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MBarang extends Model
{
    protected $table = 'm_barang';

    protected $primaryKey = 'sku';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'sku',
        'nama_barang',
    ];
}