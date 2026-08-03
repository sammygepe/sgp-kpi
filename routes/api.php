<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\AdminController;
use Illuminate\Support\Facades\Route;

// Route publik (tanpa login)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Route yang membutuhkan token login (auth:sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Task Master (Template)
    Route::post('/masters', [TaskController::class, 'createMaster']);
    Route::get('/masters', [TaskController::class, 'listMasters']);

    // Task Instance
    Route::post('/tasks/propose', [TaskController::class, 'proposeTask']);
    Route::put('/tasks/{id}/approve', [TaskController::class, 'approveTask']);
    Route::post('/tasks/{id}/submit', [TaskController::class, 'submitTask']);
    Route::post('/tasks/{id}/nilai', [TaskController::class, 'nilaiTask']);
    Route::get('/my-tasks', [TaskController::class, 'myTasks']);
    Route::get('/supervisor-queue', [TaskController::class, 'supervisorQueue']);

    // Subtask
    Route::put('/subtasks/{id}/toggle', [TaskController::class, 'toggleSubtask']);

    // Progress Log
    Route::post('/tasks/{id}/progress-log', [TaskController::class, 'addProgressLog']);

    Route::get('/tasks/{id}', [TaskController::class, 'getTask']);
    Route::put('/tasks/{id}', [TaskController::class, 'updateTask']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::put('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);

    // resubmitTask from rejected
    Route::post('/tasks/{id}/resubmit', [TaskController::class, 'resubmitTask']);

    Route::get('/reports/staff', [ReportController::class, 'staffReport']);
    Route::get('/reports/staff/{id}', [ReportController::class, 'staffDetail']);

    // Admin
    Route::get('/admin/users', [AdminController::class, 'index']);
    Route::post('/admin/users', [AdminController::class, 'store']);
    Route::put('/admin/users/{id}', [AdminController::class, 'update']);
    Route::delete('/admin/users/{id}', [AdminController::class, 'destroy']);
});
