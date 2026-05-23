<?php
session_start();
if (!isset($_SESSION['nisn']) || $_SESSION['level'] != '2') {
    header("location:login.php");
    exit();
}
include 'koneksi.php';

// Ambil NISN dari URL jika diarahkan dari halaman monitoring
$nisn_get = isset($_GET['nisn']) ? mysqli_real_escape_string($conn, $_GET['nisn']) : '';
$tanggal_pilih = isset($_POST['tanggal']) ? $_POST['tanggal'] : date('Y-m-d');

if (isset($_POST['simpan_absensi'])) {
    $nisn = mysqli_real_escape_string($conn, $_POST['nisn']);
    $status = $_POST['status'];

    // Cek apakah siswa tersebut sudah memiliki data absensi di tanggal yang dipilih
    $cek = mysqli_query($conn, "SELECT * FROM kehadiran WHERE nisn = '$nisn' AND tanggal = '$tanggal_pilih'");

    if (mysqli_num_rows($cek) > 0) {
        // Jika sudah ada, lakukan UPDATE
        $query = "UPDATE kehadiran SET status = '$status' WHERE nisn = '$nisn' AND tanggal = '$tanggal_pilih'";
    } else {
        // Jika belum ada, lakukan INSERT
        $query = "INSERT INTO kehadiran (nisn, tanggal, status) VALUES ('$nisn', '$tanggal_pilih', '$status')";
    }

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Absensi harian berhasil disimpan!'); window.location='admin_cek_siswa.php?nisn=$nisn';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Absensi Harian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow border-0" style="border-radius: 15px;">
                    <div class="card-header bg-success text-white py-3fw-bold">
                        <h5><i class="bi bi-calendar2-check me-2"></i>Pencatatan Absensi Harian</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Pilih Siswa</label>
                                <select name="nisn" class="form-select" required>
                                    <option value="">-- Pilih Siswa --</option>
                                    <?php
                                    $s_list = mysqli_query($conn, "SELECT nisn, nama FROM data_siswa WHERE level='1' ORDER BY nama ASC");
                                    while ($s = mysqli_fetch_assoc($s_list)) {
                                        $selected = ($nisn_get == $s['nisn']) ? 'selected' : '';
                                        echo "<option value='" . $s['nisn'] . "' $selected>" . $s['nama'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Tanggal</label>
                                <input type="date" name="tanggal" class="form-control" value="<?php echo $tanggal_pilih; ?>" onchange="this.form.submit()">
                                <small class="text-muted" style="font-size:0.75rem;">*Mengubah tanggal akan merefresh halaman untuk memuat status lama.</small>
                            </div>

                            <?php
                            // Mengambil status lama (jika ada) untuk dijadikan default radio button
                            $status_lama = 'Hadir';
                            if ($nisn_get) {
                                $q_status = mysqli_query($conn, "SELECT status FROM kehadiran WHERE nisn = '$nisn_get' AND tanggal = '$tanggal_pilih'");
                                if ($res = mysqli_fetch_assoc($q_status)) {
                                    $status_lama = $res['status'];
                                }
                            }
                            ?>

                            <div class="mb-4">
                                <label class="form-label fw-bold small d-block">Status Kehadiran Hari Ini</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="status" id="status1" value="Hadir" <?php echo ($status_lama == 'Hadir') ? 'checked' : ''; ?>>
                                    <label class="btn btn-outline-success" Kak untuk="status1">Hadir</label>

                                    <input type="radio" class="btn-check" name="status" id="status2" value="Izin" <?php echo ($status_lama == 'Izin') ? 'checked' : ''; ?>>
                                    <label class="btn btn-outline-warning" for="status2">Izin</label>

                                    <input type="radio" class="btn-check" name="status" id="status3" value="Sakit" <?php echo ($status_lama == 'Sakit') ? 'checked' : ''; ?>>
                                    <label class="btn btn-outline-info" for="status3">Sakit</label>

                                    <input type="radio" class="btn-check" name="status" id="status4" value="Alpha" <?php echo ($status_lama == 'Alpha') ? 'checked' : ''; ?>>
                                    <label class="btn btn-outline-danger" for="status4">Alpha</label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between pt-2">
                                <a href="admin_cek_siswa.php?nisn=<?php echo $nisn_get; ?>" class="btn btn-outline-secondary">Kembali</a>
                                <button type="submit" name="simpan_absensi" class="btn btn-success px-4">Simpan Absensi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>