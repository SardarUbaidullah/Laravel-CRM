<?php
use App\Http\Controllers\Admin\UserController;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\Manager\DashboardController;
use App\Http\Controllers\Manager\ProjectController as manager_projectController;
use App\Http\Controllers\Manager\TaskController as manager_TaskController;
use App\Http\Controllers\Manager\TeamController as manager_TeamController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\TimeLogController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\SubTaskController;
use App\Http\Controllers\Manager\SubTaskController as manager_SubtaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::resource('teams', TeamController::class);
    Route::resource('users', UserController::class);


    // Route::get('/dashboard', function () {
    //     return view('admin.dashboard');
    // })->middleware(['auth', 'verified'])->name('dashboard');

// Manager routes

Route::prefix('manager')->name('manager.')->middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Projects - Full resource routes
    Route::get('/projects', [manager_projectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{id}', [manager_projectController::class, 'show'])->name('projects.show');
    Route::get('/projects/{id}/edit', [manager_projectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{id}', [manager_projectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{id}', [manager_projectController::class, 'destroy'])->name('projects.destroy');

    // Tasks
    Route::get('/tasks', [manager_TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/create', [manager_TaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks', [manager_TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{id}', [manager_TaskController::class, 'show'])->name('tasks.show');
    Route::get('/tasks/{id}/edit', [manager_TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('/tasks/{id}', [manager_TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{id}', [manager_TaskController::class, 'destroy'])->name('tasks.destroy');

    // Team
    Route::get('/team', [manager_TeamController::class, 'index'])->name('team.index');
    Route::get('/team/{id}', [manager_TeamController::class, 'show'])->name('team.show');


    Route::get('/tasks/{taskId}/subtasks', [manager_SubtaskController::class, 'index'])->name('subtasks.index');
    Route::get('/tasks/{taskId}/subtasks/create', [manager_SubtaskController::class, 'create'])->name('subtasks.create');
    Route::post('/tasks/{taskId}/subtasks', [manager_SubtaskController::class, 'store'])->name('subtasks.store');
    Route::get('/tasks/{taskId}/subtasks/{subtaskId}/edit', [manager_SubtaskController::class, 'edit'])->name('subtasks.edit');
    Route::put('/tasks/{taskId}/subtasks/{subtaskId}', [manager_SubtaskController::class, 'update'])->name('subtasks.update');
    Route::delete('/tasks/{taskId}/subtasks/{subtaskId}', [manager_SubtaskController::class, 'destroy'])->name('subtasks.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');



});
Route::resource('time-logs', TimeLogController::class);
Route::resource('tasks', TaskController::class);
Route::resource('milestones', MilestoneController::class);
Route::resource('files', FileController::class);
Route::get('/files/{id}/download', [FileController::class, 'download'])->name('files.download');
Route::resource('subtasks', SubTaskController::class);



Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
Route::get('/projects/{id}', [ProjectController::class, 'show'])->name('projects.show');
Route::get('/projects/{id}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
Route::put('/projects/{id}', [ProjectController::class, 'update'])->name('projects.update');
Route::delete('/projects/{id}', [ProjectController::class, 'destroy'])->name('projects.destroy');
require __DIR__.'/auth.php';
