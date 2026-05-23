<?php
session_start();
if (!isset($_SESSION['nisn']) || $_SESSION['level'] != '2') {
    header("location:login.php");
    exit();
}
include 'koneksi.php';

$id_pelanggaran = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';

if ($id_pelanggaran) {
    // Ambil dulu NISN siswa sebelum datanya dihapus, agar bisa redirect kembali ke siswa yang sama
    $query_siswa = mysqli_query($conn, "SELECT nisn FROM pelanggaran WHERE id_pelanggaran = '$id_pelanggaran'");
    $data_siswa = mysqli_fetch_assoc($query_siswa);

    if ($data_siswa) {
        $nisn = $data_siswa['nisn'];

        // Jalankan perintah hapus
        $hapus = mysqli_query($conn, "DELETE FROM pelanggaran WHERE id_pelanggaran = '$id_pelanggaran'");

        if ($hapus) {
            echo "<script>alert('Catatan pelanggaran berhasil dihapus!'); window.location='admin_cek_siswa.php?nisn=$nisn';</script>";
        } else {
            echo "Gagal menghapus: " . mysqli_error($conn);
        }
    } else {
        echo "<script>alert('Data tidak ditemukan!'); window.location='admin_cek_siswa.php';</script>";
    }
} else {
    header("location:admin_cek_siswa.php");
}
