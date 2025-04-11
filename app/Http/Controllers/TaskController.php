<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

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
    public function create()
    {
        return Inertia::render('Tasks/Create', [
            'users' => \App\Models\User::all()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                ];
            }),
        ]);
    }

    public function store()
    {
        $attributes = request()->validate([
            'name' => 'required|max:255',
            'description' => 'max:900',
            'users' => 'required',
            'status' => [
                'required',
                Rule::in(['pending', 'in_progress', 'completed']),
            ],
            'users.*' => 'exists:users,id',
        ]);
        Task::create([
            'name' => $attributes['name'],
            'description' => $attributes['description'],
            'status' => $attributes['status'],
        ])->users()->attach($attributes['users']);
        return redirect('/tasks')->with('message', 'User created successfully');
    }
}
