<?php
session_start();
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaduan Sarana Sekolah - Siswa</title>
    <!-- Bootstrap 5 CSS & FontAwesome Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fa-solid fa-school me-2"></i>Pengaduan Sarana Sekolah
            </a>
            <a href="login.php" class="btn btn-outline-light btn-sm">
                <i class="fa-solid fa-user-lock me-1"></i>Login Admin
            </a>
        </div>
    </nav>

    <div class="container my-5">

        <!-- NOTIFIKASI ALERT BERHASIL / GAGAL -->
        <?php if (isset($_SESSION['pesan'])): ?>
            <div class="alert alert-<?= $_SESSION['tipe_pesan']; ?> alert-dismissible fade show shadow-sm mb-4" role="alert">
                <i class="fa-solid <?= ($_SESSION['tipe_pesan'] == 'success') ? 'fa-circle-check' : 'fa-circle-exclamation'; ?> me-2"></i>
                <?= $_SESSION['pesan']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php 
                unset($_SESSION['pesan']); 
                unset($_SESSION['tipe_pesan']);
            ?>
        <?php endif; ?>

        <div class="row g-4">
            
            <!-- FORM INPUT ASPIRASI SISWA -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="fa-solid fa-pen-to-square me-2"></i>Form Pengaduan Siswa
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="proses_simpan.php" method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold small">NIS (Nomor Induk Siswa)</label>
                                    <input type="number" name="nis" class="form-control" placeholder="Contoh: 12345" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold small">Kelas</label>
                                    <input type="text" name="kelas" class="form-control" placeholder="Contoh: XII RPL 1" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Kategori Sarana / Prasarana</label>
                                <select name="id_kategori" class="form-select" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php
                                    $query_kat = mysqli_query($koneksi, "SELECT * FROM Kategori");
                                    while ($k = mysqli_fetch_assoc($query_kat)) {
                                        echo "<option value='".$k['id_kategori']."'>".$k['ket_kategori']."</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Lokasi Spesifik Sarana</label>
                                <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Lab Komputer 2, Kamar Mandi Lantai 2" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Keterangan / Detail Pengaduan</label>
                                <textarea name="ket" class="form-control" rows="4" placeholder="Jelaskan detail masalah atau kerusakan sarana prasarana..." required></textarea>
                            </div>

                            <button type="submit" name="submit_aspirasi" class="btn btn-primary w-100 fw-bold">
                                <i class="fa-solid fa-paper-plane me-2"></i>Kirim Pengaduan
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- TABEL HISTORI & STATUS ASPIRASI -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold text-primary">
                            <i class="fa-solid fa-clock-rotate-left me-2"></i>Histori & Status Aspirasi
                        </h5>
                    </div>
                    <div class="card-body">
                        
                        <!-- Form Cari Histori berdasarkan NIS -->
                        <form method="GET" action="index.php" class="row g-2 mb-4">
                            <div class="col-8 col-sm-9">
                                <input type="number" name="cari_nis" class="form-control form-control-sm" placeholder="Masukkan NIS Anda untuk filter histori..." value="<?= $_GET['cari_nis'] ?? ''; ?>">
                            </div>
                            <div class="col-4 col-sm-3 d-flex gap-1">
                                <button type="submit" class="btn btn-primary btn-sm w-100">Cari</button>
                                <?php if (isset($_GET['cari_nis'])): ?>
                                    <a href="index.php" class="btn btn-outline-secondary btn-sm">Reset</a>
                                <?php endif; ?>
                            </div>
                        </form>

                        <!-- Tabel Data Aspirasi -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>NIS</th>
                                        <th>Lokasi</th>
                                        <th>Pengaduan</th>
                                        <th>Status</th>
                                        <th>Umpan Balik / Feedback</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Filter data jika NIS dicari
                                    $where = "";
                                    if (!empty($_GET['cari_nis'])) {
                                        $cari_nis = mysqli_real_escape_string($koneksi, $_GET['cari_nis']);
                                        $where = " WHERE i.nis = '$cari_nis'";
                                    }

                                    $sql = "SELECT i.id_pelaporan, i.nis, i.lokasi, i.ket, a.status, a.feedback 
                                            FROM Input_Aspirasi i 
                                            LEFT JOIN Aspirasi a ON i.id_kategori = a.id_kategori" . $where . "
                                            ORDER BY i.id_pelaporan DESC";
                                    
                                    $result = mysqli_query($koneksi, $sql);
                                    $no = 1;

                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            // Pengaturan Warna Badge Status
                                            $badge = "bg-warning text-dark"; // Menunggu
                                            if ($row['status'] == 'Proses') $badge = "bg-info text-white";
                                            if ($row['status'] == 'Selesai') $badge = "bg-success text-white";
                                    ?>
                                    <tr>
                                        <td class="fw-bold"><?= $no++; ?></td>
                                        <td><span class="badge bg-secondary"><?= htmlspecialchars($row['nis']); ?></span></td>
                                        <td><?= htmlspecialchars($row['lokasi']); ?></td>
                                        <td><small><?= htmlspecialchars($row['ket']); ?></small></td>
                                        <td>
                                            <span class="badge <?= $badge; ?>">
                                                <?= htmlspecialchars($row['status'] ?? 'Menunggu'); ?>
                                            </span>
                                        </td>
                                        <td><small class="text-muted"><?= htmlspecialchars($row['feedback'] ?? 'Belum ada tanggapan.'); ?></small></td>
                                    </tr>
                                    <?php 
                                        } 
                                    } else { 
                                    ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <?= isset($_GET['cari_nis']) ? 'Data pengaduan dengan NIS ini tidak ditemukan.' : 'Belum ada data pengaduan yang dikirim.'; ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>