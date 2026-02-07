<?php
echo "<h1>✅ PHP + MySQL di Podman Compose!</h1>";

$host = 'db'; // Nama service di docker-compose.yml
$db   = 'myapp';
$user = 'user';
$pass = 'pass123';
$dsn  = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    echo "<p style='color:green;'>🟢 Berhasil terhubung ke MySQL!</p>";
    echo "<ul>
            <li><b>Host:</b> $host</li>
            <li><b>Database:</b> $db</li>
            <li><b>User:</b> $user</li>
          </ul>";

} catch (PDOException $e) {
    echo "<p style='color:red;'>🔴 Gagal Terhubung: " . $e->getMessage() . "</p>";
    echo "<p><i>Tips: Tunggu beberapa saat jika container MySQL baru saja dinyalakan.</i></p>";
}
