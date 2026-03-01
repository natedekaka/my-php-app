<?php
header('Content-Type: application/json');
require_once '../config.php';

$db = getDB();

$sql = "SELECT pg.*, g.nama_guru, g.mapel 
        FROM prestasi_guru pg 
        JOIN guru g ON pg.guru_id = g.id 
        WHERE pg.status_publikasi = 'published'
        ORDER BY pg.tanggal DESC, pg.tingkat ASC 
        LIMIT 20";

$result = $db->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
