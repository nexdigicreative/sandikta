@extends('layouts.app')
@section('title', 'Bulk Upload eBook - Perpus Sandikta')
@section('page-title', 'Bulk Upload eBook')

@section('content')
<div class="mb-4 d-flex justify-content-start">
    <a href="{{ route('admin.ebooks.index') }}" class="btn btn-outline-modern btn-sm px-3 py-2" style="border-radius: 12px; font-weight: 600;">
        <i class="bi bi-arrow-left me-1"></i> Kembali / Batal
    </a>
</div>

<div class="row">
    <!-- Left Column: Dropzone & Defaults -->
    <div class="col-lg-4 mb-4">
        <!-- Default Batch Settings -->
        <div class="card-modern mb-4">
            <div class="card-header">
                <h6><i class="bi bi-sliders me-2 text-primary"></i>Pengaturan Batch Default</h6>
            </div>
            <div class="card-body">
                <p class="text-muted" style="font-size:12px">Nilai di bawah ini akan otomatis diterapkan ke semua file yang Anda masukkan ke antrean.</p>
                
                <div class="mb-3">
                    <label class="form-label" style="font-weight:600;font-size:13px">Kategori <span class="text-danger">*</span></label>
                    <select id="default-category" class="form-control form-control-modern">
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label" style="font-weight:600;font-size:13px">Penulis Default</label>
                    <input type="text" id="default-author" class="form-control form-control-modern" value="Perpustakaan Sandikta" placeholder="Nama penulis/sumber">
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight:600;font-size:13px">Kelas Tujuan Default</label>
                    <input type="text" id="default-kelas" class="form-control form-control-modern" placeholder="cth: X, XII, 10 tkj, 7 c, atau Umum" value="Umum">
                    <small class="text-muted" style="font-size:10px;display:block;margin-top:2px">Isi "Umum" agar bisa dibaca semua siswa.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight:600;font-size:13px">Penerbit Default</label>
                    <input type="text" id="default-publisher" class="form-control form-control-modern" placeholder="Nama penerbit (opsional)">
                </div>

                <div class="row">
                    <div class="col-6">
                        <label class="form-label" style="font-weight:600;font-size:13px">Tahun</label>
                        <input type="number" id="default-year" class="form-control form-control-modern" value="{{ date('Y') }}" min="1900" max="{{ date('Y')+1 }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label" style="font-weight:600;font-size:13px">Auto-Clean Nama</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="auto-clean-title" checked>
                            <label class="form-check-label" for="auto-clean-title" style="font-size:12px">Hapus angka depan (cth: "1719. ")</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Drag & Drop Zone -->
        <div class="card-modern">
            <div class="card-body p-2">
                <div id="dropzone" class="dropzone">
                    <i class="bi bi-cloud-arrow-up text-primary" style="font-size: 50px;"></i>
                    <h6 class="mt-3 font-weight-bold">Seret File PDF ke Sini</h6>
                    <p class="text-muted mb-3" style="font-size: 12px;">atau klik untuk memilih file dari komputer</p>
                    <input type="file" id="file-input" multiple accept=".pdf" style="display: none;">
                    <button type="button" class="btn btn-outline-modern btn-sm px-3" onclick="document.getElementById('file-input').click()"><i class="bi bi-folder2-open me-1"></i>Pilih File</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Queue List -->
    <div class="col-lg-8">
        <div class="card-modern">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6><i class="bi bi-list-task me-2 text-primary"></i>Antrean Upload (<span id="queue-count">0</span> File)</h6>
                <div class="d-flex gap-2">
                    <button id="btn-clear" class="btn btn-sm btn-outline-danger" disabled onclick="clearQueue()"><i class="bi bi-trash me-1"></i>Bersihkan</button>
                    <button id="btn-upload" class="btn btn-sm btn-primary-modern" disabled onclick="startUpload()"><i class="bi bi-cloud-upload-fill me-1"></i>Upload Semua</button>
                </div>
            </div>
            <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;">
                <div id="empty-state" class="text-center py-5">
                    <i class="bi bi-file-earmark-pdf text-muted" style="font-size: 60px;"></i>
                    <h6 class="mt-3 text-muted">Belum Ada File di Antrean</h6>
                    <p class="text-muted px-4" style="font-size: 13px; max-width: 400px; margin: 0 auto;">Seret beberapa file PDF ke kotak dropzone di sebelah kiri atau klik tombol "Pilih File" untuk memulai.</p>
                </div>

                <div class="table-responsive d-none" id="queue-table-container">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th style="width: 5%; text-align: center;">#</th>
                                <th style="width: 10%; text-align: center;">Cover</th>
                                <th style="width: 30%;">Info eBook (Judul & Ukuran)</th>
                                <th style="width: 20%;">Kategori</th>
                                <th style="width: 20%;">Metadata (Penulis, Kelas, ISBN)</th>
                                <th style="width: 15%; text-align: center;">Status</th>
                            </tr>
                        </thead>
                        <tbody id="queue-list">
                            <!-- JS will inject rows here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .dropzone {
        border: 3px dashed #3b82f6;
        border-radius: var(--radius);
        background: rgba(59, 130, 246, 0.02);
        padding: 50px 20px;
        text-align: center;
        cursor: pointer;
        transition: var(--transition);
    }
    .dropzone:hover, .dropzone.dragover {
        background: rgba(59, 130, 246, 0.08);
        border-color: #1e40af;
    }
    .dropzone i {
        transition: transform 0.3s ease;
    }
    .dropzone:hover i {
        transform: translateY(-5px);
    }
    .queue-row {
        transition: background-color 0.2s ease;
    }
    .queue-row.uploading {
        background-color: rgba(59, 130, 246, 0.05);
    }
    .queue-row.success-row {
        background-color: rgba(16, 185, 129, 0.03);
    }
    .queue-row.error-row {
        background-color: rgba(239, 68, 68, 0.03);
    }
    .progress-tiny {
        height: 6px;
        border-radius: 3px;
        background-color: #e2e8f0;
        overflow: hidden;
        margin-top: 5px;
    }
    .progress-tiny-bar {
        height: 100%;
        background-color: #3b82f6;
        width: 0%;
        transition: width 0.3s ease;
    }
    .bulk-cover-preview {
        width: 44px;
        height: 60px;
        border-radius: 6px;
        overflow: hidden;
        position: relative;
        cursor: pointer;
        border: 1px solid #cbd5e1;
        margin: 0 auto;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .bulk-cover-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .bulk-cover-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    .bulk-cover-preview:hover .bulk-cover-overlay {
        opacity: 1;
    }
    .bulk-cover-placeholder {
        width: 44px;
        height: 60px;
        border-radius: 6px;
        border: 2px dashed #cbd5e1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #94a3b8;
        background: #f8fafc;
        transition: all 0.2s ease;
        margin: 0 auto;
    }
    .bulk-cover-placeholder:hover {
        border-color: #3b82f6;
        color: #3b82f6;
        background: rgba(59, 130, 246, 0.02);
    }
    .bulk-cover-placeholder i {
        font-size: 14px;
        color: #94a3b8;
    }
    .bulk-cover-placeholder span {
        font-size: 8px;
        font-weight: 600;
        text-transform: uppercase;
        margin-top: 1px;
        color: #94a3b8;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
<script>
    // Configure PDF.js worker
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';

    let queue = [];
    let categoriesList = @json($categories);
    let isUploading = false;
    let currentUploadIndex = 0;

    // Drag and drop event listeners
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('file-input');

    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropzone.classList.add('dragover');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropzone.classList.remove('dragover');
        }, false);
    });

    dropzone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    });

    fileInput.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });

    function cleanFilename(filename) {
        // Remove .pdf extension
        let name = filename.replace(/\.[^/.]+$/, "");
        
        const autoClean = document.getElementById('auto-clean-title').checked;
        if (autoClean) {
            // Remove numeric prefixes like "1719. ", "01 - ", "1. ", etc.
            name = name.replace(/^[\d\s.\-_]+/, "");
        }
        
        // Replace multiple spaces, underscores, or dashes with single space
        name = name.replace(/[\s\-_]+/g, " ");
        
        // Capitalize first letters of words
        name = name.toLowerCase().split(' ').map(function(word) {
            return word.charAt(0).toUpperCase() + word.slice(1);
        }).join(' ');

        return name.trim();
    }

    function formatBytes(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    async function extractPdfCover(file) {
        try {
            const arrayBuffer = await file.arrayBuffer();
            const loadingTask = pdfjsLib.getDocument({ data: arrayBuffer });
            const pdf = await loadingTask.promise;
            const page = await pdf.getPage(1);
            
            const viewport = page.getViewport({ scale: 1.0 });
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;
            
            const renderContext = {
                canvasContext: context,
                viewport: viewport
            };
            
            await page.render(renderContext).promise;
            
            return new Promise((resolve) => {
                canvas.toBlob((blob) => {
                    resolve(blob);
                }, 'image/jpeg', 0.85);
            });
        } catch (e) {
            console.error("Gagal mengekstrak cover PDF:", e);
            return null;
        }
    }

    function handleFiles(files) {
        if (isUploading) {
            Swal.fire({
                icon: 'warning',
                title: 'Sedang Mengunggah',
                text: 'Harap tunggu hingga proses unggah selesai sebelum menambah file baru.',
                confirmButtonColor: '#3b82f6'
            });
            return;
        }

        const defaultCategoryId = document.getElementById('default-category').value;
        const defaultAuthor = document.getElementById('default-author').value;
        const defaultKelas = document.getElementById('default-kelas').value;
        const defaultPublisher = document.getElementById('default-publisher').value;
        const defaultYear = document.getElementById('default-year').value;

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            if (file.type !== "application/pdf") {
                continue; // Skip non-pdf files
            }

            const cleanTitle = cleanFilename(file.name);
            
            const newItem = {
                file: file,
                title: cleanTitle,
                category_id: defaultCategoryId,
                author: defaultAuthor,
                kelas_tujuan: defaultKelas,
                publisher: defaultPublisher,
                year: defaultYear,
                status: 'pending', // pending, uploading, success, error
                errorMsg: '',
                progress: 0,
                isbn: '',
                coverFile: null,
                isGeneratingCover: true
            };
            
            queue.push(newItem);
            
            // Extract cover page 1 asynchronously
            extractPdfCover(file).then(coverBlob => {
                if (coverBlob) {
                    const coverFileName = file.name.substring(0, file.name.lastIndexOf('.')) + '_cover.jpg';
                    newItem.coverFile = new File([coverBlob], coverFileName, { type: 'image/jpeg' });
                }
                newItem.isGeneratingCover = false;
                updateQueueUI();
            });
        }

        updateQueueUI();
    }

    function updateQueueUI() {
        const listBody = document.getElementById('queue-list');
        const emptyState = document.getElementById('empty-state');
        const tableContainer = document.getElementById('queue-table-container');
        const queueCountLabel = document.getElementById('queue-count');
        const btnUpload = document.getElementById('btn-upload');
        const btnClear = document.getElementById('btn-clear');

        queueCountLabel.textContent = queue.length;

        if (queue.length === 0) {
            emptyState.classList.remove('d-none');
            tableContainer.classList.add('d-none');
            btnUpload.disabled = true;
            btnClear.disabled = true;
            return;
        }

        emptyState.classList.add('d-none');
        tableContainer.classList.remove('d-none');
        btnUpload.disabled = isUploading;
        btnClear.disabled = isUploading;

        listBody.innerHTML = '';

        queue.forEach((item, index) => {
            const row = document.createElement('tr');
            row.id = `row-${index}`;
            row.className = `queue-row ${item.status}-row`;
            if (item.status === 'uploading') row.classList.add('uploading');

            // Category options builder
            let categoryOptions = '';
            categoriesList.forEach(cat => {
                const selected = item.category_id == cat.id ? 'selected' : '';
                categoryOptions += `<option value="${cat.id}" ${selected}>${cat.name}</option>`;
            });

            // Status Badge
            let statusHtml = '';
            if (item.status === 'pending') {
                statusHtml = `<span class="badge-modern badge-warning">Menunggu</span>`;
            } else if (item.status === 'uploading') {
                statusHtml = `
                    <span class="badge-modern badge-info animate-pulse">Mengunggah...</span>
                    <div class="progress-tiny">
                        <div id="progress-${index}" class="progress-tiny-bar" style="width: ${item.progress}%"></div>
                    </div>
                `;
            } else if (item.status === 'success') {
                statusHtml = `<span class="badge-modern badge-success"><i class="bi bi-check-circle-fill me-1"></i>Sukses</span>`;
            } else if (item.status === 'error') {
                statusHtml = `
                    <span class="badge-modern badge-danger" title="${item.errorMsg}"><i class="bi bi-exclamation-circle-fill me-1"></i>Gagal</span>
                    <small class="d-block text-danger mt-1" style="font-size:10px">${item.errorMsg}</small>
                `;
            }

            const isEditable = item.status === 'pending';
            const disabledAttr = isEditable ? '' : 'disabled';

            // Build Cover Box HTML
            let coverHtml = '';
            if (item.isGeneratingCover) {
                coverHtml = `
                    <div class="bulk-cover-placeholder">
                        <div class="spinner-border spinner-border-sm text-primary mb-1" role="status" style="width: 14px; height: 14px;"></div>
                        <span style="font-size: 7px; text-transform: none;">Memproses</span>
                    </div>
                `;
            } else if (item.coverFile) {
                const coverUrl = URL.createObjectURL(item.coverFile);
                coverHtml = `
                    <div class="bulk-cover-preview" onclick="${isEditable ? `triggerCoverInput(${index})` : ''}">
                        <img src="${coverUrl}" class="bulk-cover-img">
                        ${isEditable ? '<div class="bulk-cover-overlay"><i class="bi bi-pencil-fill text-white"></i></div>' : ''}
                    </div>
                `;
            } else {
                coverHtml = `
                    <div class="bulk-cover-placeholder" onclick="${isEditable ? `triggerCoverInput(${index})` : ''}">
                        <i class="bi bi-image"></i>
                        <span>Cover</span>
                    </div>
                `;
            }

            row.innerHTML = `
                <td class="text-center align-middle"><strong>${index + 1}</strong></td>
                <td class="text-center align-middle">
                    ${coverHtml}
                    <input type="file" id="cover-input-${index}" accept="image/*" style="display:none" onchange="handleCoverFile(${index}, this)">
                </td>
                <td>
                    <input type="text" class="form-control form-control-modern form-control-sm mb-1 fw-bold" value="${item.title}" ${disabledAttr} onchange="updateQueueItem(${index}, 'title', this.value)">
                    <small class="text-muted"><i class="bi bi-file-pdf text-danger me-1"></i>${formatBytes(item.file.size)}</small>
                </td>
                <td>
                    <select class="form-control form-control-modern form-control-sm" ${disabledAttr} onchange="updateQueueItem(${index}, 'category_id', this.value)">
                        ${categoryOptions}
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control form-control-modern form-control-sm mb-1" value="${item.author}" placeholder="Penulis" ${disabledAttr} onchange="updateQueueItem(${index}, 'author', this.value)">
                    <div class="row g-1">
                        <div class="col-6">
                            <input type="text" class="form-control form-control-modern form-control-sm" value="${item.kelas_tujuan}" placeholder="Kelas" ${disabledAttr} onchange="updateQueueItem(${index}, 'kelas_tujuan', this.value)">
                        </div>
                        <div class="col-6">
                            <input type="text" class="form-control form-control-modern form-control-sm" value="${item.isbn || ''}" placeholder="ISBN" ${disabledAttr} onchange="updateQueueItem(${index}, 'isbn', this.value)">
                        </div>
                    </div>
                </td>
                <td class="text-center align-middle">${statusHtml}</td>
            `;

            listBody.appendChild(row);
        });
    }

    function updateQueueItem(index, field, value) {
        if (queue[index]) {
            queue[index][field] = value;
        }
    }

    function triggerCoverInput(index) {
        const input = document.getElementById(`cover-input-${index}`);
        if (input) input.click();
    }

    function handleCoverFile(index, input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            if (!file.type.startsWith('image/')) {
                Swal.fire({
                    icon: 'error',
                    title: 'Bukan Gambar',
                    text: 'Silakan pilih berkas gambar yang valid (JPG/PNG/WEBP).',
                    confirmButtonColor: '#3b82f6'
                });
                return;
            }
            queue[index].coverFile = file;
            updateQueueUI();
        }
    }

    function clearQueue() {
        if (isUploading) return;
        queue = [];
        updateQueueUI();
    }

    function startUpload() {
        if (queue.length === 0 || isUploading) return;
        
        // Filter out already uploaded files
        const pendingItems = queue.filter(item => item.status === 'pending');
        if (pendingItems.length === 0) {
            Swal.fire({
                icon: 'info',
                title: 'Antrean Bersih',
                text: 'Semua file di antrean sudah berhasil diunggah.',
                confirmButtonColor: '#3b82f6'
            });
            return;
        }

        isUploading = true;
        currentUploadIndex = 0;
        
        // Lock controls
        document.getElementById('btn-upload').disabled = true;
        document.getElementById('btn-clear').disabled = true;
        
        uploadNext();
    }

    function uploadNext() {
        // Find next pending item index
        let nextIndex = -1;
        for (let i = 0; i < queue.length; i++) {
            if (queue[i].status === 'pending') {
                nextIndex = i;
                break;
            }
        }

        if (nextIndex === -1) {
            // Finished uploading everything!
            isUploading = false;
            document.getElementById('btn-upload').disabled = false;
            document.getElementById('btn-clear').disabled = false;
            
            const successCount = queue.filter(item => item.status === 'success').length;
            const errorCount = queue.filter(item => item.status === 'error').length;

            Swal.fire({
                icon: errorCount > 0 ? 'warning' : 'success',
                title: 'Proses Selesai',
                text: `Selesai memproses ${queue.length} file. ${successCount} Berhasil, ${errorCount} Gagal.`,
                confirmButtonColor: '#3b82f6'
            }).then(() => {
                updateQueueUI();
            });
            return;
        }

        const item = queue[nextIndex];
        item.status = 'uploading';
        updateQueueUI();

        const formData = new FormData();
        formData.append('pdf_file', item.file);
        formData.append('title', item.title);
        formData.append('category_id', item.category_id);
        formData.append('author', item.author);
        formData.append('kelas_tujuan', item.kelas_tujuan);
        formData.append('publisher', item.publisher);
        formData.append('year', item.year);
        if (item.isbn) {
            formData.append('isbn', item.isbn);
        }
        if (item.coverFile) {
            formData.append('cover_image', item.coverFile);
        }

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '{{ route("admin.ebooks.bulk.store") }}', true);
        xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        // Progress listener
        xhr.upload.onprogress = function(e) {
            if (e.lengthComputable) {
                const percent = Math.round((e.loaded / e.total) * 100);
                item.progress = percent;
                const progressBar = document.getElementById(`progress-${nextIndex}`);
                if (progressBar) {
                    progressBar.style.width = percent + '%';
                }
            }
        };

        xhr.onload = function() {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    item.status = 'success';
                } else {
                    item.status = 'error';
                    item.errorMsg = response.message || 'Gagal mengunggah';
                }
            } else {
                try {
                    const response = JSON.parse(xhr.responseText);
                    item.status = 'error';
                    if (response.errors && response.errors.title) {
                        item.errorMsg = response.errors.title[0];
                    } else if (response.errors && response.errors.pdf_file) {
                        item.errorMsg = response.errors.pdf_file[0];
                    } else {
                        item.errorMsg = response.message || 'Kesalahan server';
                    }
                } catch(e) {
                    item.status = 'error';
                    item.errorMsg = `Gagal (Error Code: ${xhr.status})`;
                }
            }
            updateQueueUI();
            // Upload next file in the list
            setTimeout(uploadNext, 300);
        };

        xhr.onerror = function() {
            item.status = 'error';
            item.errorMsg = 'Kesalahan koneksi jaringan';
            updateQueueUI();
            setTimeout(uploadNext, 300);
        };

        xhr.send(formData);
    }
</script>
@endpush
