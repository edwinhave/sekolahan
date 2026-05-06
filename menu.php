<?php
session_start();
if (!isset($_SESSION['nisn'])) {
    header("location:login.php");
    exit();
}
include 'koneksi.php';

$level_user = $_SESSION['level'];
$nisn_login = $_SESSION['nisn'];

// Ambil data detail siswa
$query_user = mysqli_query($conn, "SELECT * FROM data_siswa WHERE nisn = '$nisn_login'");
$user_data = mysqli_fetch_assoc($query_user);

// Menghitung total siswa terdaftar
$query_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM data_siswa WHERE level = '1'");
$total_siswa = mysqli_fetch_assoc($query_total)['total'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SMA Negeri 1 Jakarta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --dark-header: #0a0a1a;
            --card-bg: #f8f9fc;
        }

        body {
            background-color: #ffffff;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .header-panel {
            background-color: var(--dark-header);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-top: 20px;
            position: relative;
        }

        .info-card {
            background-color: var(--card-bg);
            border: none;
            border-radius: 15px;
            padding: 25px;
        }

        .info-label {
            color: #9a9a9a;
            font-size: 0.85rem;
        }

        .info-value {
            color: #333;
            font-weight: 500;
            margin-bottom: 15px;
        }

        .btn-custom-admin {
            background-color: #2b59ff;
            color: white;
            border-radius: 8px;
        }

        .btn-custom-keluar {
            background-color: #4a5568;
            color: white;
            border-radius: 8px;
        }

        .table thead th {
            background-color: #2b59ff;
            color: white;
            font-weight: 500;
            font-size: 0.85rem;
            text-align: center;
            border: none;
        }

        .table tbody td {
            font-size: 0.9rem;
            vertical-align: middle;
        }

        .avg-column {
            background-color: #eef2ff;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="container pb-5">
        <!-- Header -->
        <div class="header-panel shadow mb-4">
            <h2 class="fw-bold m-0">Sekolah Gracia Bandung</h2>
            <p class="m-0 opacity-75">Laporan Hasil Belajar Siswa</p>
        </div>

        <!-- Navigasi -->
        <div class="d-flex justify-content-between align-items-center mb-4 px-2">
            <div>
                <span class="text-muted">Halo, </span><span class="fw-bold"><?php echo $_SESSION['nama']; ?></span>
                <span class="badge bg-primary ms-1"><?php echo ($level_user == '2') ? 'Admin' : 'Siswa'; ?></span>
            </div>
            <div class="d-flex gap-2">
                <?php if ($level_user == '2'): ?>
                    <a href="tambah_nilai.php" class="btn btn-custom-admin btn-sm px-3 shadow-sm">Input Nilai</a>
                    <a href="tambah_komentar.php" class="btn btn-outline-primary btn-sm px-3 shadow-sm bg-white">Beri Komentar</a>
                <?php endif; ?>
                <a href="logout.php" class="btn btn-custom-keluar btn-sm px-4 shadow-sm">Keluar</a>
            </div>

        </div>

        <!-- Informasi Siswa -->
        <div class="card info-card shadow-sm mb-5">
            <h5 class="fw-bold mb-4">Informasi Siswa</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="info-label">Nama Siswa</div>
                    <div class="info-value"><?php echo $user_data['nama']; ?></div>
                    <div class="info-label">NIS</div>
                    <div class="info-value">2024-1842</div>
                    <div class="info-label">Tahun Ajaran</div>
                    <div class="info-value">2025-2026</div>
                </div>
                <div class="col-md-6">
                    <div class="info-label">NISN</div>
                    <div class="info-value"><?php echo $user_data['nisn']; ?></div>
                    <div class="info-label">Kelas</div>
                    <div class="info-value">Kelas <?php echo $user_data['kelas']; ?></div>
                    <div class="info-label">Semester</div>
                    <div class="info-value">Semester Genap</div>
                </div>
            </div>
        </div>

        <!-- Tabel Prestasi Akademik -->
        <h5 class="fw-bold mb-3 px-2">Prestasi Akademik</h5>
        <div class="card border-0 shadow-sm overflow-hidden mb-5" style="border-radius: 15px;">
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
                            <?php if ($level_user == '2') echo "<th>Aksi</th>"; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT tabel_nilai.*, mata_pelajaran.matapelajaran FROM tabel_nilai JOIN mata_pelajaran ON tabel_nilai.id_matapelajaran = mata_pelajaran.id_matapelajaran";
                        if ($level_user == '1') {
                            $sql .= " WHERE tabel_nilai.nisn = '$nisn_login'";
                        }
                        $query = mysqli_query($conn, $sql);
                        while ($row = mysqli_fetch_assoc($query)) {
                            $rata = ($row['pe1'] + $row['pe2'] + $row['pe3'] + $row['pe4'] + $row['pe5'] + $row['pe6'] + $row['pts'] + $row['asaj']) / 8;
                            echo "<tr class='text-center'>";
                            echo "<td class='text-start ps-4 fw-bold'>" . $row['matapelajaran'] . "</td>";
                            echo "<td>" . $row['pe1'] . "</td><td>" . $row['pe2'] . "</td><td>" . $row['pe3'] . "</td><td>" . $row['pe4'] . "</td><td>" . $row['pe5'] . "</td><td>" . $row['pe6'] . "</td><td>" . $row['pts'] . "</td><td>" . $row['asaj'] . "</td>";
                            echo "<td class='avg-column text-primary'>" . number_format($rata, 2) . "</td>";
                            if ($level_user == '2') {
                                echo "<td>
                                    <a href='edit_nilai.php?id=" . $row['id_nilai'] . "' class='btn btn-sm btn-outline-warning border-0'><i class='bi bi-pencil'></i></a>
                                    <a href='hapus_nilai.php?id=" . $row['id_nilai'] . "' class='btn btn-sm btn-outline-danger border-0' onclick='return confirm(\"Hapus?\")'><i class='bi bi-trash'></i></a>
                                  </td>";
                            }
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php
        // Logika Kehadiran & Pelanggaran (Diletakkan di dalam container)
        $q_hadir = mysqli_query($conn, "SELECT * FROM kehadiran WHERE nisn = '$nisn_login'");
        $h = (mysqli_num_rows($q_hadir) > 0) ? mysqli_fetch_assoc($q_hadir) : ['total_hari' => 0, 'hadir' => 0, 'terlambat' => 0, 'izin' => 0, 'alpha' => 0, 'sakit' => 0];
        $persen = ($h['total_hari'] > 0) ? ($h['hadir'] / $h['total_hari']) * 100 : 0;

        $q_p = mysqli_query($conn, "SELECT * FROM pelanggaran WHERE nisn = '$nisn_login'");
        $total_p = mysqli_num_rows($q_p);
        ?>

        <div class="row">
            <!-- Kehadiran -->
            <div class="col-md-6 mb-4">
                <div class="card info-card shadow-sm h-100 border-0">
                    <div class="card-body">
                        <h5 class="fw-bold mb-4">Kehadiran</h5>
                        <div class="text-center mb-4">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; border: 6px solid #f0f2f5; margin: 0 auto;">
                                <h5 class="m-0 fw-bold"><?php echo number_format($persen, 1); ?>%</h5>
                            </div>
                            <p class="mt-2 text-muted small">Tingkat Kehadiran</p>
                        </div>
                        <div class="px-2 small">
                            <div class="d-flex justify-content-between mb-2 text-muted"><span>Total Hari</span><span class="fw-bold"><?php echo $h['total_hari']; ?></span></div>
                            <div class="d-flex justify-content-between mb-2 text-success"><span>Hari Hadir</span><span class="fw-bold"><?php echo $h['hadir']; ?></span></div>
                            <div class="d-flex justify-content-between mb-2 text-danger"><span>Hari Tidak Hadir</span><span class="fw-bold"><?php echo ($h['total_hari'] - $h['hadir']); ?></span></div>
                            <div class="d-flex justify-content-between mb-2" style="color: #ffc107;"><span>Terlambat</span><span class="fw-bold"><?php echo $h['terlambat']; ?></span></div>
                            <div class="d-flex justify-content-between mb-2 text-muted"><span>Izin</span><span class="fw-bold"><?php echo $h['izin']; ?></span></div>
                            <div class="d-flex justify-content-between text-muted"><span>Alpha</span><span class="fw-bold"><?php echo $h['alpha']; ?></span></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pelanggaran -->
            <div class="col-md-6 mb-4">
                <div class="card info-card shadow-sm h-100 border-0">
                    <div class="card-body d-flex flex-column">
                        <h5 class="fw-bold mb-4">Pelanggaran Sekolah</h5>
                        <div class="flex-grow-1 text-center py-4">
                            <?php if ($total_p == 0): ?>
                                <i class="bi bi-check-circle text-success fs-1"></i>
                                <p class="small text-muted mt-2">Tidak ada catatan pelanggaran</p>
                            <?php else: ?>
                                <!-- Loop pelanggaran di sini jika ada -->
                            <?php endif; ?>
                        </div>
                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                            <span class="fw-bold small">Total Pelanggaran:</span>
                            <span class="badge bg-dark rounded-pill"><?php echo $total_p; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- End Row -->
    </div> <!-- End Container -->

    <!-- Bagian Komentar & Masukan Guru -->
    <div class="card info-card shadow-sm border-0 mt-4" style="border-radius: 15px;">
        <div class="card-header bg-light border-0 py-3" style="border-radius: 15px 15px 0 0;">
            <h6 class="fw-bold m-0 text-dark">Komentar & Masukan Guru</h6>
        </div>
        <div class="card-body p-4">
            <?php
            $q_komentar = mysqli_query($conn, "SELECT * FROM komentar_guru WHERE nisn = '$nisn_login' ORDER BY id_komentar ASC");
            if (mysqli_num_rows($q_komentar) > 0) {
                while ($k = mysqli_fetch_assoc($q_komentar)) {
            ?>
                    <div class="d-flex align-items-start mb-4">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; flex-shrink: 0;">
                            <i class="bi bi-person text-secondary"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1 small"><?php echo $k['judul_komentar']; ?></h6>
                            <p class="text-muted small mb-0"><?php echo $k['isi_komentar']; ?></p>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<p class='text-center text-muted small my-4'>Belum ada komentar dari guru.</p>";
            }
            ?>
        </div>
    </div>

    <!-- Footer Laporan -->
    <div class="d-flex justify-content-between mt-4 px-2 text-muted" style="font-size: 0.75rem;">
        <span>Laporan dibuat pada: <?php echo date('d F Y'); ?></span>
        <span>Peninjauan berikutnya: 15 Mei 2026</span>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>