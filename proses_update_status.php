<?php
session_start();
include 'koneksi.php';

if (isset($_POST['update_tanggapan'])) {
    $id_aspirasi  = $_POST['id_aspirasi'];
    $id_kategori  = $_POST['id_kategori'];
    $status       = mysqli_real_escape_string($koneksi, $_POST['status']);
    $feedback     = mysqli_real_escape_string($koneksi, $_POST['feedback']);

    // Jika data Aspirasi sudah ada, perbarui (UPDATE)
    if (!empty($id_aspirasi)) {
        $query = "UPDATE Aspirasi SET status = '$status', feedback = '$feedback' WHERE id_aspirasi = '$id_aspirasi'";
    } else {
        // Jika data Aspirasi belum ada, buat baru (INSERT)
        $query = "INSERT INTO Aspirasi (status, id_kategori, feedback) VALUES ('$status', '$id_kategori', '$feedback')";
    }

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Status dan Umpan Balik Berhasil Diperbarui!'); window.location='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Gagal Memperbarui Data!'); window.location='admin_dashboard.php';</script>";
    }
} else {
    header("Location: admin_dashboard.php");
}
?>