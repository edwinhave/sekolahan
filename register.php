<?php
include 'koneksi.php';

if (isset($_POST['register'])) {
    // Menangkap data dari form
    $nisn     = mysqli_real_escape_string($conn, $_POST['nisn']);
    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $kelas    = mysqli_real_escape_string($conn, $_POST['kelas']);
    $alamat   = mysqli_real_escape_string($conn, $_POST['alamat']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $level    = 1; // Otomatis diset menjadi 1 untuk Siswa

    // Cek apakah NISN atau Email sudah terdaftar
    $cek_user = mysqli_query($conn, "SELECT * FROM data_siswa WHERE nisn = '$nisn' OR email = '$email'");

    if (mysqli_num_rows($cek_user) > 0) {
        echo "<script>alert('NISN atau Email sudah terdaftar!'); window.location='register.php';</script>";
    } else {
        // Tambahkan kolom email ke dalam Query Insert
        $query = "INSERT INTO data_siswa (nisn, nama, email, kelas, alamat, password, level) 
                  VALUES ('$nisn', '$nama', '$email', '$kelas', '$alamat', '$password', '$level')";

        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Pendaftaran Berhasil!'); window.location='login.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Registrasi Siswa - E-Rapor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f0f2f5;
            height: 100vh;
            display: flex;
            align-items: center;
        }

        .register-card {
            width: 100%;
            max-width: 500px;
            margin: auto;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="card register-card shadow">
            <div class="card-header bg-primary text-white text-center py-3">
                <h4 class="mb-0">Daftar Akun Siswa</h4>
            </div>
            <div class="card-body p-4">
                <form action="" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NISN</label>
                            <input type="text" name="nisn" class="form-control" placeholder="10 digit" maxlength="10" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kelas</label>
                            <input type="number" name="kelas" class="form-control" placeholder="Contoh: 10" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="contoh@sekolah.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" name="register" class="btn btn-primary text-white">Daftar Sekarang</button>
                        <a href="login.php" class="btn btn-outline-secondary">Sudah punya akun? Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>