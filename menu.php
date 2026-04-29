<?php
session_start();
if (!isset($_SESSION['nisn'])) {
    header("location:login.php");
    exit();
}
include 'koneksi.php';

$level_user = $_SESSION['level'];
$nisn_login = $_SESSION['nisn'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Menu Utama - E-Rapor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <nav class="navbar navbar-dark bg-primary mb-4">
        <div class="container">
            <span class="navbar-brand mb-0 h1">Sistem Nilai Sekolah</span>
            <div class="d-flex border-start ps-3">
                <span class="text-white me-3">Halo, <strong><?php echo $_SESSION['nama']; ?></strong></span>
                <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="card shadow">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Nilai Siswa</h5>
                <?php if ($level_user == '2'): // Tombol khusus Guru 
                ?>
                    <button class="btn btn-success btn-sm">+ Tambah Nilai</button>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <table class="table table-hover table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>NISN</th>
                            <th>Nama Siswa</th>
                            <th>Mata Pelajaran</th>
                            <th>Nilai</th>
                            <?php if ($level_user == '2') echo "<th>Aksi</th>"; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Logika Query: 
                        // Jika Guru (2), tampilkan semua nilai. 
                        // Jika Siswa (1), hanya tampilkan nilai miliknya sendiri.
                        $sql = "SELECT tabel_nilai.*, data_siswa.nama, mata_pelajaran.matapelajaran 
                            FROM tabel_nilai 
                            JOIN data_siswa ON tabel_nilai.nisn = data_siswa.nisn 
                            JOIN mata_pelajaran ON tabel_nilai.id_matapelajaran = mata_pelajaran.id_matapelajaran";

                        if ($level_user == '1') {
                            $sql .= " WHERE tabel_nilai.nisn = '$nisn_login'";
                        }

                        $query = mysqli_query($conn, $sql);

                        if (mysqli_num_rows($query) == 0) {
                            echo "<tr><td colspan='5' class='text-center'>Belum ada data nilai.</td></tr>";
                        }

                        while ($row = mysqli_fetch_assoc($query)) {
                            echo "<tr>";
                            echo "<td>" . $row['nisn'] . "</td>";
                            echo "<td>" . $row['nama'] . "</td>";
                            echo "<td>" . $row['matapelajaran'] . "</td>";
                            echo "<td><span class='badge bg-primary px-3'>" . $row['nilai'] . "</span></td>";

                            if ($level_user == '2') {
                                echo "<td>
                                    <a href='#' class='btn btn-warning btn-sm'>Edit</a>
                                    <a href='#' class='btn btn-danger btn-sm'>Hapus</a>
                                  </td>";
                            }
                            echo "</tr>";
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