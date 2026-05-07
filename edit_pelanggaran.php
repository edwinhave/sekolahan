<?php
session_start();
include 'koneksi.php';
$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pelanggaran WHERE id_pelanggaran = '$id'"));

if (isset($_POST['update'])) {
    $jenis = $_POST['jenis'];
    $tgl = $_POST['tanggal'];
    $kat = $_POST['kategori'];
    mysqli_query($conn, "UPDATE pelanggaran SET jenis_pelanggaran='$jenis', tanggal='$tgl', kategori='$kat' WHERE id_pelanggaran='$id'");
    header("location:admin_cek_siswa.php?nisn=" . $data['nisn']);
}
