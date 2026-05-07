<?php
session_start();
if (!isset($_SESSION['nisn']) || $_SESSION['level'] != '2') {
    header("location:login.php");
    exit();
}
include 'koneksi.php';

if (isset($_POST['simpan'])) {
    $nisn = mysqli_real_escape_string($conn, $_POST['nisn']);
    $hadir = $_POST['hadir'];
    $izin = $_POST['izin'];
    $sakit = $_POST['sakit'];
    $alpha = $_POST['alpha'];
    $terlambat = $_POST['terlambat'];
    $total = $_POST['total_hari'];

    // Cek apakah data sudah ada
    $cek = mysqli_query($conn, "SELECT * FROM kehadiran WHERE nisn = '$nisn'");
    if (mysqli_num_rows($cek) > 0) {
        $query = "UPDATE kehadiran SET hadir='$hadir', izin='$izin', sakit='$sakit', alpha='$alpha', terlambat='$terlambat', total_hari='$total' WHERE nisn = '$nisn'";
    } else {
        $query = "INSERT INTO kehadiran (nisn, hadir, izin, sakit, alpha, terlambat, total_hari) VALUES ('$nisn', '$hadir', '$izin', '$sakit', '$alpha', '$terlambat', '$total')";
    }

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data Kehadiran Berhasil Disimpan!'); window.location='admin_cek_siswa.php?nisn=$nisn';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Input Kehadiran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow border-0" style="border-radius: 15px;">
                    <div class="card-header bg-success text-white">Input / Edit Kehadiran</div>
                    <div class="card-body p-4">
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Siswa</label>
                                <select name="nisn" class="form-select" required>
                                    <?php
                                    $s_list = mysqli_query($conn, "SELECT nisn, nama FROM data_siswa WHERE level='1'");
                                    while ($s = mysqli_fetch_assoc($s_list)) echo "<option value='" . $s['nisn'] . "'>" . $s['nama'] . "</option>";
                                    ?>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3"><label class="form-label">Total Hari</label><input type="number" name="total_hari" class="form-control" required></div>
                                <div class="col-6 mb-3"><label class="form-label">Hadir</label><input type="number" name="hadir" class="form-control" required></div>
                                <div class="col-4 mb-3"><label>Izin</label><input type="number" name="izin" class="form-control" value="0"></div>
                                <div class="col-4 mb-3"><label>Sakit</label><input type="number" name="sakit" class="form-control" value="0"></div>
                                <div class="col-4 mb-3"><label>Alpha</label><input type="number" name="alpha" class="form-control" value="0"></div>
                                <div class="col-12 mb-3"><label>Terlambat (Kali)</label><input type="number" name="terlambat" class="form-control" value="0"></div>
                            </div>
                            <button type="submit" name="simpan" class="btn btn-success w-100">Simpan Kehadiran</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>