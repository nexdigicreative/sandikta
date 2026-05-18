<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#0f172a">
    <title>{{ $ebook->title }} - Perpus Sandikta Reader</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family:'Inter',sans-serif; background:#0f172a; overflow:hidden;
            height: 100vh;
            height: -webkit-fill-available;
            height: 100dvh;
            -webkit-user-select:none; -moz-user-select:none; -ms-user-select:none; user-select:none;
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
        }

        /* ===== TOPBAR ===== */
        .reader-topbar {
            height:56px; background:rgba(15,23,42,0.98);
            -webkit-backdrop-filter:blur(20px); backdrop-filter:blur(20px);
            border-bottom:1px solid rgba(255,255,255,0.08);
            display:flex; align-items:center; justify-content:space-between;
            padding:0 12px; position:fixed; top:0; left:0; right:0; z-index:100;
            gap: 8px;
        }

        .topbar-left {
            display:flex; align-items:center; gap:10px; min-width:0; flex:1;
        }

        .topbar-title {
            overflow:hidden; white-space:nowrap; text-overflow:ellipsis;
            color:#fff; font-weight:600; font-size:14px; min-width:0;
        }

        .topbar-right {
            display:flex; align-items:center; gap:6px; flex-shrink:0;
        }

        /* ===== BUTTONS ===== */
        .btn-reader {
            background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1);
            color:#fff; padding:8px 12px; border-radius:10px; cursor:pointer;
            display:inline-flex; align-items:center; justify-content:center; gap:6px;
            font-size:13px; transition:all 0.2s; font-family:inherit;
            -webkit-tap-highlight-color:transparent; touch-action:manipulation;
            text-decoration:none; white-space:nowrap;
        }
        .btn-reader:hover:not(:disabled) { background:rgba(255,255,255,0.15); border-color:rgba(255,255,255,0.25); }
        .btn-reader:active:not(:disabled) { background:rgba(255,255,255,0.2); transform:scale(0.95); }
        .btn-reader:disabled { opacity:0.3; cursor:not-allowed; }
        .btn-reader.btn-icon { padding:8px; min-width:38px; min-height:38px; }

        /* ===== DESKTOP CONTROLS (in topbar) ===== */
        .desktop-controls {
            display:flex; align-items:center; gap:8px; color:#fff;
        }
        .page-info { font-size:13px; font-weight:500; min-width:70px; text-align:center; color:#94a3b8; }
        .divider { width:1px; height:24px; background:rgba(255,255,255,0.1); margin:0 4px; }
        .zoom-label { font-size:12px; min-width:40px; text-align:center; color:#94a3b8; font-weight:500; }
        .protected-badge {
            color:rgba(255,255,255,0.35); font-size:11px; display:flex; align-items:center; gap:6px;
        }

        /* ===== VIEWER ===== */
        .viewer-container {
            position:absolute; top:56px; left:0; right:0; bottom:0;
            overflow:auto; display:flex; justify-content:center; align-items:flex-start;
            background:#1e293b; padding:16px;
            -webkit-overflow-scrolling: touch;
            scroll-behavior: smooth;
        }

        #pdf-canvas {
            box-shadow:0 20px 40px -10px rgba(0,0,0,0.5), 0 8px 16px -6px rgba(0,0,0,0.3);
            background:#fff; display:block;
            border-radius: 2px;
        }

        /* ===== LOADING ===== */
        .loader {
            position:fixed; top:50%; left:50%; transform:translate(-50%, -50%);
            display:flex; flex-direction:column; align-items:center; gap:15px;
            color:#fff; z-index:150; padding:20px;
        }
        .spinner {
            width:40px; height:40px; border:3px solid rgba(255,255,255,0.1);
            border-top-color:#3b82f6; border-radius:50%;
            animation:spin 1s linear infinite;
        }
        @keyframes spin { to { transform:rotate(360deg); } }

        /* ===== WATERMARK ===== */
        .watermark-overlay {
            position:fixed; top:56px; left:0; right:0; bottom:0;
            pointer-events:none; z-index:110; overflow:hidden;
            opacity:0.4;
        }
        .watermark-text {
            color:rgba(255,255,255,0.1); font-size:16px; font-weight:700;
            transform:rotate(-30deg); white-space:nowrap; position:absolute;
        }

        /* ===== MOBILE BOTTOM BAR ===== */
        .mobile-bottombar {
            display:none;
            position:fixed; bottom:0; left:0; right:0; z-index:100;
            background:rgba(15,23,42,0.98);
            -webkit-backdrop-filter:blur(20px); backdrop-filter:blur(20px);
            border-top:1px solid rgba(255,255,255,0.08);
            padding:10px 16px 10px 16px;
            padding-bottom: calc(10px + constant(safe-area-inset-bottom));
            padding-bottom: calc(10px + env(safe-area-inset-bottom));
        }

        .bottombar-main {
            display:flex; align-items:center; justify-content:center; gap:12px;
        }

        .bottombar-prev,
        .bottombar-next {
            flex-shrink:0;
        }

        .bottombar-center {
            display:flex; align-items:center; gap:10px;
            background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.08);
            border-radius:12px; padding:6px 14px;
        }

        .bottombar-page {
            font-size:14px; font-weight:600; color:#e2e8f0;
            min-width:70px; text-align:center;
            letter-spacing:0.5px;
        }

        .bottombar-divider {
            width:1px; height:18px; background:rgba(255,255,255,0.12);
        }

        .bottombar-zoom {
            display:flex; align-items:center; gap:6px;
        }

        .bottombar-zoom .btn-reader.btn-icon {
            min-width:30px; min-height:30px; padding:4px;
            background:transparent; border-color:transparent;
        }
        .bottombar-zoom .btn-reader.btn-icon:active { background:rgba(255,255,255,0.1); }

        .bottombar-zoom .zoom-label {
            font-size:11px; min-width:36px; color:#64748b;
        }

        /* ===== SWIPE HINT ===== */
        .swipe-hint {
            position:fixed; bottom:80px; left:50%; transform:translateX(-50%);
            background:rgba(59,130,246,0.9); color:#fff; padding:8px 16px;
            border-radius:20px; font-size:12px; font-weight:500;
            display:flex; align-items:center; gap:8px;
            opacity:0; transition:opacity 0.3s; z-index:120;
            pointer-events:none;
        }
        .swipe-hint.show { opacity:1; }

        /* ===== PAGE TRANSITION ===== */
        .page-transition {
            transition: opacity 0.15s ease;
        }
        .page-transition.fading {
            opacity: 0.3;
        }

        @media print {
            body { display:none !important; }
        }

        /* ===== RESPONSIVE: TABLET ===== */
        @media (max-width: 768px) {
            .reader-topbar { height:50px; padding:0 10px; }
            .topbar-title { font-size:13px; }
            .desktop-controls .page-info,
            .desktop-controls .divider,
            .desktop-controls #zoom-out,
            .desktop-controls #zoom-in,
            .desktop-controls .zoom-label { display:none; }
            .protected-badge span { display:none; }

            .viewer-container { top:50px; bottom:72px; padding:10px; }
            .watermark-overlay { top:50px; }

            .mobile-bottombar { display:block; }
        }

        /* ===== RESPONSIVE: PHONE ===== */
        @media (max-width: 480px) {
            .reader-topbar { height:46px; padding:0 8px; gap:6px; }
            .topbar-title { font-size:12px; }
            .btn-reader { padding:6px 10px; font-size:12px; border-radius:8px; }
            .btn-reader.btn-icon { padding:6px; min-width:34px; min-height:34px; }
            .btn-reader span.close-text { display:none; }

            .desktop-controls { gap:4px; }
            .desktop-controls .page-info,
            .desktop-controls .divider,
            .desktop-controls #zoom-out,
            .desktop-controls #zoom-in,
            .desktop-controls .zoom-label,
            .desktop-controls #prev-page,
            .desktop-controls #next-page { display:none; }

            .viewer-container { top:46px; bottom:68px; padding:6px; }
            .watermark-overlay { top:46px; }
            .watermark-text { font-size:12px; }

            .mobile-bottombar { padding:8px 10px 8px 10px; padding-bottom: calc(8px + constant(safe-area-inset-bottom)); padding-bottom: calc(8px + env(safe-area-inset-bottom)); }
            .bottombar-center { padding:5px 10px; gap:8px; }
            .bottombar-page { font-size:13px; min-width:60px; }
            .bottombar-main { gap:8px; }
        }
    </style>
</head>
<body oncontextmenu="return false" ondragstart="return false" onselectstart="return false">

    <div id="loader" class="loader">
        <div class="spinner"></div>
        <div style="font-size:14px; font-weight:500;">Memuat eBook...</div>
    </div>

    <!-- ===== TOPBAR ===== -->
    <div class="reader-topbar">
        <div class="topbar-left">
            <a href="{{ route('ebooks.show', $ebook) }}" class="btn-reader" style="text-decoration:none; flex-shrink:0;">
                <i class="bi bi-arrow-left"></i> <span class="close-text">Tutup</span>
            </a>
            <div class="topbar-title">{{ $ebook->title }}</div>
        </div>

        <div class="desktop-controls">
            <button id="prev-page" class="btn-reader btn-icon" title="Halaman Sebelumnya"><i class="bi bi-chevron-left"></i></button>
            <span class="page-info"><span id="page-num">0</span> / <span id="page-count">0</span></span>
            <button id="next-page" class="btn-reader btn-icon" title="Halaman Berikutnya"><i class="bi bi-chevron-right"></i></button>

            <div class="divider"></div>

            <button id="zoom-out" class="btn-reader btn-icon" title="Perkecil"><i class="bi bi-dash-lg"></i></button>
            <span class="zoom-label" id="zoom-percent">100%</span>
            <button id="zoom-in" class="btn-reader btn-icon" title="Perbesar"><i class="bi bi-plus-lg"></i></button>
        </div>

        <div class="topbar-right">
            <div class="protected-badge">
                <i class="bi bi-shield-lock-fill" style="color:#3b82f6;"></i>
                <span>Protected</span>
            </div>
        </div>
    </div>

    <!-- ===== VIEWER ===== -->
    <div class="viewer-container" id="viewer-container">
        <canvas id="pdf-canvas" class="page-transition"></canvas>
    </div>

    <!-- ===== WATERMARK ===== -->
    <div class="watermark-overlay">
        @for($i = 0; $i < 20; $i++)
            <div class="watermark-text" style="top:{{ ($i * 150) - 100 }}px; left:{{ ($i % 4) * 300 - 100 }}px">
                {{ Auth::user()->name }} • {{ Auth::user()->nis ?? Auth::user()->email }} • {{ now()->format('d/m/Y H:i') }}
            </div>
        @endfor
    </div>

    <!-- ===== MOBILE BOTTOM BAR ===== -->
    <div class="mobile-bottombar" id="mobile-bottombar">
        <div class="bottombar-main">
            <button id="mob-prev" class="btn-reader btn-icon bottombar-prev" title="Sebelumnya"><i class="bi bi-chevron-left"></i></button>

            <div class="bottombar-center">
                <div class="bottombar-zoom">
                    <button id="mob-zoom-out" class="btn-reader btn-icon" title="Perkecil"><i class="bi bi-dash-lg"></i></button>
                    <span class="zoom-label" id="mob-zoom-percent">100%</span>
                    <button id="mob-zoom-in" class="btn-reader btn-icon" title="Perbesar"><i class="bi bi-plus-lg"></i></button>
                </div>

                <div class="bottombar-divider"></div>

                <div class="bottombar-page">
                    <span id="mob-page-num">0</span> / <span id="mob-page-count">0</span>
                </div>
            </div>

            <button id="mob-next" class="btn-reader btn-icon bottombar-next" title="Berikutnya"><i class="bi bi-chevron-right"></i></button>
        </div>
    </div>

    <!-- ===== SWIPE HINT ===== -->
    <div class="swipe-hint" id="swipe-hint">
        <i class="bi bi-hand-index"></i> Geser untuk berpindah halaman
    </div>

    <script>
        // PDF.js worker
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        // Cross-browser mobile detection
        function checkMobile() { return window.innerWidth <= 768 || ('ontouchstart' in window && window.innerWidth <= 1024); }
        var isMobile = checkMobile();

        var pdfDoc = null,
            pageNum = 1,
            pageRendering = false,
            pageNumPending = null,
            scale = isMobile ? 1.0 : 1.5,
            canvas = document.getElementById('pdf-canvas'),
            ctx = canvas.getContext('2d'),
            fitWidthScale = null;

        /**
         * Calculate scale to fit page width in viewport
         */
        function calculateFitWidth(page) {
            const container = document.getElementById('viewer-container');
            const containerWidth = container.clientWidth - (isMobile ? 12 : 32);
            const viewport = page.getViewport({ scale: 1.0 });
            return containerWidth / viewport.width;
        }

        /**
         * Render a page with smooth transition
         */
        function renderPage(num) {
            pageRendering = true;
            canvas.classList.add('fading');

            pdfDoc.getPage(num).then(function(page) {
                // Auto fit-width on first load for mobile
                if (fitWidthScale === null && isMobile) {
                    fitWidthScale = calculateFitWidth(page);
                    scale = fitWidthScale;
                }

                var viewport = page.getViewport({ scale: scale });

                // Use devicePixelRatio for sharp rendering (cap at 2 for iOS memory limits)
                var isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
                var dpr = Math.min(window.devicePixelRatio || 1, isIOS ? 2 : 3);
                var maxCanvasArea = isIOS ? 16777216 : 67108864; // 4096x4096 for iOS, 8192x8192 otherwise
                var renderWidth = viewport.width * dpr;
                var renderHeight = viewport.height * dpr;
                if (renderWidth * renderHeight > maxCanvasArea) {
                    var ratio = Math.sqrt(maxCanvasArea / (renderWidth * renderHeight));
                    renderWidth = Math.floor(renderWidth * ratio);
                    renderHeight = Math.floor(renderHeight * ratio);
                }
                canvas.height = renderHeight;
                canvas.width = renderWidth;
                canvas.style.width = viewport.width + 'px';
                canvas.style.height = viewport.height + 'px';
                ctx.setTransform(renderWidth / viewport.width, 0, 0, renderHeight / viewport.height, 0, 0);

                var renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };
                var renderTask = page.render(renderContext);

                renderTask.promise.then(function() {
                    pageRendering = false;
                    canvas.classList.remove('fading');

                    if (pageNumPending !== null) {
                        renderPage(pageNumPending);
                        pageNumPending = null;
                    }
                });
            });

            // Update all page counters
            document.getElementById('page-num').textContent = num;
            if (document.getElementById('mob-page-num')) {
                document.getElementById('mob-page-num').textContent = num;
            }

            // Scroll to top on page change (iOS compatible)
            try { document.getElementById('viewer-container').scrollTo({ top: 0, behavior: 'instant' }); } catch(e) { document.getElementById('viewer-container').scrollTop = 0; }
        }

        function queueRenderPage(num) {
            if (pageRendering) {
                pageNumPending = num;
            } else {
                renderPage(num);
            }
        }

        function onPrevPage() {
            if (pageNum <= 1) return;
            pageNum--;
            queueRenderPage(pageNum);
        }

        function onNextPage() {
            if (!pdfDoc || pageNum >= pdfDoc.numPages) return;
            pageNum++;
            queueRenderPage(pageNum);
        }

        function updateZoomDisplay() {
            var pct = Math.round(scale * 100) + '%';
            document.getElementById('zoom-percent').textContent = pct;
            if (document.getElementById('mob-zoom-percent')) {
                document.getElementById('mob-zoom-percent').textContent = pct;
            }
        }

        function zoomIn() {
            if (scale >= 4.0) return;
            scale += 0.25;
            updateZoomDisplay();
            queueRenderPage(pageNum);
        }

        function zoomOut() {
            if (scale <= 0.5) return;
            scale -= 0.25;
            updateZoomDisplay();
            queueRenderPage(pageNum);
        }

        // Desktop controls
        document.getElementById('prev-page').addEventListener('click', onPrevPage);
        document.getElementById('next-page').addEventListener('click', onNextPage);
        document.getElementById('zoom-in').addEventListener('click', zoomIn);
        document.getElementById('zoom-out').addEventListener('click', zoomOut);

        // Mobile controls
        document.getElementById('mob-prev').addEventListener('click', onPrevPage);
        document.getElementById('mob-next').addEventListener('click', onNextPage);
        document.getElementById('mob-zoom-in').addEventListener('click', zoomIn);
        document.getElementById('mob-zoom-out').addEventListener('click', zoomOut);

        // ===== SWIPE GESTURE =====
        var touchStartX = 0, touchStartY = 0, touchEndX = 0, touchEndY = 0;
        var viewer = document.getElementById('viewer-container');

        viewer.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
            touchStartY = e.changedTouches[0].screenY;
        }, { passive: true });

        viewer.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            touchEndY = e.changedTouches[0].screenY;
            handleSwipe();
        }, { passive: true });

        function handleSwipe() {
            var diffX = touchEndX - touchStartX;
            var diffY = touchEndY - touchStartY;
            var minSwipe = 80;

            // Only trigger if horizontal swipe is dominant
            if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > minSwipe) {
                if (diffX < 0) {
                    onNextPage();
                } else {
                    onPrevPage();
                }
            }
        }

        // ===== KEYBOARD NAVIGATION =====
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') { onPrevPage(); e.preventDefault(); }
            if (e.key === 'ArrowRight') { onNextPage(); e.preventDefault(); }
            if (e.key === '+' || e.key === '=') { zoomIn(); e.preventDefault(); }
            if (e.key === '-') { zoomOut(); e.preventDefault(); }

            // Security: block shortcuts
            if ((e.ctrlKey && (e.key === 's' || e.key === 'p' || e.key === 'u')) ||
                (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i' || e.key === 'J' || e.key === 'j' || e.key === 'C' || e.key === 'c')) ||
                e.key === 'F12') {
                e.preventDefault();
                return false;
            }
        });

        // ===== LOAD PDF =====
        var pdfUrl = '{{ route("pdf.stream", $ebook) }}?token={{ $token }}';

        var loadRetries = 0;
        function loadPdf() {
            pdfjsLib.getDocument({ url: pdfUrl }).promise.then(function(pdfDoc_) {
                pdfDoc = pdfDoc_;
                var totalPages = pdfDoc.numPages;

                document.getElementById('page-count').textContent = totalPages;
                if (document.getElementById('mob-page-count')) {
                    document.getElementById('mob-page-count').textContent = totalPages;
                }
                document.getElementById('loader').style.display = 'none';

                updateZoomDisplay();
                renderPage(pageNum);

                if (isMobile && totalPages > 1) {
                    var hint = document.getElementById('swipe-hint');
                    hint.classList.add('show');
                    setTimeout(function() { hint.classList.remove('show'); }, 3000);
                }
            }).catch(function(error) {
                console.error('Error loading PDF:', error);
                if (loadRetries < 2) {
                    loadRetries++;
                    setTimeout(loadPdf, 1500);
                    return;
                }
                document.getElementById('loader').innerHTML =
                    '<div style="color:#ef4444;text-align:center;padding:20px;">' +
                    '<i class="bi bi-exclamation-triangle" style="font-size:3rem;display:block;margin-bottom:15px;"></i>' +
                    '<div style="font-size:16px;font-weight:600;margin-bottom:8px;">Gagal memuat eBook</div>' +
                    '<div style="font-size:13px;color:#94a3b8;margin-bottom:20px;">Sesi mungkin kadaluwarsa atau file PDF tidak ditemukan di server.</div>' +
                    '<a href="" class="btn-reader" style="display:inline-flex;margin:0 auto;">' +
                    '<i class="bi bi-arrow-clockwise"></i> Muat Ulang</a></div>';
            });
        }
        loadPdf();

        // ===== HANDLE RESIZE (all browsers) =====
        var resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                isMobile = checkMobile();
                if (pdfDoc) {
                    pdfDoc.getPage(pageNum).then(function(page) {
                        if (isMobile) {
                            fitWidthScale = calculateFitWidth(page);
                            scale = fitWidthScale;
                            updateZoomDisplay();
                        }
                        renderPage(pageNum);
                    });
                }
            }, 300);
        });

        // Handle orientation change for mobile browsers
        window.addEventListener('orientationchange', function() {
            setTimeout(function() {
                isMobile = checkMobile();
                if (pdfDoc) {
                    fitWidthScale = null;
                    pdfDoc.getPage(pageNum).then(function(page) {
                        fitWidthScale = calculateFitWidth(page);
                        scale = fitWidthScale;
                        updateZoomDisplay();
                        renderPage(pageNum);
                    });
                }
            }, 500);
        });

        // ===== SECURITY MEASURES =====

        // Disable right-click
        document.addEventListener('contextmenu', e => e.preventDefault());

        // Blur on tab switch
        document.addEventListener('visibilitychange', function() {
            var container = document.getElementById('viewer-container');
            if (document.hidden) {
                container.style.filter = 'blur(15px)';
            } else {
                container.style.filter = '';
            }
        });

        // Basic DevTools detection (skip on iOS/mobile to avoid false positives)
        var isIOSDevice = /iPad|iPhone|iPod/.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
        if (!isIOSDevice && window.innerWidth > 1024) {
            var checkCount = 0;
            var checkDevTools = setInterval(function() {
                var threshold = 160;
                if (window.outerWidth - window.innerWidth > threshold || window.outerHeight - window.innerHeight > threshold) {
                    checkCount++;
                    if(checkCount > 3) {
                        document.body.innerHTML = '<div style="background:#0f172a;height:100vh;display:flex;align-items:center;justify-content:center;color:#fff;text-align:center;padding:20px;"><div><i class="bi bi-shield-slash" style="font-size:4rem;color:#ef4444;"></i><h2 style="margin:20px 0;">Developer Tools Terdeteksi</h2><p>Halaman ini ditutup untuk melindungi konten hak cipta.</p></div></div>';
                        clearInterval(checkDevTools);
                    }
                } else {
                    checkCount = 0;
                }
            }, 1000);
        }
    </script>
</body>
</html>
