<?php
require_once 'config/config.php';

// Jika sudah login, redirect ke dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error_message = '';

if ($_POST) {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        $database = new Database();
        $db = $database->getConnection();
        
        $user = new User($db);
        
        if ($user->login($username, $password)) {
            $_SESSION['user_id'] = $user->id;
            $_SESSION['username'] = $user->username;
            $_SESSION['nama_lengkap'] = $user->nama_lengkap;
            $_SESSION['user_role'] = $user->role;
            $_SESSION['email'] = $user->email;
            
            header('Location: dashboard.php');
            exit();
        } else {
            $error_message = 'Username atau password salah!';
        }
    } else {
        $error_message = 'Username dan password harus diisi!';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <div class="login-header" style="text-align: center; overflow: visible;">
                <!-- Logo Bundar Lebih di Zoom -->
                <div style="width: 140px; height: 140px; border-radius: 50%; border: 4px solid var(--primary-bg); box-shadow: var(--shadow-md); margin: 0 auto 20px auto; background-color: #FAF7F5; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                    <img src="assets/img/LEKA ONLY.png" alt="Logo Leka" style="width: 170%; height: auto; transform: scale(1.1) translateY(4px); object-fit: cover;">
                </div>
                <p>Silakan masuk dengan akun Anda</p>
            </div>

            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-full">Masuk</button>
            </form>

            <div class="login-footer">
                <p><strong>Demo Account:</strong></p>
                <p>Admin: admin / password</p>
                <p>Kasir: kasir1 / password</p>
                <p>Gudang: gudang1 / password</p>
            </div>
        </div>
    </div>
</body>
</html>
