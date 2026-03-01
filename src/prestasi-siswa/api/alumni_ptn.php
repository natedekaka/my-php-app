<?php
header('Content-Type: application/json');
require_once '../config.php';

$db = getDB();

$sql = "SELECT a.*, s.nama_siswa, s.kelas 
        FROM alumni_ptn a 
        JOIN siswa s ON a.siswa_id = s.id 
        WHERE a.status_publikasi = 'published'
        ORDER BY a.tahun_ajaran DESC, s.nama_siswa
        LIMIT 20";

$result = $db->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
