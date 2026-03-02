<?php
require_once '../config.php';

if (!isLoggedIn()) {
    header('Location: ../admin/login.php');
    exit;
}

$db = getDB();

$type = $_GET['type'] ?? 'siswa';

function exportToExcel($headers, $rows, $filename) {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    echo "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns=\"http://www.w3.org/TR/REC-html40\">";
    echo "<head><meta charset=\"UTF-8\"></head><body>";
    echo "<table border='1'>";
    
    echo "<tr style='background-color: #1e40af; color: white; font-weight: bold;'>";
    foreach ($headers as $header) {
        echo "<th>" . htmlspecialchars($header) . "</th>";
    }
    echo "</tr>";
    
    $bgColors = ['#ffffff', '#f3f4f6'];
    $rowNum = 0;
    foreach ($rows as $row) {
        echo "<tr style='background-color: " . $bgColors[$rowNum % 2] . ";'>";
        foreach ($row as $cell) {
            echo "<td>" . htmlspecialchars($cell) . "</td>";
        }
        echo "</tr>";
        $rowNum++;
    }
    
    echo "</table></body></html>";
    exit;
}

if ($type === 'siswa') {
    $result = $db->query("
        SELECT p.*, s.nama_siswa, s.kelas, s.nis 
        FROM prestasi p 
        LEFT JOIN siswa s ON p.siswa_id = s.id 
        WHERE p.status_publikasi = 'published'
        ORDER BY p.tanggal DESC
    ");
    
    $headers = ['No', 'Nama Siswa', 'Kelas', 'NIS', 'Nama Lomba', 'Jenis', 'Jenis Peserta', 'Nama Tim', 'Tingkat', 'Peringkat', 'Tanggal', 'Penyel'];
    $rows = [];
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        $rows[] = [
            $no++,
            $row['nama_siswa'] ?? ($row['nama_tim'] ?? '-'),
            $row['kelas'] ?? '-',
            $row['nis'] ?? '-',
            $row['nama_lomba'],
            $row['jenis_prestasi'],
            $row['jenis_peserta'],
            $row['nama_tim'] ?? '-',
            $row['tingkat'],
            $row['peringkat'],
            $row['tanggal'],
            $row['penyelenggara'] ?? '-'
        ];
    }
    exportToExcel($headers, $rows, 'Prestasi_Siswa_' . date('Y-m-d') . '.xls');
}

if ($type === 'guru') {
    $result = $db->query("
        SELECT pg.*, g.nama_guru, g.mapel 
        FROM prestasi_guru pg 
        JOIN guru g ON pg.guru_id = g.id 
        WHERE pg.status_publikasi = 'published'
        ORDER BY pg.tanggal DESC
    ");
    
    $headers = ['No', 'Nama Guru', 'Mata Pelajaran', 'Nama Lomba', 'Jenis', 'Tingkat', 'Peringkat', 'Tanggal', 'Penyel'];
    $rows = [];
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        $rows[] = [
            $no++,
            $row['nama_guru'],
            $row['mapel'] ?? '-',
            $row['nama_lomba'],
            $row['jenis_prestasi'],
            $row['tingkat'],
            $row['peringkat'],
            $row['tanggal'],
            $row['penyelenggara'] ?? '-'
        ];
    }
    exportToExcel($headers, $rows, 'Prestasi_Guru_' . date('Y-m-d') . '.xls');
}

if ($type === 'sekolah') {
    $result = $db->query("
        SELECT * FROM prestasi_sekolah 
        WHERE status_publikasi = 'published'
        ORDER BY tanggal DESC
    ");
    
    $headers = ['No', 'Nama Prestasi', 'Kategori', 'Tingkat', 'Peringkat', 'Tanggal', 'Penyel'];
    $rows = [];
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        $rows[] = [
            $no++,
            $row['nama_prestasi'],
            $row['kategori'] ?? '-',
            $row['tingkat'],
            $row['peringkat'],
            $row['tanggal'],
            $row['penyelenggara'] ?? '-'
        ];
    }
    exportToExcel($headers, $rows, 'Prestasi_Sekolah_' . date('Y-m-d') . '.xls');
}

if ($type === 'alumni') {
    $result = $db->query("
        SELECT a.*, s.nama_siswa, s.kelas 
        FROM alumni_ptn a 
        JOIN siswa s ON a.siswa_id = s.id 
        WHERE a.status_publikasi = 'published'
        ORDER BY a.tahun_ajaran DESC, s.nama_siswa
    ");
    
    $headers = ['No', 'Nama Siswa', 'Kelas', 'Jenis', 'Nama PT/PTS/Perusahaan', 'Fakultas', 'Prodi', 'Thn Ajaran'];
    $rows = [];
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        $jenisLabel = ['ptn' => 'PTN', 'pts' => 'PTS', 'kerja' => 'Bekerja'][$row['jenis']] ?? $row['jenis'];
        $namaTujuan = $row['jenis'] === 'kerja' ? ($row['nama_perusahaan'] ?? '-') : ($row['nama_perguruan'] ?? '-');
        $rows[] = [
            $no++,
            $row['nama_siswa'],
            $row['kelas'],
            $jenisLabel,
            $namaTujuan,
            $row['fakultas'] ?? '-',
            $row['prodi'] ?? '-',
            $row['tahun_ajaran']
        ];
    }
    exportToExcel($headers, $rows, 'Alumni_PTN_' . date('Y-m-d') . '.xls');
}

if ($type === 'all') {
    $result = $db->query("
        SELECT p.*, s.nama_siswa, s.kelas, s.nis 
        FROM prestasi p 
        LEFT JOIN siswa s ON p.siswa_id = s.id 
        WHERE p.status_publikasi = 'published'
        ORDER BY p.tanggal DESC
    ");
    
    $headers = ['No', 'Kategori', 'Nama', 'Kelas/NIP', 'Nama Lomba/Prestasi', 'Jenis', 'Tingkat', 'Peringkat', 'Tanggal'];
    $rows = [];
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        $rows[] = [
            $no++,
            'Siswa',
            $row['nama_siswa'] ?? ($row['nama_tim'] ?? '-'),
            $row['kelas'] ?? '-',
            $row['nama_lomba'],
            $row['jenis_prestasi'],
            $row['tingkat'],
            $row['peringkat'],
            $row['tanggal']
        ];
    }
    
    $resultGuru = $db->query("
        SELECT pg.*, g.nama_guru, g.mapel 
        FROM prestasi_guru pg 
        JOIN guru g ON pg.guru_id = g.id 
        WHERE pg.status_publikasi = 'published'
        ORDER BY pg.tanggal DESC
    ");
    
    while ($row = $resultGuru->fetch_assoc()) {
        $rows[] = [
            $no++,
            'Guru',
            $row['nama_guru'],
            $row['mapel'] ?? '-',
            $row['nama_lomba'],
            $row['jenis_prestasi'],
            $row['tingkat'],
            $row['peringkat'],
            $row['tanggal']
        ];
    }
    
    $resultSekolah = $db->query("
        SELECT * FROM prestasi_sekolah 
        WHERE status_publikasi = 'published'
        ORDER BY tanggal DESC
    ");
    
    while ($row = $resultSekolah->fetch_assoc()) {
        $rows[] = [
            $no++,
            'Sekolah',
            $row['nama_prestasi'],
            '-',
            $row['nama_prestasi'],
            $row['kategori'] ?? '-',
            $row['tingkat'],
            $row['peringkat'],
            $row['tanggal']
        ];
    }
    
    exportToExcel($headers, $rows, 'Rekapan_Prestasi_All_' . date('Y-m-d') . '.xls');
}

echo "Invalid export type";
