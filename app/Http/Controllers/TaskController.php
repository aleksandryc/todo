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
                ->when(request()->input('search'), function ($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->with('users')
                ->orderBy('status','desc')
                ->get()
                ->map(function ($task) {
                    return [
                        'id' => $task->id,
                        'name' => $task->name,
                        'description' => $task->description,
                        'status' => $task->status,
                        'users' => $task->users->map(function ($user) {
                            return [

                                'name' => $user->name,
                            ];
                        }),
                    ];
                })
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

    public function edit()
    {
        return Inertia::render('Tasks/Edit', [
            'task' => Task::findOrFail(request()->route('task'))->load('users'),
            'asainedUsers' => Task::findOrFail(request()->route('task'))->users->pluck('id'),
            'users' => User::all()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                ];
            }),
        ]);
    }

    public function update()
    {
        $task = request('id');
        $validation = request()->validate([
            'name' => 'max:128',
            'description' => 'max:900',
            'status' => [
                'required',
                Rule::in(['pending', 'in_progress', 'completed']),
            ],
            'seluser' => 'required'
        ]);

        $taskModel = Task::query()
            ->findOrFail($task);

        $taskModel->update([
            'name' => $validation['name'],
            'description' => $validation['description'],
            'status' => $validation['status']
        ]);

        $taskModel->users()->sync($validation['seluser'] ?? []);
        return redirect('/tasks')->with('message', 'User updated successfully');
    }

    public function destroy($id)
    {
        Task::query()->findOrFail($id)->delete();
        return redirect('/tasks')->with('message', 'Task deleted successfully');
    }
}
