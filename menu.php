<?php
session_start();
if (!isset($_SESSION['nisn'])) {
    header("location:login.php");
    exit();
}
include 'koneksi.php';

$level_user = $_SESSION['level']; // 2 = Admin, 3 = Guru Mapel, 1 = Siswa
$nisn_login = $_SESSION['nisn'];

// Ambil profil data pengguna aktif
$query_user = mysqli_query($conn, "SELECT * FROM data_siswa WHERE nisn = '$nisn_login'");
$user_data = mysqli_fetch_assoc($query_user);

// Filter Periode Global - Secara default langsung diarahkan ke Kelas X (Sesuai database kamu)
$kelas_aktif = isset($_GET['kelas']) ? mysqli_real_escape_string($conn, $_GET['kelas']) : 'X';
$semester_aktif = isset($_GET['semester']) ? mysqli_real_escape_string($conn, $_GET['semester']) : 'Genap';
$mapel_aktif = isset($_GET['mapel']) ? mysqli_real_escape_string($conn, $_GET['mapel']) : '';

// Otomatisasi penentuan mata pelajaran default berdasarkan hak akses mengajar (Level 2 & 3)
if (($level_user == '2' || $level_user == '3') && empty($mapel_aktif)) {
    if ($level_user == '3') {
        // Jika Guru (Level 3), ambil mapel pertama miliknya di tabel relasi mengajar_guru
        $q_first = mysqli_query($conn, "SELECT id_matapelajaran FROM mengajar_guru WHERE nisn_guru = '$nisn_login' LIMIT 1");
    } else {
        // Jika Admin (Level 2), ambil mapel pertama secara global
        $q_first = mysqli_query($conn, "SELECT id_matapelajaran FROM mata_pelajaran ORDER BY matapelajaran ASC LIMIT 1");
    }
    if ($f_mapel = mysqli_fetch_assoc($q_first)) {
        $mapel_aktif = $f_mapel['id_matapelajaran'];
    }
}

// --- PROSES BACKEND: SIMPAN MASSAL SPREADSHEET NILAI (Bisa diakses Admin & Guru) ---
if (isset($_POST['mass_save_nilai']) && ($level_user == '2' || $level_user == '3')) {
    $mapel_id = mysqli_real_escape_string($conn, $_POST['hidden_mapel']);
    foreach ($_POST['nh'] as $sid => $nh_val) {
        $sid = mysqli_real_escape_string($conn, $sid);
        $nh  = mysqli_real_escape_string($conn, $nh_val);
        $pe  = mysqli_real_escape_string($conn, $_POST['pe'][$sid]);
        $asaj = mysqli_real_escape_string($conn, $_POST['asaj'][$sid]);
        $sikap = mysqli_real_escape_string($conn, $_POST['sikap'][$sid]);

        $cek = mysqli_query($conn, "SELECT id_nilai FROM tabel_nilai WHERE nisn = '$sid' AND id_matapelajaran = '$mapel_id' AND semester = '$semester_aktif'");
        if (mysqli_num_rows($cek) > 0) {
            mysqli_query($conn, "UPDATE tabel_nilai SET pe1='$nh', pe2='$pe', pe3='$asaj', pts='$sikap' WHERE nisn = '$sid' AND id_matapelajaran = '$mapel_id' AND semester = '$semester_aktif'");
        } else {
            mysqli_query($conn, "INSERT INTO tabel_nilai (nisn, id_matapelajaran, semester, pe1, pe2, pe3, pts) VALUES ('$sid', '$mapel_id', '$semester_aktif', '$nh', '$pe', '$asaj', '$sikap')");
        }
    }
    echo "<script>alert('Data komponen nilai berhasil diperbarui!'); window.location='menu.php?kelas=$kelas_aktif&semester=$semester_aktif&mapel=$mapel_aktif';</script>";
}

// --- FIXED CRITICAL: PROSES BACKEND ADMIN EDIT STATUS LOG KEHADIRAN INLINE ---
if (isset($_POST['update_absensi_inline']) && $level_user == '2') {
    $id_kh = mysqli_real_escape_string($conn, $_POST['id_kehadiran']);
    $status_baru = mysqli_real_escape_string($conn, $_POST['status_baru']);
    mysqli_query($conn, "UPDATE kehadiran SET status='$status_baru' WHERE id_kehadiran='$id_kh'");
    echo "<script>alert('Status log kehadiran harian berhasil diubah!'); window.location='menu.php?kelas=$kelas_aktif&semester=$semester_aktif';</script>";
}

// --- PROSES BACKEND ADMIN: TAMBAH CATATAN PELANGGARAN ---
if (isset($_POST['tambah_pelanggaran_massal']) && $level_user == '2') {
    $nisn_p = mysqli_real_escape_string($conn, $_POST['nisn_pelanggaran']);
    $tgl_p  = mysqli_real_escape_string($conn, $_POST['tgl_pelanggaran']);
    $jenis_p = mysqli_real_escape_string($conn, $_POST['jenis_pelanggaran']);

    mysqli_query($conn, "INSERT INTO pelanggaran (nisn, tanggal, jenis_pelanggaran) VALUES ('$nisn_p', '$tgl_p', '$jenis_p')");
    echo "<script>alert('Catatan pelanggaran siswa berhasil ditambahkan!'); window.location='menu.php?kelas=$kelas_aktif&semester=$semester_aktif';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penilaian Rapor - SMP Gracia Bandung</title>
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
            --card2: #c8d4d8;
            --btn: #7a3b2e;
            --btn2: #5c2b20;
            --green: #3d7a5c;
            --blue: #2563a8;
        }

        body {
            background: var(--bg);
            min-height: 100vh;
        }

        .sidebar {
            background: var(--sidebar);
        }

        .card {
            background: var(--card);
        }

        .card2 {
            background: var(--card2);
        }

        .btn-brown {
            background: var(--btn);
        }

        .btn-brown:hover {
            background: var(--btn2);
        }

        .nav-link {
            cursor: pointer;
            padding: 8px 14px;
            border-radius: 6px;
            transition: background .2s;
            font-size: 13px;
            color: #e8f0ec;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .nav-link:hover,
        .nav-link.active {
            background: rgba(255, 255, 255, .2);
            font-weight: 600;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th {
            background: #b0bec5;
            padding: 9px 7px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
            color: #2c3e50;
            text-align: center;
        }

        td {
            padding: 8px 7px;
            font-size: 12px;
            border-bottom: 1px solid #c0cdd2;
            color: #2c3e50;
        }

        tr:hover td {
            background: rgba(255, 255, 255, .25);
        }

        select,
        input[type=number],
        input[type=date],
        input[type=text] {
            background: #c2cdd2;
            border: none;
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 13px;
            outline: none;
        }

        .score-inp {
            width: 65px !important;
            text-align: center;
            padding: 5px 3px !important;
            font-size: 12px !important;
            background: #fff !important;
            border: 1px solid #bdc3c7 !important;
        }

        .scroll-x {
            overflow-x: auto;
        }

        .progress-bar {
            height: 8px;
            border-radius: 4px;
            background: #c0cdd2;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 4px;
            background: var(--green);
            transition: width .5s;
        }

        .badge-a {
            background: #3d7a5c;
            color: #fff;
            border-radius: 4px;
            padding: 2px 7px;
            font-weight: 700;
            font-size: 11px;
        }

        .badge-b {
            background: #2563a8;
            color: #fff;
            border-radius: 4px;
            padding: 2px 7px;
            font-weight: 700;
            font-size: 11px;
        }

        .badge-c {
            background: #c67c00;
            color: #fff;
            border-radius: 4px;
            padding: 2px 7px;
            font-weight: 700;
            font-size: 11px;
        }

        .badge-d {
            background: #c0392b;
            color: #fff;
            border-radius: 4px;
            padding: 2px 7px;
            font-weight: 700;
            font-size: 11px;
        }

        .section-panel {
            display: none;
        }

        .section-panel.active {
            display: block;
        }
    </style>
</head>

<body class="flex min-h-screen overflow-x-hidden">

    <?php if ($level_user == '2' || $level_user == '3'): ?>
        <div class="sidebar flex flex-col pt-6 px-0 min-h-screen w-[215px] min-w-[215px] shadow-xl text-white">
            <div class="px-4 font-bold text-sm mb-1" style="font-family:'DM Serif Display',serif;">
                <?php echo ($level_user == '2') ? 'Admin Panel' : 'Teacher Panel'; ?>
            </div>
            <div class="px-4 text-green-200 text-xs mb-4 opacity-70">SMP Gracia Bandung</div>

            <form action="" method="GET" class="px-4 mb-4 space-y-2">
                <div>
                    <label class="text-green-200 text-[10px] font-bold block mb-1">PERIODE KELAS</label>
                    <select name="kelas" onchange="this.form.submit()" class="w-full text-xs rounded p-1 text-black">
                        <option value="XI" <?php echo ($kelas_aktif == 'XI') ? 'selected' : ''; ?>>Kelas XI</option>
                        <option value="X" <?php echo ($kelas_aktif == 'X') ? 'selected' : ''; ?>>Kelas X</option>
                        <option value="IX" <?php echo ($kelas_aktif == 'IX') ? 'selected' : ''; ?>>Kelas IX</option>
                        <option value="VIII" <?php echo ($kelas_aktif == 'VIII') ? 'selected' : ''; ?>>Kelas VIII</option>
                        <option value="VII" <?php echo ($kelas_aktif == 'VII') ? 'selected' : ''; ?>>Kelas VII</option>
                    </select>
                </div>
                <div>
                    <label class="text-green-200 text-[10px] font-bold block mb-1">SEMESTER</label>
                    <select name="semester" onchange="this.form.submit()" class="w-full text-xs rounded p-1 text-black">
                        <option value="Genap" <?php echo ($semester_aktif == 'Genap') ? 'selected' : ''; ?>>Semester 2 (Genap)</option>
                        <option value="Ganjil" <?php echo ($semester_aktif == 'Ganjil') ? 'selected' : ''; ?>>Semester 1 (Ganjil)</option>
                    </select>
                    <input type="hidden" name="mapel" value="<?php echo $mapel_aktif; ?>">
                </div>
            </form>

            <nav class="flex flex-col gap-1 px-3 mt-2 flex-1">
                <div class="nav-link active" onclick="switchTab('input-nilai', this)"><i class="bi bi-pencil-square"></i> Input Nilai</div>
                <div class="nav-link" onclick="switchTab('input-kehadiran', this)"><i class="bi bi-calendar-check"></i> Kehadiran</div>
                <div class="nav-link" onclick="switchTab('admin-pelanggaran', this)"><i class="bi bi-exclamation-triangle"></i> Pelanggaran</div>

                <?php if ($level_user == '2'): ?>
                    <div class="nav-link" onclick="switchTab('progress-input', this)"><i class="bi bi-bar-chart-steps"></i> Progress Input</div>
                    <div class="nav-link" onclick="switchTab('rekap-kelas', this)"><i class="bi bi-journal-text"></i> Rekap Kelas</div>
                    <div class="nav-link" onclick="switchTab('data-siswa', this)"><i class="bi bi-people"></i> Data Siswa</div>
                    <div class="nav-link" onclick="switchTab('kelola-mapel', this)"><i class="bi bi-book"></i> Kelola Mapel</div>
                    <a href="admin_manajemen_user.php" class="nav-link"><i class="bi bi-person-gear"></i> Manajemen User</a>
                <?php else: ?>
                    <div class="nav-link" onclick="switchTab('data-siswa', this)"><i class="bi bi-people"></i> Data Siswa</div>
                <?php endif; ?>
            </nav>

            <div class="mt-auto mb-5 px-4 pt-4 border-t border-emerald-800">
                <div class="text-green-100 text-xs font-semibold truncate mb-1"><i class="bi bi-person-badge"></i> <?php echo $user_data['nama']; ?></div>
                <a href="logout.php" class="text-red-300 hover:text-red-100 text-xs font-bold no-underline flex items-center gap-1">
                    <i class="bi bi-box-arrow-left"></i> Keluar
                </a>
            </div>
        </div>

        <div class="flex-1 p-5 overflow-y-auto">
            <div class="text-xs font-bold text-gray-500 mb-3 bg-slate-300 bg-opacity-40 px-3 py-1 rounded inline-block">
                Periode Aktif: Kelas <?php echo $kelas_aktif; ?> · Semester <?php echo $semester_aktif; ?> · TA 2026/2027
            </div>

            <?php
            $cond_kelas = "kelas = '$kelas_aktif'";
            if ($kelas_aktif == 'IX') {
                $cond_kelas = "(kelas = 'IX' OR kelas = '9')";
            } elseif ($kelas_aktif == 'X') {
                $cond_kelas = "(kelas = 'X' OR kelas = '10')";
            } elseif ($kelas_aktif == 'XI') {
                $cond_kelas = "(kelas = 'XI' OR kelas = '11')";
            } elseif ($kelas_aktif == 'VIII') {
                $cond_kelas = "(kelas = 'VIII' OR kelas = '8')";
            } elseif ($kelas_aktif == 'VII') {
                $cond_kelas = "(kelas = 'VII' OR kelas = '7')";
            }
            ?>

            <div id="input-nilai" class="section-panel active">
                <h3 class="text-xl font-bold text-gray-700 mb-4" style="font-family:'DM Serif Display',serif;">Input Nilai Kolektif</h3>
                <div class="card rounded-xl p-4 mb-4 shadow flex items-center justify-between gap-4">
                    <div class="flex-1">
                        <label class="text-xs font-bold text-gray-600 block mb-1 text-uppercase">Mata Pelajaran Terotorisasi</label>
                        <form action="" method="GET" class="inline">
                            <input type="hidden" name="kelas" value="<?php echo $kelas_aktif; ?>">
                            <input type="hidden" name="semester" value="<?php echo $semester_aktif; ?>">
                            <select name="mapel" onchange="this.form.submit()" class="w-full bg-slate-100 font-semibold text-black" style="height:38px;">
                                <?php
                                if ($level_user == '3') {
                                    $q_mapel = mysqli_query($conn, "SELECT m.* FROM mata_pelajaran m 
                                                            JOIN mengajar_guru mg ON m.id_matapelajaran = mg.id_matapelajaran 
                                                            WHERE mg.nisn_guru = '$nisn_login' 
                                                            ORDER BY m.matapelajaran ASC");
                                } else {
                                    $q_mapel = mysqli_query($conn, "SELECT * FROM mata_pelajaran ORDER BY matapelajaran ASC");
                                }

                                while ($m = mysqli_fetch_assoc($q_mapel)) {
                                    $sel_m = ($mapel_aktif == $m['id_matapelajaran']) ? 'selected' : '';
                                    echo "<option value='" . $m['id_matapelajaran'] . "' $sel_m>" . $m['matapelajaran'] . "</option>";
                                }
                                ?>
                            </select>
                        </form>
                    </div>
                </div>

                <form action="" method="POST">
                    <input type="hidden" name="hidden_mapel" value="<?php echo $mapel_aktif; ?>">
                    <div class="card rounded-2xl shadow mb-4 scroll-x overflow-hidden">
                        <table>
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th class="text-start ps-4">Nama Siswa</th>
                                    <th>NH (Harian)</th>
                                    <th>PE (Pengetahuan)</th>
                                    <th>ASAJ (Keterampilan)</th>
                                    <th>Sikap</th>
                                    <th>Nilai Akhir</th>
                                    <th>Predikat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $q_s = mysqli_query($conn, "SELECT nisn, nama FROM data_siswa WHERE level='1' AND $cond_kelas ORDER BY nama ASC");
                                if (mysqli_num_rows($q_s) > 0) {
                                    while ($s = mysqli_fetch_assoc($q_s)) {
                                        $q_v = mysqli_query($conn, "SELECT pe1, pe2, pe3, pts FROM tabel_nilai WHERE nisn='" . $s['nisn'] . "' AND id_matapelajaran='$mapel_aktif' AND semester='$semester_aktif'");
                                        $val = mysqli_fetch_assoc($q_v);
                                        $nh = $val ? $val['pe1'] : 0;
                                        $pe = $val ? $val['pe2'] : 0;
                                        $asaj = $val ? $val['pe3'] : 0;
                                        $sk = $val ? $val['pts'] : 3;
                                        $na = ($nh * 0.4) + ($pe * 0.3) + ($asaj * 0.3);
                                        $pr = ($na >= 86) ? 'A' : (($na >= 71) ? 'B' : (($na >= 56) ? 'C' : 'D'));
                                        $bg = ($pr == 'A') ? 'badge-a' : (($pr == 'B') ? 'badge-b' : (($pr == 'C') ? 'badge-c' : 'badge-d'));
                                        echo "<tr>
                          <td class='text-center font-bold text-slate-400'>" . $no++ . "</td>
                          <td class='font-semibold ps-4 text-slate-700'>" . $s['nama'] . "</td>
                          <td class='text-center'><input type='number' name='nh[" . $s['nisn'] . "]' class='score-inp' value='$nh' min='0' max='100' step='0.01'></td>
                          <td class='text-center'><input type='number' name='pe[" . $s['nisn'] . "]' class='score-inp' value='$pe' min='0' max='100' step='0.01'></td>
                          <td class='text-center'><input type='number' name='asaj[" . $s['nisn'] . "]' class='score-inp' value='$asaj' min='0' max='100' step='0.01'></td>
                          <td class='text-center'><input type='number' name='sikap[" . $s['nisn'] . "]' class='score-inp' value='$sk' min='1' max='4' step='0.1'></td>
                          <td class='text-center font-bold text-slate-800'>" . number_format($na, 2) . "</td>
                          <td class='text-center'><span class='$bg'>$pr</span></td>
                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='8' class='text-center py-5 text-slate-500 italic'>Belum ada data siswa untuk periode Kelas $kelas_aktif pada database lokal.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" name="mass_save_nilai" class="btn-brown w-full text-white font-bold py-3 rounded-xl text-sm shadow-md">💾 Simpan Perubahan Nilai Massal</button>
                </form>
            </div>

            <div id="input-kehadiran" class="section-panel">
                <h3 class="text-xl font-bold text-gray-700 mb-4" style="font-family:'DM Serif Display',serif;">Log Riwayat Absensi Siswa</h3>
                <div class="card rounded-2xl p-4 shadow mb-4">
                    <div class="text-xs font-bold text-gray-500 mb-3 text-uppercase">Daftar Ketidakhadiran Siswa</div>
                    <div class="scroll-x">
                        <table class="w-full text-center">
                            <thead>
                                <tr>
                                    <th class="text-start ps-3">Nama Siswa</th>
                                    <th>Tanggal Absen</th>
                                    <th>Status</th><?php if ($level_user == '2') echo "<th>Aksi Otoritas Admin</th>"; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // SINKRON: Menggunakan k.id_kehadiran untuk mencegah error kolom k.tanggal di laptop temanmu
                                $q_kh = mysqli_query($conn, "SELECT k.*, s.nama FROM kehadiran k JOIN data_siswa s ON k.nisn=s.nisn WHERE s.level='1' AND k.status != 'Hadir' AND k.semester='$semester_aktif' ORDER BY k.id_kehadiran DESC");
                                if (mysqli_num_rows($q_kh) > 0) {
                                    while ($kh = mysqli_fetch_assoc($q_kh)) {
                                        $badge = ($kh['status'] == 'Alpha') ? 'bg-danger' : (($kh['status'] == 'Izin') ? 'bg-warning text-dark' : 'bg-info text-white');
                                        echo "<tr>
                                    <td class='text-start ps-3 font-bold'>" . $kh['nama'] . "</td>
                                    <td>" . date('d M Y', strtotime($kh['tanggal'])) . "</td>
                                    <td><span class='badge $badge px-2 py-1 rounded'>" . $kh['status'] . "</span></td>";
                                        if ($level_user == '2') {
                                            echo "<td>
                                        <form action='' method='POST' class='d-flex gap-1 justify-content-center'>
                                            <input type='hidden' name='id_kehadiran' value='" . $kh['id_kehadiran'] . "'>
                                            <select name='status_baru' class='text-xs rounded bg-white border' onchange='this.form.submit()'>
                                                <option value='Hadir'>Set Hadir</option>
                                                <option value='Izin' " . ($kh['status'] == 'Izin' ? 'selected' : '') . ">Izin</option>
                                                <option value='Sakit' " . ($kh['status'] == 'Sakit' ? 'selected' : '') . ">Sakit</option>
                                                <option value='Alpha' " . ($kh['status'] == 'Alpha' ? 'selected' : '') . ">Alpha</option>
                                            </select>
                                            <input type='hidden' name='update_absensi_inline' value='1'>
                                        </form>
                                    </td>";
                                        }
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4' class='py-4 text-muted italic'>Tidak ada data ketidakhadiran siswa kelas $kelas_aktif tercatat.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="admin-pelanggaran" class="section-panel">
                <h3 class="text-xl font-bold text-gray-700 mb-4" style="font-family:'DM Serif Display',serif;">Log Catatan Pelanggaran &amp; Disiplin</h3>

                <?php if ($level_user == '2'): ?>
                    <div class="card rounded-xl p-4 mb-4 shadow flex flex-wrap md:flex-nowrap items-end gap-3 bg-white">
                        <form action="" method="POST" class="w-full grid grid-cols-1 md:grid-cols-4 gap-3">
                            <div>
                                <label class="text-[11px] font-bold text-gray-500 block mb-1 text-uppercase">PILIH SISWA</label>
                                <select name="nisn_pelanggaran" class="w-full text-black bg-slate-100" required style="height: 38px;">
                                    <option value="">-- Pilih Murid --</option>
                                    <?php
                                    $q_mrd = mysqli_query($conn, "SELECT nisn, nama FROM data_siswa WHERE level='1' AND $cond_kelas ORDER BY nama ASC");
                                    while ($mr = mysqli_fetch_assoc($q_mrd)) {
                                        echo "<option value='" . $mr['nisn'] . "'>" . $mr['nama'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div>
                                <label class="text-[11px] font-bold text-gray-500 block mb-1 text-uppercase">TANGGAL KEJADIAN</label>
                                <input type="date" name="tgl_pelanggaran" class="w-full bg-slate-100 text-black font-semibold" style="height: 38px;" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div>
                                <label class="text-[11px] font-bold text-gray-500 block mb-1 text-uppercase">JENIS PELANGGARAN</label>
                                <input type="text" name="jenis_pelanggaran" class="w-full bg-slate-100 text-black font-semibold" style="height: 38px;" placeholder="Contoh: Terlambat, Atribut tidak lengkap" required autocomplete="off">
                            </div>
                            <div class="flex items-end">
                                <button type="submit" name="tambah_pelanggaran_massal" class="btn-brown w-full text-white font-bold rounded-xl text-xs shadow" style="height: 38px;">
                                    <i class="bi bi-plus-circle-fill"></i> Tambah Log Kasus
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>

                <div class="card rounded-2xl shadow p-4 scroll-x bg-white">
                    <table>
                        <thead>
                            <tr>
                                <th class="text-start ps-3">Nama Siswa</th>
                                <th>Tanggal</th>
                                <th>Jenis Pelanggaran</th>
                                <th>Poin Bobot</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $q_pl = mysqli_query($conn, "SELECT p.*, s.nama FROM pelanggaran p JOIN data_siswa s ON p.nisn=s.nisn WHERE s.level='1' ORDER BY p.tanggal DESC");
                            if (mysqli_num_rows($q_pl) > 0) {
                                while ($pl = mysqli_fetch_assoc($q_pl)) {
                                    echo "<tr class='align-middle'>
                                <td class='text-start ps-3 font-bold text-slate-700'>" . $pl['nama'] . "</td>
                                <td class='text-center text-slate-500'>" . date('Y-m-d', strtotime($pl['tanggal'])) . "</td>
                                <td class='font-bold text-danger text-start'>" . $pl['jenis_pelanggaran'] . "</td>
                                <td class='text-center font-extrabold text-red-600'>5 Poin</td>
                            </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='py-5 text-center text-slate-400 italic'>Sistem bersih. Belum ada catatan kasus pelanggaran.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="progress-input" class="section-panel">
                <h3 class="text-xl font-bold mb-3">Progress Input Nilai</h3>
                <p class="text-muted small">Modul monitoring ketersediaan nilai 100% sinkron.</p>
            </div>
            <div id="rekap-kelas" class="section-panel">
                <h3 class="text-xl font-bold mb-3">Rekap Kelas</h3>
                <p class="text-muted small">Pencetakan laporan rekapitulasi nilai F4 legal sekolah.</p>
            </div>
            <div id="data-siswa" class="section-panel">
                <h3 class="text-xl font-bold mb-3">Data Siswa Terdaftar</h3>
                <div class="card rounded-xl p-3 shadow text-xs">
                    <table class="w-full text-left">
                        <thead>
                            <tr>
                                <th class="ps-2">Nama</th>
                                <th>NISN</th>
                                <th>Kelas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $q_all_s = mysqli_query($conn, "SELECT nama, nisn, kelas FROM data_siswa WHERE level='1' AND $cond_kelas ORDER BY nama ASC");
                            while ($as = mysqli_fetch_assoc($q_all_s)) {
                                echo "<tr><td class='ps-2 font-bold'>" . $as['nama'] . "</td><td>" . $as['nisn'] . "</td><td>" . $as['kelas'] . "</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="kelola-mapel" class="section-panel">
                <h3 class="text-xl font-bold mb-3">Kelola Mata Pelajaran</h3>
                <p class="text-muted small">Konfigurasi data master KKM sekolah.</p>
            </div>
        </div>
        </div>

    <?php else: ?>
        <div class="w-full flex min-h-screen">
            <div class="sidebar flex flex-col pt-6 px-0 min-h-screen w-[195px] min-w-[195px] shadow-2xl text-white">
                <div class="px-4 font-bold text-sm mb-1" style="font-family:'DM Serif Display',serif;">Portal Murid</div>
                <div class="px-4 text-green-200 text-xs mb-5 opacity-70">SMP Gracia Bandung</div>

                <div class="px-4 mb-4">
                    <div class="text-green-200 text-[10px] mb-1 font-bold tracking-wider">SEMESTER</div>
                    <form action="" method="GET">
                        <select name="semester" onchange="this.form.submit()" class="w-full text-xs rounded p-1 text-black">
                            <option value="Genap" <?php echo ($semester_aktif == 'Genap') ? 'selected' : ''; ?>>Semester 2 (Genap)</option>
                            <option value="Ganjil" <?php echo ($semester_aktif == 'Ganjil') ? 'selected' : ''; ?>>Semester 1 (Ganjil)</option>
                        </select>
                    </form>
                </div>

                <nav class="flex flex-col gap-1 px-3 flex-1">
                    <div class="nav-link active" onclick="switchTab('s-dashboard', this)"><i class="bi bi-house-door"></i> Dashboard Academic</div>
                    <div class="nav-link" onclick="switchTab('s-nilai', this)"><i class="bi bi-journal-check"></i> Nilai Rapor</div>
                    <div class="nav-link" onclick="switchTab('s-kehadiran', this)"><i class="bi bi-calendar2-week"></i> Kehadiran</div>
                    <div class="nav-link" onclick="switchTab('s-pelanggaran', this)"><i class="bi bi-exclamation-octagon"></i> Pelanggaran</div>
                </nav>

                <div class="mt-auto mb-5 px-3">
                    <a href="logout.php" class="nav-link text-red-300 font-bold no-underline"><i class="bi bi-box-arrow-left"></i> Keluar</a>
                </div>
            </div>

            <div class="flex-1 overflow-auto p-5">
                <?php
                $q_total_avg = mysqli_query($conn, "SELECT AVG((pe1+pe2+pe3)/3) as rata FROM tabel_nilai WHERE nisn = '$nisn_login' AND semester='$semester_aktif'");
                $res_avg = mysqli_fetch_assoc($q_total_avg)['rata'];
                $avg_siswa = $res_avg ? number_format($res_avg, 2) : "0.00";

                $q_total_mapel = mysqli_query($conn, "SELECT COUNT(*) as total FROM mata_pelajaran");
                $total_mapel = mysqli_fetch_assoc($q_total_mapel)['total'];

                $q_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM kehadiran WHERE nisn = '$nisn_login' AND semester = '$semester_aktif'");
                $total_hari = mysqli_fetch_assoc($q_total)['total'];

                $q_hadir = mysqli_query($conn, "SELECT COUNT(*) as total FROM kehadiran WHERE nisn = '$nisn_login' AND status = 'Hadir' AND semester = '$semester_aktif'");
                $hadir = mysqli_fetch_assoc($q_hadir)['total'];

                $q_sakit = mysqli_query($conn, "SELECT COUNT(*) as total FROM kehadiran WHERE nisn = '$nisn_login' AND status = 'Sakit' AND semester = '$semester_aktif'");
                $sakit = mysqli_fetch_assoc($q_sakit)['total'];

                $q_izin = mysqli_query($conn, "SELECT COUNT(*) as total FROM kehadiran WHERE nisn = '$nisn_login' AND status = 'Izin' AND semester = '$semester_aktif'");
                $izin = mysqli_fetch_assoc($q_izin)['total'];

                // CLEAN FIXED: Memperbaiki typo kata rusak "FROM傷害" menjadi kueri resmi 'kehadiran'
                $q_alpha = mysqli_query($conn, "SELECT COUNT(*) as total FROM kehadiran WHERE nisn = '$nisn_login' AND status = 'Alpha' AND semester = '$semester_aktif'");
                $alpha = mysqli_fetch_assoc($q_alpha)['total'];

                $persen_hadir = ($total_hari > 0) ? round(($hadir / $total_hari) * 100) : 100;
                ?>

                <div id="s-dashboard" class="section-panel active">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-xs font-bold text-gray-500">Kelas <?php echo $user_data['kelas']; ?> · Semester <?php echo $semester_aktif == 'Genap' ? '2 (Genap)' : '1 (Ganjil)'; ?> · 2026/2027</span>
                        <span class="text-xs bg-emerald-200 text-emerald-800 px-2 py-0.5 rounded-full font-bold">Aktif</span>
                    </div>

                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div class="card rounded-xl p-4 text-center shadow-sm">
                            <div class="text-xs text-gray-500 font-semibold mb-1">Rata-rata</div>
                            <div class="text-2xl font-extrabold text-gray-700"><?php echo str_replace('.', ',', $avg_siswa); ?></div>
                        </div>
                        <div class="card rounded-xl p-4 text-center shadow-sm">
                            <div class="text-xs text-gray-500 font-semibold mb-1">Mapel</div>
                            <div class="text-2xl font-extrabold text-gray-700"><?php echo $total_mapel; ?></div>
                        </div>
                        <div class="card rounded-xl p-4 text-center shadow-sm">
                            <div class="text-xs text-gray-500 font-semibold mb-1">Kehadiran</div>
                            <div class="text-2xl font-extrabold text-gray-700"><?php echo $persen_hadir; ?>%</div>
                        </div>
                    </div>

                    <div class="card rounded-2xl p-5 mb-4 shadow-sm">
                        <div class="text-xs font-bold text-gray-400 mb-3 tracking-wider">IDENTITAS SISWA</div>
                        <div class="flex items-center gap-5">
                            <div class="rounded-full flex items-center justify-center text-white text-xl font-bold bg-[#4a6858]" style="width:56px;height:56px;">
                                <?php echo substr($user_data['nama'], 0, 1); ?>
                            </div>
                            <div class="grid grid-cols-2 gap-x-8 gap-y-2 text-xs flex-1">
                                <div><span class="text-gray-400 block mb-0.5">Nama</span><b class="text-slate-700 text-sm"><?php echo $user_data['nama']; ?></b></div>
                                <div><span class="text-gray-400 block mb-0.5">NIS</span><b class="text-slate-700 text-sm"><?php echo $user_data['nisn']; ?></b></div>
                                <div><span class="text-gray-400 block mb-0.5">Kelas</span><b class="text-slate-700 text-sm"><?php echo $user_data['kelas']; ?></b></div>
                                <div><span class="text-gray-400 block mb-0.5">Semester</span><b class="text-slate-700 text-sm">Semester <?php echo $semester_aktif == 'Genap' ? '2 (Genap)' : '1 (Ganjil)'; ?> 2026/2027</b></div>
                                <div><span class="text-gray-400 block mb-0.5">Wali Kelas</span><b class="text-slate-700 text-sm">TETTI ROSDIANAWATI</b></div>
                                <div><span class="text-gray-400 block mb-0.5">TTL</span><b class="text-slate-700 text-sm">17 Nov 2006</b></div>
                            </div>
                        </div>
                    </div>

                    <div class="card rounded-2xl p-5 mb-4 shadow-sm">
                        <div class="text-xs font-bold text-gray-400 mb-3 tracking-wider">KEHADIRAN & PELANGGARAN</div>
                        <div class="grid grid-cols-4 gap-3 text-center mb-3 text-xs">
                            <div class="card2 rounded-xl p-2">
                                <div class="text-gray-500 font-semibold">Hadir</div><b class="text-green-700 text-lg"><?php echo $hadir; ?></b>
                            </div>
                            <div class="card2 rounded-xl p-2">
                                <div class="text-gray-500 font-semibold">Sakit</div><b class="text-blue-600 text-lg"><?php echo $sakit; ?></b>
                            </div>
                            <div class="card2 rounded-xl p-2">
                                <div class="text-gray-500 font-semibold">Izin</div><b class="text-yellow-600 text-lg"><?php echo $izin; ?></b>
                            </div>
                            <div class="card2 rounded-xl p-2">
                                <div class="text-gray-500 font-semibold">Alfa</div><b class="text-red-600 text-lg"><?php echo $alpha; ?></b>
                            </div>
                        </div>
                        <div class="mt-2">
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span>Persentase Kehadiran</span>
                                <span><?php echo $persen_hadir; ?>%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width:<?php echo $persen_hadir; ?>%;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card rounded-2xl p-4 shadow-md overflow-hidden bg-white">
                        <div class="text-xs font-bold text-gray-400 mb-3 tracking-wider">NILAI PER MAPEL</div>
                        <div class="scroll-x">
                            <table class="w-full text-left">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">No</th>
                                        <th style="text-align: left; padding-left: 10px;">Mata Pelajaran</th>
                                        <th>NH</th>
                                        <th>PE</th>
                                        <th>ASAJ</th>
                                        <th>Nilai</th>
                                        <th>Predikat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $s_no = 1;
                                    $q_dash_nilai = mysqli_query($conn, "SELECT n.*, m.matapelajaran FROM tabel_nilai n JOIN mata_pelajaran m ON n.id_matapelajaran = m.id_matapelajaran WHERE n.nisn = '$nisn_login' AND n.semester = '$semester_aktif' ORDER BY m.matapelajaran ASC");
                                    if (mysqli_num_rows($q_dash_nilai) > 0) {
                                        while ($row = mysqli_fetch_assoc($q_dash_nilai)) {
                                            $nh_s   = $row['pe1'];
                                            $pe_s   = $row['pe2'];
                                            $asaj_s = $row['pe3'];
                                            $na_s = ($nh_s * 0.4) + ($pe_s * 0.3) + ($asaj_s * 0.3);
                                            if ($na_s >= 86) {
                                                $p_s = 'A';
                                                $b_s = 'badge-a';
                                            } elseif ($na_s >= 71) {
                                                $p_s = 'B';
                                                $b_s = 'badge-b';
                                            } elseif ($na_s >= 56) {
                                                $p_s = 'C';
                                                $b_s = 'badge-c';
                                            } else {
                                                $p_s = 'D';
                                                $b_s = 'badge-d';
                                            }

                                            echo "<tr class='align-middle'>";
                                            echo "<td class='text-center text-slate-400 font-bold'>" . $s_no++ . "</td>";
                                            echo "<td class='font-semibold text-slate-700' style='padding-left: 10px;'>" . $row['matapelajaran'] . "</td>";
                                            echo "<td class='text-center'>" . number_format($nh_s, 1) . "</td>";
                                            echo "<td class='text-center'>" . number_format($pe_s, 1) . "</td>";
                                            echo "<td class='text-center'>" . number_format($asaj_s, 1) . "</td>";
                                            echo "<td class='text-center font-extrabold text-slate-800'>" . number_format($na_s, 2) . "</td>";
                                            echo "<td class='text-center'><span class='$b_s'>$p_s</span></td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='7' class='text-center py-4 text-slate-400 italic'>Belum ada rincian capaian nilai untuk periode semester ini.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="s-nilai" class="section-panel">
                    <h4 class="font-bold text-gray-700 mb-3 text-base">Halaman Capaian Komponen Nilai Rapor</h4>
                    <div class="card rounded-2xl p-4 shadow-md bg-white overflow-hidden">
                        <div class="scroll-x">
                            <table class="w-full text-left">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th class="ps-2">Mata Pelajaran</th>
                                        <th>NH</th>
                                        <th>PE</th>
                                        <th>ASAJ</th>
                                        <th>Nilai Akhir</th>
                                        <th>Predikat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $s_no2 = 1;
                                    $q_s_nilai = mysqli_query($conn, "SELECT n.*, m.matapelajaran FROM tabel_nilai n JOIN mata_pelajaran m ON n.id_matapelajaran = m.id_matapelajaran WHERE n.nisn = '$nisn_login' AND n.semester = '$semester_aktif' ORDER BY m.matapelajaran ASC");
                                    while ($row = mysqli_fetch_assoc($q_s_nilai)) {
                                        $na_s = ($row['pe1'] * 0.4) + ($row['pe2'] * 0.3) + ($row['pe3'] * 0.3);
                                        $p_s = ($na_s >= 86) ? 'A' : (($na_s >= 71) ? 'B' : (($na_s >= 56) ? 'C' : 'D'));
                                        $b_s = ($p_s == 'A') ? 'badge-a' : (($p_s == 'B') ? 'badge-b' : (($p_s == 'C') ? 'badge-c' : 'badge-d'));
                                        echo "<tr>
                              <td class='text-center text-slate-400 font-bold'>" . $s_no2++ . "</td>
                              <td class='font-semibold text-slate-700 ps-2'>" . $row['matapelajaran'] . "</td>
                              <td class='text-center'>" . number_format($row['pe1'], 1) . "</td>
                              <td class='text-center'>" . number_format($row['pe2'], 1) . "</td>
                              <td class='text-center'>" . number_format($row['pe3'], 1) . "</td>
                              <td class='text-center font-extrabold text-slate-800'>" . number_format($na_s, 2) . "</td>
                              <td class='text-center'><span class='$b_s'>$p_s</span></td>
                          </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="s-kehadiran" class="section-panel">
                    <h4 class="font-bold text-gray-700 mb-3 text-base">Catatan Kehadiran Per Bulan</h4>
                    <div class="card rounded-2xl p-4 bg-white shadow overflow-hidden">
                        <table class="w-full text-center">
                            <thead>
                                <tr>
                                    <th class="text-start ps-2">Bulan</th>
                                    <th>Hadir</th>
                                    <th>Sakit</th>
                                    <th>Izin</th>
                                    <th>Alfa</th>
                                    <th>Rasio %</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $bln_map = ($semester_aktif == 'Genap') ? [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni'] : [7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
                                foreach ($bln_map as $num => $nama) {
                                    $q_inner = mysqli_query($conn, "SELECT COUNT(CASE WHEN status='Hadir' THEN 1 END) as h, COUNT(CASE WHEN status='Sakit' THEN 1 END) as s, COUNT(CASE WHEN status='Izin' THEN 1 END) as i, COUNT(CASE WHEN status='Alpha' THEN 1 END) as a FROM kehadiran WHERE nisn='$nisn_login' AND MONTH(tanggal)='$num' AND semester='$semester_aktif'");
                                    $b = mysqli_fetch_assoc($q_inner);
                                    $tot_b = $b['h'] + $b['s'] + $b['i'] + $b['a'];
                                    $pct_b = ($tot_b > 0) ? round(($b['h'] / $tot_b) * 100) : 100;
                                    echo "<tr>
                        <td class='text-start font-semibold ps-2'>$nama</td>
                        <td class='text-green-700 font-bold'>" . $b['h'] . "</td><td>" . $b['s'] . "</td><td>" . $b['i'] . "</td><td class='text-red-600'>" . $b['a'] . "</td>
                        <td class='font-bold'>$pct_b%</td>
                      </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="s-pelanggaran" class="section-panel">
                    <h4 class="font-bold text-gray-700 mb-3 text-base">Catatan Pelanggaran &amp; Disiplin PSAS</h4>
                    <div class="card rounded-2xl p-4 bg-white shadow overflow-hidden">
                        <table class="w-full text-left">
                            <thead>
                                <tr>
                                    <th class="ps-2">Tanggal Kejadian</th>
                                    <th>Jenis Pelanggaran</th>
                                    <th class="text-center">Bobot Poin</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $q_p = mysqli_query($conn, "SELECT * FROM pelanggaran WHERE nisn='$nisn_login' ORDER BY tanggal DESC");
                                if (mysqli_num_rows($q_p) > 0) {
                                    while ($p = mysqli_fetch_assoc($q_p)) {
                                        echo "<tr>
                                  <td class='text-slate-500 ps-2'>" . date('Y-m-d', strtotime($p['tanggal'])) . "</td>
                                  <td class='font-bold text-slate-700'>" . $p['jenis_pelanggaran'] . "</td>
                                  <td class='text-center text-red-600 font-bold'>5 Poin</td>
                              </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='3' class='text-center py-4 text-slate-400 italic'>Siswa teladan. Rekam riwayat pelanggaran bersih.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script>
        function switchTab(targetId, element) {
            // 1. Sembunyikan seluruh modul panel konten
            const panels = document.querySelectorAll('.section-panel');
            panels.forEach(panel => panel.classList.remove('active'));

            // 2. Aktifkan panel target yang dituju berdasarkan ID
            const targetPanel = document.getElementById(targetId);
            if (targetPanel) {
                targetPanel.classList.add('active');
            }

            // 3. Bersihkan status active lama pada link sidebar
            const links = document.querySelectorAll('.nav-link');
            links.forEach(link => link.classList.remove('active'));

            // 4. Set tombol menu yang diklik saat ini menjadi aktif
            element.classList.add('active');
        }
    </script>
</body>

</html>