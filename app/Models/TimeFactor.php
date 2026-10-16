<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeFactor extends Model
{
    protected $fillable = [
        'code',
        'selisih_min',
        'selisih_max',
        'faktor',
        'label',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'faktor' => 'decimal:2',
        'selisih_min' => 'integer',
        'selisih_max' => 'integer',
    ];
}
