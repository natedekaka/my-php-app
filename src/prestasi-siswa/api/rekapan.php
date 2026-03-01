<?php
header('Content-Type: application/json');
require_once '../config.php';

$db = getDB();

// Total dari semua kategori
$totalSiswa = $db->query("SELECT COUNT(*) as total FROM prestasi WHERE status_publikasi = 'published'")->fetch_assoc()['total'];
$totalGuru = $db->query("SELECT COUNT(*) as total FROM prestasi_guru WHERE status_publikasi = 'published'")->fetch_assoc()['total'];
$totalSekolah = $db->query("SELECT COUNT(*) as total FROM prestasi_sekolah WHERE status_publikasi = 'published'")->fetch_assoc()['total'];
$totalAlumni = $db->query("SELECT COUNT(*) as total FROM alumni_ptn WHERE status_publikasi = 'published'")->fetch_assoc()['total'];

// Per Tingkat - Siswa
$perTingkatSiswa = [];
$res = $db->query("SELECT tingkat, COUNT(*) as total FROM prestasi WHERE status_publikasi = 'published' GROUP BY tingkat");
while ($row = $res->fetch_assoc()) {
    $perTingkatSiswa[$row['tingkat']] = $row['total'];
}

// Per Tingkat - Guru
$perTingkatGuru = [];
$res = $db->query("SELECT tingkat, COUNT(*) as total FROM prestasi_guru WHERE status_publikasi = 'published' GROUP BY tingkat");
while ($row = $res->fetch_assoc()) {
    $perTingkatGuru[$row['tingkat']] = $row['total'];
}

// Per Tingkat - Sekolah
$perTingkatSekolah = [];
$res = $db->query("SELECT tingkat, COUNT(*) as total FROM prestasi_sekolah WHERE status_publikasi = 'published' GROUP BY tingkat");
while ($row = $res->fetch_assoc()) {
    $perTingkatSekolah[$row['tingkat']] = $row['total'];
}

// Per Jenis - Siswa
$perJenisSiswa = [];
$res = $db->query("SELECT jenis_prestasi, COUNT(*) as total FROM prestasi WHERE status_publikasi = 'published' GROUP BY jenis_prestasi");
while ($row = $res->fetch_assoc()) {
    $perJenisSiswa[$row['jenis_prestasi']] = $row['total'];
}

// Per Jenis - Guru
$perJenisGuru = [];
$res = $db->query("SELECT jenis_prestasi, COUNT(*) as total FROM prestasi_guru WHERE status_publikasi = 'published' GROUP BY jenis_prestasi");
while ($row = $res->fetch_assoc()) {
    $perJenisGuru[$row['jenis_prestasi']] = $row['total'];
}

// Per Tahun - Siswa
$perTahunSiswa = [];
$res = $db->query("SELECT YEAR(tanggal) as tahun, COUNT(*) as total FROM prestasi WHERE status_publikasi = 'published' GROUP BY YEAR(tanggal) ORDER BY tahun DESC");
while ($row = $res->fetch_assoc()) {
    $perTahunSiswa[$row['tahun']] = $row['total'];
}

// Per Tahun - Guru
$perTahunGuru = [];
$res = $db->query("SELECT YEAR(tanggal) as tahun, COUNT(*) as total FROM prestasi_guru WHERE status_publikasi = 'published' GROUP BY YEAR(tanggal) ORDER BY tahun DESC");
while ($row = $res->fetch_assoc()) {
    $perTahunGuru[$row['tahun']] = $row['total'];
}

// Alumni per Jenis
$alumniPerJenis = [];
$res = $db->query("SELECT jenis, COUNT(*) as total FROM alumni_ptn WHERE status_publikasi = 'published' GROUP BY jenis");
while ($row = $res->fetch_assoc()) {
    $alumniPerJenis[$row['jenis']] = $row['total'];
}

// PTN Favorit (PTN + PTS)
$ptnFavorit = [];
$res = $db->query("SELECT nama_perguruan, COUNT(*) as total FROM alumni_ptn WHERE status_publikasi = 'published' AND nama_perguruan IS NOT NULL AND nama_perguruan != '' GROUP BY nama_perguruan ORDER BY total DESC LIMIT 5");
while ($row = $res->fetch_assoc()) {
    $ptnFavorit[$row['nama_perguruan']] = $row['total'];
}

// Perusahaan Favorit (Bekerja)
$perusahaanFavorit = [];
$res = $db->query("SELECT nama_perusahaan, COUNT(*) as total FROM alumni_ptn WHERE status_publikasi = 'published' AND nama_perusahaan IS NOT NULL AND nama_perusahaan != '' GROUP BY nama_perusahaan ORDER BY total DESC LIMIT 5");
while ($row = $res->fetch_assoc()) {
    $perusahaanFavorit[$row['nama_perusahaan']] = $row['total'];
}

// Ranking Siswa
$ranks = $db->query("
    SELECT s.id, s.nama_siswa, s.kelas, 
           COUNT(p.id) as total,
           SUM(CASE 
               WHEN p.peringkat = '1' THEN 3
               WHEN p.peringkat = '2' THEN 2
               WHEN p.peringkat = '3' THEN 1
               ELSE 0
           END) as poin_juara
    FROM siswa s
    LEFT JOIN prestasi p ON s.id = p.siswa_id AND p.status_publikasi = 'published'
    GROUP BY s.id
    HAVING total > 0
    ORDER BY total DESC, poin_juara DESC
    LIMIT 10
");

$ranking = [];
while ($row = $ranks->fetch_assoc()) {
    $row['poin'] = (int)$row['poin_juara'];
    unset($row['poin_juara']);
    $ranking[] = $row;
}

// Ranking Guru
$rankGuru = $db->query("
    SELECT g.id, g.nama_guru, g.mapel, 
           COUNT(pg.id) as total,
           SUM(CASE 
               WHEN pg.peringkat = '1' THEN 3
               WHEN pg.peringkat = '2' THEN 2
               WHEN pg.peringkat = '3' THEN 1
               ELSE 0
           END) as poin_juara
    FROM guru g
    LEFT JOIN prestasi_guru pg ON g.id = pg.guru_id AND pg.status_publikasi = 'published'
    GROUP BY g.id
    HAVING total > 0
    ORDER BY total DESC, poin_juara DESC
    LIMIT 10
");

$rankingGuru = [];
while ($row = $rankGuru->fetch_assoc()) {
    $row['poin'] = (int)$row['poin_juara'];
    unset($row['poin_juara']);
    $rankingGuru[] = $row;
}

echo json_encode([
    'totalSiswa' => $totalSiswa,
    'totalGuru' => $totalGuru,
    'totalSekolah' => $totalSekolah,
    'totalAlumni' => $totalAlumni,
    'alumniPerJenis' => $alumniPerJenis,
    'perTingkatSiswa' => $perTingkatSiswa,
    'perTingkatGuru' => $perTingkatGuru,
    'perTingkatSekolah' => $perTingkatSekolah,
    'perJenisSiswa' => $perJenisSiswa,
    'perJenisGuru' => $perJenisGuru,
    'perTahunSiswa' => $perTahunSiswa,
    'perTahunGuru' => $perTahunGuru,
    'ptnFavorit' => $ptnFavorit,
    'perusahaanFavorit' => $perusahaanFavorit,
    'rankingSiswa' => $ranking,
    'rankingGuru' => $rankingGuru
]);
