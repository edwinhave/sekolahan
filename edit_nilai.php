<?php
session_start();
// Proteksi: Hanya Guru/Admin (level 2) yang boleh mengakses
if (!isset($_SESSION['nisn']) || $_SESSION['level'] != '2') {
    header("location:menu.php");
    exit();
}
include 'koneksi.php';

// Mengambil ID Nilai dari parameter URL
if (!isset($_GET['id'])) {
    header("location:menu.php");
    exit();
}
$id_edit = mysqli_real_escape_string($conn, $_GET['id']);

// Ambil data nilai lama untuk ditampilkan di form
$query_lama = mysqli_query($conn, "SELECT tabel_nilai.*, data_siswa.nama, mata_pelajaran.matapelajaran 
                                   FROM tabel_nilai 
                                   JOIN data_siswa ON tabel_nilai.nisn = data_siswa.nisn 
                                   JOIN mata_pelajaran ON tabel_nilai.id_matapelajaran = mata_pelajaran.id_matapelajaran 
                                   WHERE id_nilai = '$id_edit'");
$data = mysqli_fetch_assoc($query_lama);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='menu.php';</script>";
    exit();
}

// Proses Update saat tombol ditekan
if (isset($_POST['update'])) {
    $pe1  = mysqli_real_escape_string($conn, $_POST['pe1']);
    $pe2  = mysqli_real_escape_string($conn, $_POST['pe2']);
    $pe3  = mysqli_real_escape_string($conn, $_POST['pe3']);
    $pe4  = mysqli_real_escape_string($conn, $_POST['pe4']);
    $pe5  = mysqli_real_escape_string($conn, $_POST['pe5']);
    $pe6  = mysqli_real_escape_string($conn, $_POST['pe6']);
    $pts  = mysqli_real_escape_string($conn, $_POST['pts']);
    $asaj = mysqli_real_escape_string($conn, $_POST['asaj']);

    $sql_update = "UPDATE tabel_nilai SET 
                    pe1='$pe1', pe2='$pe2', pe3='$pe3', pe4='$pe4', pe5='$pe5', pe6='$pe6', 
                    pts='$pts', asaj='$asaj' 
                    WHERE id_nilai='$id_edit'";

    if (mysqli_query($conn, $sql_update)) {
        echo "<script>alert('Data Berhasil Diperbarui!'); window.location='menu.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Nilai - Panel Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7f6;
        }

        .card {
            border-radius: 15px;
            border: none;
        }

        .section-title {
            border-left: 4px solid #ffc107;
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
                    <div class="card-header bg-warning text-dark py-3 text-center">
                        <h5 class="mb-0 fw-bold">Edit Komponen Nilai</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-info py-2">
                            Mengedit nilai untuk: <strong><?php echo $data['nama']; ?></strong>
                            (Mapel: <?php echo $data['matapelajaran']; ?>)
                        </div>

                        <form action="" method="POST">
                            <div class="section-title text-dark">Komponen Penilaian (PE)</div>
                            <div class="row g-3 mb-4">
                                <?php for ($i = 1; $i <= 6; $i++): ?>
                                    <div class="col-md-2 col-4">
                                        <label class="form-label small">PE <?php echo $i; ?></label>
                                        <input type="number" name="pe<?php echo $i; ?>" class="form-control"
                                            min="0" max="100" value="<?php echo $data['pe' . $i]; ?>" required>
                                    </div>
                                <?php endfor; ?>
                            </div>

                            <div class="section-title text-dark">Ujian Akhir</div>
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nilai PTS</label>
                                    <input type="number" name="pts" class="form-control"
                                        min="0" max="100" value="<?php echo $data['pts']; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nilai ASAJ</label>
                                    <input type="number" name="asaj" class="form-control"
                                        min="0" max="100" value="<?php echo $data['asaj']; ?>" required>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between pt-3 border-top">
                                <a href="menu.php" class="btn btn-light px-4">Batal</a>
                                <button type="submit" name="update" class="btn btn-warning px-5 fw-bold">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>