<?php
session_start();
if (!isset($_SESSION['nisn'])) {
    header("location:login.php");
    exit();
}
include 'koneksi.php';

$level_user = $_SESSION['level'];
$nisn_login = $_SESSION['nisn'];

// Menangkap filter semester khusus siswa
$semester_aktif = isset($_GET['semester']) ? mysqli_real_escape_string($conn, $_GET['semester']) : 'Ganjil';

$query_user = mysqli_query($conn, "SELECT * FROM data_siswa WHERE nisn = '$nisn_login'");
$user_data = mysqli_fetch_assoc($query_user);

if ($level_user == '2') {
    $q_total_siswa = mysqli_query($conn, "SELECT COUNT(*) as total FROM data_siswa WHERE level = '1'");
    $total_siswa = mysqli_fetch_assoc($q_total_siswa)['total'];

    $q_total_mapel = mysqli_query($conn, "SELECT COUNT(*) as total FROM mata_pelajaran");
    $total_mapel = mysqli_fetch_assoc($q_total_mapel)['total'];

    $q_avg = mysqli_query($conn, "SELECT AVG((pe1+pe2+pe3+pe4+pe5+pe6+pts+asaj)/8) as rata_global FROM tabel_nilai");
    $avg_data = mysqli_fetch_assoc($q_avg);
    $rata_rata_sekolah = ($avg_data['rata_global']) ? number_format($avg_data['rata_global'], 1) : 0;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sekolah Gracia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --dark-header: #0a0a1a;
            --primary-blue: #2b59ff;
            --card-bg: #f8f9fc;
        }

        body {
            background-color: #D4EAF7;
            font-family: 'Segoe UI', sans-serif;
            overflow-x: hidden;
        }

        .header-panel {
            background-color: var(--dark-header);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-top: 20px;
        }

        .info-card {
            background-color: var(--card-bg);
            border: none;
            border-radius: 15px;
            padding: 25px;
        }

        .menu-card {
            transition: transform 0.2s;
            cursor: pointer;
            border-radius: 15px;
            text-decoration: none;
        }

        .menu-card:hover {
            transform: translateY(-5px);
            background-color: #f0f4ff;
        }

        .table thead th {
            background-color: #64b5f6 !important;
            color: white;
            font-size: 0.85rem;
            text-align: center;
            border: none;
        }

        .avg-column {
            background-color: #eef2ff;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="container pb-5">
        <div class="header-panel shadow mb-4 text-center" style="background-color: #64B5F6;" data-aos="zoom-in" data-aos-duration="800">
            <h2 class="fw-bold m-0">Sekolah Gracia</h2>
            <p class="m-0 opacity-75">Sistem Informasi Akademik Sekolah</p>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4 px-2" data-aos="fade-down" data-aos-delay="200">
            <div>
                <span>Selamat Datang, </span><span class="fw-bold"><?php echo $user_data['nama']; ?></span>
                <span class="badge bg-primary ms-1"><?php echo ($level_user == '2') ? 'Guru / Admin' : 'Siswa'; ?></span>
            </div>
            <a href="logout.php" class="btn btn-dark btn-sm px-4 shadow-sm">Keluar</a>
        </div>

        <?php if ($level_user == '2'): ?>
            <div class="card info-card shadow-sm mb-4" data-aos="fade-up" data-aos-duration="800">
                <h5 class="fw-bold mb-4 small text-uppercase text-muted">Informasi Admin / Sekolah</h5>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="p-3 bg-white rounded shadow-sm border-start border-primary border-4">
                            <div class="text-muted small">Total Siswa</div>
                            <div class="h4 fw-bold mb-0"><?php echo $total_siswa; ?> Siswa</div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="p-3 bg-white rounded shadow-sm border-start border-success border-4">
                            <div class="text-muted small">Mata Pelajaran Aktif</div>
                            <div class="h4 fw-bold mb-0"><?php echo $total_mapel; ?> Pelajaran</div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="p-3 bg-white rounded shadow-sm border-start border-warning border-4">
                            <div class="text-muted small">Rata Nilai Sekolah</div>
                            <div class="h4 fw-bold mb-0"><?php echo $rata_rata_sekolah; ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="fw-bold mb-3 px-2" data-aos="fade-right">Menu Kelola Data</h5>
            <div class="row g-3">
                <div class="col-md-3"><a href="admin_cek_siswa.php" class="card menu-card shadow-sm h-100 p-4 text-center border-0"><i class="bi bi-search fs-2 text-primary mb-2"></i>
                        <h6 class="fw-bold text-dark mb-1">Monitor Siswa</h6><small class="text-muted">Cek nilai & absensi</small>
                    </a></div>
                <div class="col-md-3"><a href="tambah_nilai.php" class="card menu-card shadow-sm h-100 p-4 text-center border-0"><i class="bi bi-plus-circle fs-2 text-success mb-2"></i>
                        <h6 class="fw-bold text-dark mb-1">Input Nilai</h6><small class="text-muted">Masukkan rincian nilai</small>
                    </a></div>
                <div class="col-md-3"><a href="tambah_komentar.php" class="card menu-card shadow-sm h-100 p-4 text-center border-0"><i class="bi bi-chat-dots fs-2 text-info mb-2"></i>
                        <h6 class="fw-bold text-dark mb-1">Beri Komentar</h6><small class="text-muted">Tulis masukan evaluasi</small>
                    </a></div>
                <div class="col-md-3"><a href="admin_manajemen_user.php" class="card menu-card shadow-sm h-100 p-4 text-center border-0"><i class="bi bi-people fs-2 text-dark mb-2"></i>
                        <h6 class="fw-bold text-dark mb-1">Manajemen User</h6><small class="text-muted">Waiting list verifikasi</small>
                    </a></div>
            </div>

        <?php else: ?>
            <div class="card info-card shadow-sm mb-4" data-aos="fade-up" data-aos-duration="700">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold small text-uppercase text-muted m-0">Informasi Siswa</h5>
                    <form action="" method="GET" class="d-flex align-items-center gap-2">
                        <small class="text-muted fw-bold text-uppercase" style="font-size:0.75rem;">Lihat Periode:</small>
                        <select name="semester" class="form-select form-select-sm border-secondary" onchange="this.form.submit()" style="border-radius:6px; width:150px;">
                            <option value="Ganjil" <?php echo ($semester_aktif == 'Ganjil') ? 'selected' : ''; ?>>Semester Ganjil</option>
                            <option value="Genap" <?php echo ($semester_aktif == 'Genap') ? 'selected' : ''; ?>>Semester Genap</option>
                        </select>
                    </form>
                </div>
                <div class="row small">
                    <div class="col-md-6">
                        <div class="mb-2">Nama: <strong><?php echo $user_data['nama']; ?></strong></div>
                        <div class="mb-2">NISN: <strong><?php echo $user_data['nisn']; ?></strong></div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-2">Kelas: <strong><?php echo $user_data['kelas']; ?></strong></div>
                        <div class="mb-2">Tahun Ajaran / Semester: <strong>2025/2026 (<span class="text-primary"><?php echo $semester_aktif; ?></span>)</strong></div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3 px-2" data-aos="fade-right">
                <h5 class="fw-bold m-0">Prestasi Akademik (<?php echo $semester_aktif; ?>)</h5>
                <a href="cetak.php?nisn=<?php echo $nisn_login; ?>&semester=<?php echo $semester_aktif; ?>" target="_blank" class="btn btn-primary btn-sm px-3 shadow-sm">
                    <i class="bi bi-printer me-1"></i> Cetak Laporan Nilai
                </a>
            </div>

            <div class="card border-0 shadow-sm overflow-hidden mb-4" style="border-radius: 15px;" data-aos="zoom-in-up" data-aos-duration="800">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="text-start ps-4">Mata Pelajaran</th>
                                <th>PE1</th>
                                <th>PE2</th>
                                <th>PE3</th>
                                <th>PE4</th>
                                <th>PE5</th>
                                <th>PE6</th>
                                <th>PTS</th>
                                <th>ASAJ</th>
                                <th class="avg-column text-primary">Rata-Rata</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $q_nilai = mysqli_query($conn, "SELECT n.*, m.matapelajaran FROM tabel_nilai n JOIN mata_pelajaran m ON n.id_matapelajaran = m.id_matapelajaran WHERE n.nisn = '$nisn_login' AND n.semester = '$semester_aktif'");
                            if (mysqli_num_rows($q_nilai) > 0) {
                                while ($row = mysqli_fetch_assoc($q_nilai)) {
                                    $rata = ($row['pe1'] + $row['pe2'] + $row['pe3'] + $row['pe4'] + $row['pe5'] + $row['pe6'] + $row['pts'] + $row['asaj']) / 8;
                                    echo "<tr class='text-center'>";
                                    echo "<td class='text-start ps-4 fw-bold'>" . $row['matapelajaran'] . "</td>";
                                    echo "<td>" . $row['pe1'] . "</td><td>" . $row['pe2'] . "</td><td>" . $row['pe3'] . "</td><td>" . $row['pe4'] . "</td><td>" . $row['pe5'] . "</td><td>" . $row['pe6'] . "</td><td>" . $row['pts'] . "</td><td>" . $row['asaj'] . "</td>";
                                    echo "<td class='avg-column text-primary'>" . number_format($rata, 2) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='10' class='text-center py-4 text-muted'>Belum ada data nilai di Semester " . $semester_aktif . ".</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <h5 class="fw-bold mb-3 px-2" data-aos="fade-right">Kehadiran (<?php echo $semester_aktif; ?>)</h5>
            <?php
            $q_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM kehadiran WHERE nisn = '$nisn_login' AND semester = '$semester_aktif'");
            $total_hari = mysqli_fetch_assoc($q_total)['total'];

            $q_hadir = mysqli_query($conn, "SELECT COUNT(*) as total FROM kehadiran WHERE nisn = '$nisn_login' AND status = 'Hadir' AND semester = '$semester_aktif'");
            $hadir = mysqli_fetch_assoc($q_hadir)['total'];

            $q_izin_sakit = mysqli_query($conn, "SELECT COUNT(*) as total FROM kehadiran WHERE nisn = '$nisn_login' AND status IN ('Izin', 'Sakit') AND semester = '$semester_aktif'");
            $izin_sakit = mysqli_fetch_assoc($q_izin_sakit)['total'];

            $q_alpha = mysqli_query($conn, "SELECT COUNT(*) as total FROM kehadiran WHERE nisn = '$nisn_login' AND status = 'Alpha' AND semester = '$semester_aktif'");
            $alpha = mysqli_fetch_assoc($q_alpha)['total'];

            $persen = ($total_hari > 0) ? ($hadir / $total_hari) * 100 : 0;
            ?>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card info-card shadow-sm text-center h-100 py-4">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto shadow-sm mb-3" style="width: 80px; height: 80px; border: 5px solid #d1e7dd;">
                            <span class="fw-bold text-success fs-5"><?php echo number_format($persen, 1); ?>%</span>
                        </div>
                        <h6 class="fw-bold text-success mb-1">Hadir</h6>
                        <div class="h4 fw-bold text-dark mb-0"><?php echo $hadir; ?> <span class="fs-6 text-muted fw-normal">/ <?php echo $total_hari; ?> Hari</span></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card info-card shadow-sm text-center h-100 py-4">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto shadow-sm mb-3" style="width: 80px; height: 80px; border: 5px solid #fff3cd;"><i class="bi bi-envelope-paper text-warning fs-3"></i></div>
                        <h6 class="fw-bold text-warning mb-1">Izin / Sakit</h6>
                        <div class="h4 fw-bold text-dark mb-0"><?php echo $izin_sakit; ?> <span class="fs-6 text-muted fw-normal">Hari</span></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card info-card shadow-sm text-center h-100 py-4">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto shadow-sm mb-3" style="width: 80px; height: 80px; border: 5px solid #f8d7da;"><i class="bi bi-x-circle text-danger fs-3"></i></div>
                        <h6 class="fw-bold text-danger mb-1">Alpha</h6>
                        <div class="h4 fw-bold text-dark mb-0"><?php echo $alpha; ?> <span class="fs-6 text-muted fw-normal">Hari</span></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card info-card shadow-sm h-100">
                        <h5 class="fw-bold mb-4">Pelanggaran Sekolah</h5>
                        <div class="overflow-auto small" style="max-height: 150px;">
                            <?php
                            $q_p = mysqli_query($conn, "SELECT * FROM pelanggaran WHERE nisn = '$nisn_login'");
                            if (mysqli_num_rows($q_p) > 0) {
                                while ($p = mysqli_fetch_assoc($q_p)) {
                                    echo "<div class='border-bottom pb-2 mb-2'><span class='fw-bold text-danger'>• " . $p['jenis_pelanggaran'] . "</span><br><small class='text-muted'>" . date('d/m/Y', strtotime($p['tanggal'])) . "</small></div>";
                                }
                            } else {
                                echo "<p class='text-center text-muted my-4'>Tidak ada catatan pelanggaran.</p>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card info-card shadow-sm h-100">
                        <h5 class="fw-bold mb-4">Komentar & Masukan Guru</h5>
                        <div class="overflow-auto small" style="max-height: 150px;">
                            <?php
                            $q_k = mysqli_query($conn, "SELECT * FROM komentar_guru WHERE nisn = '$nisn_login'");
                            if (mysqli_num_rows($q_k) > 0) {
                                while ($k = mysqli_fetch_assoc($q_k)) {
                                    echo "<div class='d-flex mb-2 align-items-start bg-white p-2 rounded border'>
                                        <div><strong class='small text-primary'>" . $k['judul_komentar'] . "</strong><p class='text-muted small mb-0'>" . $k['isi_komentar'] . "</p></div>
                                      </div>";
                                }
                            } else {
                                echo "<p class='small text-muted'>Belum ada komentar dari guru.</p>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            once: true
        });
    </script>
</body>

</html>