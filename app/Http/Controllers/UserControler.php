<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request as IRequest;

class UserControler extends Controller
{
    public function index()
    {
        return Inertia::render('Users/Index', [
            'users' => User::query()
                ->when(Request::input('search'), function ($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->paginate(10)
                ->withQueryString()
                ->through(fn($user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'can' => [
                        'update' => Gate::allows('update', $user)
                    ]
                ]),

            'filters' => Request::only(['search']),
            'can' => [
                'create' => Gate::allows('create', User::class)
            ]
        ]);
    }

    public function home()
    {
        return Inertia::render('Welcome', [
            'time' => now()->toTimeString(),
            'stats' => [
            'allTasks' => Task::count(),
            'completedTasks' => Task::where('status', 'completed')->count(),
            'pendingTasks' => Task::where('status', 'pending')->count(),
            'inProgressTasks' => Task::where('status', 'in_progress')->count(),
            'usersCount' => User::count(),
            'usersWithoutTasks' => User::doesntHave('tasks')->count(),
            'usersWithTasks' => User::has('tasks')->count(),
            'listOfUsersWOTasks' => User::doesntHave('tasks')->get()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                ];
            }),
            ]
        ]);
    }

    public function create()
    {
        return Inertia::render('Users/Create');
    }

    public function store(UpdateUserRequest $request)
    {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        return redirect('/users')->with('message', 'User created successfully');
    }

    public function edit()
    {
        return Inertia::render('Users/Edit', [
            'user' => User::findOrFail(request()->route('user'))
        ]);
    }

    public function update()
    {
        $user = request('id');
        $validation = request()->validate([
            'name' => 'max:128',
            'email' => [
                'email',
                Rule::unique('users')->ignore($user),
            ]
        ]);
        User::query()->findOrFail($user)->update($validation);
        return redirect('/users')->with('message', 'User updated successfully');
    }

    public function destroy($user)
    {
        User::query()->findOrFail($user)->delete();
        return redirect('/users')->with('message', 'User deleted successfully');
    }
}
