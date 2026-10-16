<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KpiPeriod extends Model
{
    protected $fillable = [
        'year',
        'month',
        'grace_period_days',
        'status',
        'closed_at',
        'closed_by',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
        'grace_period_days' => 'integer',
    ];

    // Relasi: KpiPeriod memiliki banyak TaskInstance
    public function tasks(): HasMany
    {
        return $this->hasMany(TaskInstance::class, 'kpi_period_id');
    }

    // Relasi: KpiPeriod ditutup oleh User
    public function closer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }
}
