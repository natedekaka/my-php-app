<?php
// admin/ekspor_excel.php - Ekspor ke Excel

require_once '../config/database.php';

if (!isset($_GET['ujian']) || empty($_GET['ujian'])) {
    die("Parameter tidak valid");
}

$id_ujian = (int)$_GET['ujian'];

$stmt = $conn->prepare("SELECT judul_ujian FROM ujian WHERE id = ?");
$stmt->bind_param("i", $id_ujian);
$stmt->execute();
$result = $stmt->get_result();
$ujian = $result->fetch_assoc();
$stmt->close();

if (!$ujian) {
    die("Ujian tidak ditemukan");
}

$stmt = $conn->prepare("SELECT nis, nama, kelas, total_skor, waktu_submit FROM hasil_ujian WHERE id_ujian = ? ORDER BY total_skor DESC");
$stmt->bind_param("i", $id_ujian);
$stmt->execute();
$result = $stmt->get_result();

$hasil_list = [];
while ($row = $result->fetch_assoc()) {
    $hasil_list[] = $row;
}
$stmt->close();

$nama_file = 'rekap_' . str_replace(' ', '_', $ujian['judul_ujian']) . '_' . date('Ymd_His');

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $nama_file . '.xls"');
header('Cache-Control: max-age=0');
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rekap Nilai</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000; padding: 8px; }
        th { background-color: #4472C4; color: #fff; }
    </style>
</head>
<body>
    <h2>Rekap: <?= htmlspecialchars($ujian['judul_ujian']) ?></h2>
    <p>Tanggal: <?= date('d/m/Y H:i:s') ?></p>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama</th>
                <th>Kelas</th>
                <th>Skor</th>
                <th>Waktu Submit</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($hasil_list as $hasil): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($hasil['nis']) ?></td>
                <td><?= htmlspecialchars($hasil['nama']) ?></td>
                <td><?= htmlspecialchars($hasil['kelas']) ?></td>
                <td><?= $hasil['total_skor'] ?></td>
                <td><?= date('d/m/Y H:i:s', strtotime($hasil['waktu_submit'])) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (count($hasil_list) === 0): ?>
            <tr><td colspan="6" style="text-align: center;">Tidak ada data</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
