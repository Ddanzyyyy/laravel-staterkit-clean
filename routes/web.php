<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskListController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ponytail: custom pages live in pages/custom, starter kit pages stay untouched
Route::get('/', function () {
    return Inertia::render('custom/Welcome');
})->name('home');

// ponytail: custom dashboard lives in pages/custom, starter kit Dashboard.vue stays untouched
Route::get('dashboard', function () {
    return Inertia::render('custom/Dashboard', [
        'totalUsers' => User::count(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    Route::post('task-lists', [TaskListController::class, 'store'])->name('task-lists.store');
    Route::patch('task-lists/{list}', [TaskListController::class, 'update'])->name('task-lists.update');
    Route::delete('task-lists/{list}', [TaskListController::class, 'destroy'])->name('task-lists.destroy');

    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::post('users/bulk-destroy', [UserController::class, 'bulkDestroy'])->name('users.bulk-destroy');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
