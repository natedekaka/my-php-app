<?php
session_start();
include '../includes/koneksi.php';
include '../includes/security.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../public/login.php');
    exit;
}

$filter_event = $_GET['filter_event'] ?? '';
$filter_tgl_awal = $_GET['filter_tgl_awal'] ?? '';
$filter_tgl_akhir = $_GET['filter_tgl_akhir'] ?? '';
$type = $_GET['type'] ?? 'xlsx';

$whereClause = [];
$params = [];

if (!empty($filter_event)) {
    $whereClause[] = "p.event_id = ?";
    $params[] = $filter_event;
}
if (!empty($filter_tgl_awal)) {
    $whereClause[] = "DATE(p.waktu) >= ?";
    $params[] = $filter_tgl_awal;
}
if (!empty($filter_tgl_akhir)) {
    $whereClause[] = "DATE(p.waktu) <= ?";
    $params[] = $filter_tgl_akhir;
}

$whereStr = !empty($whereClause) ? "WHERE " . implode(" AND ", $whereClause) : "";

$fields = [];
$result = $conn->query("SELECT * FROM form_fields WHERE aktif = 'Y' AND tipe != 'signature' ORDER BY urutan ASC");
while ($row = $result->fetch_assoc()) {
    $fields[] = $row;
}

$sql = "SELECT p.*, e.nama_event FROM presensi p JOIN events e ON p.event_id = e.id $whereStr ORDER BY p.waktu DESC";
$stmt = $conn->prepare($sql);
if (count($params) > 0) {
    $stmt->bind_param(str_repeat("s", count($params)), ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$headerLabels = ['No', 'Waktu', 'Acara'];
foreach ($fields as $f) $headerLabels[] = $f['label'];
$headerLabels[] = 'Tanda Tangan';

if ($type === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="daftar_hadir_'.date('Ymd').'.csv"');
    
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    fputcsv($output, $headerLabels, ';');
    
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        $data = json_decode($row['data_json'], true);
        $line = [
            $no++,
            date('d/m/Y H:i', strtotime($row['waktu'])),
            $row['nama_event']
        ];
        foreach ($fields as $f) {
            $line[] = $data[$f['nama_field']] ?? '-';
        }
        $line[] = $row['ttd_file'] ? 'Ada' : '-';
        fputcsv($output, $line, ';');
    }
    fclose($output);
} else {
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="daftar_hadir_'.date('Ymd').'.xlsx"');
    
    echo '<html xmlns="http://www.w3.org/1999/xhtml" xmlns:x="urn:schemas-microsoft-com:office:excel">
    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>';
    echo '<table border="1" style="border-collapse:collapse">';
    echo '<tr style="background:#667eea;color:white;font-weight:bold">';
    foreach ($headerLabels as $h) echo '<th style="padding:8px">'.htmlspecialchars($h).'</th>';
    echo '</tr>';
    
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        $data = json_decode($row['data_json'], true);
        $bg = $no % 2 == 0 ? '#f8f9fa' : 'white';
        echo '<tr style="background:'.$bg.'">';
        echo '<td style="padding:6px">'.$no++.'</td>';
        echo '<td style="padding:6px">'.date('d/m/Y H:i', strtotime($row['waktu'])).'</td>';
        echo '<td style="padding:6px">'.htmlspecialchars($row['nama_event']).'</td>';
        foreach ($fields as $f) {
            echo '<td style="padding:6px">'.htmlspecialchars($data[$f['nama_field']] ?? '-').'</td>';
        }
        echo '<td style="padding:6px">'.($row['ttd_file'] ? 'Ada' : '-').'</td>';
        echo '</tr>';
    }
    echo '</table></body></html>';
}

$stmt->close();
$conn->close();