<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $ebook->title }} - Perpus Sandikta Reader</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:#0f172a; overflow:hidden; height:100vh;
            -webkit-user-select:none; -moz-user-select:none; -ms-user-select:none; user-select:none; }
        
        .reader-topbar {
            height:60px; background:rgba(15,23,42,0.9); backdrop-filter:blur(20px);
            border-bottom:1px solid rgba(255,255,255,0.1);
            display:flex; align-items:center; justify-content:space-between;
            padding:0 20px; position:fixed; top:0; left:0; right:0; z-index:100;
        }

        .reader-controls {
            display:flex; align-items:center; gap:15px; color:#fff;
        }

        .btn-reader {
            background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1);
            color:#fff; padding:6px 12px; border-radius:8px; cursor:pointer;
            display:flex; align-items:center; justify-content:center; gap:6px;
            font-size:13px; transition:all 0.2s;
        }

        .btn-reader:hover:not(:disabled) { background:rgba(255,255,255,0.15); border-color:rgba(255,255,255,0.3); }
        .btn-reader:disabled { opacity:0.3; cursor:not-allowed; }

        .page-info { font-size:13px; font-weight:500; min-width:80px; text-align:center; }

        .viewer-container {
            position:absolute; top:60px; left:0; right:0; bottom:0;
            overflow:auto; display:flex; justify-content:center;
            background:#1e293b; padding:20px;
        }

        #pdf-canvas {
            box-shadow:0 20px 25px -5px rgba(0,0,0,0.3), 0 10px 10px -5px rgba(0,0,0,0.2);
            background:#fff; max-width:100%; height:auto;
        }

        /* Loading Spinner */
        .loader {
            position:fixed; top:50%; left:50%; transform:translate(-50%, -50%);
            display:flex; flex-direction:column; align-items:center; gap:15px;
            color:#fff; z-index:150;
        }
        .spinner {
            width:40px; height:40px; border:3px solid rgba(255,255,255,0.1);
            border-top-color:#3b82f6; border-radius:50%;
            animation:spin 1s linear infinite;
        }
        @keyframes spin { to { transform:rotate(360deg); } }

        .watermark-overlay {
            position:fixed; top:60px; left:0; right:0; bottom:0;
            pointer-events:none; z-index:110; overflow:hidden;
            opacity:0.4;
        }
        .watermark-text {
            color:rgba(255,255,255,0.1); font-size:18px; font-weight:700;
            transform:rotate(-30deg); white-space:nowrap; position:absolute;
        }

        @media print {
            body { display:none !important; }
        }
    </style>
</head>
<body oncontextmenu="return false" ondragstart="return false" onselectstart="return false">
    
    <div id="loader" class="loader">
        <div class="spinner"></div>
        <div style="font-size:14px; font-weight:500;">Memuat eBook...</div>
    </div>

    <div class="reader-topbar">
        <div style="display:flex; align-items:center; gap:15px; width:30%;">
            <a href="{{ route('ebooks.show', $ebook) }}" class="btn-reader" style="text-decoration:none;">
                <i class="bi bi-arrow-left"></i> <span>Tutup</span>
            </a>
            <div style="overflow:hidden; white-space:nowrap; text-overflow:ellipsis;">
                <div style="color:#fff; font-weight:600; font-size:14px;">{{ $ebook->title }}</div>
            </div>
        </div>

        <div class="reader-controls">
            <button id="prev-page" class="btn-reader"><i class="bi bi-chevron-left"></i></button>
            <span class="page-info">Halaman <span id="page-num">0</span> / <span id="page-count">0</span></span>
            <button id="next-page" class="btn-reader"><i class="bi bi-chevron-right"></i></button>
            
            <div style="width:1px; height:24px; background:rgba(255,255,255,0.1); margin:0 5px;"></div>
            
            <button id="zoom-out" class="btn-reader"><i class="bi bi-dash-lg"></i></button>
            <span id="zoom-percent" style="font-size:13px; min-width:45px; text-align:center;">100%</span>
            <button id="zoom-in" class="btn-reader"><i class="bi bi-plus-lg"></i></button>
        </div>

        <div style="width:30%; display:flex; justify-content:flex-end;">
            <div style="color:rgba(255,255,255,0.4); font-size:12px; display:flex; align-items:center; gap:8px;">
                <i class="bi bi-shield-lock-fill text-primary"></i>
                <span class="d-none d-sm-inline">Protected Reader Content</span>
            </div>
        </div>
    </div>

    <div class="viewer-container" id="viewer-container">
        <canvas id="pdf-canvas"></canvas>
    </div>

    <!-- Watermark Layer -->
    <div class="watermark-overlay">
        @for($i = 0; $i < 20; $i++)
            <div class="watermark-text" style="top:{{ ($i * 150) - 100 }}px; left:{{ ($i % 4) * 300 - 100 }}px">
                {{ Auth::user()->name }} • {{ Auth::user()->nis ?? Auth::user()->email }} • {{ now()->format('d/m/Y H:i') }}
            </div>
        @endfor
    </div>

    <script>
        // PDF.js worker
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        let pdfDoc = null,
            pageNum = 1,
            pageRendering = false,
            pageNumPending = null,
            scale = 1.5,
            canvas = document.getElementById('pdf-canvas'),
            ctx = canvas.getContext('2d');

        /**
         * Get page info from document, resize canvas accordingly, and render page.
         * @param num Page number.
         */
        function renderPage(num) {
            pageRendering = true;
            // Using promise to fetch the page
            pdfDoc.getPage(num).then(function(page) {
                const viewport = page.getViewport({scale: scale});
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                // Render PDF page into canvas context
                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };
                const renderTask = page.render(renderContext);

                // Wait for rendering to finish
                renderTask.promise.then(function() {
                    pageRendering = false;
                    if (pageNumPending !== null) {
                        // New page rendering is pending
                        renderPage(pageNumPending);
                        pageNumPending = null;
                    }
                });
            });

            // Update page counters
            document.getElementById('page-num').textContent = num;
        }

        /**
         * If another page rendering in progress, waits until the rendering is
         * finised. Otherwise, executes rendering immediately.
         */
        function queueRenderPage(num) {
            if (pageRendering) {
                pageNumPending = num;
            } else {
                renderPage(num);
            }
        }

        /**
         * Displays previous page.
         */
        function onPrevPage() {
            if (pageNum <= 1) {
                return;
            }
            pageNum--;
            queueRenderPage(pageNum);
        }
        document.getElementById('prev-page').addEventListener('click', onPrevPage);

        /**
         * Displays next page.
         */
        function onNextPage() {
            if (pageNum >= pdfDoc.numPages) {
                return;
            }
            pageNum++;
            queueRenderPage(pageNum);
        }
        document.getElementById('next-page').addEventListener('click', onNextPage);

        /**
         * Zoom In
         */
        document.getElementById('zoom-in').addEventListener('click', function() {
            if (scale >= 4.0) return;
            scale += 0.25;
            document.getElementById('zoom-percent').textContent = Math.round(scale * 100) + '%';
            queueRenderPage(pageNum);
        });

        /**
         * Zoom Out
         */
        document.getElementById('zoom-out').addEventListener('click', function() {
            if (scale <= 0.5) return;
            scale -= 0.25;
            document.getElementById('zoom-percent').textContent = Math.round(scale * 100) + '%';
            queueRenderPage(pageNum);
        });

        /**
         * Asynchronously downloads PDF.
         */
        const pdfUrl = '{{ route('pdf.stream', $ebook) }}?token={{ $token }}';
        
        pdfjsLib.getDocument(pdfUrl).promise.then(function(pdfDoc_) {
            pdfDoc = pdfDoc_;
            document.getElementById('page-count').textContent = pdfDoc.numPages;
            document.getElementById('loader').style.display = 'none';

            // Initial/first page rendering
            renderPage(pageNum);
        }).catch(function(error) {
            console.error('Error loading PDF:', error);
            document.getElementById('loader').innerHTML = '<div style="color:#ef4444;text-align:center">Gagal memuat eBook. Sesi mungkin kedaluwarsa.<br><br><a href="" class="btn-reader">Refresh Halaman</a></div>';
        });

        // --- Security measures ---

        // Disable keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey && (e.key === 's' || e.key === 'p' || e.key === 'u')) ||
                (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i' || e.key === 'J' || e.key === 'j' || e.key === 'C' || e.key === 'c')) ||
                e.key === 'F12') {
                e.preventDefault();
                return false;
            }
        });

        // Disable right-click
        document.addEventListener('contextmenu', e => e.preventDefault());

        // Blur on tab switch
        document.addEventListener('visibilitychange', function() {
            const container = document.getElementById('viewer-container');
            if (document.hidden) {
                container.style.filter = 'blur(15px)';
            } else {
                container.style.filter = '';
            }
        });

        // Basic DevTools detection
        let checkCount = 0;
        const checkDevTools = setInterval(function() {
            const threshold = 160;
            if (window.outerWidth - window.innerWidth > threshold || window.outerHeight - window.innerHeight > threshold) {
                checkCount++;
                if(checkCount > 2) {
                    document.body.innerHTML = '<div style="background:#0f172a;height:100vh;display:flex;align-items:center;justify-content:center;color:#fff;text-align:center;padding:20px;"><div><i class="bi bi-shield-slash" style="font-size:4rem;color:#ef4444;"></i><h2 style="margin:20px 0;">Developer Tools Terdeteksi</h2><p>Halaman ini ditutup untuk melindungi konten hak cipta.</p></div></div>';
                    clearInterval(checkDevTools);
                }
            } else {
                checkCount = 0;
            }
        }, 1000);

    </script>
</body>
</html>
