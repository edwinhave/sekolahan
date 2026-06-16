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
    // MENANGKAP INPUT SEMESTER BARU
    $semester  = mysqli_real_escape_string($conn, $_POST['semester']);

    // Menangkap 8 komponen nilai baru
    $pe1  = mysqli_real_escape_string($conn, $_POST['pe1']);
    $pe2  = mysqli_real_escape_string($conn, $_POST['pe2']);
    $pe3  = mysqli_real_escape_string($conn, $_POST['pe3']);
    $pe4  = mysqli_real_escape_string($conn, $_POST['pe4']);
    $pe5  = mysqli_real_escape_string($conn, $_POST['pe5']);
    $pe6  = mysqli_real_escape_string($conn, $_POST['pe6']);
    $pts  = mysqli_real_escape_string($conn, $_POST['pts']);
    $asaj = mysqli_real_escape_string($conn, $_POST['asaj']);

    // Query INSERT disesuaikan dengan menambahkan kolom semester
    $query = "INSERT INTO tabel_nilai (nisn, id_matapelajaran, semester, pe1, pe2, pe3, pe4, pe5, pe6, pts, asaj) 
              VALUES ('$nisn', '$id_mapel', '$semester', '$pe1', '$pe2', '$pe3', '$pe4', '$pe5', '$pe6', '$pts', '$asaj')";

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Library CSS Select2 untuk Dropdown Pencarian -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        body {
            background-color: #f4f7f6;
            font-family: 'Segoe UI', sans-serif;
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

        /* Styling Kustom Penyelarasan Select2 dengan Bootstrap 5 */
        .select2-container--default .select2-selection--single {
            height: 40px !important;
            padding: 5px 10px;
            border: 1px solid #dee2e6;
            border-radius: 8px !important;
            background-color: #fff;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #212529 !important;
            line-height: 28px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px !important;
        }

        .select2-dropdown {
            border: 1px solid #dee2e6 !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
    </style>
</head>

<body class="py-5">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Input Komponen Nilai Siswa</h5>
                        <a href="menu.php" class="btn btn-outline-light btn-sm"><i class="bi bi-house-door"></i> Kembali</a>
                    </div>
                    <div class="card-body p-4">
                        <form action="" method="POST">
                            <div class="section-title text-primary">Data Identitas & Periode</div>

                            <div class="row mb-4">
                                <!-- 1. PILIH MURID (SELECT2) -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Pilih Murid</label>
                                    <select name="nisn" id="select-siswa" class="form-select" required style="width: 100%;">
                                        <option value="">-- Ketik Nama / NISN --</option>
                                        <?php
                                        $siswa = mysqli_query($conn, "SELECT nisn, nama FROM data_siswa WHERE level='1' ORDER BY nama ASC");
                                        while ($s = mysqli_fetch_assoc($siswa)) {
                                            echo "<option value='" . $s['nisn'] . "'>" . $s['nisn'] . " - " . $s['nama'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!-- 2. PILIH MATA PELAJARAN -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Mata Pelajaran</label>
                                    <select name="id_mapel" class="form-select" style="border-radius: 8px; height: 40px;" required>
                                        <option value="">-- Pilih Mapel --</option>
                                        <?php
                                        $mapel = mysqli_query($conn, "SELECT * FROM mata_pelajaran ORDER BY matapelajaran ASC");
                                        while ($m = mysqli_fetch_assoc($mapel)) {
                                            echo "<option value='" . $m['id_matapelajaran'] . "'>" . $m['matapelajaran'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!-- 3. FITUR BARU: PILIH SEMESTER INPUT -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Periode Semester</label>
                                    <select name="semester" class="form-select" style="border-radius: 8px; height: 40px;" required>
                                        <option value="Ganjil">Semester Ganjil</option>
                                        <option value="Genap">Semester Genap</option>
                                    </select>
                                </div>
                            </div>

                            <div class="section-title text-success">Komponen Penilaian (PE)</div>
                            <div class="row g-3 mb-4">
                                <?php for ($i = 1; $i <= 6; $i++): ?>
                                    <div class="col-md-2 col-4">
                                        <label class="form-label small">PE <?php echo $i; ?></label>
                                        <input type="number" name="pe<?php echo $i; ?>" class="form-control" min="0" max="100" value="0" style="border-radius: 8px;" required>
                                    </div>
                                <?php endfor; ?>
                            </div>

                            <div class="section-title text-warning">Ujian Akhir</div>
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nilai PTS</label>
                                    <input type="number" name="pts" class="form-control" min="0" max="100" value="0" style="border-radius: 8px;" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nilai ASAJ</label>
                                    <input type="number" name="asaj" class="form-control" min="0" max="100" value="0" style="border-radius: 8px;" required>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between pt-3">
                                <a href="menu.php" class="btn btn-light px-4">Batal</a>
                                <button type="submit" name="simpan" class="btn btn-primary px-5" style="border-radius: 8px;">Simpan Semua Nilai</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Pendukung Integrasi Select2 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#select-siswa').select2({
                placeholder: "-- Ketik Nama atau NISN Siswa --",
                allowClear: true
            });
        });
    </script>
</body>

</html>