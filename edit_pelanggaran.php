<?php
session_start();
if (!isset($_SESSION['nisn']) || $_SESSION['level'] != '2') {
    header("location:login.php");
    exit();
}
include 'koneksi.php';

// Ambil ID pelanggaran dari URL
$id_pelanggaran = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';

// Ambil data lama berdasarkan ID
$query_lama = mysqli_query($conn, "SELECT p.*, s.nama FROM pelanggaran p JOIN data_siswa s ON p.nisn = s.nisn WHERE p.id_pelanggaran = '$id_pelanggaran'");
$data = mysqli_fetch_assoc($query_lama);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='admin_cek_siswa.php';</script>";
    exit();
}

if (isset($_POST['update_pelanggaran'])) {
    $jenis_pelanggaran = mysqli_real_escape_string($conn, $_POST['jenis_pelanggaran']);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);

    $update = mysqli_query($conn, "UPDATE pelanggaran SET jenis_pelanggaran = '$jenis_pelanggaran', tanggal = '$tanggal' WHERE id_pelanggaran = '$id_pelanggaran'");

    if ($update) {
        echo "<script>alert('Catatan pelanggaran berhasil diperbarui!'); window.location='admin_cek_siswa.php?nisn=" . $data['nisn'] . "';</script>";
    } else {
        echo "Gagal mengupdate: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Pelanggaran - <?php echo $data['nama']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow border-0" style="border-radius: 15px;">
                    <div class="card-header bg-warning text-dark py-3 fw-bold">
                        Edit Catatan Pelanggaran Sekolah
                    </div>
                    <div class="card-body p-4">
                        <p class="small text-muted mb-3">Siswa: <strong><?php echo $data['nama']; ?></strong> (<?php echo $data['nisn']; ?>)</p>

                        <form action="" method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Jenis Pelanggaran</label>
                                <input type="text" name="jenis_pelanggaran" class="form-control" value="<?php echo $data['jenis_pelanggaran']; ?>" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small">Tanggal Kejadian</label>
                                <input type="date" name="tanggal" class="form-control" value="<?php echo $data['tanggal']; ?>" required>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="admin_cek_siswa.php?nisn=<?php echo $data['nisn']; ?>" class="btn btn-outline-secondary">Batal</a>
                                <button type="submit" name="update_pelanggaran" class="btn btn-warning fw-bold">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>