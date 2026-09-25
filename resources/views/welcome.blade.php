<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PT KIMPAI DYNA TUBE</title>
    
    <!-- Memanggil Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* 1. Pengaturan Latar Belakang (Layar Penuh) */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-image: url('{{ asset("image/bg-gedung.png") }}'); /* Panggil gambar di sini */
            background-size: cover;
            background-position: center;
            height: 100vh; /* Tinggi layar 100% */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* 2. Kotak Utama yang membungkus Kiri & Kanan */
        .kotak-utama {
            display: flex;
            width: 900px;
            max-width: 90%;
            gap: 50px; /* Jarak antara teks dan form */
        }

        /* 3. Pengaturan Teks Sebelah Kiri */
        .teks-kiri {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }
        
        .teks-kiri h1, .teks-kiri h2 {
            color: #735d25; /* Warna emas gelap */
            margin-bottom: 5px;
            font-family: "Times New Roman", Times, serif; /* Font bergaya klasik */
        }
        
        .teks-kiri p {
            color: #555;
            line-height: 1.5;
        }

        /* 4. Pengaturan Form Sebelah Kanan */
        .form-kanan {
            flex: 1;
            background-color: white;
            padding: 40px;
            border-radius: 15px; /* Membuat sudut form melengkung */
            box-shadow: 0 5px 15px rgba(0,0,0,0.1); /* Efek bayangan */
            text-align: center;
        }

        .form-kanan h3 {
            color: #735d25;
            font-family: "Times New Roman", Times, serif;
            font-size: 24px;
            margin-bottom: 30px;
        }

        /* 5. Pengaturan Kolom Isian (Input) */
        .grup-input {
            text-align: left;
            margin-bottom: 20px;
            position: relative;
        }

        .grup-input label {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }

        .grup-input input {
            width: 100%;
            padding: 12px 10px 12px 35px; /* Ruang ekstra di kiri untuk icon */
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .grup-input i {
            position: absolute;
            left: 12px;
            bottom: 12px;
            color: #999;
        }

        /* 6. Pengaturan Opsi Bawah & Tombol */
        .opsi-bawah {
            display: flex;
            justify-content: space-between; /* Pisahkan ke kiri dan kanan */
            font-size: 13px;
            margin-bottom: 20px;
        }

        .opsi-bawah a {
            color: #d4a32a; /* Warna emas */
            text-decoration: none;
            font-weight: bold;
        }

        .tombol-masuk {
            width: 100%;
            padding: 12px;
            background-color: #d4a32a;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer; /* Ubah kursor jadi tangan saat disentuh */
        }

        .tombol-masuk:hover {
            background-color: #b88d22; /* Warna sedikit gelap saat disorot */
        }

        /* 7. Pengaturan untuk Layar HP (Responsive) */
        @media (max-width: 768px) {
            .kotak-utama {
                flex-direction: column; /* Ubah susunan jadi atas-bawah di HP */
            }
        }
    </style>
</head>
<body>

    <div class="kotak-utama">
        
        <!-- Bagian Kiri -->
        <div class="teks-kiri">
            <h1>QualiSync</h1>
            <h2>Selamat Datang!</h2>
            <p>Silakan masuk untuk melanjutkan<br>ke sistem.</p>
            <a href="/login" >Masuk</href>
        </div>
        
        <!-- Bagian Kanan -->
        <!-- <div class="form-kanan">
            <i class="far fa-user-circle" style="font-size: 50px; color: #d4a32a; margin-bottom: 10px;"></i>
            <h3>Login</h3>
            
            <form action="#" method="POST">
                @csrf
                
                <div class="grup-input">
                    <label>User</label>
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="Masukkan username Anda" required>
                </div>
                
                <div class="grup-input">
                    <label>Pass</label>
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Masukkan password Anda" required>
                </div>
                
                <div class="opsi-bawah">
                    <label><input type="checkbox" name="remember"> Ingat saya</label>
                    <a href="#">Lupa password?</a>
                </div>
                
                <button type="submit" class="tombol-masuk">Masuk</button>
            </form>
        </div> -->

    </div>

</body>
</html>