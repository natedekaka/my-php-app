<?php
header('Content-Type: application/json');
require_once '../config.php';

$db = getDB();

$res = $db->query("SELECT DISTINCT YEAR(tanggal) as tahun FROM prestasi ORDER BY tahun DESC");
$tahun = [];
while ($row = $res->fetch_assoc()) {
    $tahun[] = $row;
}

echo json_encode($tahun);
