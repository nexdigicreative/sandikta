@extends('layouts.app')
@section('title', 'Edit eBook - Perpus Sandikta')
@section('page-title', 'Edit eBook')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card-modern">
            <div class="card-header"><h6><i class="bi bi-pencil-square me-2 text-primary"></i>Edit: {{ $ebook->title }}</h6></div>
            <div class="card-body" style="padding:32px">
                <form method="POST" action="{{ route('admin.ebooks.update', $ebook) }}" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label" style="font-weight:600;font-size:13px">Judul eBook</label>
                            <input type="text" name="title" value="{{ old('title', $ebook->title) }}" class="form-control form-control-modern" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight:600;font-size:13px">Tahun</label>
                            <input type="number" name="year" value="{{ old('year', $ebook->year) }}" class="form-control form-control-modern">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight:600;font-size:13px">Penulis</label>
                            <input type="text" name="author" value="{{ old('author', $ebook->author) }}" class="form-control form-control-modern" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight:600;font-size:13px">Penerbit</label>
                            <input type="text" name="publisher" value="{{ old('publisher', $ebook->publisher) }}" class="form-control form-control-modern">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight:600;font-size:13px">Kategori</label>
                            <select name="category_id" class="form-control form-control-modern" required>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $ebook->category_id)==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight:600;font-size:13px">Kelas Tujuan</label>
                            <input type="text" name="kelas_tujuan" value="{{ old('kelas_tujuan', $ebook->kelas_tujuan) }}" class="form-control form-control-modern" placeholder="cth: X, XII, 10 tkj, 7 c, atau Umum">
                            <small class="text-muted" style="font-size:10px;display:block;margin-top:2px">Ketik "Umum" agar tampil untuk semua kelas.</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight:600;font-size:13px">ISBN</label>
                            <input type="text" name="isbn" value="{{ old('isbn', $ebook->isbn) }}" class="form-control form-control-modern">
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-weight:600;font-size:13px">Deskripsi</label>
                            <textarea name="description" class="form-control form-control-modern" rows="3">{{ old('description', $ebook->description) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight:600;font-size:13px">Ganti File PDF</label>
                            <input type="file" name="pdf_file" class="form-control form-control-modern" accept=".pdf">
                            <small class="text-muted">Kosongkan jika tidak ingin mengganti. Saat ini: {{ $ebook->formatted_file_size }}</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight:600;font-size:13px">Ganti Cover</label>
                            <input type="file" name="cover_image" class="form-control form-control-modern" accept="image/*">
                            <small class="text-muted">Kosongkan jika tidak ingin mengganti</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <a href="{{ route('admin.ebooks.index') }}" class="btn btn-outline-modern">Batal</a>
                        <button type="submit" class="btn btn-primary-modern"><i class="bi bi-check-lg me-1"></i>Perbarui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
