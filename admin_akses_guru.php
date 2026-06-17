<?php
session_start();
// Proteksi: Hanya Super Admin (Level 2) yang bisa masuk panel ini
if (!isset($_SESSION['nisn']) || $_SESSION['level'] != '2') {
    header("location:login.php");
    exit();
}
include 'koneksi.php';

// Tangkap NISN Guru yang dipilih dari URL
$nisn_guru = isset($_GET['nisn']) ? mysqli_real_escape_string($conn, $_GET['nisn']) : '';

// Ambil profil data guru tersebut
$q_guru = mysqli_query($conn, "SELECT * FROM data_siswa WHERE nisn = '$nisn_guru' AND level = '3'");
$guru = mysqli_fetch_assoc($q_guru);

if (!$guru) {
    echo "<script>alert('Data Guru Tidak Ditemukan!'); window.location='admin_manajemen_user.php';</script>";
    exit();
}

// --- LOGIKA PROSES SIMPAN CHECKBOX MASSAL ---
if (isset($_POST['simpan_akses'])) {
    // 1. Hapus semua akses mengajar lama milik guru ini agar tidak duplikat data
    mysqli_query($conn, "DELETE FROM mengajar_guru WHERE nisn_guru = '$nisn_guru'");

    // 2. Jika ada checkbox mapel yang dicentang (True), masukkan ke database
    if (isset($_POST['mapel_dipilih']) && is_array($_POST['mapel_dipilih'])) {
        foreach ($_POST['mapel_dipilih'] as $id_mapel) {
            $id_mapel_clean = mysqli_real_escape_string($conn, $id_mapel);
            mysqli_query($conn, "INSERT INTO mengajar_guru (nisn_guru, id_matapelajaran) VALUES ('$nisn_guru', '$id_mapel_clean')");
        }
    }
    echo "<script>alert('Hak akses mata pelajaran Guru berhasil diperbarui!'); window.location='admin_manajemen_user.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Admin - Atur Hak Akses Mengajar Guru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Serif+Display&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        * {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        :root {
            --sidebar: #4a6858;
            --bg: #b8c5cc;
            --card: #d4dce0;
            --btn: #7a3b2e;
        }

        body {
            background: #b8c5cc;
        }
    </style>
</head>

<body class="p-5 flex justify-center items-center min-h-screen">

    <div class="bg-[#d4dce0] rounded-2xl shadow-xl p-6 w-full max-w-2xl border border-white border-opacity-40">
        <!-- HEADER PANEL -->
        <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-400 border-opacity-30">
            <div>
                <h2 class="text-xl font-extrabold text-slate-700" style="font-family:'DM Serif Display',serif;">Atur Otoritas Mengajar</h2>
                <p class="text-xs text-slate-500 mt-0.5">Berikan akses kontrol entri komponen nilai</p>
            </div>
            <a href="admin_manajemen_user.php" class="bg-gray-500 hover:bg-gray-600 text-white text-xs font-bold px-3 py-2 rounded-lg no-underline shadow">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <!-- INFORMASI PROFIL GURU -->
        <div class="bg-slate-100 bg-opacity-60 rounded-xl p-4 mb-5 flex items-center gap-3 border border-slate-300">
            <div class="bg-[#4a6858] text-white rounded-full w-12 h-12 flex items-center justify-center font-bold text-lg shadow-inner">
                <?php echo substr($guru['nama'], 0, 1); ?>
            </div>
            <div>
                <h4 class="font-bold text-slate-700 text-sm"><?php echo $guru['nama']; ?></h4>
                <p class="text-xs text-slate-500">NUPTK/NISN: <?php echo $guru['nisn']; ?> · Akun Level: <span class="bg-amber-600 text-white px-1.5 py-0.5 rounded text-[10px] font-bold">Guru Mapel</span></p>
            </div>
        </div>

        <!-- FORM CHECKBOX HAK MAPEL -->
        <form action="" method="POST">
            <p class="text-xs font-bold text-slate-600 mb-3 tracking-wider"><i class="bi bi-check2-square text-emerald-700"></i> SILAHKAN CENTANG MATA PELAJARAN YANG DIAJAR:</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 bg-white p-4 rounded-xl shadow-inner border border-slate-300 max-h-[280px] overflow-y-auto mb-5">
                <?php
                // Ambil seluruh mata pelajaran terdaftar di sekolah
                $q_all_mapel = mysqli_query($conn, "SELECT * FROM mata_pelajaran ORDER BY matapelajaran ASC");
                while ($m = mysqli_fetch_assoc($q_all_mapel)) {

                    // Cek apakah guru ini sudah memiliki hak akses (True) untuk mapel ini di tabel relasi
                    $q_cek_akses = mysqli_query($conn, "SELECT id_mengajar FROM mengajar_guru WHERE nisn_guru = '$nisn_guru' AND id_matapelajaran = '" . $m['id_matapelajaran'] . "'");
                    $is_checked = (mysqli_num_rows($q_cek_akses) > 0) ? 'checked' : '';
                ?>
                    <label class="flex items-center gap-3 p-2 bg-slate-50 rounded-lg hover:bg-slate-100 border border-slate-200 cursor-pointer transition-colors">
                        <!-- CHECKBOX UTAMA -->
                        <input type="checkbox" name="mapel_dipilih[]" value="<?php echo $m['id_matapelajaran']; ?>" <?php echo $is_checked; ?> class="w-4 h-4 accent-[#7a3b2e] rounded cursor-pointer">
                        <div class="text-xs font-semibold text-slate-700">
                            <?php echo $m['matapelajaran']; ?>
                            <span class="text-[10px] text-slate-400 block font-normal">ID: <?php echo $m['id_matapelajaran']; ?></span>
                        </div>
                    </label>
                <?php
                }
                ?>
            </div>

            <!-- BUTTON SIMPAN MASSAL -->
            <div class="flex gap-3">
                <button type="submit" name="simpan_akses" class="bg-[#7a3b2e] hover:bg-[#5c2b20] text-white font-bold py-3 px-4 rounded-xl text-xs flex-1 transition-all shadow shadow-amber-900/40">
                    <i class="bi bi-shield-check"></i> Simpan &amp; Terapkan Otoritas Mengajar
                </button>
            </div>
        </form>
    </div>

</body>

</html>