<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Pengaduan Sekolah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">

    <div class="card shadow-sm border-0 style-card" style="width: 100%; max-width: 400px;">
        <div class="card-body p-4">
            <h4 class="fw-bold text-center text-primary mb-4">Login Admin</h4>
            <form action="proses_login.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="Username" class="form-control" placeholder="Username Admin" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Password Admin" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100">Masuk</button>
                <a href="index.php" class="btn btn-link w-100 text-center mt-2 text-decoration-none">Kembali ke Halaman Siswa</a>
            </form>
        </div>
    </div>

</body>
</html>