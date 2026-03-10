<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . '/../core/init.php';
require_once __DIR__ . '/../core/Database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || !verify_csrf($_POST['csrf_token'])) {
        $_SESSION['error'] = "Token keamanan tidak valid!";
        header("Location: index.php");
        exit;
    }
    
    $tanggal = isset($_POST['tanggal']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['tanggal']) ? $_POST['tanggal'] : date('Y-m-d');
    $semester_id = isset($_POST['semester_id']) ? (int)$_POST['semester_id'] : 0;
    $statuses = $_POST['status'] ?? [];
    
    if (!$semester_id) {
        $_SESSION['error'] = "Semester harus dipilih!";
        header("Location: index.php");
        exit;
    }
    
    $saved = 0;
    foreach ($statuses as $siswa_id => $status) {
        $siswa_id = (int)$siswa_id;
        $status = in_array($status, ['Hadir', 'Sakit', 'Izin', 'Alfa', 'Terlambat']) ? $status : 'Hadir';
        
        $check = conn()->prepare("SELECT id FROM absensi WHERE siswa_id = ? AND tanggal = ? AND semester_id = ?");
        $check->bind_param("isi", $siswa_id, $tanggal, $semester_id);
        $check->execute();
        $check->store_result();
        
        if ($check->num_rows > 0) {
            $stmt = conn()->prepare("UPDATE absensi SET status = ?, semester_id = ? WHERE siswa_id = ? AND tanggal = ?");
            $stmt->bind_param("siis", $status, $semester_id, $siswa_id, $tanggal);
        } else {
            $stmt = conn()->prepare("INSERT INTO absensi (siswa_id, tanggal, status, semester_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("issi", $siswa_id, $tanggal, $status, $semester_id);
        }
        
        $stmt->execute();
        $saved++;
    }
    
    $_SESSION['success'] = "Berhasil menyimpan $saved absensi!";
    header("Location: index.php");
    exit;
}
