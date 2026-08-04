<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subtask extends Model
{
    protected $fillable = [
        'task_instance_id',
        'judul',
        'porsi',
        'is_done',
    ];

    protected $casts = [
        'is_done' => 'boolean',
    ];

    // Relasi: Subtask milik TaskInstance
    public function taskInstance(): BelongsTo
    {
        return $this->belongsTo(TaskInstance::class, 'task_instance_id');
    }
}
