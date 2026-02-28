<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <h1>Akses Ditolak</h1>
                <p>Anda tidak memiliki hak akses untuk halaman ini</p>
            </div>

            <div class="alert alert-error">
                <strong>Error 403:</strong> Akses ditolak. Silakan hubungi administrator untuk mendapatkan akses yang sesuai.
            </div>

            <div class="form-group">
                <a href="dashboard.php" class="btn btn-primary btn-full">Kembali ke Dashboard</a>
            </div>

            <div class="form-group">
                <a href="logout.php" class="btn btn-secondary btn-full">Logout</a>
            </div>
        </div>
    </div>
</body>
</html>
