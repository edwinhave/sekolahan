<?php
session_start();
// Proteksi: Hanya Guru/Admin (level 2) yang boleh mengakses
if (!isset($_SESSION['nisn']) || $_SESSION['level'] != '2') {
    header("location:menu.php");
    exit();
}
include 'koneksi.php';

if (isset($_POST['simpan'])) {
    $nisn      = mysqli_real_escape_string($conn, $_POST['nisn']);
    $id_mapel  = mysqli_real_escape_string($conn, $_POST['id_mapel']);

    // Menangkap 8 komponen nilai baru
    $pe1  = mysqli_real_escape_string($conn, $_POST['pe1']);
    $pe2  = mysqli_real_escape_string($conn, $_POST['pe2']);
    $pe3  = mysqli_real_escape_string($conn, $_POST['pe3']);
    $pe4  = mysqli_real_escape_string($conn, $_POST['pe4']);
    $pe5  = mysqli_real_escape_string($conn, $_POST['pe5']);
    $pe6  = mysqli_real_escape_string($conn, $_POST['pe6']);
    $pts  = mysqli_real_escape_string($conn, $_POST['pts']);
    $asaj = mysqli_real_escape_string($conn, $_POST['asaj']);

    // Query INSERT disesuaikan dengan struktur tabel yang baru
    $query = "INSERT INTO tabel_nilai (nisn, id_matapelajaran, pe1, pe2, pe3, pe4, pe5, pe6, pts, asaj) 
              VALUES ('$nisn', '$id_mapel', '$pe1', '$pe2', '$pe3', '$pe4', '$pe5', '$pe6', '$pts', '$asaj')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data Nilai Berhasil Disimpan!'); window.location='menu.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Panel Admin - Input Nilai Rinci</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7f6;
        }

        .card {
            border-radius: 15px;
            border: none;
        }

        .form-label {
            font-weight: 500;
            color: #555;
        }

        .section-title {
            border-left: 4px solid #0d6efd;
            padding-left: 10px;
            margin-bottom: 20px;
            font-weight: bold;
        }
    </style>
</head>

<body class="py-5">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0">Input Komponen Nilai Siswa</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="" method="POST">
                            <div class="section-title text-primary">Data Identitas</div>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Pilih Murid</label>
                                    <select name="nisn" class="form-select" required>
                                        <option value="">-- Pilih Siswa --</option>
                                        <?php
                                        $siswa = mysqli_query($conn, "SELECT nisn, nama FROM data_siswa WHERE level='1'");
                                        while ($s = mysqli_fetch_assoc($siswa)) {
                                            echo "<option value='" . $s['nisn'] . "'>" . $s['nisn'] . " - " . $s['nama'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mata Pelajaran</label>
                                    <select name="id_mapel" class="form-select" required>
                                        <option value="">-- Pilih Mapel --</option>
                                        <?php
                                        $mapel = mysqli_query($conn, "SELECT * FROM mata_pelajaran");
                                        while ($m = mysqli_fetch_assoc($mapel)) {
                                            echo "<option value='" . $m['id_matapelajaran'] . "'>" . $m['matapelajaran'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="section-title text-success">Komponen Penilaian (PE)</div>
                            <div class="row g-3 mb-4">
                                <?php for ($i = 1; $i <= 6; $i++): ?>
                                    <div class="col-md-2 col-4">
                                        <label class="form-label small">PE <?php echo $i; ?></label>
                                        <input type="number" name="pe<?php echo $i; ?>" class="form-control" min="0" max="100" value="0" required>
                                    </div>
                                <?php endfor; ?>
                            </div>

                            <div class="section-title text-warning">Ujian Akhir</div>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Nilai PTS</label>
                                    <input type="number" name="pts" class="form-control" min="0" max="100" value="0" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nilai ASAJ</label>
                                    <input type="number" name="asaj" class="form-control" min="0" max="100" value="0" required>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between pt-3">
                                <a href="menu.php" class="btn btn-light px-4">Batal</a>
                                <button type="submit" name="simpan" class="btn btn-primary px-5">Simpan Semua Nilai</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>