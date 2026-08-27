<?php
session_start();
include 'koneksi.php';

// Memastikan form dikirim melalui tombol submit
if (isset($_POST['submit_aspirasi'])) {
    // Sanitasi input data untuk mencegah SQL Injection
    $nis         = mysqli_real_escape_string($koneksi, trim($_POST['nis']));
    $kelas       = mysqli_real_escape_string($koneksi, trim($_POST['kelas']));
    $id_kategori = mysqli_real_escape_string($koneksi, trim($_POST['id_kategori']));
    $lokasi      = mysqli_real_escape_string($koneksi, trim($_POST['lokasi']));
    $ket         = mysqli_real_escape_string($koneksi, trim($_POST['ket']));

    // Validasi form tidak boleh ada yang kosong
    if (empty($nis) || empty($kelas) || empty($id_kategori) || empty($lokasi) || empty($ket)) {
        $_SESSION['pesan'] = "Semua kolom form wajib diisi!";
        $_SESSION['tipe_pesan'] = "danger";
        header("Location: index.php");
        exit();
    }

    // -------------------------------------------------------------
    // LANGKAH 1: Cek & Simpan/Update Data Siswa di Tabel `Siswa`
    // -------------------------------------------------------------
    $cek_siswa = mysqli_query($koneksi, "SELECT * FROM Siswa WHERE nis = '$nis'");
    
    if (mysqli_num_rows($cek_siswa) == 0) {
        // Jika NIS belum ada, masukkan data siswa baru
        $sql_siswa = "INSERT INTO Siswa (nis, kelas) VALUES ('$nis', '$kelas')";
        $query_siswa = mysqli_query($koneksi, $sql_siswa);

        if (!$query_siswa) {
            $_SESSION['pesan'] = "Gagal menyimpan data siswa: " . mysqli_error($koneksi);
            $_SESSION['tipe_pesan'] = "danger";
            header("Location: index.php");
            exit();
        }
    } else {
        // Jika NIS sudah ada, update kelas siswa jika ada perubahan
        mysqli_query($koneksi, "UPDATE Siswa SET kelas = '$kelas' WHERE nis = '$nis'");
    }

    // -------------------------------------------------------------
    // LANGKAH 2: Simpan Data ke Tabel `Input_Aspirasi`
    // -------------------------------------------------------------
    $sql_input = "INSERT INTO Input_Aspirasi (nis, id_kategori, lokasi, ket) 
                  VALUES ('$nis', '$id_kategori', '$lokasi', '$ket')";

    if (mysqli_query($koneksi, $sql_input)) {
        
        // -------------------------------------------------------------
        // LANGKAH 3: Inisialisasi Status di Tabel `Aspirasi` (Default: Menunggu)
        // -------------------------------------------------------------
        $sql_aspirasi = "INSERT INTO Aspirasi (status, id_kategori, feedback) 
                         VALUES ('Menunggu', '$id_kategori', 'Aspirasi diterima dan sedang menunggu penanganan.')";
        mysqli_query($koneksi, $sql_aspirasi);

        // Set pesan sukses
        $_SESSION['pesan'] = "Aspirasi berhasil dikirim! Terima kasih atas partisipasi Anda.";
        $_SESSION['tipe_pesan'] = "success";

        // Redirect kembali dengan filter NIS siswa tersebut
        header("Location: index.php?cari_nis=" . $nis);
        exit();

    } else {
        $_SESSION['pesan'] = "Gagal mengirim aspirasi: " . mysqli_error($koneksi);
        $_SESSION['tipe_pesan'] = "danger";
        header("Location: index.php");
        exit();
    }

} else {
    // Jika diakses tanpa submit form
    header("Location: index.php");
    exit();
}
?>