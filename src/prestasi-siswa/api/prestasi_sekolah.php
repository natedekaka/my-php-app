<?php
header('Content-Type: application/json');
require_once '../config.php';

$db = getDB();

$sql = "SELECT * FROM prestasi_sekolah 
        WHERE status_publikasi = 'published'
        ORDER BY tanggal DESC, tingkat ASC 
        LIMIT 20";

$result = $db->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
