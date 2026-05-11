<?php
session_start();
if (!isset($_SESSION['nisn']) || $_SESSION['level'] != '2') {
    header("location:login.php");
    exit();
}
include 'koneksi.php';

// Menangkap NISN yang dipilih dari dropdown
$nisn_terpilih = isset($_GET['nisn']) ? mysqli_real_escape_string($conn, $_GET['nisn']) : '';

// Data Siswa Terpilih
$user_data = null;
if ($nisn_terpilih) {
    $q_user = mysqli_query($conn, "SELECT * FROM data_siswa WHERE nisn = '$nisn_terpilih'");
    $user_data = mysqli_fetch_assoc($q_user);
}

// Statistik Singkat untuk Header Admin
$q_count_siswa = mysqli_query($conn, "SELECT COUNT(*) as total FROM data_siswa WHERE level = '1'");
$total_siswa = mysqli_fetch_assoc($q_count_siswa)['total'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Monitoring Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --dark-header: #0a0a1a;
            --primary-blue: #2b59ff;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Segoe UI', sans-serif;
        }

        .header-panel {
            background-color: var(--dark-header);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
        }

        .info-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .table thead th {
            background-color: var(--primary-blue);
            color: white;
            font-size: 0.8rem;
            vertical-align: middle;
        }

        .btn-edit {
            background-color: #ffc107;
            border: none;
            color: #000;
            font-size: 0.75rem;
        }

        .btn-edit:hover {
            background-color: #e0a800;
        }
    </style>
</head>

<body class="pb-5">

    <div class="container">
        <div class="header-panel mb-4 d-flex justify-content-between align-items-center" style="background-color: #64B5F6;">
            <div>
                <h4 class="fw-bold m-0">Monitoring Akademik</h4>
                <small class="opacity-75">Panel Kontrol Guru & Admin</small>
            </div>
            <a href="menu.php" class="btn btn-outline-light btn-sm px-4">
                <i class="bi bi-house-door me-1"></i> Dashboard
            </a>
        </div>

        <div class="card info-card mb-4">
            <div class="card-body p-4">
                <form action="" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label fw-bold small text-muted text-uppercase">Pilih Siswa</label>
                        <select name="nisn" class="form-select shadow-sm" onchange="this.form.submit()" style="border-radius: 10px;">
                            <option value="">-- Pilih Nama atau NISN Siswa --</option>
                            <?php
                            $siswa_list = mysqli_query($conn, "SELECT nisn, nama FROM data_siswa WHERE level='1' ORDER BY nama ASC");
                            while ($s = mysqli_fetch_assoc($siswa_list)) {
                                $sel = ($nisn_terpilih == $s['nisn']) ? 'selected' : '';
                                echo "<option value='" . $s['nisn'] . "' $sel>" . $s['nisn'] . " - " . $s['nama'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2 bg-light rounded text-center border">
                            <small class="text-muted d-block">Total Siswa Terdaftar</small>
                            <span class="fw-bold"><?php echo $total_siswa; ?> Orang</span>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($user_data): ?>
            <div class="alert alert-white shadow-sm border-0 mb-4 d-flex align-items-center" style="border-radius: 15px; background: white;">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                    <i class="bi bi-person-fill fs-4"></i>
                </div>
                <div>
                    <h5 class="fw-bold m-0"><?php echo $user_data['nama']; ?></h5>
                    <small class="text-muted">NISN: <?php echo $user_data['nisn']; ?> | Kelas: <?php echo $user_data['kelas']; ?></small>
                </div>
            </div>

            <div class="card info-card mb-4 overflow-hidden">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold m-0"><i class="bi bi-trophy me-2 text-primary"></i>Rincian Nilai Akademik</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="text-center">
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
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $q_nilai = mysqli_query($conn, "SELECT n.*, m.matapelajaran FROM tabel_nilai n JOIN mata_pelajaran m ON n.id_matapelajaran = m.id_matapelajaran WHERE n.nisn = '$nisn_terpilih'");
                            if (mysqli_num_rows($q_nilai) > 0) {
                                while ($n = mysqli_fetch_assoc($q_nilai)):
                            ?>
                                    <tr class="text-center align-middle">
                                        <td class="text-start ps-4 fw-bold"><?php echo $n['matapelajaran']; ?></td>
                                        <td><?php echo $n['pe1']; ?></td>
                                        <td><?php echo $n['pe2']; ?></td>
                                        <td><?php echo $n['pe3']; ?></td>
                                        <td><?php echo $n['pe4']; ?></td>
                                        <td><?php echo $n['pe5']; ?></td>
                                        <td><?php echo $n['pe6']; ?></td>
                                        <td><?php echo $n['pts']; ?></td>
                                        <td><?php echo $n['asaj']; ?></td>
                                        <td>
                                            <a href="edit_nilai.php?id=<?php echo $n['id_nilai']; ?>" class="btn btn-edit btn-sm px-3 rounded-pill shadow-sm">
                                                <i class="bi bi-pencil-square me-1"></i> Edit
                                            </a>
                                        </td>
                                    </tr>
                            <?php
                                endwhile;
                            } else {
                                echo "<tr><td colspan='10' class='text-center py-5 text-muted'>Siswa ini belum memiliki data nilai.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card info-card h-100">
                        <div class="card-body">
                            <h6 class="fw-bold mb-4 border-bottom pb-2"><i class="bi bi-calendar-check me-2 text-success"></i>Data Kehadiran <a href="tambah_kehadiran.php?nisn=<?php echo $nisn_terpilih; ?>" class="btn btn-sm btn-outline-success">Update</a></h6>

                            <?php
                            $q_h = mysqli_query($conn, "SELECT * FROM kehadiran WHERE nisn = '$nisn_terpilih'");
                            $h = mysqli_fetch_assoc($q_h) ?: ['total_hari' => 0, 'hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0, 'terlambat' => 0];
                            ?>
                            <div class="row g-3 text-center">
                                <div class="col-4">
                                    <div class="p-2 bg-light rounded">
                                        <small class="text-muted d-block small">Hadir</small>
                                        <span class="fw-bold text-success"><?php echo $h['hadir']; ?></span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 bg-light rounded">
                                        <small class="text-muted d-block small">Izin/Sakit</small>
                                        <span class="fw-bold text-warning"><?php echo $h['izin'] + $h['sakit']; ?></span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 bg-light rounded">
                                        <small class="text-muted d-block small">Alpha</small>
                                        <span class="fw-bold text-danger"><?php echo $h['alpha']; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 p-2 border rounded bg-white small">
                                Total Hari Efektif: <strong><?php echo $h['total_hari']; ?> Hari</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card info-card h-100">
                        <div class="card-body">
                            <h6 class="fw-bold mb-4 border-bottom pb-2"><i class="bi bi-exclamation-triangle me-2 text-danger"></i>Catatan Pelanggaran</h6>
                            <a href="tambah_pelanggaran.php?nisn=<?php echo $nisn_terpilih; ?>" class="btn btn-sm btn-outline-danger">+ Tambah</a>
                            <div class="overflow-auto" style="max-height: 150px;">
                                <?php
                                $q_p = mysqli_query($conn, "SELECT * FROM pelanggaran WHERE nisn = '$nisn_terpilih'");
                                if (mysqli_num_rows($q_p) > 0) {
                                    while ($p = mysqli_fetch_assoc($q_p)) {
                                        echo "<div class='d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded'>
                                            <span class='small'>" . $p['jenis_pelanggaran'] . "</span>
                                            <span class='badge bg-danger' style='font-size:0.6rem;'>" . date('d M Y', strtotime($p['tanggal'])) . "</span>
                                          </div>";
                                    }
                                } else {
                                    echo "<div class='text-center py-4 text-muted small italic'>Tidak ada catatan pelanggaran untuk siswa ini.</div>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        <?php else: ?>
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-radar text-primary" style="font-size: 6rem; opacity: 0.5;"></i>
                </div>
                <h5 class="text-muted fw-bold">Siap Memantau Data Siswa?</h5>
                <p class="text-muted small">Silakan pilih nama siswa dari menu dropdown di atas<br>untuk menarik laporan akademik lengkap.</p>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>