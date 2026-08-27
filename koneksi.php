<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "pengaduan_sarpras"; // Sesuaikan nama database Anda

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}
?>