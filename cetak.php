<?php
session_start();
// Proteksi Keamanan: Harus login terlebih dahulu
if (!isset($_SESSION['nisn'])) {
    header("location:login.php");
    exit();
}
include 'koneksi.php';

// Menangkap parameter NISN siswa yang akan dicetak
if (!isset($_GET['nisn']) || empty($_GET['nisn'])) {
    echo "<script>alert('Akses ilegal: Parameter NISN tidak ditemukan!'); window.close();</script>";
    exit();
}

$nisn_cetak = mysqli_real_escape_string($conn, trim($_GET['nisn']));

// Validasi Otoritas Akses (Siswa tidak boleh iseng mengetik NISN siswa lain di URL)
if ($_SESSION['level'] == '1' && $_SESSION['nisn'] != $nisn_cetak) {
    echo "<script>alert('Anda tidak memiliki hak akses untuk mencetak dokumen ini!'); window.close();</script>";
    exit();
}

// Ambil rincian data siswa
$q_siswa = mysqli_query($conn, "SELECT * FROM data_siswa WHERE nisn = '$nisn_cetak' AND level = '1'");
$siswa = mysqli_fetch_assoc($q_siswa);

if (!$siswa) {
    echo "<script>alert('Data siswa tidak ditemukan di database!'); window.close();</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Rapor Resmi - <?php echo $siswa['nama']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #fff;
            color: #000;
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
        }

        .kop-surat {
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }

        .table-rapor th {
            background-color: #f2f2f2 !important;
            color: #000 !important;
            border: 1px solid #000 !important;
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
        }

        .table-rapor td {
            border: 1px solid #000 !important;
            font-size: 11pt;
        }

        .info-value {
            font-weight: bold;
        }

        /* ATURAN CSS KHUSUS SAAT DICETAK (PRINTER FRIENDLY) */
        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
</head>

<body class="py-4">

    <!-- PANEL ATAS: HANYA TAMPIL DI LAYAR LAPTOP, HILANG SAAT DI-PRINT -->
    <div class="container no-print mb-4">
        <div class="alert alert-info d-flex justify-content-between align-items-center border-0 shadow-sm">
            <div>
                <i class="bi bi-info-circle-fill me-2"></i>
                Dokumen pratinjau rapor digital sekolah. Klik tombol di kanan untuk memicu dialog printer.
            </div>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-dark px-4"><i class="bi bi-printer me-1"></i> Cetak Sekarang</button>
                <button onclick="window.close()" class="btn btn-outline-secondary">Tutup Halaman</button>
            </div>
        </div>
        <hr>
    </div>

    <!-- AREA DOKUMEN YANG AKAN DI-PRINT -->
    <div class="container px-5">

        <!-- HEADER / KOP LAPORAN RESMI -->
        <div class="kop-surat text-center mb-4">
            <h3 class="fw-bold text-uppercase m-0" style="letter-spacing: 1px;">YAYASAN PENDIDIKAN SEKOLAH GRACIA</h3>
            <p class="m-0 small text-muted">Jl. Jend. Sudirman No. 123, Kota Bandung, Jawa Barat</p>
            <p class="m-0 small text-muted">Email: info@graciaschool.sch.id | Telp: (022) 555-1234</p>
        </div>

        <h4 class="text-center fw-bold text-uppercase mb-4" style="text-decoration: underline;">LAPORAN HASIL BELAJAR SISWA (RAPOR)</h4>

        <!-- IDENTITAS DETAIL SISWA -->
        <div class="row mb-4">
            <div class="col-6">
                <table class="table table-borderless table-sm small">
                    <tr>
                        <td style="width: 35%;">Nama Peserta Didik</td>
                        <td style="width: 5%;">:</td>
                        <td class="info-value"><?php echo $siswa['nama']; ?></td>
                    </tr>
                    <tr>
                        <td>Nomor Induk / NISN</td>
                        <td>:</td>
                        <td class="info-value"><?php echo $siswa['nisn']; ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-6">
                <table class="table table-borderless table-sm small">
                    <tr>
                        <td style="width: 35%;">Kelas / Tingkat</td>
                        <td style="width: 5%;">:</td>
                        <td class="info-value"><?php echo $siswa['kelas']; ?></td>
                    </tr>
                    <tr>
                        <td>Tahun Ajaran</td>
                        <td>:</td>
                        <td class="info-value">2025/2026</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- ASPEK 1: CAPAIAN AKADEMIK -->
        <h6 class="fw-bold mb-2 text-uppercase" style="font-size: 11pt;">A. CAPAIAN NILAI AKADEMIK</h6>
        <table class="table table-bordered table-rapor align-middle mb-4">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="text-align: left; padding-left: 15px;">Mata Pelajaran</th>
                    <th style="width: 8%;">PE1</th>
                    <th style="width: 8%;">PE2</th>
                    <th style="width: 8%;">PE3</th>
                    <th style="width: 8%;">PE4</th>
                    <th style="width: 8%;">PE5</th>
                    <th style="width: 8%;">PE6</th>
                    <th style="width: 8%;">PTS</th>
                    <th style="width: 8%;">ASAJ</th>
                    <th style="width: 12%;">Rata-Rata</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $q_nilai = mysqli_query($conn, "SELECT n.*, m.matapelajaran FROM tabel_nilai n JOIN mata_pelajaran m ON n.id_matapelajaran = m.id_matapelajaran WHERE n.nisn = '$nisn_cetak'");
                if (mysqli_num_rows($q_nilai) > 0) {
                    while ($n = mysqli_fetch_assoc($q_nilai)) {
                        $rata = ($n['pe1'] + $n['pe2'] + $n['pe3'] + $n['pe4'] + $n['pe5'] + $n['pe6'] + $n['pts'] + $n['asaj']) / 8;
                        echo "<tr class='text-center'>";
                        echo "<td>" . $no++ . "</td>";
                        echo "<td style='text-align: left; padding-left: 15px;' class='fw-bold'>" . $n['matapelajaran'] . "</td>";
                        echo "<td>" . $n['pe1'] . "</td><td>" . $n['pe2'] . "</td><td>" . $n['pe3'] . "</td><td>" . $n['pe4'] . "</td><td>" . $n['pe5'] . "</td><td>" . $n['pe6'] . "</td><td>" . $n['pts'] . "</td><td>" . $n['asaj'] . "</td>";
                        echo "<td class='fw-bold'>" . number_format($rata, 1) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='11' class='text-center py-3 text-muted italic'>Data nilai belum diisi oleh guru pengampu.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <div class="row">
            <!-- ASPEK 2: KEHADIRAN (REKAP ABSENSI) -->
            <div class="col-6">
                <h6 class="fw-bold mb-2 text-uppercase" style="font-size: 11pt;">B. REKAPITULASI KEHADIRAN</h6>
                <?php
                $q_tot = mysqli_query($conn, "SELECT COUNT(*) as total FROM kehadiran WHERE nisn = '$nisn_cetak'");
                $total_hari = mysqli_fetch_assoc($q_tot)['total'];

                $q_h = mysqli_query($conn, "SELECT COUNT(*) as total FROM kehadiran WHERE nisn = '$nisn_cetak' AND status = 'Hadir'");
                $hadir = mysqli_fetch_assoc($q_h)['total'];

                $q_is = mysqli_query($conn, "SELECT COUNT(*) as total FROM kehadiran WHERE nisn = '$nisn_cetak' AND status IN ('Izin', 'Sakit')");
                $izin_sakit = mysqli_fetch_assoc($q_is)['total'];

                $q_a = mysqli_query($conn, "SELECT COUNT(*) as total FROM kehadiran WHERE nisn = '$nisn_cetak' AND status = 'Alpha'");
                $alpha = mysqli_fetch_assoc($q_a)['total'];
                ?>
                <table class="table table-bordered table-rapor table-sm mb-4">
                    <thead>
                        <tr>
                            <th style="width: 70%; text-align: left; padding-left: 15px;">Keterangan Absensi</th>
                            <th style="width: 30%;">Jumlah Durasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding-left: 15px;">1. Kehadiran (Hadir)</td>
                            <td class="text-center fw-bold text-success"><?php echo $hadir; ?> Hari</td>
                        </tr>
                        <tr>
                            <td style="padding-left: 15px;">2. Izin atau Sakit (I/S)</td>
                            <td class="text-center"><?php echo $izin_sakit; ?> Hari</td>
                        </tr>
                        <tr>
                            <td style="padding-left: 15px;">3. Tanpa Keterangan (Alpha)</td>
                            <td class="text-center text-danger fw-bold"><?php echo $alpha; ?> Hari</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- ASPEK 3: CATATAN PELANGGARAN -->
            <div class="col-6">
                <h6 class="fw-bold mb-2 text-uppercase" style="font-size: 11pt;">C. CATATAN PELANGGARAN</h6>
                <table class="table table-bordered table-rapor table-sm mb-4">
                    <thead>
                        <tr>
                            <th style="width: 65%; text-align: left; padding-left: 15px;">Jenis Pelanggaran</th>
                            <th style="width: 35%;">Tanggal Catat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $q_pel = mysqli_query($conn, "SELECT * FROM pelanggaran WHERE nisn = '$nisn_cetak' ORDER BY tanggal DESC");
                        if (mysqli_num_rows($q_pel) > 0) {
                            while ($p = mysqli_fetch_assoc($q_pel)) {
                                echo "<tr>
                                        <td style='padding-left: 15px;' class='text-danger fw-bold'>• " . $p['jenis_pelanggaran'] . "</td>
                                        <td class='text-center small'>" . date('d/m/Y', strtotime($p['tanggal'])) . "</td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='2' class='text-center text-muted py-2 small italic'>Bersih. Tidak ada riwayat pelanggaran.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TANDA TANGAN RESMI WALI KELAS & KEPALA SEKOLAH -->
        <div class="row mt-5" style="font-size: 11pt;">
            <div class="col-4 text-center">
                <p class="mb-5">Mengetahui,<br>Orang Tua / Wali Siswa</p>
                <p class="fw-bold mt-4">( ............................................ )</p>
            </div>
            <div class="col-4"></div>
            <div class="col-4 text-center">
                <p class="mb-5">Bandung, <?php echo date('d F Y'); ?><br>Wali Kelas / Admin Akademik</p>
                <p class="fw-bold mt-4" style="text-decoration: underline;">( Nama, Gelar )</p>
            </div>
        </div>

    </div>

</body>

</html>