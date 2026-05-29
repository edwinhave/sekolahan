<?php
session_start();
if (!isset($_SESSION['nisn'])) {
    header("location:login.php");
    exit();
}
include 'koneksi.php';

$level_user = $_SESSION['level'];
$nisn_login = $_SESSION['nisn'];

// Ambil data detail user yang login
$query_user = mysqli_query($conn, "SELECT * FROM data_siswa WHERE nisn = '$nisn_login'");
$user_data = mysqli_fetch_assoc($query_user);

// --- LOGIKA STATISTIK UNTUK ADMIN ---
if ($level_user == '2') {
    // 1. Total Siswa
    $q_total_siswa = mysqli_query($conn, "SELECT COUNT(*) as total FROM data_siswa WHERE level = '1'");
    $total_siswa = mysqli_fetch_assoc($q_total_siswa)['total'];

    // 2. Total Mata Pelajaran
    $q_total_mapel = mysqli_query($conn, "SELECT COUNT(*) as total FROM mata_pelajaran");
    $total_mapel = mysqli_fetch_assoc($q_total_mapel)['total'];

    // 3. Rata-rata Nilai Seluruh Siswa (Global)
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
            /* Mencegah horizontal scroll akibat animasi AOS */
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

        .stat-card {
            border-radius: 15px;
            border: none;
        }

        .menu-card {
            transition: transform 0.2s;
            cursor: pointer;
            border-radius: 15px;
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
            <p class=" m-0 opacity-75">Sistem Informasi Akademik Sekolah</p>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4 px-2" data-aos="fade-down" data-aos-delay="200">
            <div>
                <span class="text-muted">Selamat Datang, </span><span class="fw-bold"><?php echo $user_data['nama']; ?></span>
                <span class="badge bg-primary ms-1"><?php echo ($level_user == '2') ? 'Guru / Admin' : 'Siswa'; ?></span>
            </div>
            <a href="logout.php" class="btn btn-dark btn-sm px-4 shadow-sm">Keluar</a>
        </div>

        <?php if ($level_user == '2'): ?>
            <div class="card info-card shadow-sm mb-4" data-aos="fade-up" data-aos-duration="800">
                <h5 class="fw-bold mb-4 small text-uppercase text-muted">Informasi Admin / Sekolah</h5>
                <div class="row">
                    <div class="col-md-4 mb-3" data-aos="zoom-in-up" data-aos-delay="100">
                        <div class="p-3 bg-white rounded shadow-sm border-start border-primary border-4">
                            <div class="text-muted small">Total Siswa Terdaftar</div>
                            <div class="h4 fw-bold mb-0"><?php echo $total_siswa; ?> Siswa</div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3" data-aos="zoom-in-up" data-aos-delay="200">
                        <div class="p-3 bg-white rounded shadow-sm border-start border-success border-4">
                            <div class="text-muted small">Mata Pelajaran Aktif</div>
                            <div class="h4 fw-bold mb-0"><?php echo $total_mapel; ?> Pelajaran</div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3" data-aos="zoom-in-up" data-aos-delay="300">
                        <div class="p-3 bg-white rounded shadow-sm border-start border-warning border-4">
                            <div class="text-muted small">Rata-rata Nilai Sekolah</div>
                            <div class="h4 fw-bold mb-0"><?php echo $rata_rata_sekolah; ?> / 100</div>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="fw-bold mb-3 px-2" data-aos="fade-right">Menu Kelola Data</h5>
            <div class="row g-3">
                <div class="col-md-4" data-aos="flip-left" data-aos-delay="100">
                    <a href="admin_cek_siswa.php" class="card menu-card text-decoration-none shadow-sm h-100 p-4 text-center border-0">
                        <i class="bi bi-search fs-2 text-primary mb-2"></i>
                        <h6 class="fw-bold text-dark mb-1">Monitor Siswa</h6>
                        <small class="text-muted">Cek nilai & absensi per murid</small>
                    </a>
                </div>
                <div class="col-md-4" data-aos="flip-left" data-aos-delay="200">
                    <a href="tambah_nilai.php" class="card menu-card text-decoration-none shadow-sm h-100 p-4 text-center border-0">
                        <i class="bi bi-plus-circle fs-2 text-success mb-2"></i>
                        <h6 class="fw-bold text-dark mb-1">Input Nilai</h6>
                        <small class="text-muted">Masukkan rincian nilai ujian</small>
                    </a>
                </div>
                <div class="col-md-4" data-aos="flip-left" data-aos-delay="300">
                    <a href="tambah_komentar.php" class="card menu-card text-decoration-none shadow-sm h-100 p-4 text-center border-0">
                        <i class="bi bi-chat-dots fs-2 text-info mb-2"></i>
                        <h6 class="fw-bold text-dark mb-1">Beri Komentar</h6>
                        <small class="text-muted">Tulis catatan untuk perkembangan</small>
                    </a>
                </div>
            </div>

        <?php else: ?>
            <div class="card info-card shadow-sm mb-4" data-aos="fade-up" data-aos-duration="700">
                <h5 class="fw-bold mb-4 small text-uppercase text-muted">Informasi Siswa</h5>
                <div class="row small">
                    <div class="col-md-6">
                        <div class="mb-2">Nama: <strong><?php echo $user_data['nama']; ?></strong></div>
                        <div class="mb-2">NISN: <strong><?php echo $user_data['nisn']; ?></strong></div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-2">Kelas: <strong><?php echo $user_data['kelas']; ?></strong></div>
                        <div class="mb-2">Tahun Ajaran: <strong>2025/2026</strong></div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3 px-2" data-aos="fade-right">
                <h5 class="fw-bold m-0">Prestasi Akademik</h5>
                <a href="cetak.php?nisn=<?php echo $nisn_login; ?>" target="_blank" class="btn btn-primary btn-sm px-3 shadow-sm">
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
                            $q_nilai = mysqli_query($conn, "SELECT n.*, m.matapelajaran FROM tabel_nilai n JOIN mata_pelajaran m ON n.id_matapelajaran = m.id_matapelajaran WHERE n.nisn = '$nisn_login'");
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
                                echo "<tr><td colspan='10' class='text-center py-4 text-muted'>Belum ada data nilai.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <h5 class="fw-bold mb-3 px-2" data-aos="fade-right">Kehadiran</h5>
            <?php
            // Logika hitung log absensi harian murid
            $q_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM kehadiran WHERE nisn = '$nisn_login'");
            $total_hari = mysqli_fetch_assoc($q_total)['total'];

            $q_hadir = mysqli_query($conn, "SELECT COUNT(*) as total FROM kehadiran WHERE nisn = '$nisn_login' AND status = 'Hadir'");
            $hadir = mysqli_fetch_assoc($q_hadir)['total'];

            $q_izin_sakit = mysqli_query($conn, "SELECT COUNT(*) as total FROM kehadiran WHERE nisn = '$nisn_login' AND status IN ('Izin', 'Sakit')");
            $izin_sakit = mysqli_fetch_assoc($q_izin_sakit)['total'];

            $q_alpha = mysqli_query($conn, "SELECT COUNT(*) as total FROM kehadiran WHERE nisn = '$nisn_login' AND status = 'Alpha'");
            $alpha = mysqli_fetch_assoc($q_alpha)['total'];

            $persen = ($total_hari > 0) ? ($hadir / $total_hari) * 100 : 0;
            ?>
            <div class="row g-3 mb-4">
                <div class="col-md-4" data-aos="zoom-in-right" data-aos-delay="100">
                    <div class="card info-card shadow-sm text-center h-100 d-flex flex-column justify-content-center py-4">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto shadow-sm mb-3" style="width: 80px; height: 80px; border: 5px solid #d1e7dd;">
                            <span class="fw-bold text-success fs-5"><?php echo number_format($persen, 1); ?>%</span>
                        </div>
                        <h6 class="fw-bold text-success mb-1">Hadir</h6>
                        <div class="h4 fw-bold text-dark mb-0"><?php echo $hadir; ?> <span class="fs-6 text-muted fw-normal">/ <?php echo $total_hari; ?> Hari</span></div>
                    </div>
                </div>

                <div class="col-md-4" data-aos="zoom-in-up" data-aos-delay="200">
                    <div class="card info-card shadow-sm text-center h-100 d-flex flex-column justify-content-center py-4">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto shadow-sm mb-3" style="width: 80px; height: 80px; border: 5px solid #fff3cd;">
                            <i class="bi bi-envelope-paper text-warning fs-3"></i>
                        </div>
                        <h6 class="fw-bold text-warning mb-1">Izin / Sakit</h6>
                        <div class="h4 fw-bold text-dark mb-0"><?php echo $izin_sakit; ?> <span class="fs-6 text-muted fw-normal">Hari</span></div>
                    </div>
                </div>

                <div class="col-md-4" data-aos="zoom-in-left" data-aos-delay="300">
                    <div class="card info-card shadow-sm text-center h-100 d-flex flex-column justify-content-center py-4">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto shadow-sm mb-3" style="width: 80px; height: 80px; border: 5px solid #f8d7da;">
                            <i class="bi bi-x-circle text-danger fs-3"></i>
                        </div>
                        <h6 class="fw-bold text-danger mb-1">Alpha / Tanpa Keterangan</h6>
                        <div class="h4 fw-bold text-dark mb-0"><?php echo $alpha; ?> <span class="fs-6 text-muted fw-normal">Hari</span></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-4" data-aos="fade-up" data-aos-duration="600">
                    <div class="card info-card shadow-sm">
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
            </div>

            <div class="card info-card shadow-sm border-0 mb-4" data-aos="fade-up" data-aos-duration="600" data-aos-delay="100">
                <h6 class="fw-bold mb-3">Komentar & Masukan Guru</h6>
                <?php
                $q_k = mysqli_query($conn, "SELECT * FROM komentar_guru WHERE nisn = '$nisn_login'");
                if (mysqli_num_rows($q_k) > 0) {
                    while ($k = mysqli_fetch_assoc($q_k)) {
                        echo "<div class='d-flex mb-3 align-items-start bg-white p-3 rounded shadow-sm border'>
                            <div class='bg-light rounded-circle me-3 p-2' style='width:40px; height:40px; text-align:center;'><i class='bi bi-person'></i></div>
                            <div><strong class='small'>" . $k['judul_komentar'] . "</strong><br><p class='text-muted small mb-0'>" . $k['isi_komentar'] . "</p></div>
                          </div>";
                    }
                } else {
                    echo "<p class='small text-muted'>Belum ada komentar dari guru.</p>";
                }
                ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            once: true // Animasi hanya berjalan 1 kali saat di-scroll pertama kali agar tidak mengganggu fokus presentasi
        });
    </script>
</body>

</html>