<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:admin,accountant,salesperson'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        User::create($data);

        return redirect()->route('admin.users.index')->with('status', 'User created.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', 'in:admin,accountant,salesperson'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $user->update($data);

        return redirect()->route('admin.users.index')->with('status', 'User updated.');
    }

    public function deactivate(User $user)
    {
        $user->update(['is_active' => false]);

        return redirect()->route('admin.users.index')->with('status', 'User deactivated.');
    }

    public function destroy(User $user)
    {
        if ((int) $user->id === (int) auth()->id()) {
            return redirect()->route('admin.users.index')->with('status', 'You cannot delete your own account from this screen.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('status', 'User deleted.');
    }
}
