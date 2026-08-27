<?php
session_start();
include 'koneksi.php';

// Cek autentikasi admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// ==========================================
// LOGIKA FILTER DATA
// ==========================================
$where_clause = [];

if (!empty($_GET['filter_nis'])) {
    $nis = mysqli_real_escape_string($koneksi, $_GET['filter_nis']);
    $where_clause[] = "i.nis LIKE '%$nis%'";
}

if (!empty($_GET['filter_kategori'])) {
    $kategori = mysqli_real_escape_string($koneksi, $_GET['filter_kategori']);
    $where_clause[] = "i.id_kategori = '$kategori'";
}

if (!empty($_GET['filter_status'])) {
    $status = mysqli_real_escape_string($koneksi, $_GET['filter_status']);
    $where_clause[] = "a.status = '$status'";
}

$sql_where = "";
if (count($where_clause) > 0) {
    $sql_where = " WHERE " . implode(" AND ", $where_clause);
}

// QUERY UTAMA (Ditambahkan GROUP BY i.id_pelaporan untuk mencegah duplikasi baris)
$query_sql = "SELECT i.id_pelaporan, i.nis, i.lokasi, i.ket, i.id_kategori, k.ket_kategori, 
                     a.id_aspirasi, a.status, a.feedback 
              FROM Input_Aspirasi i 
              LEFT JOIN Kategori k ON i.id_kategori = k.id_kategori
              LEFT JOIN Aspirasi a ON i.id_kategori = a.id_kategori" . $sql_where . " 
              GROUP BY i.id_pelaporan
              ORDER BY i.id_pelaporan DESC";

$result_aspirasi = mysqli_query($koneksi, $query_sql);

// STATISTIK KARTU
$q_total    = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM Input_Aspirasi");
$q_menunggu = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM Aspirasi WHERE status = 'Menunggu'");
$q_proses   = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM Aspirasi WHERE status = 'Proses'");
$q_selesai  = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM Aspirasi WHERE status = 'Selesai'");

$total_pengaduan = mysqli_fetch_assoc($q_total)['total'] ?? 0;
$total_menunggu  = mysqli_fetch_assoc($q_menunggu)['total'] ?? 0;
$total_proses    = mysqli_fetch_assoc($q_proses)['total'] ?? 0;
$total_selesai   = mysqli_fetch_assoc($q_selesai)['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Pengaduan Sarana Sekolah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="admin_dashboard.php">
                <i class="fa-solid fa-gauge me-2"></i>Panel Admin Pengaduan
            </a>
            <div class="d-flex align-items-center">
                <span class="text-light me-3 small">Halo, <strong><?= htmlspecialchars($_SESSION['admin_user'] ?? 'Admin'); ?></strong></span>
                <a href="logout.php" class="btn btn-outline-danger btn-sm" onclick="return confirm('Yakin ingin keluar?')">
                    <i class="fa-solid fa-right-from-bracket me-1"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 my-4">

        <!-- Notifikasi Pesan -->
        <?php if (isset($_SESSION['pesan'])): ?>
            <div class="alert alert-<?= $_SESSION['tipe_pesan']; ?> alert-dismissible fade show shadow-sm mb-4" role="alert">
                <?= $_SESSION['pesan']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php 
                unset($_SESSION['pesan']); 
                unset($_SESSION['tipe_pesan']);
            ?>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-0">Dashboard Kelola Aspirasi</h3>
                <p class="text-muted small mb-0">Kelola dan tanggapi pengaduan sarana prasarana sekolah.</p>
            </div>
        </div>

        <!-- Kartu Statistik -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-primary text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase mb-1 small fw-bold">Total Pengaduan</h6>
                            <h2 class="fw-bold mb-0"><?= $total_pengaduan; ?></h2>
                        </div>
                        <i class="fa-solid fa-list-check fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-warning text-dark">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase mb-1 small fw-bold">Menunggu</h6>
                            <h2 class="fw-bold mb-0"><?= $total_menunggu; ?></h2>
                        </div>
                        <i class="fa-solid fa-clock fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-info text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase mb-1 small fw-bold">Dalam Proses</h6>
                            <h2 class="fw-bold mb-0"><?= $total_proses; ?></h2>
                        </div>
                        <i class="fa-solid fa-spinner fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-success text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase mb-1 small fw-bold">Selesai</h6>
                            <h2 class="fw-bold mb-0"><?= $total_selesai; ?></h2>
                        </div>
                        <i class="fa-solid fa-circle-check fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Data -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-filter me-2"></i>Filter Data Pengaduan</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="admin_dashboard.php" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Cari NIS Siswa</label>
                        <input type="text" name="filter_nis" class="form-control form-control-sm" placeholder="Masukkan NIS..." value="<?= htmlspecialchars($_GET['filter_nis'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Kategori Sarana</label>
                        <select name="filter_kategori" class="form-select form-select-sm">
                            <option value="">-- Semua Kategori --</option>
                            <?php
                            $q_kat = mysqli_query($koneksi, "SELECT * FROM Kategori");
                            while ($k = mysqli_fetch_assoc($q_kat)) {
                                $selected = (isset($_GET['filter_kategori']) && $_GET['filter_kategori'] == $k['id_kategori']) ? 'selected' : '';
                                echo "<option value='".$k['id_kategori']."' $selected>".htmlspecialchars($k['ket_kategori'])."</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Status Penyelesaian</label>
                        <select name="filter_status" class="form-select form-select-sm">
                            <option value="">-- Semua Status --</option>
                            <option value="Menunggu" <?= (isset($_GET['filter_status']) && $_GET['filter_status'] == 'Menunggu') ? 'selected' : ''; ?>>Menunggu</option>
                            <option value="Proses" <?= (isset($_GET['filter_status']) && $_GET['filter_status'] == 'Proses') ? 'selected' : ''; ?>>Proses</option>
                            <option value="Selesai" <?= (isset($_GET['filter_status']) && $_GET['filter_status'] == 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100"><i class="fa-solid fa-magnifying-glass me-1"></i>Terapkan</button>
                        <a href="admin_dashboard.php" class="btn btn-outline-secondary btn-sm">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel Aspirasi -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0 text-dark">Daftar Aspirasi Keseluruhan</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3">No</th>
                                <th>NIS</th>
                                <th>Kategori</th>
                                <th>Lokasi</th>
                                <th>Keterangan Pengaduan</th>
                                <th>Status</th>
                                <th>Umpan Balik (Feedback)</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            if (mysqli_num_rows($result_aspirasi) > 0) {
                                while ($row = mysqli_fetch_assoc($result_aspirasi)) {
                                    $status_badge = "bg-warning text-dark";
                                    if (($row['status'] ?? '') == 'Proses') $status_badge = "bg-info text-white";
                                    if (($row['status'] ?? '') == 'Selesai') $status_badge = "bg-success text-white";
                            ?>
                            <tr>
                                <td class="px-3 fw-bold"><?= $no++; ?></td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($row['nis'] ?? ''); ?></span></td>
                                <td><?= htmlspecialchars($row['ket_kategori'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['lokasi'] ?? ''); ?></td>
                                <td><?= htmlspecialchars($row['ket'] ?? ''); ?></td>
                                <td>
                                    <span class="badge <?= $status_badge; ?>">
                                        <?= htmlspecialchars($row['status'] ?? 'Menunggu'); ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($row['feedback'] ?? '-'); ?></td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <!-- Tombol Tanggapi -->
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalTindakLanjut<?= $row['id_pelaporan']; ?>">
                                            <i class="fa-solid fa-pen-to-square me-1"></i>Tanggapi
                                        </button>
                                        <!-- Tombol Hapus -->
                                        <a href="proses_hapus.php?id=<?= $row['id_pelaporan']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus pengaduan ini?')">
                                            <i class="fa-solid fa-trash me-1"></i>Hapus
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <!-- MODAL FORM TANGGAPAN (Perbaikan Bug Sanitasi Variable) -->
                            <div class="modal fade" id="modalTindakLanjut<?= $row['id_pelaporan']; ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Tindak Lanjut Pengaduan #<?= htmlspecialchars($row['id_pelaporan'] ?? ''); ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="proses_update_status.php" method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="id_pelaporan" value="<?= htmlspecialchars($row['id_pelaporan'] ?? ''); ?>">
                                                <input type="hidden" name="id_aspirasi" value="<?= htmlspecialchars($row['id_aspirasi'] ?? ''); ?>">
                                                <input type="hidden" name="id_kategori" value="<?= htmlspecialchars($row['id_kategori'] ?? ''); ?>">

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Ubah Status Penyelesaian</label>
                                                    <select name="status" class="form-select" required>
                                                        <option value="Menunggu" <?= (($row['status'] ?? '') == 'Menunggu') ? 'selected' : ''; ?>>Menunggu</option>
                                                        <option value="Proses" <?= (($row['status'] ?? '') == 'Proses') ? 'selected' : ''; ?>>Proses</option>
                                                        <option value="Selesai" <?= (($row['status'] ?? '') == 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Umpan Balik (Feedback / Catatan)</label>
                                                    <textarea name="feedback" class="form-control" rows="4" placeholder="Tuliskan umpan balik atau perkembangan perbaikan sarana..." required><?= htmlspecialchars($row['feedback'] ?? ''); ?></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" name="update_tanggapan" class="btn btn-primary">Simpan Tanggapan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <?php 
                                } 
                            } else { 
                            ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Data pengaduan tidak ditemukan.</td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>