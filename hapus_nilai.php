<?php
// hapus_nilai.php
session_start();
if (isset($_SESSION['level']) && $_SESSION['level'] == '2') {
    include 'koneksi.php';
    $id = $_GET['id'];
    mysqli_query($conn, "DELETE FROM tabel_nilai WHERE id_nilai = '$id'");
}
header("location:menu.php");
exit();
