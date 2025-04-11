<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index()
    {
        return Inertia::render('Tasks/Index',  [
            'tasks' => Task::query()
                ->with('users')
                ->get()
                ->map(function ($task) {
                    return [
                        'name' => $task->name,
                        'description' => $task->description,
                        'status' => $task->status,
                        'users' => $task->users->map(function ($user) {
                            return [
                                'name' => $user->name,
                            ];
                        }),
                    ];
                }),
        ]);
    }
}
