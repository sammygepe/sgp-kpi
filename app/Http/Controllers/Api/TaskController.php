<?php

namespace App\Http\Controllers\Api;

use App\Models\TaskInstance;
use App\Models\TaskMaster;
use App\Models\Subtask;
use App\Models\ProgressLog;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TaskController extends Controller
{
    // === MASTER (TEMPLATE) ===

    // 1. Buat Task Master (Template)
    public function createMaster(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'bobot' => 'required|integer|min:1|max:10',
            'jenis' => 'required|in:rutin,proyek',
            'is_recurring' => 'required|boolean',
            'recurrence_type' => 'nullable|in:daily,weekly,monthly,yearly,custom',
            'recurrence_day' => 'nullable|integer|min:1|max:31',
            'recurrence_month' => 'nullable|integer|min:1|max:12',
            'recurrence_interval' => 'nullable|integer|min:1',
            'generate_offset_days' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $master = TaskMaster::create([
            ...$validated,
            'created_by' => auth()->id(),
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'message' => 'Task master created successfully',
            'data' => $master,
        ], 201);
    }

    // 2. Daftar semua master (untuk atasan/supervisor)
    public function listMasters(Request $request)
    {
        $masters = TaskMaster::with('creator')->paginate(20);
        return response()->json($masters);
    }

    // === INSTANCE (EKSEKUSI TUGAS) ===

    // 1. Usulan staf (Bottom-Up)
    public function proposeTask(Request $request)
    {
        $validated = $request->validate([
            'judul_spesifik' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'jenis' => 'required|in:rutin,proyek',
            'recurrence_type' => 'nullable|in:daily,weekly,monthly,yearly',
            'recurrence_day' => 'nullable|integer|min:1|max:31',
            'recurrence_month' => 'nullable|integer|min:1|max:12',
            'generate_offset_days' => 'nullable|integer|min:0|max:365',
            'bobot' => 'required|integer|min:1|max:10',
            'deadline' => 'required|date|after_or_equal:today',
            'subtasks' => 'nullable|array',
            'subtasks.*.judul' => 'required|string|max:255',
            'tanggal_mulai' => 'nullable|date|before_or_equal:deadline',
        ]);

        $task = TaskInstance::create([
            'judul_spesifik' => $validated['judul_spesifik'],
            'deskripsi' => $validated['deskripsi'],
            'jenis' => $validated['jenis'],
            'recurrence_type' => $validated['recurrence_type'] ?? null,
            'recurrence_day' => $validated['recurrence_day'] ?? null,
            'recurrence_month' => $validated['recurrence_month'] ?? null,
            'generate_offset_days' => $validated['generate_offset_days'] ?? 0,
            'bobot' => $validated['bobot'],
            'deadline' => $validated['deadline'],
            'assignee_id' => auth()->id(),
            'assignor_id' => auth()->id(),
            'initiation_type' => 'proposal',
            'status' => 'proposed',
            'nilai' => 100,
            'tanggal_mulai' => $validated['tanggal_mulai'] ?? null,
        ]);

        // Jika ada subtasks
        if (!empty($validated['subtasks'])) {
            foreach ($validated['subtasks'] as $subtask) {
                Subtask::create([
                    'task_instance_id' => $task->id,
                    'judul' => $subtask['judul'],
                    'is_done' => false,
                ]);
            }
        }

        return response()->json([
            'message' => 'Task proposed successfully',
            'data' => $task->load('subtasks'),
        ], 201);
    }

    // 2. Atasan approve usulan (atau revisi/tolak)
    public function approveTask(Request $request, $taskId)
    {
        $task = TaskInstance::findOrFail($taskId);

        // Hanya atasan yang bisa approve
        if (!in_array(auth()->user()->role, ['atasan', 'supervisor', 'admin'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'bobot' => 'nullable|integer|min:1|max:10',
            'deadline' => 'nullable|date',
            'reason' => 'nullable|string|max:255',
        ]);

        // Catat perubahan di audit_logs
        foreach (['bobot', 'deadline'] as $field) {
            if (isset($validated[$field]) && $task->$field != $validated[$field]) {
                AuditLog::create([
                    'task_instance_id' => $task->id,
                    'changed_by' => auth()->id(),
                    'field_name' => $field,
                    'old_value' => (string) $task->$field,
                    'new_value' => (string) $validated[$field],
                    'reason' => $validated['reason'] ?? 'Approval adjustment',
                ]);
            }
        }

        $task->status = $validated['status'];
        if (isset($validated['bobot'])) {
            $task->bobot = $validated['bobot'];
        }
        if (isset($validated['deadline'])) {
            $task->deadline = $validated['deadline'];
        }
        $task->save();

        return response()->json([
            'message' => "Task {$task->status}",
            'data' => $task,
        ]);
    }

    // 3. Staf submit tugas (setelah selesai)
    public function submitTask(Request $request, $taskId)
    {
        $task = TaskInstance::findOrFail($taskId);

        if ($task->assignee_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Cek subtasks (jika ada) harus 100%
        $totalSubtasks = $task->subtasks()->count();
        if ($totalSubtasks > 0) {
            $doneSubtasks = $task->subtasks()->where('is_done', true)->count();
            if ($doneSubtasks < $totalSubtasks) {
                return response()->json([
                    'message' => 'All subtasks must be completed before submit',
                ], 422);
            }
        }

        $task->status = 'submitted';
        $task->tanggal_submit = now()->toDateString();
        $task->submitted_at = now();

        // Hitung faktor waktu via PostgreSQL Function
        $result = DB::selectOne(
            'SELECT hitung_kpi(?, ?, ?, ?) as faktor',
            [
                $task->bobot,
                100, // Nilai default 100 (sementara)
                $task->deadline,
                $task->tanggal_submit,
            ]
        );

        $task->faktor_waktu = $result->faktor ?? 1.0;
        $task->save();

        return response()->json([
            'message' => 'Task submitted successfully',
            'data' => $task,
        ]);
    }

    // 4. Atasan memberi nilai
    public function nilaiTask(Request $request, $taskId)
    {
        $task = TaskInstance::findOrFail($taskId);

        if (!in_array(auth()->user()->role, ['atasan', 'supervisor', 'admin'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'nilai' => 'required|integer|min:0|max:100',
            'reason' => 'nullable|string|max:255',
        ]);

        // Catat perubahan jika nilai < 100
        if ($validated['nilai'] < 100) {
            AuditLog::create([
                'task_instance_id' => $task->id,
                'changed_by' => auth()->id(),
                'field_name' => 'nilai',
                'old_value' => (string) $task->nilai,
                'new_value' => (string) $validated['nilai'],
                'reason' => $validated['reason'] ?? 'Nilai adjusted',
            ]);
        }

        $task->nilai = $validated['nilai'];
        $task->status = 'completed';

        // Hitung skor akhir via PostgreSQL Function
        $result = DB::selectOne(
            'SELECT hitung_kpi(?, ?, ?, ?) as skor',
            [
                $task->bobot,
                $task->nilai,
                $task->deadline,
                $task->tanggal_submit,
            ]
        );

        $task->skor_akhir = $result->skor ?? 0;
        $task->save();

        return response()->json([
            'message' => 'Task scored successfully',
            'data' => $task,
        ]);
    }

    // 5. Daftar tugas staf (dashboard staf)
    public function myTasks(Request $request)
    {
        $tasks = TaskInstance::where('assignee_id', auth()->id())
            ->with(['assignor', 'master', 'subtasks'])
            ->paginate(20);

        return response()->json($tasks);
    }

    // 6. Daftar tugas untuk atasan (antrian approval & penilaian)
    public function supervisorQueue(Request $request)
    {
        $user = auth()->user();
        $role = $user->role;

        $query = TaskInstance::with(['assignee', 'assignor', 'master', 'subtasks']);

        if ($role === 'atasan' || $role === 'admin') {
            // Lihat semua tugas dari bawahannya
            $subordinateIds = User::where('supervisor_id', $user->id)->pluck('id');
            $query->whereIn('assignee_id', $subordinateIds);
        } elseif ($role === 'supervisor') {
            // Lihat tugas dari yang dikoordinasi
            $coordinatedIds = User::where('coordinator_id', $user->id)->pluck('id');
            $query->whereIn('assignee_id', $coordinatedIds);
        }

        // Filter status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $tasks = $query->paginate(20);

        return response()->json($tasks);
    }

    // === SUBTASK ===

    // Centang subtask (auto-save)
    public function toggleSubtask(Request $request, $subtaskId)
    {
        $subtask = Subtask::findOrFail($subtaskId);

        $task = $subtask->taskInstance;
        if ($task->assignee_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $subtask->is_done = !$subtask->is_done;
        $subtask->save();

        // Update progress task
        $total = $task->subtasks()->count();
        $done = $task->subtasks()->where('is_done', true)->count();
        $task->progress = ($total > 0) ? round(($done / $total) * 100) : 0;
        $task->save();

        return response()->json([
            'message' => 'Subtask updated',
            'data' => $subtask,
            'progress' => $task->progress,
        ]);
    }

    // === PROGRESS LOG (untuk proyek) ===

    public function addProgressLog(Request $request, $taskId)
    {
        $task = TaskInstance::findOrFail($taskId);

        if ($task->assignee_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'progress' => 'required|integer|min:0|max:100',
            'catatan' => 'required|string|max:500',
        ]);

        $log = ProgressLog::create([
            'task_instance_id' => $task->id,
            'tanggal' => now()->toDateString(),
            'progress' => $validated['progress'],
            'catatan' => $validated['catatan'],
        ]);

        // Update progress task
        $task->progress = $validated['progress'];
        $task->save();

        return response()->json([
            'message' => 'Progress log added',
            'data' => $log,
        ]);
    }

    public function getTask($id)
    {
        $task = TaskInstance::with(['subtasks', 'assignee', 'assignor'])
            ->findOrFail($id);

        // Cek apakah user punya akses (assignee atau atasan)
        if (auth()->id() !== $task->assignee_id && auth()->user()->role !== 'atasan') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($task);
    }

    public function updateTask(Request $request, $id)
    {
        $task = TaskInstance::findOrFail($id);

        // Cek akses: hanya assignee yang bisa mengupdate catatan
        if (auth()->id() !== $task->assignee_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'catatan_harian' => 'nullable|string',
        ]);

        $task->update($validated);

        return response()->json([
            'message' => 'Catatan updated successfully',
            'data' => $task
        ]);
    }
}
