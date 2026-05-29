<?php
session_start();
// Proteksi: Hanya Super Admin/Guru (level 2) yang bisa mengeksekusi
if (!isset($_SESSION['nisn']) || $_SESSION['level'] != '2') {
    header("location:login.php");
    exit();
}
include 'koneksi.php';

if (isset($_GET['aksi']) && isset($_GET['nisn'])) {
    $aksi = mysqli_real_escape_string($conn, $_GET['aksi']);
    $nisn_user = mysqli_real_escape_string($conn, $_GET['nisn']);

    if ($aksi == 'approve') {
        // Ubah status akun menjadi Approved agar bisa login
        $query = "UPDATE data_siswa SET status_akun = 'Approved' WHERE nisn = '$nisn_user'";
        $redirect = "approved";
    } elseif ($aksi == 'reject') {
        // Ubah status menjadi Rejected
        $query = "UPDATE data_siswa SET status_akun = 'Rejected' WHERE nisn = '$nisn_user'";
        $redirect = "rejected";
    } elseif ($aksi == 'delete') {
        // Hapus permanen dari database
        $query = "DELETE FROM data_siswa WHERE nisn = '$nisn_user'";
        $redirect = "deleted";
    }

    if (mysqli_query($conn, $query)) {
        header("location:admin_manajemen_user.php?msg=" . $redirect);
        exit();
    } else {
        echo "Gagal memproses logika sistem: " . mysqli_error($conn);
    }
} else {
    header("location:admin_manajemen_user.php");
    exit();
}
