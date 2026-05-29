<?php
session_start();
// Proteksi: Hanya Guru/Admin (level 2) yang boleh mengakses
if (!isset($_SESSION['nisn']) || $_SESSION['level'] != '2') {
    header("location:menu.php");
    exit();
}
include 'koneksi.php';

// Inisialisasi variabel untuk mode Edit
$is_edit = false;
$id_komentar_edit = "";
$nisn_edit = "";
$judul_edit = "";
$isi_edit = "";

// --- 1. PROSES DELETE (HAPUS KOMENTAR) ---
if (isset($_GET['hapus'])) {
    $id_hapus = mysqli_real_escape_string($conn, $_GET['hapus']);
    $q_delete = "DELETE FROM komentar_guru WHERE id_komentar = '$id_hapus'";
    if (mysqli_query($conn, $q_delete)) {
        echo "<script>alert('Komentar berhasil dihapus!'); window.location='tambah_komentar.php';</script>";
    }
}

// --- 2. PROSES AMBIL DATA UNTUK EDIT ---
if (isset($_GET['edit'])) {
    $is_edit = true;
    $id_komentar_edit = mysqli_real_escape_string($conn, $_GET['edit']);
    $q_get_edit = mysqli_query($conn, "SELECT * FROM komentar_guru WHERE id_komentar = '$id_komentar_edit'");
    if ($dt_edit = mysqli_fetch_assoc($q_get_edit)) {
        $nisn_edit  = $dt_edit['nisn'];
        $judul_edit = $dt_edit['judul_komentar'];
        $isi_edit   = $dt_edit['isi_komentar'];
    }
}

// --- 3. PROSES INSERT ATAU UPDATE ---
if (isset($_POST['simpan_komentar'])) {
    $nisn           = mysqli_real_escape_string($conn, $_POST['nisn']);
    $judul_komentar = mysqli_real_escape_string($conn, $_POST['judul_komentar']);
    $isi_komentar   = mysqli_real_escape_string($conn, $_POST['isi_komentar']);
    $tanggal        = date('Y-m-d');

    if (isset($_POST['mode_edit']) && $_POST['mode_edit'] == 'true') {
        $id_update = mysqli_real_escape_string($conn, $_POST['id_komentar']);
        $query = "UPDATE komentar_guru SET 
                  nisn = '$nisn', 
                  judul_komentar = '$judul_komentar', 
                  isi_komentar = '$isi_komentar' 
                  WHERE id_komentar = '$id_update'";
        $msg = "Komentar berhasil diperbarui!";
    } else {
        $query = "INSERT INTO komentar_guru (nisn, judul_komentar, isi_komentar, tanggal_input) 
                  VALUES ('$nisn', '$judul_komentar', '$isi_komentar', '$tanggal')";
        $msg = "Komentar berhasil ditambahkan!";
    }

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('$msg'); window.location='tambah_komentar.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Komentar Guru - E-Rapor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
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

        .select2-container--default .select2-selection--single {
            height: 40px !important;
            padding: 5px 10px;
            border: 1px solid #dee2e6;
            border-radius: 8px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #212529 !important;
            line-height: 28px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px !important;
        }
    </style>
</head>

<body class="py-5">

    <div class="container">
        <div class="row justify-content-center">

            <div class="col-lg-10 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><?php echo $is_edit ? '<i class="bi bi-pencil-square me-2 text-warning"></i>Edit Komentar / Masukan' : '<i class="bi bi-chat-left-text-fill me-2"></i>Berikan Masukan & Komentar'; ?></h5>
                        <a href="menu.php" class="btn btn-outline-light btn-sm"><i class="bi bi-house-door"></i> Menu Utama</a>
                    </div>
                    <div class="card-body p-4">
                        <form action="" method="POST">
                            <input type="hidden" name="mode_edit" value="<?php echo $is_edit ? 'true' : 'false'; ?>">
                            <input type="hidden" name="id_komentar" value="<?php echo $id_komentar_edit; ?>">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Pilih Siswa</label>
                                    <select name="nisn" id="select-siswa" class="form-select" required style="width: 100%;">
                                        <option value="">-- Ketik Nama atau NISN --</option>
                                        <?php
                                        $siswa = mysqli_query($conn, "SELECT nisn, nama FROM data_siswa WHERE level='1' ORDER BY nama ASC");
                                        while ($s = mysqli_fetch_assoc($siswa)) {
                                            $selected = ($nisn_edit == $s['nisn']) ? 'selected' : '';
                                            echo "<option value='" . $s['nisn'] . "' $selected>" . $s['nisn'] . " - " . $s['nama'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Kategori Komentar</label>
                                    <input type="text" name="judul_komentar" class="form-control" style="border-radius: 8px; height: 40px;" placeholder="Contoh: Kinerja Keseluruhan" value="<?php echo htmlspecialchars($judul_edit); ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted text-uppercase">Isi Pesan / Masukan Berharga</label>
                                <textarea name="isi_komentar" class="form-control" rows="4" style="border-radius: 8px;" placeholder="Tuliskan masukan evaluasi belajar siswa di sini..." required><?php echo htmlspecialchars($isi_edit); ?></textarea>
                            </div>

                            <div class="d-flex justify-content-end gap-2 pt-2">
                                <?php if ($is_edit): ?>
                                    <a href="tambah_komentar.php" class="btn btn-light px-4">Batal Edit</a>
                                <?php endif; ?>
                                <button type="submit" name="simpan_komentar" class="btn <?php echo $is_edit ? 'btn-warning' : 'btn-dark'; ?> px-5">
                                    <?php echo $is_edit ? 'Perbarui Komentar' : 'Kirim Komentar'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-10">
                <div class="card shadow">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 fw-bold text-dark"><i class="bi bi-journal-text me-2 text-primary"></i>Riwayat Komentar Guru</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="tabel-komentar">
                            <thead class="table-light small text-uppercase">
                                <tr>
                                    <th class="ps-3">Siswa</th>
                                    <th>Kategori</th>
                                    <th>Isi Komentar</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <?php
                                $q_list = mysqli_query($conn, "SELECT k.*, s.nama FROM komentar_guru k JOIN data_siswa s ON k.nisn = s.nisn ORDER BY k.id_komentar DESC");
                                if (mysqli_num_rows($q_list) > 0) {
                                    while ($row = mysqli_fetch_assoc($q_list)) {
                                        // Kita selipkan atribut data-nisn pada setiap baris tr untuk disaring oleh JavaScript
                                        echo "<tr class='baris-komentar' data-nisn='" . $row['nisn'] . "'>
                                                <td class='ps-3 fw-bold text-primary'>" . $row['nama'] . "<br><span class='text-muted fw-normal' style='font-size:0.75rem;'>NISN: " . $row['nisn'] . "</span></td>
                                                <td class='fw-medium'>" . $row['judul_komentar'] . "</td>
                                                <td class='text-muted'>" . $row['isi_komentar'] . "</td>
                                                <td class='text-center'>
                                                    <div class='btn-group' role='group'>
                                                        <a href='tambah_komentar.php?edit=" . $row['id_komentar'] . "' class='btn btn-sm btn-outline-warning py-1 px-2'><i class='bi bi-pencil-square'></i></a>
                                                        <a href='tambah_komentar.php?hapus=" . $row['id_komentar'] . "' class='btn btn-sm btn-outline-danger py-1 px-2' onclick='return confirm(\"Apakah Anda yakin?\")'><i class='bi bi-trash3-fill'></i></a>
                                                    </div>
                                                </td>
                                              </tr>";
                                    }
                                } else {
                                    echo "<tr id='baris-kosong'><td colspan='4' class='text-center py-4 text-muted'>Belum ada riwayat komentar.</td></tr>";
                                }
                                ?>
                                <tr id="pencarian-kosong" style="display: none;">
                                    <td colspan="4" class="text-center py-4 text-muted italic">Tidak ada riwayat komentar untuk siswa ini.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // 1. Inisialisasi Select2
            $('#select-siswa').select2({
                placeholder: "-- Ketik Nama atau NISN Siswa --",
                allowClear: true
            });

            // 2. Fungsi Logika Live-Filtering Otomatis
            function filterKomentar() {
                var nisnDipilih = $('#select-siswa').val();

                if (nisnDipilih === "") {
                    // Jika searchbox kosong, tampilkan kembali SEMUA baris komentar
                    $('.baris-komentar').show();
                    $('#pencarian-kosong').hide();
                } else {
                    // Sembunyikan semua baris terlebih dahulu
                    $('.baris-komentar').hide();

                    // Hanya tampilkan baris yang memiliki atribut data-nisn sesuai pilihan searchbox
                    var barisCocok = $('.baris-komentar[data-nisn="' + nisnDipilih + '"]');

                    if (barisCocok.length > 0) {
                        barisCocok.show();
                        $('#pencarian-kosong').hide();
                    } else {
                        // Jika siswa tersebut belum punya komentar sama sekali, tampilkan baris info kosong
                        $('#pencarian-kosong').show();
                    }
                }
            }

            // Run fungsi filter saat searchbox berubah pilihan (on change)
            $('#select-siswa').on('change', function() {
                filterKomentar();
            });

            // Jalankan sekali saat halaman pertama dimuat (antisipasi mode Edit agar langsung ter-filter)
            filterKomentar();
        });
    </script>
</body>

</html>