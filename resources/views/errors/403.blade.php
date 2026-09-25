<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Dilarang | PT KIMPAI DYNA TUBE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            margin: 0; padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f1f5f9; color: #1e293b;
            display: flex; align-items: center; justify-content: center;
            height: 100vh; text-align: center;
        }
        .error-container {
            background: #ffffff; padding: 50px 40px; border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05); max-width: 450px; width: 100%;
            border: 1px solid #e2e8f0;
        }
        .error-icon {
            font-size: 64px; color: #ef4444; margin-bottom: 20px;
        }
        h1 { font-size: 72px; margin: 0; color: #0f172a; font-weight: 800; line-height: 1; }
        h3 { font-size: 20px; margin: 15px 0 10px 0; color: #334155; }
        p { color: #64748b; font-size: 14px; margin-bottom: 30px; line-height: 1.5; }
        .btn-home {
            display: inline-flex; align-items: center; gap: 10px;
            background: #ef4444; color: white; padding: 12px 24px;
            border-radius: 8px; text-decoration: none; font-weight: 600;
            font-size: 14px; transition: background 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
        }
        .btn-home:hover { background: #dc2626; }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-shield-halved"></i>
        </div>
        <h1>403</h1>
        <h3>Akses Dilarang (Forbidden)</h3>
        <p>Maaf, jabatan atau hak akses akun Anda tidak diizinkan untuk membuka halaman atau modul departemen ini.</p>
        <a href="{{ url('/home') }}" class="btn-home">
            <i class="fas fa-home"></i> Kembali ke Portal Utama
        </a>
    </div>
</body>
</html>