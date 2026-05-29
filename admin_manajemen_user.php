<?php
session_start();
// Proteksi: Hanya Super Admin/Guru (level 2) yang bisa masuk
if (!isset($_SESSION['nisn']) || $_SESSION['level'] != '2') {
    header("location:login.php");
    exit();
}
include 'koneksi.php';

// Notifikasi pesan sukses/gagal
$notif = isset($_GET['msg']) ? $_GET['msg'] : '';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Admin - Manajemen Pengguna & Waiting List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fc;
            font-family: 'Segoe UI', sans-serif;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .badge-waiting {
            background-color: #ffc107;
            color: #000;
        }

        .badge-approved {
            background-color: #198754;
            color: #fff;
        }

        .badge-rejected {
            background-color: #dc3545;
            color: #fff;
        }
    </style>
</head>

<body class="py-4">

    <div class="container">
        <!-- HEADER PANEL -->
        <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-dark text-white rounded-3 shadow-sm">
            <div>
                <h4 class="fw-bold m-0"><i class="bi bi-people-fill me-2 text-info"></i>Manajemen Pengguna</h4>
                <small class="opacity-75">Sistem Verifikasi Waiting List Akun Sekolah</small>
            </div>
            <a href="menu.php" class="btn btn-outline-light btn-sm px-4"><i class="bi bi-house-door"></i> Dashboard</a>
        </div>

        <?php if ($notif == 'approved'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> Akun pengguna berhasil **Disetujui (Approved)** dan sekarang sudah bisa login!
                <button type="button" class="btn-close" data-bs-dismiss='alert'></button>
            </div>
        <?php elseif ($notif == 'rejected'): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> Pendaftaran akun berhasil **Ditolak (Rejected)**.
                <button type="button" class="btn-close" data-bs-dismiss='alert'></button>
            </div>
        <?php elseif ($notif == 'deleted'): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-trash-fill me-2"></i> Data pengguna berhasil dihapus dari sistem.
                <button type="button" class="btn-close" data-bs-dismiss='alert'></button>
            </div>
        <?php endif; ?>

        <!-- TABEL 1: WAITING LIST (ANTREAN VERIFIKASI) -->
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark fw-bold py-3">
                <i class="bi bi-hourglass-split me-2"></i> Pendaftaran Butuh Verifikasi (Waiting List)
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th class="ps-3">Identitas Pengguna</th>
                            <th>Email</th>
                            <th>Role / Hak Akses</th>
                            <th>Status Antrean</th>
                            <th class="text-center">Aksi Konfirmasi</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        <?php
                        $q_wait = mysqli_query($conn, "SELECT * FROM data_siswa WHERE status_akun = 'Waiting' ORDER BY nama ASC");
                        if (mysqli_num_rows($q_wait) > 0) {
                            while ($w = mysqli_fetch_assoc($q_wait)) {
                                $role = ($w['level'] == '2') ? 'Guru/Admin' : 'Siswa';
                                echo "<tr>
                                        <td class='ps-3 fw-bold'>" . $w['nama'] . "<br><span class='text-muted fw-normal' style='font-size:0.75rem;'>NISN: " . $w['nisn'] . " | Kelas: " . $w['kelas'] . "</span></td>
                                        <td>" . $w['email'] . "</td>
                                        <td><span class='badge bg-secondary'>" . $role . "</span></td>
                                        <td><span class='badge badge-waiting'>Waiting Approval</span></td>
                                        <td class='text-center'>
                                            <a href='proses_verifikasi.php?aksi=approve&nisn=" . $w['nisn'] . "' class='btn btn-success btn-sm px-3 rounded-pill me-1'><i class='bi bi-check-lg'></i> Setujui</a>
                                            <a href='proses_verifikasi.php?aksi=reject&nisn=" . $w['nisn'] . "' class='btn btn-outline-danger btn-sm px-3 rounded-pill' onclick='return confirm(\"Tolak pendaftaran ini?\")'><i class='bi bi-x-lg'></i> Tolak</a>
                                        </td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-4 text-muted italic'>Hore! Tidak ada antrean pendaftaran baru saat ini.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TABEL 2: DATA PENGGUNA AKTIF (APPROVED & REJECTED) -->
        <div class="card">
            <div class="card-header bg-white py-3 fw-bold text-dark border-bottom">
                <i class="bi bi-shield-check me-2 text-success"></i> Seluruh Database Pengguna Terdaftar
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th class="ps-3">Nama / NISN</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status Akun</th>
                            <th class="text-center">Manajemen</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        <?php
                        $q_all = mysqli_query($conn, "SELECT * FROM data_siswa WHERE status_akun IN ('Approved', 'Rejected') ORDER BY status_akun ASC, nama ASC");
                        if (mysqli_num_rows($q_all) > 0) {
                            while ($a = mysqli_fetch_assoc($q_all)) {
                                $role = ($a['level'] == '2') ? 'Guru/Admin' : 'Siswa';
                                $badge_status = ($a['status_akun'] == 'Approved') ? 'badge-approved' : 'badge-rejected';
                                echo "<tr>
                                        <td class='ps-3 fw-bold'>" . $a['nama'] . "<br><span class='text-muted fw-normal' style='font-size:0.75rem;'>NISN: " . $a['nisn'] . "</span></td>
                                        <td>" . $a['email'] . "</td>
                                        <td><span class='badge bg-light text-dark border'>" . $role . "</span></td>
                                        <td><span class='badge " . $badge_status . "'>" . $a['status_akun'] . "</span></td>
                                        <td class='text-center'>
                                            <a href='proses_verifikasi.php?aksi=delete&nisn=" . $a['nisn'] . "' class='btn btn-sm btn-outline-danger px-2 py-1' onclick='return confirm(\"Apakah Anda yakin ingin menghapus permanen user ini dari database?\")' title='Hapus User'><i class='bi bi-trash3-fill'></i> Hapus</a>
                                        </td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-4 text-muted'>Belum ada data pengguna aktif.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>