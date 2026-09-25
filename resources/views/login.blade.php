<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - QualiSync</title>
    
    <!-- Memanggil Icon FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url("{{ asset('image/bg-gedung.png') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 1;
        }

        .login-card {
            background: #ffffff;
            width: 100%;
            max-width: 400px;
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 0 15px 25px rgba(0,0,0,0.2);
            z-index: 2; 
            border-top: 5px solid #d4a32a;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
            color: #735d25;
            font-family: "Times New Roman", Times, serif;
            margin: 0;
            font-size: 24px;
            letter-spacing: 1px;
        }

        .login-header p {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group i {
            position: absolute;
            top: 14px;
            left: 15px;
            color: #aaa;
        }

        .form-group input {
            width: 100%;
            padding: 12px 12px 12px 40px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
            transition: 0.3s;
        }

        .form-group input:focus {
            border-color: #d4a32a;
            outline: none;
            box-shadow: 0 0 5px rgba(212, 163, 42, 0.3);
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background-color: #d4a32a;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn-login:hover {
            background-color: #b88d22;
        }

        .footer-text {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #888;
        }

        /* Tambahan styling untuk pesan error login */
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 13px;
            text-align: center;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>

    <!-- Lapisan Gelap Background -->
    <div class="overlay"></div>

    <!-- Kotak Login -->
    <div class="login-card">
        <div class="login-header">
            <h1>QualiSync</h1>
            <p>Portal</p>
        </div>

        <!-- Menampilkan pesan error jika login gagal -->
        @if($errors->any())
            <div class="alert-danger">
                <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
            </div>
        @endif

        <!-- ACTION SUDAH DIPERBAIKI KE /login -->
        <form action="{{ url('/login') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <i class="fas fa-envelope"></i>
                <!-- NAME DIPERBAIKI MENJADI email -->
                <input type="email" name="email" placeholder="Email Anda (contoh: qcline@gmail.com)" required autocomplete="off">
            </div>

            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit" class="btn-login">Masuk <i class="fas fa-sign-in-alt" style="margin-left: 5px;"></i></button>
        </form>

        <div class="footer-text">
            &copy; 2026 PT Kimpai Dyna Tube. All rights reserved.
        </div>
    </div>

</body>
</html>