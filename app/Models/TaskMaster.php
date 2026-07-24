<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskMaster extends Model
{
    protected $fillable = [
        'judul',
        'deskripsi',
        'bobot',
        'jenis',
        'is_recurring',
        'recurrence_type',
        'recurrence_day',
        'recurrence_month',
        'recurrence_interval',
        'generate_offset_days',
        'created_by',
        'is_active',
    ];

    protected $casts = [
        'is_recurring' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relasi: TaskMaster dibuat oleh User
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi: TaskMaster memiliki banyak TaskInstance
    public function instances(): HasMany
    {
        return $this->hasMany(TaskInstance::class, 'master_id');
    }
}
