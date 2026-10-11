<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MVendor extends Model
{
    protected $table = 'm_vendor';

    protected $primaryKey = 'id_vendor';

    protected $fillable = [
        'nama_vendor',
    ];
}