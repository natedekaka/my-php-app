<?php
/**
 * File: simpan.php
 * Deskripsi: Penyimpanan data kehadiran
 */

error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

@session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'gagal', 'message' => 'Invalid request']);
    exit;
}

$event_id = (int)($_POST['event_id'] ?? 0);
if (empty($event_id)) {
    echo json_encode(['status' => 'gagal', 'message' => 'Pilih acara']);
    exit;
}

$signatureData = $_POST['signature_data'] ?? '';
if (empty($signatureData)) {
    echo json_encode(['status' => 'gagal', 'message' => 'TTD kosong']);
    exit;
}

// Decode base64
$img = preg_replace('/^data:image\/\w+;base64,/', '', $signatureData);
$img = str_replace(' ', '+', $img);
$decoded = base64_decode($img);

if (!$decoded || strlen($decoded) < 50) {
    echo json_encode(['status' => 'gagal', 'message' => 'TTD corrupt']);
    exit;
}

// Try to find writable directory
$baseDir = __DIR__;
$uploadDir = $baseDir . '/uploads';

// Ensure uploads directory exists
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0777, true);
}

// Check if we can write to uploads, if not use temp
$canWrite = is_dir($uploadDir) && is_writable($uploadDir);
$saveDir = $canWrite ? $uploadDir : sys_get_temp_dir();

$file = time() . '_sign.png';
$path = $saveDir . '/' . $file;

// Store relative path (uploads/filename) in DB
$dbFilename = 'uploads/' . $file;

if (@file_put_contents($path, $decoded) === false) {
    echo json_encode(['status' => 'gagal', 'message' => 'Cannot write file']);
    exit;
}

// Prepare data
$data = [];
foreach ($_POST as $k => $v) {
    if (!in_array($k, ['signature_data', 'csrf_token']) && is_string($v)) {
        $data[$k] = trim($v);
    }
}

// Save to DB
$json = json_encode($data, JSON_UNESCAPED_UNICODE);
$time = date('Y-m-d H:i:s');

// Store relative path in DB
//$relativePath = str_replace(__DIR__ . '/', '', $path);

$sql = "INSERT INTO presensi (event_id, data_json, ttd_file, waktu) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isss", $event_id, $json, $dbFilename, $time);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    echo json_encode(['status' => 'sukses', 'message' => 'Berhasil!']);
} else {
    @unlink($path);
    $stmt->close();
    $conn->close();
    echo json_encode(['status' => 'gagal', 'message' => 'DB error']);
}
