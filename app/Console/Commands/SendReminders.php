<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\TaskInstance;
use App\Models\User;
use Illuminate\Console\Command;

class SendReminders extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Send task reminders to staff and supervisors';

    public function handle()
    {
        $today = now()->toDateString();
        $tomorrow = now()->addDay()->toDateString();

        // ========== 1. DEADLINE BESOK (H-1) ==========
        $tasksH1 = TaskInstance::whereDate('deadline', $tomorrow)
            ->whereNotIn('status', ['completed', 'rejected'])
            ->get();

        foreach ($tasksH1 as $task) {
            $this->createNotification(
                $task->assignee_id,
                '⏰ Deadline Besok!',
                "Tugas '{$task->judul_spesifik}' deadline besok ({$tomorrow}). Jangan lupa selesaikan!",
                'warning',
                "/tasks/{$task->id}"
            );
        }

        // ========== 2. DEADLINE HARI INI (H-0) ==========
        $tasksH0 = TaskInstance::whereDate('deadline', $today)
            ->whereNotIn('status', ['completed', 'rejected'])
            ->get();

        foreach ($tasksH0 as $task) {
            $this->createNotification(
                $task->assignee_id,
                '🔥 Deadline Hari Ini!',
                "Tugas '{$task->judul_spesifik}' deadline hari ini ({$today}). Segera selesaikan!",
                'danger',
                "/tasks/{$task->id}"
            );
        }

        // ========== 3. OVERDUE (TERLAMBAT) ==========
        $tasksOverdue = TaskInstance::whereDate('deadline', '<', $today)
            ->whereNotIn('status', ['completed', 'rejected'])
            ->get();

        foreach ($tasksOverdue as $task) {
            $days = now()->diffInDays($task->deadline);
            $this->createNotification(
                $task->assignee_id,
                '⚠️ Tugas Terlambat!',
                "Tugas '{$task->judul_spesifik}' sudah terlambat {$days} hari. Segera selesaikan!",
                'danger',
                "/tasks/{$task->id}"
            );
        }

        // ========== 4. USULAN MENUNGGU APPROVAL (UNTUK ATASAN) ==========
        $proposedTasks = TaskInstance::where('status', 'proposed')->get();

        if ($proposedTasks->count() > 0) {
            // Ambil semua atasan
            $supervisors = User::where('role', 'atasan')->orWhere('role', 'supervisor')->get();

            foreach ($supervisors as $supervisor) {
                // Hitung usulan dari bawahannya saja
                $subordinateIds = User::where('supervisor_id', $supervisor->id)->pluck('id');
                $count = TaskInstance::where('status', 'proposed')
                    ->whereIn('assignee_id', $subordinateIds)
                    ->count();

                if ($count > 0) {
                    $this->createNotification(
                        $supervisor->id,
                        '📩 Usulan Menunggu Persetujuan',
                        "Ada {$count} usulan tugas dari staf Anda yang menunggu persetujuan.",
                        'info',
                        '/dashboard/atasan?tab=proposed'
                    );
                }
            }
        }

        // ========== 5. TUGAS MENUNGGU NILAI (UNTUK ATASAN) ==========
        $submittedTasks = TaskInstance::where('status', 'submitted')->get();

        if ($submittedTasks->count() > 0) {
            $supervisors = User::where('role', 'atasan')->orWhere('role', 'supervisor')->get();

            foreach ($supervisors as $supervisor) {
                $subordinateIds = User::where('supervisor_id', $supervisor->id)->pluck('id');
                $count = TaskInstance::where('status', 'submitted')
                    ->whereIn('assignee_id', $subordinateIds)
                    ->count();

                if ($count > 0) {
                    $this->createNotification(
                        $supervisor->id,
                        '📊 Tugas Menunggu Nilai',
                        "Ada {$count} tugas dari staf Anda yang sudah disubmit dan menunggu penilaian.",
                        'info',
                        '/dashboard/atasan?tab=submitted'
                    );
                }
            }
        }

        $this->info('✅ Reminders sent successfully.');
    }

    private function createNotification($userId, $title, $message, $type, $link = null)
    {
        Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link,
        ]);
    }
}
