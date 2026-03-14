<?php
session_start();
include 'koneksi.php';
include 'security.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $conn->prepare("SELECT ttd_file FROM presensi WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $ttdFile = $row['ttd_file'];
        
        $stmt2 = $conn->prepare("DELETE FROM presensi WHERE id = ?");
        $stmt2->bind_param("i", $id);
        $stmt2->execute();
        $stmt2->close();
        
        if ($ttdFile && file_exists(__DIR__ . '/uploads/' . $ttdFile)) {
            unlink(__DIR__ . '/uploads/' . $ttdFile);
        }
    }
    $stmt->close();
}

header('Location: rekap_admin.php?status=hapus_ok');
exit;
