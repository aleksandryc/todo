<?php

namespace App\Http\Controllers;

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
        ]);
    }

    public function create()
    {
        return Inertia::render('Users/Create');
    }

    public function store()
    {
        $attributes = request()->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required',
        ]);
        User::create($attributes);
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
