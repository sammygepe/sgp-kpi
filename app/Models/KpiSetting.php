<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpiSetting extends Model
{
    protected $fillable = [
        'grace_period_days',
        'grace_period_december_days',
        'max_time_factor_floor',
        'auto_close_enabled',
        'updated_by',
    ];

    protected $casts = [
        'auto_close_enabled' => 'boolean',
        'max_time_factor_floor' => 'decimal:2',
        'grace_period_days' => 'integer',
        'grace_period_december_days' => 'integer',
    ];
}
