<?php
session_start();
// Proteksi halaman: jika belum login, lempar ke login.php
if (!isset($_SESSION['nisn'])) {
    header("location:login.php");
    exit();
}
include 'koneksi.php';

$level_user = $_SESSION['level']; // 1 = Siswa, 2 = Guru/Superadmin
$nisn_login = $_SESSION['nisn'];
$nama_user  = $_SESSION['nama'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Utama - E-Rapor</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar-brand {
            font-weight: bold;
        }

        .badge-nilai {
            font-size: 0.9rem;
        }
    </style>
</head>

<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">E-RAPOR SMK</a>
            <div class="d-flex align-items-center">
                <span class="navbar-text text-white me-3 d-none d-md-inline">
                    Selamat Datang, <strong><?php echo $nama_user; ?></strong>
                    <span class="badge bg-light text-primary ms-1">
                        <?php echo ($level_user == '2') ? 'Guru/Admin' : 'Siswa'; ?>
                    </span>
                </span>
                <a href="logout.php" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin keluar?')">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">Daftar Nilai Siswa</h5>

                <!-- Tombol Tambah Nilai: Hanya muncul jika level_user adalah 2 (Guru/Superadmin) -->
                <?php if ($level_user == '2') : ?>
                    <a href="tambah_nilai.php" class="btn btn-success btn-sm d-flex align-items-center">
                        <i class="bi bi-plus-lg me-1"></i> + Tambah Nilai
                    </a>
                <?php endif; ?>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>NISN</th>
                                <th>Nama Siswa</th>
                                <th>Mata Pelajaran</th>
                                <th class="text-center">Nilai</th>
                                <?php if ($level_user == '2') echo "<th class='text-center'>Aksi</th>"; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            /**
                             * QUERY LOGIC:
                             * Menggabungkan (JOIN) tabel_nilai dengan data_siswa dan mata_pelajaran
                             * agar bisa menampilkan nama lengkap dan nama mapel.
                             */
                            $sql = "SELECT tabel_nilai.*, data_siswa.nama, mata_pelajaran.matapelajaran 
                                    FROM tabel_nilai 
                                    JOIN data_siswa ON tabel_nilai.nisn = data_siswa.nisn 
                                    JOIN mata_pelajaran ON tabel_nilai.id_matapelajaran = mata_pelajaran.id_matapelajaran";

                            // Jika user adalah SISWA (level 1), filter agar hanya melihat nilainya sendiri
                            if ($level_user == '1') {
                                $sql .= " WHERE tabel_nilai.nisn = '$nisn_login'";
                            }

                            $query = mysqli_query($conn, $sql);

                            if (mysqli_num_rows($query) == 0) {
                                echo "<tr><td colspan='5' class='text-center text-muted py-4'>Tidak ada data nilai ditemukan.</td></tr>";
                            } else {
                                while ($row = mysqli_fetch_assoc($query)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['nisn'] . "</td>";
                                    echo "<td>" . $row['nama'] . "</td>";
                                    echo "<td>" . $row['matapelajaran'] . "</td>";

                                    // Pewarnaan Badge Nilai
                                    $bg_nilai = ($row['nilai'] < 75) ? 'bg-danger' : 'bg-primary';
                                    echo "<td class='text-center'><span class='badge $bg_nilai px-3 badge-nilai'>" . $row['nilai'] . "</span></td>";

                                    // Aksi CRUD: Hanya untuk Guru/Admin
                                    if ($level_user == '2') {
                                        echo "<td class='text-center'>
                                                <div class='btn-group' role='group'>
                                                    <a href='edit_nilai.php?id=" . $row['id_nilai'] . "' class='btn btn-warning btn-sm'>Edit</a>
                                                    <a href='hapus_nilai.php?id=" . $row['id_nilai'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Hapus nilai ini?')\">Hapus</a>
                                                </div>
                                              </td>";
                                    }
                                    echo "</tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white text-muted small">
                Data ditarik secara real-time dari database lokal.
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>