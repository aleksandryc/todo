<?php

use App\Http\Controllers\Auth\LoginController as AuthLoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\TablesController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TodosController;
use App\Http\Controllers\UserControler;
use App\Http\Controllers\UserFormController;
use App\Http\Controllers\WorkshopsController;
use App\Http\Middleware\Role;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Validation\Rule;

Route::get('login', [AuthLoginController::class, 'create'])->name('login');
Route::post('login', [AuthLoginController::class, 'store'])->name('login.store');
Route::post('/logout', [AuthLoginController::class, 'logout'])->name('logout')->middleware('auth');

//Form for portal
Route::prefix('forms')->name('forms.')->group(function(){
    // Show form by key
    Route::get('/{formKey}', [UserFormController::class, 'show'])->name('show');
    // Submit form
    Route::post('/{formKey}/submit', [UserFormController::class, 'submit'])->name('submit');
});



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
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::get('/tasks/{task}/update', [TaskController::class, 'edit']);
    Route::post('/tasks/{task}/update', [TaskController::class, 'update']);
    Route::post('/tasks/{task}/delete', [TaskController::class, 'destroy']);

    // Route for new app name
    // Admin routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/name/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/name/orders', [OrdersController::class, 'index']);
        Route::get('/name/tables', [TablesController::class, 'index']);
    });
    // Routes for workers
    Route::middleware('role:worker')->group(function () {
        Route::get('/name/worker/', [WorkshopsController::class, 'index'])->name('worker.index');
        Route::put('/name/worker/{tableId}/{shopId}/', [WorkshopsController::class, 'completeProcess'])->name('worker.process.complete');
    });

    // Routes for client (access only to their orders)
    Route::middleware('role:client')->group(function(){
        Route::get('name/client/orders', [OrdersController::class]);
    });
});


