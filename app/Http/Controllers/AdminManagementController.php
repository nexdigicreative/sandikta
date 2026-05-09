<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminManagementController extends Controller
{
    public function index()
    {
        $admins = User::where('role', 'admin')->latest()->paginate(20);
        return view('superadmin.admins.index', compact('admins'));
    }

    public function create()
    {
        return view('superadmin.admins.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);
        $admin = User::create([
            'name' => $request->name, 'email' => $request->email,
            'password' => $request->password, 'role' => 'admin',
            'must_change_password' => false, 'is_active' => true,
        ]);
        ActivityLog::log('create_admin', "Tambah admin: {$admin->name}", User::class, $admin->id);
        return redirect()->route('superadmin.admins.index')->with('success', 'Admin berhasil ditambahkan!');
    }

    public function edit(User $admin)
    {
        if ($admin->role !== 'admin') abort(404);
        return view('superadmin.admins.edit', compact('admin'));
    }

    public function update(Request $request, User $admin)
    {
        if ($admin->role !== 'admin') abort(404);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$admin->id,
        ]);
        $admin->update($request->only(['name','email']));
        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8|confirmed']);
            $admin->update(['password' => Hash::make($request->password)]);
        }
        ActivityLog::log('edit_admin', "Edit admin: {$admin->name}", User::class, $admin->id);
        return redirect()->route('superadmin.admins.index')->with('success', 'Admin berhasil diperbarui!');
    }

    public function toggleStatus(User $admin)
    {
        if ($admin->role !== 'admin') abort(404);
        $admin->update(['is_active' => !$admin->is_active]);
        $s = $admin->is_active ? 'diaktifkan' : 'dinonaktifkan';
        ActivityLog::log('toggle_admin', "Admin {$admin->name} {$s}", User::class, $admin->id);
        return back()->with('success', "Admin berhasil {$s}!");
    }

    public function destroy(User $admin)
    {
        if ($admin->role !== 'admin') abort(404);
        $name = $admin->name;
        $admin->delete();
        ActivityLog::log('delete_admin', "Hapus admin: {$name}", null, null, 'danger');
        return redirect()->route('superadmin.admins.index')->with('success', 'Admin berhasil dihapus!');
    }
}
