@extends('layouts.app')
@section('title', 'Upload eBook - Perpus Sandikta')
@section('page-title', 'Upload eBook')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card-modern">
            <div class="card-header"><h6><i class="bi bi-cloud-arrow-up me-2 text-primary"></i>Upload eBook Baru</h6></div>
            <div class="card-body" style="padding:32px">
                <form method="POST" action="{{ route('admin.ebooks.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label" style="font-weight:600;font-size:13px">Judul eBook <span class="text-danger">*</span></label>
                            <input type="text" name="title" value="{{ old('title') }}" class="form-control form-control-modern @error('title') is-invalid @enderror" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight:600;font-size:13px">Tahun</label>
                            <input type="number" name="year" value="{{ old('year', date('Y')) }}" class="form-control form-control-modern" min="1900" max="{{ date('Y')+1 }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight:600;font-size:13px">Penulis <span class="text-danger">*</span></label>
                            <input type="text" name="author" value="{{ old('author') }}" class="form-control form-control-modern @error('author') is-invalid @enderror" required>
                            @error('author')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight:600;font-size:13px">Penerbit</label>
                            <input type="text" name="publisher" value="{{ old('publisher') }}" class="form-control form-control-modern">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight:600;font-size:13px">Kategori <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-control form-control-modern @error('category_id') is-invalid @enderror" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id')==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight:600;font-size:13px">Kelas Tujuan</label>
                            <input type="text" name="kelas_tujuan" value="{{ old('kelas_tujuan') }}" class="form-control form-control-modern" placeholder="cth: X, XI, XII">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight:600;font-size:13px">ISBN</label>
                            <input type="text" name="isbn" value="{{ old('isbn') }}" class="form-control form-control-modern">
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-weight:600;font-size:13px">Deskripsi</label>
                            <textarea name="description" class="form-control form-control-modern" rows="3">{{ old('description') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight:600;font-size:13px">File PDF <span class="text-danger">*</span></label>
                            <input type="file" name="pdf_file" class="form-control form-control-modern @error('pdf_file') is-invalid @enderror" accept=".pdf" required>
                            <small class="text-muted">Maks. 50MB, format PDF</small>
                            @error('pdf_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight:600;font-size:13px">Cover (Opsional)</label>
                            <input type="file" name="cover_image" class="form-control form-control-modern @error('cover_image') is-invalid @enderror" accept="image/*">
                            <small class="text-muted">Maks. 2MB, format JPG/PNG/WEBP</small>
                            @error('cover_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <a href="{{ route('admin.ebooks.index') }}" class="btn btn-outline-modern">Batal</a>
                        <button type="submit" class="btn btn-primary-modern"><i class="bi bi-cloud-arrow-up me-1"></i>Upload eBook</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
