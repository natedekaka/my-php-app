<?php
session_start();
if (!isset($_SESSION['user'])) {
    die('Akses ditolak.');
}

require_once '../core/init.php';
require_once '../core/Database.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$semester_id = $_GET['semester_id'] ?? '';
$kelas_id = $_GET['kelas_id'] ?? '';
$tgl_awal = $_GET['tgl_awal'] ?? date('Y-m-01');
$tgl_akhir = $_GET['tgl_akhir'] ?? date('Y-m-t');
$type = $_GET['type'] ?? 'pdf';

if (!$semester_id || !$kelas_id) {
    die('Semester dan Kelas harus dipilih.');
}

$kelas = conn()->query("SELECT nama_kelas, wali_kelas FROM kelas WHERE id = $kelas_id")->fetch_assoc();
$semester = conn()->query("SELECT nama FROM semester WHERE id = $semester_id")->fetch_assoc();

$siswa = conn()->query("
    SELECT s.id, s.nama, s.nis, s.jenis_kelamin,
        COALESCE(SUM(CASE WHEN a.status = 'Hadir' THEN 1 ELSE 0 END), 0) as hadir,
        COALESCE(SUM(CASE WHEN a.status = 'Terlambat' THEN 1 ELSE 0 END), 0) as terlambat,
        COALESCE(SUM(CASE WHEN a.status = 'Sakit' THEN 1 ELSE 0 END), 0) as sakit,
        COALESCE(SUM(CASE WHEN a.status = 'Izin' THEN 1 ELSE 0 END), 0) as izin,
        COALESCE(SUM(CASE WHEN a.status = 'Alfa' THEN 1 ELSE 0 END), 0) as alfa
    FROM siswa s
    LEFT JOIN absensi a ON s.id = a.siswa_id 
        AND a.semester_id = $semester_id
        AND a.kelas_id = $kelas_id
        AND a.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'
    WHERE s.kelas_id = $kelas_id AND (s.status = 'aktif' OR s.status IS NULL)
    GROUP BY s.id, s.nama, s.nis, s.jenis_kelamin
    ORDER BY s.nama ASC
");

$data = [];
if ($siswa && $siswa->num_rows > 0) {
    while ($row = $siswa->fetch_assoc()) {
        $data[] = $row;
    }
}

$total_hari = (strtotime($tgl_akhir) - strtotime($tgl_awal)) / (60*60*24) + 1;

if ($type === 'excel' || $type === 'xlsx') {
    require_once '../vendor/autoload.php';

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('A1', 'REKAP ABSENSI SISWA');
    $sheet->setCellValue('A2', 'Kelas: ' . $kelas['nama_kelas']);
    $sheet->setCellValue('A3', 'Periode: ' . date('d/m/Y', strtotime($tgl_awal)) . ' - ' . date('d/m/Y', strtotime($tgl_akhir)));
    $sheet->setCellValue('A4', 'Semester: ' . $semester['nama']);
    
    $sheet->mergeCells('A1:I1');
    $sheet->mergeCells('A2:I2');
    $sheet->mergeCells('A3:I3');
    $sheet->mergeCells('A4:I4');
    
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
    $sheet->getStyle('A2:A4')->getFont()->setSize(11);

    $sheet->setCellValue('A6', 'No');
    $sheet->setCellValue('B6', 'NIS');
    $sheet->setCellValue('C6', 'Nama Siswa');
    $sheet->setCellValue('D6', 'Jenis Kelamin');
    $sheet->setCellValue('E6', 'Hadir');
    $sheet->setCellValue('F6', 'Terlambat');
    $sheet->setCellValue('G6', 'Sakit');
    $sheet->setCellValue('H6', 'Izin');
    $sheet->setCellValue('I6', 'Alfa');
    $sheet->setCellValue('J6', '% Kehadiran');

    $sheet->getStyle('A6:J6')->getFont()->setBold(true);
    $sheet->getStyle('A6:J6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FF25D366');
    $sheet->getStyle('A6:J6')->getFont()->getColor()->setARGB('FFFFFFFF');

    $no = 1;
    foreach ($data as $row) {
        $persen = $total_hari > 0 ? round(($row['hadir'] / $total_hari) * 100, 1) : 0;
        $sheet->setCellValue('A' . ($no + 6), $no);
        $sheet->setCellValue('B' . ($no + 6), $row['nis']);
        $sheet->setCellValue('C' . ($no + 6), $row['nama']);
        $sheet->setCellValue('D' . ($no + 6), $row['jenis_kelamin']);
        $sheet->setCellValue('E' . ($no + 6), $row['hadir']);
        $sheet->setCellValue('F' . ($no + 6), $row['terlambat']);
        $sheet->setCellValue('G' . ($no + 6), $row['sakit']);
        $sheet->setCellValue('H' . ($no + 6), $row['izin']);
        $sheet->setCellValue('I' . ($no + 6), $row['alfa']);
        $sheet->setCellValue('J' . ($no + 6), $persen . '%');
        $no++;
    }

    foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'] as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="rekap_absensi_' . str_replace(' ', '_', $kelas['nama_kelas']) . '_' . date('Y-m-d') . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

if ($type === 'pdf') {
    require_once '../vendor/tcpdf/tcpdf.php';

    class RekapPDF extends TCPDF {
        public function Header() {
            $this->SetFont('helvetica', 'B', 14);
            $this->Cell(0, 10, 'LAPORAN REKAP ABSENSI SISWA', 0, 1, 'C');
            $this->Ln(2);
        }

        public function Footer() {
            $this->SetY(-15);
            $this->SetFont('helvetica', 'I', 8);
            $this->Cell(0, 10, 'Dicetak: ' . date('d/m/Y H:i') . ' | Halaman ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
        }
    }

    $pdf = new RekapPDF('L', 'mm', 'A4');
    $pdf->SetMargins(10, 25, 10);
    $pdf->SetAutoPageBreak(true, 20);
    $pdf->AddPage();

    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 5, 'Kelas: ' . $kelas['nama_kelas'] . ' | Wali Kelas: ' . ($kelas['wali_kelas'] ?? '-'), 0, 1);
    $pdf->Cell(0, 5, 'Periode: ' . date('d/m/Y', strtotime($tgl_awal)) . ' - ' . date('d/m/Y', strtotime($tgl_akhir)), 0, 1);
    $pdf->Cell(0, 5, 'Semester: ' . $semester['nama'], 0, 1);
    $pdf->Ln(5);

    $pdf->SetFont('helvetica', 'B', 9);
    $header = ['No', 'NIS', 'Nama Siswa', 'JK', 'Hadir', 'Telat', 'Sakit', 'Izin', 'Alfa', '% Hadir'];
    $w = [8, 20, 55, 12, 15, 15, 15, 15, 15, 20];

    $pdf->SetFillColor(37, 211, 102);
    $pdf->SetTextColor(255, 255, 255);
    for ($i = 0; $i < count($header); $i++) {
        $pdf->Cell($w[$i], 8, $header[$i], 1, 0, 'C', true);
    }
    $pdf->Ln();

    $pdf->SetFont('helvetica', '', 8);
    $pdf->SetTextColor(0, 0, 0);
    $no = 1;
    foreach ($data as $row) {
        $persen = $total_hari > 0 ? round(($row['hadir'] / $total_hari) * 100, 1) : 0;
        
        $bg = ($no % 2 == 0) ? [245, 245, 245] : [255, 255, 255];
        $pdf->SetFillColor($bg[0], $bg[1], $bg[2]);
        
        $pdf->Cell($w[0], 7, $no, 1, 0, 'C', true);
        $pdf->Cell($w[1], 7, $row['nis'], 1, 0, 'L', true);
        $pdf->Cell($w[2], 7, $row['nama'], 1, 0, 'L', true);
        $pdf->Cell($w[3], 7, substr($row['jenis_kelamin'], 0, 1), 1, 0, 'C', true);
        $pdf->Cell($w[4], 7, $row['hadir'], 1, 0, 'C', true);
        $pdf->Cell($w[5], 7, $row['terlambat'], 1, 0, 'C', true);
        $pdf->Cell($w[6], 7, $row['sakit'], 1, 0, 'C', true);
        $pdf->Cell($w[7], 7, $row['izin'], 1, 0, 'C', true);
        $pdf->Cell($w[8], 7, $row['alfa'], 1, 0, 'C', true);
        $pdf->Cell($w[9], 7, $persen . '%', 1, 0, 'C', true);
        $pdf->Ln();
        $no++;
    }

    if (empty($data)) {
        $pdf->Cell(0, 10, 'Tidak ada data absensi.', 1, 1, 'C');
    }

    $pdf->Output('rekap_absensi_' . str_replace(' ', '_', $kelas['nama_kelas']) . '_' . date('Y-m-d') . '.pdf', 'D');
    exit;
}

die('Jenis ekspor tidak valid.');
