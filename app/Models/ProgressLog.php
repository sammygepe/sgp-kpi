<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressLog extends Model
{
    protected $fillable = [
        'task_instance_id',
        'tanggal',
        'progress',
        'catatan',
        'activity_type',
        'description',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'progress' => 'integer',
    ];

    // Relasi: ProgressLog milik TaskInstance
    public function taskInstance(): BelongsTo
    {
        return $this->belongsTo(TaskInstance::class, 'task_instance_id');
    }
}
