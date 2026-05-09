<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('ebooks')->latest()->paginate(20);
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);
        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);
        ActivityLog::log('create_category', "Tambah kategori: {$category->name}", Category::class, $category->id);
        return back()->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'.$category->id,
            'description' => 'nullable|string',
        ]);
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);
        ActivityLog::log('edit_category', "Edit kategori: {$category->name}", Category::class, $category->id);
        return back()->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroy(Category $category)
    {
        if ($category->ebooks()->count() > 0) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena masih memiliki eBook.');
        }
        $name = $category->name;
        $category->delete();
        ActivityLog::log('delete_category', "Hapus kategori: {$name}", null, null, 'warning');
        return back()->with('success', 'Kategori berhasil dihapus!');
    }
}
