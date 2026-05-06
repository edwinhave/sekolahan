<?php
session_start();
// Hanya level 2 (Guru/Admin) yang boleh masuk
if (!isset($_SESSION['nisn']) || $_SESSION['level'] != '2') {
    header("location:menu.php");
    exit();
}
include 'koneksi.php';

if (isset($_POST['simpan'])) {
    $nisn      = mysqli_real_escape_string($conn, $_POST['nisn']);
    $id_mapel  = mysqli_real_escape_string($conn, $_POST['id_mapel']);
    $nilai     = mysqli_real_escape_string($conn, $_POST['nilai']);

    $query = "INSERT INTO tabel_nilai (nisn, id_matapelajaran, nilai) VALUES ('$nisn', '$id_mapel', '$nilai')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Nilai berhasil ditambahkan!'); window.location='menu.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Nilai - E-Rapor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light p-5">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Tambah Nilai Murid</h5>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Pilih Murid</label>
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
                                <label class="form-label">Mata Pelajaran</label>
                                <select name="id_mapel" class="form-select" required>
                                    <option value="">-- Pilih Mapel --</option>
                                    <?php
                                    $mapel = mysqli_query($conn, "SELECT * FROM mata_pelajaran");
                                    while ($m = mysqli_fetch_assoc($mapel)) {
                                        echo "<option value='" . $m['id_matapelajaran'] . "'>" . $m['matapelajaran'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Input Nilai</label>
                                <input type="number" name="nilai" class="form-control" min="0" max="100" required>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="menu.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" name="simpan" class="btn btn-primary">Simpan Nilai</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>