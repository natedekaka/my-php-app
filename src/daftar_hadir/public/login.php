<?php
session_start();
include '../includes/security.php';

$error = '';
$login_rate_key = ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . '_login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!check_rate_limit($login_rate_key, 5, 300)) {
        $error = 'Terlalu banyak percobaan. Tunggu 5 menit.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        $admin_user = 'admin';
        $admin_pass = 'admin123';
        
        if ($username === $admin_user && $password === $admin_pass) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['login_time'] = time();
            header('Location: admin.php');
            exit;
        } else {
            $error = 'Username atau password salah!';
        }
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Daftar Hadir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding: 20px;
        }
        .login-card { 
            border: none; 
            border-radius: 24px; 
            box-shadow: 0 25px 50px rgba(0,0,0,0.3); 
            overflow: hidden;
        }
        .card-header { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            border-radius: 24px 24px 0 0 !important; 
            padding: 24px;
        }
        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102,126,234,0.15);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
        }
        @media (max-width: 480px) {
            .col-md-4 { width: 100%; max-width: 360px; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4 col-12">
            <div class="card login-card">
                <div class="card-header text-center py-4">
                    <h4 class="mb-0"><i class="fas fa-lock"></i> Login Admin</h4>
                    <small>Daftar Hadir Digital</small>
                </div>
                <div class="card-body p-4">
                    <?php if($error): ?>
                        <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?= e($error) ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" required autocomplete="username">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required autocomplete="current-password">
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                    <hr>
                    <div class="text-center">
                        <a href="index.php" class="text-white text-decoration-none">← Kembali ke Beranda</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
