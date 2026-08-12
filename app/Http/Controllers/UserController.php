<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('custom/users/Index', [
            'users' => User::query()
                ->when($request->string('search')->toString(), function ($query, string $search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->latest()
                ->paginate($request->integer('per_page', 10))
                ->withQueryString(),
            'filters' => [
                'search' => $request->string('search')->toString(),
                'per_page' => $request->integer('per_page', 10),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('custom/users/Create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return to_route('users.index');
    }

    public function edit(User $user): Response
    {
        return Inertia::render('custom/users/Edit', [
            'user' => $user,
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return to_route('users.index');
    }

    public function destroy(User $user): RedirectResponse
    {
        User::whereKey($user->id)->delete();

        return to_route('users.index');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        User::destroy($validated['ids']);

        return to_route('users.index');
    }
}
