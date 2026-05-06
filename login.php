<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login - Aplikasi Nilai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            margin: auto;
        }
    </style>
</head>

<body>
    <div class="card login-card shadow">
        <div class="card-body p-4">
            <h3 class="text-center mb-4">Login E-Rapor</h3>
            <form action="validasi.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Username / NISN</label>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan NISN" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <!-- Jika kamu tetap ingin batas 10 karakter -->
                    <input type="password" name="password" class="form-control" maxlength="10" required>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Masuk</button>
                </div>
            </form>

            <!-- Tambahan Link ke Register -->
            <div class="mt-4 text-center">
                <p class="mb-0 text-muted">Belum punya akun?</p>
                <a href="register.php" class="text-decoration-none">Daftar Akun Siswa Baru</a>
            </div>
        </div>
    </div>
</body>

</html>