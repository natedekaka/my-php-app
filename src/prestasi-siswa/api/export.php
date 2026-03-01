<?php
require_once '../config.php';

$db = getDB();

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Prestasi_Siswa_'.date('Ymd').'.xls"');
header('Pragma: no-cache');
header('Expires: 0');

echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
echo '<head><meta charset="UTF-8"></head><body>';
echo '<table border="1">';
echo '<tr style="background:#1e40af; color:white; font-weight:bold;">
    <th>No</th>
    <th>Nama Siswa</th>
    <th>Kelas</th>
    <th>NIS</th>
    <th>Nama Lomba</th>
    <th>Jenis</th>
    <th>Tingkat</th>
    <th>Peringkat</th>
    <th>Tanggal</th>
    <th>Penyelenggara</th>
    </tr>';

$no = 1;
$res = $db->query("
    SELECT s.nama_siswa, s.kelas, s.nis, p.nama_lomba, 
           p.jenis_prestasi, p.tingkat, p.peringkat, p.tanggal, p.penyelenggara
    FROM prestasi p
    JOIN siswa s ON p.siswa_id = s.id
    WHERE p.status_publikasi = 'published'
    ORDER BY p.tanggal DESC
");

while ($row = $res->fetch_assoc()) {
    echo '<tr>';
    echo '<td>'.$no++.'</td>';
    echo '<td>'.$row['nama_siswa'].'</td>';
    echo '<td>'.$row['kelas'].'</td>';
    echo '<td>'.$row['nis'].'</td>';
    echo '<td>'.$row['nama_lomba'].'</td>';
    echo '<td>'.$row['jenis_prestasi'].'</td>';
    echo '<td>'.$row['tingkat'].'</td>';
    echo '<td>Juara '.$row['peringkat'].'</td>';
    echo '<td>'.$row['tanggal'].'</td>';
    echo '<td>'.$row['penyelenggara'].'</td>';
    echo '</tr>';
}

echo '</table></body></html>';
