<?php
/**
 * File: koneksi.php
 * Deskripsi: Koneksi database dengan keamanan
 */

$host = 'db:3306';
$user = 'root';
$pass = 'rootpass';
$db   = 'daftar_hadir_db';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    error_log("Koneksi gagal: " . $conn->connect_error);
    die("Sistem sedang maintenance");
}

$conn->set_charset("utf8mb4");

// Disable SQL errors from being displayed
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn->query("SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION'");
} catch (Exception $e) {
    error_log("SQL mode error: " . $e->getMessage());
}
