<?php
header('Content-Type: application/json');
require_once '../config.php';

$db = getDB();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 9;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? $_GET['search'] : '';
$jenis = isset($_GET['jenis']) ? $_GET['jenis'] : '';
$tingkat = isset($_GET['tingkat']) ? $_GET['tingkat'] : '';
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';

$where = "WHERE p.status_publikasi = 'published'";
$params = [];
$types = "";

if ($search) {
    $where .= " AND (s.nama_siswa LIKE ? OR p.nama_lomba LIKE ? OR p.nama_tim LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "sss";
}

if ($jenis) {
    $where .= " AND p.jenis_prestasi = ?";
    $params[] = $jenis;
    $types .= "s";
}

if ($tingkat) {
    $where .= " AND p.tingkat = ?";
    $params[] = $tingkat;
    $types .= "s";
}

if ($tahun) {
    $where .= " AND YEAR(p.tanggal) = ?";
    $params[] = $tahun;
    $types .= "i";
}

$sql = "SELECT p.*, s.nama_siswa, s.kelas, s.nis 
        FROM prestasi p 
        JOIN siswa s ON p.siswa_id = s.id 
        $where 
        ORDER BY p.tanggal DESC, p.tingkat ASC 
        LIMIT ? OFFSET ?";

$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$stmt = $db->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
