<?php
session_start();
if (!isset($_SESSION['nisn']) || $_SESSION['level'] != '2') {
    header("location:login.php");
    exit();
}
include 'koneksi.php';

if (isset($_POST['simpan'])) {
    $nisn = $_POST['nisn'];
    $jenis = $_POST['jenis'];
    $tgl = $_POST['tanggal'];
    $kat = $_POST['kategori'];
    mysqli_query($conn, "INSERT INTO pelanggaran (nisn, jenis_pelanggaran, tanggal, kategori) VALUES ('$nisn', '$jenis', '$tgl', '$kat')");
    echo "<script>alert('Pelanggaran dicatat!'); window.location='admin_cek_siswa.php?nisn=$nisn';</script>";
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Input Pelanggaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow border-0" style="border-radius: 15px;">
                    <div class="card-header bg-danger text-white">Catat Pelanggaran Baru</div>
                    <div class="card-body p-4">
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label>Siswa</label>
                                <select name="nisn" class="form-select">
                                    <?php
                                    $s_list = mysqli_query($conn, "SELECT nisn, nama FROM data_siswa WHERE level='1'");
                                    while ($s = mysqli_fetch_assoc($s_list)) echo "<option value='" . $s['nisn'] . "'>" . $s['nama'] . "</option>";
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3"><label>Jenis Pelanggaran</label><input type="text" name="jenis" class="form-control" placeholder="Contoh: Terlambat Masuk" required></div>
                            <div class="mb-3"><label>Tanggal</label><input type="date" name="tanggal" class="form-control" value="<?php echo date('Y-m-d'); ?>" required></div>
                            <div class="mb-3"><label>Kategori</label>
                                <select name="kategori" class="form-select">
                                    <option value="Ringan">Ringan</option>
                                    <option value="Sedang">Sedang</option>
                                    <option value="Berat">Berat</option>
                                </select>
                            </div>
                            <button type="submit" name="simpan" class="btn btn-danger w-100">Simpan Catatan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>