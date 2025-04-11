<?php

use App\Http\Controllers\Auth\LoginController as AuthLoginController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TodosController;
use App\Http\Controllers\UserControler;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Validation\Rule;

Route::get('login', [AuthLoginController::class, 'create'])->name('login');
Route::post('login', [AuthLoginController::class, 'store'])->name('login.store');
Route::post('/logout', [AuthLoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/', [UserControler::class, 'home'])->name('home');
    Route::get('/users', [UserControler::class, 'index'])->name('users');
    Route::get('/users/create', [UserControler::class, 'create'])->name('users.create')->can('create', 'App\Models\User');
    Route::post('/users', [UserControler::class, 'store']);
    Route::get('/users/{user}/edit', [UserControler::class, 'edit']);
    Route::post('/users/{user}/update', [UserControler::class, 'update']);
    Route::post('/users/{user}/delete', [UserControler::class, 'destroy']);

    Route::get('/settings', function () {
        return Inertia::render('Settings');
    });

    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks');

});
