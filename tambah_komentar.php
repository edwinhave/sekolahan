<?php
session_start();
// Proteksi: Hanya Guru/Admin (level 2) yang boleh mengakses
if (!isset($_SESSION['nisn']) || $_SESSION['level'] != '2') {
    header("location:menu.php");
    exit();
}
include 'koneksi.php';

if (isset($_POST['simpan_komentar'])) {
    $nisn           = mysqli_real_escape_string($conn, $_POST['nisn']);
    $judul_komentar = mysqli_real_escape_string($conn, $_POST['judul_komentar']);
    $isi_komentar   = mysqli_real_escape_string($conn, $_POST['isi_komentar']);
    $tanggal        = date('Y-m-d');

    $query = "INSERT INTO komentar_guru (nisn, judul_komentar, isi_komentar, tanggal_input) 
              VALUES ('$nisn', '$judul_komentar', '$isi_komentar', '$tanggal')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Komentar berhasil ditambahkan!'); window.location='menu.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Input Komentar Guru - E-Rapor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7f6;
        }

        .card {
            border-radius: 15px;
            border: none;
        }
    </style>
</head>

<body class="py-5">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card shadow">
                    <div class="card-header bg-dark text-white py-3">
                        <h5 class="mb-0">Berikan Masukan & Komentar</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Pilih Siswa</label>
                                <select name="nisn" class="form-select" required>
                                    <option value="">-- Pilih Siswa --</option>
                                    <?php
                                    $siswa = mysqli_query($conn, "SELECT nisn, nama FROM data_siswa WHERE level='1'");
                                    while ($s = mysqli_fetch_assoc($siswa)) {
                                        echo "<option value='" . $s['nisn'] . "'>" . $s['nisn'] . " - " . $s['nama'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Kategori Komentar</label>
                                <input type="text" name="judul_komentar" class="form-control" placeholder="Contoh: Kinerja Keseluruhan / Matematika" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Isi Pesan/Masukan</label>
                                <textarea name="isi_komentar" class="form-control" rows="5" placeholder="Tuliskan masukan untuk siswa di sini..." required></textarea>
                            </div>

                            <div class="d-flex justify-content-between pt-3">
                                <a href="menu.php" class="btn btn-outline-secondary px-4">Kembali</a>
                                <button type="submit" name="simpan_komentar" class="btn btn-dark px-5">Kirim Komentar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>