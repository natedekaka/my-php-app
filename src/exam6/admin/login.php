<?php
// admin/login.php - Halaman Login Admin

session_start();

require_once '../config/database.php';
require_once '../config/init_sekolah.php';

$sekolah = getKonfigurasiSekolah($conn);

$message = '';

if (isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if ($username && $password) {
        $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_nama'] = $user['nama_lengkap'];
                $_SESSION['admin_username'] = $user['username'];
                
                header('Location: index.php');
                exit;
            } else {
                $message = 'Password salah!';
            }
        } else {
            $message = 'Username tidak ditemukan!';
        }
        $stmt->close();
    } else {
        $message = 'Mohon isi username dan password!';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Sistem Ujian Online</title>
    <link href="../vendor/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../vendor/bootstrap-icons/bootstrap-icons.min.css">
    <style>
        body { 
            background: linear-gradient(135deg, <?= $sekolah['warna_primer'] ?> 0%, <?= $sekolah['warna_sekunder'] ?> 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            padding: 40px;
            max-width: 400px;
            width: 100%;
        }
        .login-icon {
            font-size: 50px;
            color: #667eea;
        }
        .school-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }
        .school-logo i {
            font-size: 2.5rem;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="login-card">
                    <div class="text-center mb-4">
                        <div class="school-logo">
                            <?php if ($sekolah['logo'] && file_exists('../uploads/' . $sekolah['logo'])): ?>
                                <img src="../uploads/<?= $sekolah['logo'] ?>" alt="Logo" style="width: 100%; height: 100%; object-fit: contain; border-radius: 50%;">
                            <?php else: ?>
                                <i class="bi bi-mortarboard-fill"></i>
                            <?php endif; ?>
                        </div>
                        <h5 class="fw-bold text-primary"><?= htmlspecialchars($sekolah['nama_sekolah']) ?></h5>
                        <h4 class="mt-2">Sistem Ujian Online</h4>
                        <p class="text-muted">Login Admin</p>
                    </div>
                    
                    <?php if ($message): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= $message ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" name="username" class="form-control" required autofocus>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../vendor/bootstrap/bootstrap.bundle.min.js" defer></script>
</body>
</html>
