<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with('roles')->latest()->paginate(20);
        $roles = Role::all();

        return view('manage_user.index', compact('users', 'roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'nullable|exists:roles,name',
            'phone' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'phone' => $data['phone'] ?? null,
            'status' => $data['status'] ?? 'active',
            'business_id' => auth()->user()->business_id,
        ]);

        if ($data['role'] ?? false) {
            $user->assignRole($data['role']);
        }

        return back()->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,'.$user->id,
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:6',
            'role' => 'nullable|exists:roles,name',
            'phone' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        $user->fill($data);
        if (! empty($data['password'])) {
            $user->password = bcrypt($data['password']);
        }
        $user->save();

        if ($data['role'] ?? false) {
            $user->syncRoles($data['role']);
        }

        return back()->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return back()->with('success', 'Pengguna berhasil dihapus.');
    }
}
