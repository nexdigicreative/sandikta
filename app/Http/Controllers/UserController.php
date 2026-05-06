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
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")->orWhere('nis', 'like', "%{$s}%")->orWhere('kelas', 'like', "%{$s}%"));
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
            'nis' => $request->nis,
            'name' => $request->name,
            'kelas' => $request->kelas,
            'tanggal_lahir' => $request->tanggal_lahir,
            'password' => $password,
            'role' => 'user',
            'must_change_password' => true,
            'is_active' => true,
        ]);
        ActivityLog::log('create_user', "Tambah user: {$user->name} (NIS: {$user->nis})", User::class, $user->id);
        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan!');
    }

    public function edit(User $user)
    {
        if ($user->role === 'superadmin' && auth()->user()->role !== 'superadmin')
            abort(403);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->role === 'superadmin' && auth()->user()->role !== 'superadmin')
            abort(403);
        $request->validate([
            'nis' => 'required|string|max:20|unique:users,nis,' . $user->id,
            'name' => 'required|string|max:255',
            'kelas' => 'required|string|max:50',
            'tanggal_lahir' => 'required|date',
        ]);
        $user->update($request->only(['nis', 'name', 'kelas', 'tanggal_lahir']));
        ActivityLog::log('edit_user', "Edit user: {$user->name}", User::class, $user->id);
        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui!');
    }

    public function toggleStatus(User $user)
    {
        if ($user->role === 'superadmin')
            abort(403);
        $user->update(['is_active' => !$user->is_active]);
        $s = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        ActivityLog::log('toggle_user', "User {$user->name} {$s}", User::class, $user->id);
        return back()->with('success', "User berhasil {$s}!");
    }

    public function resetPassword(User $user)
    {
        if ($user->role === 'superadmin' && auth()->user()->role !== 'superadmin')
            abort(403);
        $newPassword = $user->tanggal_lahir ? $user->tanggal_lahir->format('dmY') : '12345678';
        $user->update(['password' => Hash::make($newPassword), 'must_change_password' => true]);
        ActivityLog::log('reset_password', "Reset password user: {$user->name}", User::class, $user->id, 'warning');
        return back()->with('success', "Password {$user->name} berhasil direset!");
    }

    public function destroy(User $user)
    {
        if (auth()->user()->role !== 'superadmin')
            abort(403);
        if ($user->role === 'superadmin')
            abort(403);
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

        $callback = function () {
            $file = fopen('php://output', 'w');
            // Header CSV
            fputcsv($file, ['NIS', 'Nama Lengkap', 'Kelas', 'Tanggal Lahir']);
            // Contoh Data
            fputcsv($file, ['10001', 'Budi Santoso', 'XII RPL 1', '15-05-2008']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt|max:5120']);
        $file = $request->file('file');

        // Deteksi delimiter otomatis (koma vs titik koma)
        $handle = fopen($file->getPathname(), 'r');
        $firstLine = fgets($handle);
        rewind($handle);
        $delimiter = (strpos($firstLine, ';') !== false) ? ';' : ',';

        // Lewati BOM (Byte Order Mark) jika ada
        if (strpos($firstLine, "\xEF\xBB\xBF") === 0) {
            fseek($handle, 3);
        }

        $header = fgetcsv($handle, 0, $delimiter);
        $imported = 0;
        $errors = [];
        $lineNumber = 1;

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $lineNumber++;

                // Lewati baris kosong
                if (empty(array_filter($row)))
                    continue;

                if (count($row) < 4) {
                    $errors[] = "Baris {$lineNumber}: Kolom tidak lengkap (Minimal 4 kolom)";
                    continue;
                }

                $nis = trim($row[0]);
                $name = trim($row[1]);
                $kelas = trim($row[2]);
                $tgl = trim($row[3]);

                if (empty($nis) || empty($name)) {
                    $errors[] = "Baris {$lineNumber}: NIS atau Nama tidak boleh kosong";
                    continue;
                }

                if (User::where('nis', $nis)->exists()) {
                    $errors[] = "Baris {$lineNumber}: NIS {$nis} sudah terdaftar";
                    continue;
                }

                // Normalisasi format tanggal (Ubah / ke - agar strtotime mengenali sebagai DD-MM-YYYY)
                $cleanTgl = str_replace('/', '-', $tgl);
                $timestamp = strtotime($cleanTgl);

                if (!$timestamp) {
                    $errors[] = "Baris {$lineNumber}: Format tanggal lahir tidak valid ({$tgl})";
                    continue;
                }

                $password = date('dmY', $timestamp); // Default password dari tgl lahir

                User::create([
                    'nis' => $nis,
                    'name' => $name,
                    'kelas' => $kelas,
                    'tanggal_lahir' => date('Y-m-d', $timestamp),
                    'password' => $password, // Akan di-hash otomatis oleh model User (hashed cast)
                    'role' => 'user',
                    'must_change_password' => true,
                    'is_active' => true,
                ]);
                $imported++;
            }

            \Illuminate\Support\Facades\DB::commit();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            fclose($handle);
            return back()->with('error', "Gagal mengimpor data: " . $e->getMessage());
        }

        fclose($handle);

        ActivityLog::log('import_users', "Import {$imported} users dari CSV", null, null);

        $msg = "Berhasil mengimpor {$imported} user.";
        if (!empty($errors)) {
            $msg .= ' Beberapa baris gagal: ' . implode(', ', array_slice($errors, 0, 3));
            if (count($errors) > 3)
                $msg .= ' ...dan ' . (count($errors) - 3) . ' baris lainnya.';
        }

        return back()->with('success', $msg);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt|max:5120']);
        $file = $request->file('file');

        $handle = fopen($file->getPathname(), 'r');
        $firstLine = fgets($handle);
        rewind($handle);
        $delimiter = (strpos($firstLine, ';') !== false) ? ';' : ',';

        if (strpos($firstLine, "\xEF\xBB\xBF") === 0)
            fseek($handle, 3);

        $header = fgetcsv($handle, 0, $delimiter);
        $deleted = 0;
        $errors = [];
        $lineNumber = 1;

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $lineNumber++;
                if (empty(array_filter($row)))
                    continue;

                $nis = trim($row[0]);
                if (empty($nis))
                    continue;

                // Pastikan hanya menghapus user dengan role 'user'
                $user = User::where('nis', $nis)->where('role', 'user')->first();
                if ($user) {
                    $user->delete();
                    $deleted++;
                } else {
                    $errors[] = "Baris {$lineNumber}: NIS {$nis} tidak ditemukan";
                }
            }
            \Illuminate\Support\Facades\DB::commit();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            fclose($handle);
            return back()->with('error', "Gagal menghapus data: " . $e->getMessage());
        }

        fclose($handle);
        ActivityLog::log('bulk_delete_users', "Hapus massal {$deleted} user melalui CSV", null, null, 'danger');

        $msg = "Berhasil menghapus {$deleted} user secara massal.";
        if (!empty($errors)) {
            $msg .= ' Beberapa NIS tidak ditemukan: ' . implode(', ', array_slice($errors, 0, 3));
        }

        return back()->with('success', $msg);
    }
}
