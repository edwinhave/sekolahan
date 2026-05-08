<?php
session_start();
include 'koneksi.php';

$user_input = mysqli_real_escape_string($conn, $_POST['user_input']);
$password   = mysqli_real_escape_string($conn, $_POST['password']);

// Query fleksibel: Cek email OR nisn
$query = mysqli_query($conn, "SELECT * FROM data_siswa WHERE (email = '$user_input' OR nisn = '$user_input') AND password = '$password'");

if (mysqli_num_rows($query) > 0) {
    $data = mysqli_fetch_assoc($query);

    $_SESSION['nisn']  = $data['nisn'];
    $_SESSION['nama']  = $data['nama'];
    $_SESSION['level'] = $data['level'];

    header("location:menu.php");
} else {
    echo "<script>alert('Email/NISN atau Password salah!'); window.location='login.php';</script>";
}
