<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
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
});
