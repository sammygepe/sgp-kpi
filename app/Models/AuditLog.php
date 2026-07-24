<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = [
        'task_instance_id',
        'changed_by',
        'field_name',
        'old_value',
        'new_value',
        'reason',
    ];

    // Relasi: AuditLog milik TaskInstance
    public function taskInstance(): BelongsTo
    {
        return $this->belongsTo(TaskInstance::class, 'task_instance_id');
    }

    // Relasi: AuditLog dibuat oleh User
    public function changer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
