<?php
header('Content-Type: application/json');
require_once '../config.php';

$db = getDB();

// Stats
$totalPrestasi = $db->query("SELECT COUNT(*) as total FROM prestasi WHERE status_publikasi = 'published'")->fetch_assoc()['total'];
$totalSiswa = $db->query("SELECT COUNT(DISTINCT siswa_id) as total FROM prestasi WHERE status_publikasi = 'published'")->fetch_assoc()['total'];
$totalJuara = $db->query("SELECT COUNT(*) as total FROM prestasi WHERE status_publikasi = 'published' AND peringkat IN ('1','2','3')")->fetch_assoc()['total'];

echo json_encode([
    'totalPrestasi' => $totalPrestasi,
    'totalSiswa' => $totalSiswa,
    'totalJuara' => $totalJuara
]);
