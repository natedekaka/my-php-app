<?php
session_start();
require_once '../config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_nama'] = $row['nama_lengkap'];
            $_SESSION['admin_role'] = $row['role'];
            header('Location: index.php');
            exit;
        }
    }
    $error = 'Username atau password salah!';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Prestasi SMA Negeri 6 Cimahi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e40af',
                        secondary: '#3b82f6',
                        accent: '#f59e0b'
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-primary to-secondary min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <i class="fas fa-trophy text-5xl text-primary mb-4"></i>
            <h1 class="text-2xl font-bold text-gray-800">Admin Login</h1>
            <p class="text-gray-500">Prestasi SMA Negeri 6 Cimahi</p>
        </div>
        
        <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-gray-700 mb-2">Username</label>
                <input type="text" name="username" required
                       class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:outline-none"
                       placeholder="Masukkan username">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Password</label>
                <input type="password" name="password" required
                       class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:outline-none"
                       placeholder="Masukkan password">
            </div>
            <button type="submit" class="w-full bg-primary text-white py-3 rounded-lg hover:bg-blue-700 transition font-bold">
                <i class="fas fa-sign-in-alt mr-2"></i> Login
            </button>
        </form>
        
        <div class="mt-6 text-center">
            <a href="../index.php" class="text-primary hover:underline">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</body>
</html>
