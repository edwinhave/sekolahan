<?php
session_start();
// Proteksi Keamanan: Hanya Admin/Guru Level 2 yang diizinkan memantau monitoring data
if (!isset($_SESSION['nisn']) || $_SESSION['level'] != '2') {
    header("location:login.php");
    exit();
}
include 'koneksi.php';

$nisn_terpilih = isset($_GET['nisn']) ? mysqli_real_escape_string($conn, trim($_GET['nisn'])) : '';
$semester_terpilih = isset($_GET['semester']) ? mysqli_real_escape_string($conn, $_GET['semester']) : 'Genap';

// --- LOGIKA UPDATE STATUS ABSENSI SECARA INSTAN VIA POST ---
if (isset($_POST['update_absensi_inline'])) {
    $id_kh = mysqli_real_escape_string($conn, $_POST['id_kehadiran']);
    $status_baru = mysqli_real_escape_string($conn, $_POST['status_baru']);

    mysqli_query($conn, "UPDATE kehadiran SET status='$status_baru' WHERE id_kehadiran='$id_kh'");
    echo "<script>alert('Status kehadiran siswa berhasil diperbarui!'); window.location='admin_cek_siswa.php?nisn=$nisn_terpilih&semester=$semester_terpilih';</script>";
    exit();
}

$user_data = null;
if (!empty($nisn_terpilih)) {
    $q_user = mysqli_query($conn, "SELECT * FROM data_siswa WHERE nisn = '$nisn_terpilih' AND level = '1'");
    $user_data = mysqli_fetch_assoc($q_user);
}

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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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

        .select2-container--default .select2-selection--single {
            height: 46px !important;
            padding: 8px 12px;
            border: 1px solid #dee2e6;
            border-radius: 10px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #212529 !important;
            line-height: 28px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 44px !important;
        }
    </style>
</head>

<body class="pb-5">

    <div class="container">
        <div class="header-panel mb-4 d-flex justify-content-between align-items-center" style="background-color: #64B5F6;">
            <div>
                <h4 class="fw-bold m-0 text-white">Monitoring Akademik</h4>
                <small class="text-white opacity-75">Panel Kontrol Guru & Admin</small>
            </div>
            <a href="menu.php?kelas=IX&semester=<?php echo $semester_terpilih; ?>" class="btn btn-outline-light btn-sm px-4">
                <i class="bi bi-house-door me-1"></i> Dashboard
            </a>
        </div>

        <div class="card info-card mb-4">
            <div class="card-body p-4">
                <form action="" method="GET" id="form-monitor" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label fw-bold small text-muted text-uppercase">Pilih Siswa</label>
                        <select name="nisn" id="select-siswa" class="form-select shadow-sm" style="width: 100%;">
                            <option value="">-- Ketik Nama atau NISN Siswa --</option>
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
                        <label class="form-label fw-bold small text-muted text-uppercase">Periode Semester</label>
                        <select name="semester" id="select-semester" class="form-select" style="height: 46px; border-radius: 10px;">
                            <option value="Genap" <?php echo ($semester_terpilih == 'Genap') ? 'selected' : ''; ?>>Semester 2 (Genap)</option>
                            <option value="Ganjil" <?php echo ($semester_terpilih == 'Ganjil') ? 'selected' : ''; ?>>Semester 1 (Ganjil)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="p-2 bg-light rounded text-center border" style="height: 46px; display: flex; align-items: center; justify-content: center; gap: 10px; border-radius: 10px !important;">
                            <small class="text-muted">Total Siswa:</small>
                            <span class="fw-bold"><?php echo $total_siswa; ?> Orang</span>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($user_data): ?>
            <div class="alert alert-white shadow-sm border-0 mb-4 d-flex align-items-center justify-content-between" style="border-radius: 15px; background: white;">
                <div class="d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="bi bi-person-fill fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold m-0"><?php echo $user_data['nama']; ?></h5>
                        <small class="text-muted">NISN: <?php echo $user_data['nisn']; ?> | Kelas: <?php echo $user_data['kelas']; ?> | <span class="badge bg-dark">Semester <?php echo $semester_terpilih; ?></span></small>
                    </div>
                </div>
                <a href="cetak.php?nisn=<?php echo $nisn_terpilih; ?>&semester=<?php echo $semester_terpilih; ?>" target="_blank" class="btn btn-outline-primary shadow-sm px-4 rounded-pill">
                    <i class="bi bi-printer me-1"></i> Cetak Rapor
                </a>
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
                            $q_nilai = mysqli_query($conn, "SELECT n.*, m.matapelajaran FROM tabel_nilai n JOIN mata_pelajaran m ON n.id_matapelajaran = m.id_matapelajaran WHERE n.nisn = '$nisn_terpilih' AND n.semester='$semester_terpilih'");
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
                                        <td><a href="edit_nilai.php?id=<?php echo $n['id_nilai']; ?>" class="btn btn-edit btn-sm px-3 rounded-pill shadow-sm"><i class="bi bi-pencil-square me-1"></i> Edit</a></td>
                                    </tr>
                            <?php
                                endwhile;
                            } else {
                                echo "<tr><td colspan='10' class='text-center py-5 text-muted'>Siswa ini belum memiliki data nilai di semester ini.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card info-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                                <h6 class="fw-bold m-0"><i class="bi bi-calendar-check me-2 text-success"></i>Rasio Kehadiran</h6>
                            </div>
                            <?php
                            $q_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM kehadiran WHERE nisn = '$nisn_terpilih' AND semester='$semester_terpilih'");
                            $total_hari = mysqli_fetch_assoc($q_total)['total'];

                            $q_hadir = mysqli_query($conn, "SELECT COUNT(*) as total FROM kehadiran WHERE nisn = '$nisn_terpilih' AND status = 'Hadir' AND semester='$semester_terpilih'");
                            $hadir = mysqli_fetch_assoc($q_hadir)['total'];

                            $q_izin_sakit = mysqli_query($conn, "SELECT COUNT(*) as total FROM kehadiran WHERE nisn = '$nisn_terpilih' AND status IN ('Izin', 'Sakit') AND semester='$semester_terpilih'");
                            $izin_sakit = mysqli_fetch_assoc($q_izin_sakit)['total'];

                            $q_alpha = mysqli_query($conn, "SELECT COUNT(*) as total FROM kehadiran WHERE nisn = '$nisn_terpilih' AND status = 'Alpha' AND semester='$semester_terpilih'");
                            $alpha = mysqli_fetch_assoc($q_alpha)['total'];

                            $persentase = ($total_hari > 0) ? ($hadir / $total_hari) * 100 : 100;
                            ?>
                            <div class="text-center mb-4">
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 90px; height: 90px; border: 6px solid #eef2ff;">
                                    <span class="fw-bold fs-5 text-dark"><?php echo number_format($persentase, 1); ?>%</span>
                                </div>
                            </div>
                            <div class="row g-2 text-center small">
                                <div class="col-4">
                                    <div class="p-1 bg-light rounded"><small class="text-muted d-block">Hadir</small><span class="fw-bold text-success"><?php echo $hadir; ?></span></div>
                                </div>
                                <div class="col-4">
                                    <div class="p-1 bg-light rounded"><small class="text-muted d-block">I/S</small><span class="fw-bold text-warning"><?php echo $izin_sakit; ?></span></div>
                                </div>
                                <div class="col-4">
                                    <div class="p-1 bg-light rounded"><small class="text-muted d-block">Alpha</small><span class="fw-bold text-danger"><?php echo $alpha; ?></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8 mb-4">
                    <div class="card info-card h-100 overflow-hidden">
                        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold m-0"><i class="bi bi-clock-history me-2 text-warning"></i>Log Ketidakhadiran Siswa</h6>
                            <a href="tambah_kehadiran.php?nisn=<?php echo $nisn_terpilih; ?>" class="btn btn-xs btn-primary py-1 px-2 rounded" style="font-size: 11px;">+ Entri Log</a>
                        </div>
                        <div class="table-responsive" style="max-height: 220px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr class="text-center">
                                        <th class="text-start ps-3">Tanggal Absen</th>
                                        <th>Status Saat Ini</th>
                                        <th>Ubah Otoritas Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Mengambil log khusus siswa yang statusnya bukan 'Hadir' (Izin/Sakit/Alpha)
                                    $q_log = mysqli_query($conn, "SELECT * FROM kehadiran WHERE nisn='$nisn_terpilih' AND status != 'Hadir' AND semester='$semester_terpilih' ORDER BY tanggal DESC");
                                    if (mysqli_num_rows($q_log) > 0) {
                                        while ($log = mysqli_fetch_assoc($q_log)) {
                                            $badge_col = ($log['status'] == 'Alpha') ? 'bg-danger' : (($log['status'] == 'Izin') ? 'bg-warning text-dark' : 'bg-info');
                                    ?>
                                            <tr class="text-center">
                                                <td class="text-start ps-3 fw-bold text-slate-600"><?php echo date('d M Y', strtotime($log['tanggal'])); ?></td>
                                                <td><span class="badge <?php echo $badge_col; ?>"><?php echo $log['status']; ?></span></td>
                                                <td>
                                                    <form action="" method="POST" class="d-flex gap-1 justify-content-center">
                                                        <input type="hidden" name="id_kehadiran" value="<?php echo $log['id_kehadiran']; ?>">
                                                        <select name="status_baru" class="form-select form-select-sm py-0 shadow-sm" style="width: auto; font-size: 11px;" onchange="this.form.submit()">
                                                            <option value="Hadir">Set Hadir</option>
                                                            <option value="Izin" <?php echo ($log['status'] == 'Izin') ? 'selected' : ''; ?>>Izin</option>
                                                            <option value="Sakit" <?php echo ($log['status'] == 'Sakit') ? 'selected' : ''; ?>>Sakit</option>
                                                            <option value="Alpha" <?php echo ($log['status'] == 'Alpha') ? 'selected' : ''; ?>>Alpha</option>
                                                        </select>
                                                        <input type="hidden" name="update_absensi_inline" value="1">
                                                    </form>
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    } else {
                                        echo "<tr><td colspan='3' class='text-center py-4 text-muted small italic'>Siswa rajin! Belum ada riwayat ketidakhadiran di semester ini.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card info-card mb-4 overflow-hidden">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold m-0"><i class="bi bi-exclamation-triangle me-2 text-danger"></i>Catatan Pelanggaran &amp; Disiplin</h6>
                    <a href="tambah_pelanggaran.php?nisn=<?php echo $nisn_terpilih; ?>" class="btn btn-sm btn-outline-danger py-1 px-3 rounded-pill">+ Tambah Riwayat</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th class="text-start ps-4">Tanggal Kejadian</th>
                                <th class="text-start">Jenis Pelanggaran</th>
                                <th>Bobot Poin</th>
                                <th>Manajemen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $q_p = mysqli_query($conn, "SELECT * FROM pelanggaran WHERE nisn = '$nisn_terpilih' ORDER BY tanggal DESC");
                            if (mysqli_num_rows($q_p) > 0) {
                                while ($p = mysqli_fetch_assoc($q_p)) {
                            ?>
                                    <tr>
                                        <td class="text-start ps-4 text-muted small"><?php echo date('d M Y', strtotime($p['tanggal'])); ?></td>
                                        <td class="text-start fw-bold text-danger"><?php echo $p['jenis_pelanggaran']; ?></td>
                                        <td class="font-extrabold text-danger">5 Poin</td>
                                        <td>
                                            <a href="hapus_pelanggaran.php?id=<?php echo $p['id_pelanggaran']; ?>" class="btn btn-sm btn-outline-danger px-3 rounded-pill" onclick="return confirm('Apakah Anda yakin ingin menghapus data pelanggaran ini?')"><i class="bi bi-trash"></i> Hapus</a>
                                        </td>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center py-4 text-muted small italic'>Tidak ada catatan riwayat pelanggaran. Siswa berkelakuan baik.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php else: ?>
            <div class="text-center py-5">
                <div class="mb-4"><i class="bi bi-radar text-primary" style="font-size: 6rem; opacity: 0.5;"></i></div>
                <h5 class="text-muted fw-bold">Siap Memantau Data Siswa?</h5>
                <p class="text-muted small">Silakan pilih nama atau ketik NISN siswa pada searchbox di atas.</p>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#select-siswa').select2({
                placeholder: "-- Ketik Nama atau NISN Siswa --",
                allowClear: true
            });
            $('#select-siswa, #select-semester').on('change', function() {
                if ($('#select-siswa').val() !== "") {
                    $('#form-monitor').submit();
                }
            });
        });
    </script>
</body>

</html>