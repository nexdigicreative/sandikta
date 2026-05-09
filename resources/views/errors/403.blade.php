<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family:'Inter',sans-serif; min-height:100vh; background:linear-gradient(135deg,#0f172a,#1e3a8a); display:flex; align-items:center; justify-content:center; margin:0; }
        .error-card { text-align:center; color:#fff; padding:40px; }
        .error-code { font-size:120px; font-weight:800; background:linear-gradient(135deg,#3b82f6,#60a5fa); -webkit-background-clip:text; -webkit-text-fill-color:transparent; line-height:1; }
        .error-title { font-size:24px; font-weight:700; margin:16px 0 8px; }
        .error-msg { color:rgba(255,255,255,0.5); font-size:15px; margin-bottom:32px; }
        .btn-home { display:inline-block; padding:14px 32px; background:linear-gradient(135deg,#3b82f6,#1e40af); border-radius:14px; color:#fff; text-decoration:none; font-weight:700; transition:all .3s; }
        .btn-home:hover { transform:translateY(-2px); box-shadow:0 8px 30px rgba(59,130,246,0.4); }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-code">403</div>
        <div class="error-title">Akses Ditolak</div>
        <p class="error-msg">{{ $exception->getMessage() ?: 'Anda tidak memiliki izin untuk mengakses halaman ini.' }}</p>
        <a href="{{ url('/') }}" class="btn-home">Kembali ke Beranda</a>
    </div>
</body>
</html>
