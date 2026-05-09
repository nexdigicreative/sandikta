<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Ebook;
use App\Models\ReadingHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EbookController extends Controller
{
    public function index(Request $request)
    {
        $query = Ebook::with('category')->where('is_active', true);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('title','like',"%{$s}%")->orWhere('author','like',"%{$s}%"));
        }
        if ($request->filled('category')) $query->where('category_id', $request->category);
        if ($request->filled('kelas')) $query->where('kelas_tujuan', $request->kelas);
        $ebooks = $query->latest()->paginate(12);
        $categories = Category::where('is_active', true)->get();
        return view('user.ebooks.index', compact('ebooks', 'categories'));
    }

    public function show(Ebook $ebook)
    {
        if (!$ebook->is_active && !Auth::user()->isAdminOrAbove()) abort(404);
        $ebook->load('category');
        $relatedEbooks = Ebook::where('category_id', $ebook->category_id)
            ->where('id','!=',$ebook->id)
            ->where('is_active',true)
            ->take(4)->get();
        return view('user.ebooks.show', compact('ebook', 'relatedEbooks'));
    }

    public function read(Ebook $ebook)
    {
        if (!$ebook->is_active && !Auth::user()->isAdminOrAbove()) abort(404);
        $user = Auth::user();

        // Handle reading history - fix first read issue
        $history = ReadingHistory::where('user_id', $user->id)
            ->where('ebook_id', $ebook->id)
            ->first();

        if ($history) {
            $history->update([
                'last_read_at' => now(),
                'read_count' => $history->read_count + 1,
            ]);
        } else {
            ReadingHistory::create([
                'user_id' => $user->id,
                'ebook_id' => $ebook->id,
                'last_read_at' => now(),
                'read_count' => 1,
            ]);
        }

        $ebook->incrementViewCount();
        ActivityLog::log('read_ebook', "User {$user->name} membaca eBook: {$ebook->title}", Ebook::class, $ebook->id);

        $token = Str::random(64);
        session(["pdf_token_{$ebook->id}" => $token, "pdf_token_time_{$ebook->id}" => time()]);
        return view('user.ebooks.read', compact('ebook', 'token'));
    }

    public function streamPdf(Request $request, Ebook $ebook)
    {
        $sessionToken = session("pdf_token_{$ebook->id}");
        $tokenTime = session("pdf_token_time_{$ebook->id}");

        if (!$sessionToken || $request->query('token') !== $sessionToken) {
            ActivityLog::log('illegal_access', "Percobaan akses ilegal PDF: {$ebook->title}", Ebook::class, $ebook->id, 'danger');
            abort(403, 'Token akses tidak valid.');
        }
        if (!$tokenTime || (time() - $tokenTime > 7200)) {
            session()->forget(["pdf_token_{$ebook->id}", "pdf_token_time_{$ebook->id}"]);
            abort(403, 'Token akses kedaluwarsa. Silakan buka ulang eBook.');
        }

        if (!Storage::disk('local')->exists($ebook->file_path)) {
            abort(404, 'File PDF tidak ditemukan di server.');
        }

        $filePath = Storage::disk('local')->path($ebook->file_path);

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . Str::slug($ebook->title) . '.pdf"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
        ]);
    }

    // === ADMIN METHODS ===

    public function adminIndex(Request $request)
    {
        $query = Ebook::with(['category', 'uploader']);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('title','like',"%{$s}%")->orWhere('author','like',"%{$s}%"));
        }
        $ebooks = $query->latest()->paginate(15);
        $categories = Category::all();
        return view('admin.ebooks.index', compact('ebooks', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.ebooks.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:'.(date('Y')+1),
            'isbn' => 'nullable|string|max:20',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'kelas_tujuan' => 'nullable|string|max:50',
            'pdf_file' => 'required|file|mimes:pdf|max:51200',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'title.required' => 'Judul wajib diisi.',
            'author.required' => 'Penulis wajib diisi.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'pdf_file.required' => 'File PDF wajib diupload.',
            'pdf_file.mimes' => 'File harus berformat PDF.',
            'pdf_file.max' => 'Ukuran file maksimal 50MB.',
        ]);

        $pdfFile = $request->file('pdf_file');
        if ($pdfFile->getMimeType() !== 'application/pdf') {
            return back()->withErrors(['pdf_file' => 'File bukan PDF valid.'])->withInput();
        }

        $fileName = Str::uuid() . '.pdf';
        $filePath = $pdfFile->storeAs('ebooks/pdfs', $fileName, 'local');

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $c = $request->file('cover_image');
            $coverPath = $c->storeAs('ebooks/covers', Str::uuid().'.'.$c->getClientOriginalExtension(), 'public');
        }

        $ebook = Ebook::create([
            'title' => $request->title, 'author' => $request->author,
            'publisher' => $request->publisher, 'year' => $request->year,
            'isbn' => $request->isbn, 'category_id' => $request->category_id,
            'description' => $request->description, 'kelas_tujuan' => $request->kelas_tujuan,
            'file_path' => $filePath, 'file_hash' => hash_file('sha256', $pdfFile->getPathname()),
            'file_size' => $pdfFile->getSize(), 'cover_image' => $coverPath,
            'uploaded_by' => Auth::id(),
        ]);
        ActivityLog::log('upload_ebook', "Upload eBook: {$ebook->title}", Ebook::class, $ebook->id);
        return redirect()->route('admin.ebooks.index')->with('success', 'eBook berhasil diupload!');
    }

    public function edit(Ebook $ebook)
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.ebooks.edit', compact('ebook', 'categories'));
    }

    public function update(Request $request, Ebook $ebook)
    {
        $request->validate([
            'title' => 'required|string|max:255', 'author' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'pdf_file' => 'nullable|file|mimes:pdf|max:51200',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);
        $data = $request->only(['title','author','publisher','year','isbn','category_id','description','kelas_tujuan']);
        if ($request->hasFile('pdf_file')) {
            if ($ebook->file_path) Storage::disk('local')->delete($ebook->file_path);
            $p = $request->file('pdf_file');
            $data['file_path'] = $p->storeAs('ebooks/pdfs', Str::uuid().'.pdf', 'local');
            $data['file_hash'] = hash_file('sha256', $p->getPathname());
            $data['file_size'] = $p->getSize();
        }
        if ($request->hasFile('cover_image')) {
            if ($ebook->cover_image) Storage::disk('public')->delete($ebook->cover_image);
            $c = $request->file('cover_image');
            $data['cover_image'] = $c->storeAs('ebooks/covers', Str::uuid().'.'.$c->getClientOriginalExtension(), 'public');
        }
        $ebook->update($data);
        ActivityLog::log('edit_ebook', "Edit eBook: {$ebook->title}", Ebook::class, $ebook->id);
        return redirect()->route('admin.ebooks.index')->with('success', 'eBook berhasil diperbarui!');
    }

    public function destroy(Ebook $ebook)
    {
        $title = $ebook->title;
        if ($ebook->file_path) Storage::disk('local')->delete($ebook->file_path);
        if ($ebook->cover_image) Storage::disk('public')->delete($ebook->cover_image);
        $ebook->delete();
        ActivityLog::log('delete_ebook', "Hapus eBook: {$title}", null, null, 'warning');
        return redirect()->route('admin.ebooks.index')->with('success', 'eBook berhasil dihapus!');
    }

    public function toggleStatus(Ebook $ebook)
    {
        $ebook->update(['is_active' => !$ebook->is_active]);
        $s = $ebook->is_active ? 'diaktifkan' : 'dinonaktifkan';
        ActivityLog::log('toggle_ebook', "eBook {$ebook->title} {$s}", Ebook::class, $ebook->id);
        return back()->with('success', "eBook berhasil {$s}!");
    }
}
