<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'user');
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name','like',"%{$s}%")->orWhere('nis','like',"%{$s}%")->orWhere('kelas','like',"%{$s}%"));
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        $users = $query->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|string|max:20|unique:users,nis',
            'name' => 'required|string|max:255',
            'kelas' => 'required|string|max:50',
            'tanggal_lahir' => 'required|date',
        ]);

        $password = date('dmY', strtotime($request->tanggal_lahir));
        $user = User::create([
            'nis' => $request->nis, 'name' => $request->name,
            'kelas' => $request->kelas, 'tanggal_lahir' => $request->tanggal_lahir,
            'password' => $password, 'role' => 'user',
            'must_change_password' => true, 'is_active' => true,
        ]);
        ActivityLog::log('create_user', "Tambah user: {$user->name} (NIS: {$user->nis})", User::class, $user->id);
        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan!');
    }

    public function edit(User $user)
    {
        if ($user->role === 'superadmin' && auth()->user()->role !== 'superadmin') abort(403);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->role === 'superadmin' && auth()->user()->role !== 'superadmin') abort(403);
        $request->validate([
            'nis' => 'required|string|max:20|unique:users,nis,'.$user->id,
            'name' => 'required|string|max:255',
            'kelas' => 'required|string|max:50',
            'tanggal_lahir' => 'required|date',
        ]);
        $user->update($request->only(['nis','name','kelas','tanggal_lahir']));
        ActivityLog::log('edit_user', "Edit user: {$user->name}", User::class, $user->id);
        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui!');
    }

    public function toggleStatus(User $user)
    {
        if ($user->role === 'superadmin') abort(403);
        $user->update(['is_active' => !$user->is_active]);
        $s = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        ActivityLog::log('toggle_user', "User {$user->name} {$s}", User::class, $user->id);
        return back()->with('success', "User berhasil {$s}!");
    }

    public function resetPassword(User $user)
    {
        if ($user->role === 'superadmin' && auth()->user()->role !== 'superadmin') abort(403);
        $newPassword = $user->tanggal_lahir ? $user->tanggal_lahir->format('dmY') : '12345678';
        $user->update(['password' => Hash::make($newPassword), 'must_change_password' => true]);
        ActivityLog::log('reset_password', "Reset password user: {$user->name}", User::class, $user->id, 'warning');
        return back()->with('success', "Password {$user->name} berhasil direset!");
    }

    public function destroy(User $user)
    {
        if (auth()->user()->role !== 'superadmin') abort(403);
        if ($user->role === 'superadmin') abort(403);
        $name = $user->name;
        $user->delete();
        ActivityLog::log('delete_user', "Hapus user: {$name}", null, null, 'danger');
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus!');
    }

    public function template()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Template_Import_Murid.csv"',
        ];
        
        $callback = function() {
            $file = fopen('php://output', 'w');
            // Header CSV
            fputcsv($file, ['NIS', 'Nama Lengkap', 'Kelas', 'Tanggal Lahir']);
            // Contoh Data
            fputcsv($file, ['10001', 'Budi Santoso', 'XII RPL 1', '2008-05-15']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt|max:5120']);
        $file = $request->file('file');
        $handle = fopen($file->getPathname(), 'r');
        $header = fgetcsv($handle);
        $imported = 0; $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 4) continue;
            try {
                $nis = trim($row[0]); $name = trim($row[1]);
                $kelas = trim($row[2]); $tgl = trim($row[3]);
                if (User::where('nis', $nis)->exists()) { $errors[] = "NIS {$nis} sudah ada"; continue; }
                $password = date('dmY', strtotime($tgl));
                User::create([
                    'nis' => $nis, 'name' => $name, 'kelas' => $kelas,
                    'tanggal_lahir' => date('Y-m-d', strtotime($tgl)),
                    'password' => $password, 'role' => 'user',
                    'must_change_password' => true, 'is_active' => true,
                ]);
                $imported++;
            } catch (\Exception $e) { $errors[] = "Baris error: {$row[0]}"; }
        }
        fclose($handle);
        ActivityLog::log('import_users', "Import {$imported} users dari CSV", null, null);
        $msg = "Berhasil import {$imported} user.";
        if (!empty($errors)) $msg .= ' Errors: ' . implode(', ', array_slice($errors, 0, 5));
        return back()->with('success', $msg);
    }
}
