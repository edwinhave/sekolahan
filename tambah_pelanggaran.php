<?php
session_start();
if (!isset($_SESSION['nisn']) || $_SESSION['level'] != '2') {
    header("location:login.php");
    exit();
}
include 'koneksi.php';

// 1. Tangkap NISN dari URL (jika ada)
$nisn_otomatis = isset($_GET['nisn']) ? mysqli_real_escape_string($conn, $_GET['nisn']) : '';

// 2. Jika form di-submit
if (isset($_POST['simpan_pelanggaran'])) {
    // Jika NISN dilempar dari URL, gunakan itu. Jika tidak, ambil dari dropdown POST
    $nisn = !empty($nisn_otomatis) ? $nisn_otomatis : mysqli_real_escape_string($conn, $_POST['nisn']);
    $jenis_pelanggaran = mysqli_real_escape_string($conn, $_POST['jenis_pelanggaran']);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']); // Menyesuaikan input gambar kamu

    $query = "INSERT INTO pelanggaran (nisn, jenis_pelanggaran, tanggal, kategori) 
              VALUES ('$nisn', '$jenis_pelanggaran', '$tanggal', '$kategori')";

    if (mysqli_query($conn, $query)) {
        // Otomatis balik ke halaman monitor siswa yang bersangkutan
        echo "<script>alert('Catatan pelanggaran berhasil disimpan!'); window.location='admin_cek_siswa.php?nisn=$nisn';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// 3. Ambil nama siswa untuk keperluan display text jika NISN-nya otomatis
$nama_siswa_otomatis = "";
if (!empty($nisn_otomatis)) {
    $q_s = mysqli_query($conn, "SELECT nama FROM data_siswa WHERE nisn = '$nisn_otomatis'");
    if ($dt = mysqli_fetch_assoc($q_s)) {
        $nama_siswa_otomatis = $dt['nama'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Catat Pelanggaran Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow border-0" style="border-radius: 15px;">
                    <div class="card-header bg-danger text-white py-3">
                        <h5 class="mb-0">Catat Pelanggaran Baru</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="" method="POST">

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Siswa</label>
                                <?php if (!empty($nisn_otomatis)): ?>
                                    <div class="p-2 bg-light border rounded fw-bold text-dark">
                                        <?php echo $nisn_otomatis . " - " . $nama_siswa_otomatis; ?>
                                    </div>
                                    <input type="hidden" name="nisn" value="<?php echo $nisn_otomatis; ?>">
                                <?php else: ?>
                                    <select name="nisn" class="form-select" required>
                                        <option value="">-- Pilih Siswa --</option>
                                        <?php
                                        $s_list = mysqli_query($conn, "SELECT nisn, nama FROM data_siswa WHERE level='1' ORDER BY nama ASC");
                                        while ($s = mysqli_fetch_assoc($s_list)) {
                                            echo "<option value='" . $s['nisn'] . "'>" . $s['nama'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Jenis Pelanggaran</label>
                                <input type="text" name="jenis_pelanggaran" class="form-control" placeholder="Contoh: Terlambat Masuk" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Tanggal</label>
                                <input type="date" name="tanggal" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small">Kategori</label>
                                <select name="kategori" class="form-select" required>
                                    <option value="Ringan">Ringan</option>
                                    <option value="Sedang">Sedang</option>
                                    <option value="Berat">Berat</option>
                                </select>
                            </div>

                            <div class="d-flex justify-content-between pt-2">
                                <a href="admin_cek_siswa.php?nisn=<?php echo $nisn_otomatis; ?>" class="btn btn-outline-secondary">Batal</a>
                                <button type="submit" name="simpan_pelanggaran" class="btn btn-danger px-4">Simpan Catatan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>