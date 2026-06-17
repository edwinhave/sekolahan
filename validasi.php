<?php
session_start();

// PROTEKSI KETAT: Hanya Super Admin (Level 2) yang berhak melakukan Approve/Reject/Delete Akun
if (!isset($_SESSION['nisn']) || $_SESSION['level'] != '2') {
    header("location:login.php");
    exit();
}
include 'koneksi.php';

if (isset($_GET['aksi']) && isset($_GET['nisn'])) {
    $aksi = mysqli_real_escape_string($conn, $_GET['aksi']);
    $nisn_user = mysqli_real_escape_string($conn, $_GET['nisn']);

    if ($aksi == 'approve') {
        // Mengubah status akun menjadi Approved agar bisa lolos dari login.php
        $query = "UPDATE data_siswa SET status_akun = 'Approved' WHERE nisn = '$nisn_user'";
        $redirect = "approved";
    } elseif ($aksi == 'reject') {
        // Mengubah status menjadi Rejected
        $query = "UPDATE data_siswa SET status_akun = 'Rejected' WHERE nisn = '$nisn_user'";
        $redirect = "rejected";
    } elseif ($aksi == 'delete') {
        // Hapus permanen user dari database sekolah
        $query = "DELETE FROM data_siswa WHERE nisn = '$nisn_user'";
        $redirect = "deleted";
    }

    if (mysqli_query($conn, $query)) {
        // KOREKSI GLOBAL: Melempar kembali ke manajemen user dengan parameter teks semester yang sinkron
        header("location:admin_manajemen_user.php?msg=" . $redirect . "&kelas=IX&semester=Genap");
        exit();
    } else {
        echo "Gagal memproses validasi sistem database: " . mysqli_error($conn);
    }
} else {
    header("location:admin_manajemen_user.php");
    exit();
}
