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
                    <label>Username / NISN</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Masuk</button>
            </form>
        </div>
    </div>
</body>

</html>