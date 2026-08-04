<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskInstance extends Model
{
    // === STATUSES (6 status, tanpa on_progress) ===
    const STATUSES = ['draft', 'proposed', 'approved', 'submitted', 'completed', 'rejected'];

    protected $fillable = [
        'master_id',
        'judul_spesifik',
        'jenis',
        'recurrence_type',
        'recurrence_day',
        'recurrence_month',
        'generate_offset_days',
        'deskripsi',
        'bobot',
        'deadline',
        'assignee_id',
        'assignor_id',
        'initiation_type',
        'status',
        'progress',
        'tanggal_submit',
        'submitted_at',
        'nilai',
        'faktor_waktu',
        'skor_akhir',
        'catatan_harian',
        'tanggal_mulai',
    ];

    protected $casts = [
        'deadline' => 'date',
        'tanggal_submit' => 'date',
        'submitted_at' => 'datetime',
        'nilai' => 'integer',
        'faktor_waktu' => 'decimal:2',
        'skor_akhir' => 'decimal:2',
    ];

    // Relasi: TaskInstance milik TaskMaster (opsional)
    public function master(): BelongsTo
    {
        return $this->belongsTo(TaskMaster::class, 'master_id');
    }

    // Relasi: TaskInstance dikerjakan oleh User (assignee)
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    // Relasi: TaskInstance dibuat oleh User (assignor)
    public function assignor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignor_id');
    }

    // Relasi: TaskInstance memiliki banyak Subtask
    public function subtasks(): HasMany
    {
        return $this->hasMany(Subtask::class, 'task_instance_id');
    }

    // Relasi: TaskInstance memiliki banyak ProgressLog
    public function progressLogs(): HasMany
    {
        return $this->hasMany(ProgressLog::class, 'task_instance_id');
    }

    // Relasi: TaskInstance memiliki banyak AuditLog
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'task_instance_id');
    }
}
