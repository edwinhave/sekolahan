<?php
session_start();
include 'koneksi.php';

// Menangkap data yang dikirim dari form login.php
$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

// Query mencari user di tabel data_siswa berdasarkan NISN dan Password
// Kita pastikan kolom 'password' sudah kamu tambahkan di tabel data_siswa
$query = "SELECT * FROM data_siswa WHERE nisn='$username' AND password='$password'";
$login = mysqli_query($conn, $query);
$cek = mysqli_num_rows($login);

if ($cek > 0) {
    $data = mysqli_fetch_assoc($login);

    // Menyimpan informasi user ke Session agar bisa digunakan di menu.php
    $_SESSION['nisn']  = $data['nisn'];
    $_SESSION['nama']  = $data['nama'];
    $_SESSION['level'] = $data['level']; // Level 1 untuk Siswa, 2 untuk Guru/Admin

    // Alihkan ke halaman menu utama
    header("location:menu.php");
} else {
    // Jika data tidak ditemukan, tampilkan pesan error
    echo "<script>
            alert('Login Gagal! NISN atau Password salah.');
            window.location='login.php';
          </script>";
}
