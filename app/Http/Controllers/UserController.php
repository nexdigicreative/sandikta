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
            // nis row 01, nama lengkap row 02, kelas row 03, tanggal lahir row 04
            // Header CSV
            fputcsv($file, ['NIS', 'Nama Lengkap', 'Kelas', 'Tanggal Lahir'], ';');
            // Contoh Data dari Gambar
            fputcsv($file, ['10001', 'Arya Prasetyo', 'VIII A', '01-01-2010'], ';');
            fputcsv($file, ['10002', 'Aditya Pratama', 'IX B', '02-01-2010'], ';');
            fputcsv($file, ['10003', 'Budi Santoso', 'X TKJ 1', '03-01-2010'], ';');
            fputcsv($file, ['10004', 'Citra Lestari', 'X IPA 1', '04-01-2010'], ';');
            fputcsv($file, ['10005', 'Deni Saputra', 'XI TKJ 2', '05-01-2010'], ';');
            fputcsv($file, ['10006', 'Eka Sari', 'XI IPS 1', '06-01-2010'], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120'
        ], [
            'file.mimes' => 'File harus berformat CSV atau TXT.',
            'file.required' => 'Pilih file terlebih dahulu.'
        ]);

        $file = $request->file('file');

        try {
            $content = file_get_contents($file->getPathname());
            // Remove UTF-8 BOM if present
            $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

            // Normalize line endings
            $content = str_replace(["\r\n", "\r"], "\n", $content);
            $lines = explode("\n", $content);

            if (count($lines) < 2) {
                return back()->with('error', 'File CSV kosong atau tidak valid.');
            }

            // Detect delimiter from header
            $headerLine = $lines[0];
            $delimiter = str_contains($headerLine, ';') ? ';' : ',';

            array_shift($lines); // Remove header

            $imported = 0;
            $errors = [];
            $skipped = 0;

            foreach ($lines as $index => $line) {
                if (empty(trim($line)))
                    continue;

                $row = str_getcsv($line, $delimiter);

                // Expecting at least 4 columns: NIS, Nama, Kelas, Tgl Lahir
                if (count($row) < 4) {
                    $errors[] = "Baris " . ($index + 2) . ": Format kolom tidak lengkap.";
                    continue;
                }

                $nis = trim($row[0]);
                $name = trim($row[1]);
                $kelas = trim($row[2]);
                $tglRaw = trim($row[3]);

                if (empty($nis) || empty($name)) {
                    $skipped++;
                    continue;
                }

                // Validation
                if (User::where('nis', $nis)->exists()) {
                    $errors[] = "Baris " . ($index + 2) . ": NIS {$nis} sudah terdaftar.";
                    continue;
                }

                try {
                    // Standardize date
                    $timestamp = strtotime($tglRaw);
                    if (!$timestamp) {
                        // Try d/m/Y if Y-m-d fails
                        $dateObj = \DateTime::createFromFormat('d/m/Y', $tglRaw);
                        if ($dateObj)
                            $timestamp = $dateObj->getTimestamp();
                    }

                    if (!$timestamp) {
                        $errors[] = "Baris " . ($index + 2) . ": Format tanggal salah ({$tglRaw}). Gunakan YYYY-MM-DD atau DD/MM/YYYY.";
                        continue;
                    }

                    $tgl = date('Y-m-d', $timestamp);
                    $password = date('dmY', $timestamp); // Password default ddmmyyyy

                    User::create([
                        'nis' => $nis,
                        'name' => $name,
                        'kelas' => $kelas,
                        'tanggal_lahir' => $tgl,
                        'password' => $password, // Laravel 11 hashes this via model cast
                        'role' => 'user',
                        'must_change_password' => true,
                        'is_active' => true,
                    ]);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 2) . ": Gagal menyimpan (" . $e->getMessage() . ")";
                }
            }

            ActivityLog::log('import_users', "Import {$imported} users dari CSV", null, null);

            if ($imported > 0) {
                $msg = "Berhasil mengimpor {$imported} user.";
                if (!empty($errors)) {
                    $msg .= " Ada " . count($errors) . " baris bermasalah.";
                    return back()->with('success', $msg)->with('warning', implode('<br>', array_slice($errors, 0, 5)));
                }
                return back()->with('success', $msg);
            } else {
                return back()->with('error', 'Tidak ada data yang berhasil diimpor. Periksa format file Anda.')->with('warning', implode('<br>', array_slice($errors, 0, 5)));
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat membaca file: ' . $e->getMessage());
        }
    }

    public function deleteTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Template_Hapus_Masal.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['NIS', 'Nama Lengkap', 'Kelas', 'Tanggal Lahir'], ';');
            fputcsv($file, ['10001', 'Arya Prasetyo', 'VIII A', '01-01-2010'], ';');
            fputcsv($file, ['10002', 'Aditya Pratama', 'IX B', '02-01-2010'], ';');
            fputcsv($file, ['10003', 'Budi Santoso', 'X TKJ 1', '03-01-2010'], ';');
            fputcsv($file, ['10004', 'Citra Lestari', 'X IPA 1', '04-01-2010'], ';');
            fputcsv($file, ['10005', 'Deni Saputra', 'XI TKJ 2', '05-01-2010'], ';');
            fputcsv($file, ['10006', 'Eka Sari', 'XI IPS 1', '06-01-2010'], ';');
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function bulkDelete(Request $request)
    {

        // Check if it's from CSV or checkboxes
        if ($request->hasFile('file')) {
            try {
                $request->validate(['file' => 'required|file|mimes:csv,txt|max:5120']);
                $file = $request->file('file');
                $content = file_get_contents($file->getPathname());
                // Remove UTF-8 BOM if present
                $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
                // Normalize line endings
                $content = str_replace(["\r\n", "\r"], "\n", $content);
                $lines = explode("\n", $content);

                if (count($lines) < 2) {
                    return back()->with('error', 'File CSV kosong atau tidak valid.');
                }

                // Detect delimiter from header
                $headerLine = $lines[0];
                $delimiter = str_contains($headerLine, ';') ? ';' : ',';
                array_shift($lines); // Remove header

                $deleted = 0;
                $notFound = [];

                foreach ($lines as $line) {
                    if (empty(trim($line)))
                        continue;
                    $row = str_getcsv($line, $delimiter);
                    if (empty($row[0]))
                        continue;

                    $nis = trim($row[0]);
                    $user = User::where('nis', $nis)->where('role', 'user')->first();
                    if ($user) {
                        $user->delete();
                        $deleted++;
                    } else {
                        $notFound[] = $nis;
                    }
                }

                ActivityLog::log('bulk_delete_users', "Hapus masal {$deleted} users via CSV", null, null, 'danger');
                $msg = "Berhasil menghapus {$deleted} anggota.";
                if (!empty($notFound)) {
                    $msg .= " " . count($notFound) . " NIS tidak ditemukan.";
                }
                return back()->with('success', $msg);
            } catch (\Exception $e) {
                return back()->with('error', 'Terjadi kesalahan saat membaca file: ' . $e->getMessage());
            }
        } elseif ($request->has('selected_users')) {
            $ids = $request->selected_users;
            $count = User::whereIn('id', $ids)->where('role', 'user')->delete();
            ActivityLog::log('bulk_delete_users', "Hapus masal {$count} users via checkbox", null, null, 'danger');
            return back()->with('success', "Berhasil menghapus {$count} anggota terpilih.");
        }

        return back()->with('error', 'Tidak ada data yang dipilih.');
    }
}
