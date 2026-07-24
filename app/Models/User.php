<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // <-- Tambahkan ini!

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // <-- Tambahkan HasApiTokens di sini!

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'supervisor_id',
        'coordinator_id',
        'jabatan',
        'nik',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi: User memiliki banyak task sebagai assignee (staf)
    public function tasks()
    {
        return $this->hasMany(TaskInstance::class, 'assignee_id');
    }

    // Relasi: User memiliki banyak task sebagai assignor (atasan)
    public function assignedTasks()
    {
        return $this->hasMany(TaskInstance::class, 'assignor_id');
    }

    // Relasi: User memiliki supervisor (atasan langsung)
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    // Relasi: User memiliki banyak bawahan (staf)
    public function subordinates()
    {
        return $this->hasMany(User::class, 'supervisor_id');
    }

    // Relasi: User memiliki coordinator (pembantu atasan)
    public function coordinator()
    {
        return $this->belongsTo(User::class, 'coordinator_id');
    }

    // Relasi: User memiliki banyak bawahan yang dikoordinasi
    public function coordinatedUsers()
    {
        return $this->hasMany(User::class, 'coordinator_id');
    }
}
