<?php
session_start();
if (!isset($_SESSION['nisn']) || $_SESSION['level'] != '2') {
    header("location:login.php");
    exit();
}
include 'koneksi.php';

// Ambil ID nilai dari URL
$id_nilai = $_GET['id'];

// Ambil data nilai lama
$query_lama = mysqli_query($conn, "SELECT n.*, s.nama, m.matapelajaran 
                                   FROM tabel_nilai n 
                                   JOIN data_siswa s ON n.nisn = s.nisn 
                                   JOIN mata_pelajaran m ON n.id_matapelajaran = m.id_matapelajaran 
                                   WHERE n.id_nilai = '$id_nilai'");
$data = mysqli_fetch_assoc($query_lama);

if (isset($_POST['update_nilai'])) {
    $pe1  = $_POST['pe1'];
    $pe2  = $_POST['pe2'];
    $pe3  = $_POST['pe3'];
    $pe4  = $_POST['pe4'];
    $pe5  = $_POST['pe5'];
    $pe6  = $_POST['pe6'];
    $pts  = $_POST['pts'];
    $asaj = $_POST['asaj'];

    $update = mysqli_query($conn, "UPDATE tabel_nilai SET 
                pe1='$pe1', pe2='$pe2', pe3='$pe3', pe4='$pe4', 
                pe5='$pe5', pe6='$pe6', pts='$pts', asaj='$asaj' 
                WHERE id_nilai = '$id_nilai'");

    if ($update) {
        echo "<script>alert('Nilai berhasil diperbarui!'); window.location='admin_cek_siswa.php?nisn=" . $data['nisn'] . "';</script>";
    } else {
        echo "Gagal mengupdate: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Nilai - <?php echo $data['nama']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow border-0" style="border-radius: 15px;">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0">Edit Nilai: <?php echo $data['matapelajaran']; ?></h5>
                        <small><?php echo $data['nama']; ?> (<?php echo $data['nisn']; ?>)</small>
                    </div>
                    <div class="card-body p-4">
                        <form action="" method="POST">
                            <div class="row g-3">
                                <?php
                                $fields = ['pe1', 'pe2', 'pe3', 'pe4', 'pe5', 'pe6', 'pts', 'asaj'];
                                foreach ($fields as $f): ?>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold text-uppercase small"><?php echo $f; ?></label>
                                        <input type="number" name="<?php echo $f; ?>" class="form-control" value="<?php echo $data[$f]; ?>" min="0" max="100" required>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="d-flex justify-content-between mt-5">
                                <a href="admin_cek_siswa.php?nisn=<?php echo $data['nisn']; ?>" class="btn btn-outline-secondary px-4">Batal</a>
                                <button type="submit" name="update_nilai" class="btn btn-primary px-5">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>