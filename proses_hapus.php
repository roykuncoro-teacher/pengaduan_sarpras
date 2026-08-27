<?php
session_start();
include 'koneksi.php';

// Cek autentikasi admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id_pelaporan = mysqli_real_escape_string($koneksi, $_GET['id']);

    // Hapus data dari tabel Input_Aspirasi
    $query = "DELETE FROM Input_Aspirasi WHERE id_pelaporan = '$id_pelaporan'";

    if (mysqli_query($koneksi, $query)) {
        $_SESSION['pesan'] = "Pengaduan berhasil dihapus!";
        $_SESSION['tipe_pesan'] = "success";
    } else {
        $_SESSION['pesan'] = "Gagal menghapus pengaduan: " . mysqli_error($koneksi);
        $_SESSION['tipe_pesan'] = "danger";
    }
} else {
    $_SESSION['pesan'] = "ID Pengaduan tidak ditemukan!";
    $_SESSION['tipe_pesan'] = "warning";
}

header("Location: admin_dashboard.php");
exit();
?>