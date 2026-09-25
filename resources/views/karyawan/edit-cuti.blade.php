<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengajuan Cuti</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 30px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; }
        .form-control { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        .btn { padding: 10px 15px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn-secondary { background: #6c757d; text-decoration: none; display: inline-block; padding: 10px 15px; color: white; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h3>Edit Pengajuan Cuti</h3>
        <form action="{{ url('/pengajuan-cuti/update/' . $cuti->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Jumlah Hari</label>
                <input type="number" name="jumlah_hari" class="form-control" value="{{ $cuti->jumlah_hari }}" required>
            </div>
            <div class="form-group">
                <label>Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" class="form-control" value="{{ $cuti->tanggal_mulai }}" required>
            </div>
            <div class="form-group">
                <label>Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" class="form-control" value="{{ $cuti->tanggal_selesai }}" required>
            </div>
            <div class="form-group">
                <label>Alasan Cuti</label>
                <textarea name="alasan" class="form-control" rows="3" required>{{ $cuti->alasan }}</textarea>
            </div>
            <button type="submit" class="btn">Perbarui Pengajuan</button>
            <a href="{{ url('/pengajuan-cuti') }}" class="btn-secondary">Kembali</a>
        </form>
    </div>
</body>
</html>