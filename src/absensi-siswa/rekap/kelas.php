<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../core/init.php';
require_once '../core/Database.php';

$title = 'Rekap Absensi - Sistem Absensi Siswa';

$semester_id = $_GET['semester_id'] ?? '';
$kelas_id = $_GET['kelas_id'] ?? '';
$tgl_awal = $_GET['tgl_awal'] ?? date('Y-m-01');
$tgl_akhir = $_GET['tgl_akhir'] ?? date('Y-m-t');

ob_start();
?>

<style>
:root {
    --wa-primary: #25D366;
    --wa-dark: #128C7E;
    --wa-light: #DCF8C6;
    --gradient-wa: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
    --gradient-purple: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --gradient-orange: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --gradient-teal: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

body {
    background: #f8fafc;
}

.filter-card {
    border: none;
    border-radius: 24px;
    background: white;
    box-shadow: 0 10px 40px rgba(0,0,0,0.08);
    overflow: hidden;
}
.filter-header {
    background: var(--gradient-wa);
    color: white;
    padding: 1.5rem 2rem;
    border-radius: 0;
}
.stat-card {
    border: none;
    border-radius: 20px;
    padding: 1.5rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    position: relative;
}
.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
}
.stat-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}
.stat-card.hadir::before { background: var(--gradient-wa); }
.stat-card.terlambat::before { background: linear-gradient(90deg, #ffc107, #ff9800); }
.stat-card.sakit::before { background: var(--gradient-teal); }
.stat-card.izin::before { background: var(--gradient-purple); }
.stat-card.alfa::before { background: var(--gradient-orange); }

.stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}
.stat-hadir { background: rgba(37, 211, 102, 0.15); color: #128C7E; }
.stat-terlambat { background: rgba(255, 193, 7, 0.15); color: #e6a800; }
.stat-sakit { background: rgba(79, 172, 254, 0.15); color: #0ea5e9; }
.stat-izin { background: rgba(102, 126, 234, 0.15); color: #5a67d8; }
.stat-alfa { background: rgba(245, 87, 108, 0.15); color: #e53e3e; }

.stat-value {
    font-size: 2rem;
    font-weight: 800;
    line-height: 1;
    background: linear-gradient(135deg, #1a1a2e 0%, #4a4a6a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stat-label {
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table-rekap {
    border: none;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
.table-rekap thead {
    background: linear-gradient(135deg, #1a1a2e 0%, #2d2d44 100%);
    color: white;
}
.table-rekap thead th {
    border: none;
    padding: 1rem;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
}
.table-rekap tbody tr {
    transition: all 0.2s;
    border-bottom: 1px solid #f1f5f9;
}
.table-rekap tbody tr:hover {
    background: linear-gradient(90deg, rgba(37, 211, 102, 0.05) 0%, rgba(37, 211, 102, 0.02) 100%);
}
.table-rekap td {
    padding: 1rem;
    vertical-align: middle;
}

.rekap-badge {
    padding: 0.5rem 0.85rem;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.8rem;
    display: inline-block;
    min-width: 45px;
}
.badge-hadir { background: rgba(37, 211, 102, 0.15); color: #128C7E; }
.badge-terlambat { background: rgba(255, 193, 7, 0.2); color: #b38600; }
.badge-sakit { background: rgba(79, 172, 254, 0.15); color: #0ea5e9; }
.badge-izin { background: rgba(102, 126, 234, 0.15); color: #5a67d8; }
.badge-alfa { background: rgba(245, 87, 108, 0.15); color: #e53e3e; }

.progress-custom {
    height: 10px;
    border-radius: 10px;
    background: #e2e8f0;
    overflow: hidden;
}
.progress-bar-gradient {
    background: var(--gradient-wa);
    border-radius: 10px;
    transition: width 1s ease-in-out;
}

.kelas-info-card {
    border: none;
    border-radius: 20px;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-left: 5px solid #25D366;
}
.kelas-info-card .kelas-icon {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    background: var(--gradient-wa);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}

.chart-card {
    border: none;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.08);
    overflow: hidden;
}
.chart-card .card-header {
    background: white;
    border-bottom: 2px solid #f1f5f9;
    padding: 1.25rem 1.5rem;
    font-weight: 700;
    color: #1a1a2e;
}
.chart-card .card-body {
    padding: 1.5rem;
}

.avatar-mini {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.9rem;
    color: white;
}
.avatar-laki { background: var(--gradient-purple); }
.avatar-perempuan { background: var(--gradient-orange); }

.percentage-excellent { color: #128C7E; font-weight: 700; }
.percentage-good { color: #f59e0b; font-weight: 700; }
.percentage-poor { color: #e53e3e; font-weight: 700; }

.btn-export {
    border-radius: 12px;
    padding: 0.6rem 1.25rem;
    font-weight: 600;
    transition: all 0.3s;
}
.btn-export:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.form-select, .form-control {
    border-radius: 12px;
    border: 2px solid #e2e8f0;
    padding: 0.75rem 1rem;
    font-weight: 500;
    transition: all 0.3s;
}
.form-select:focus, .form-control:focus {
    border-color: #25D366;
    box-shadow: 0 0 0 4px rgba(37, 211, 102, 0.1);
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}
.empty-state-icon {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2.5rem;
    color: #94a3b8;
}

.floating-filters {
    animation: float 3s ease-in-out infinite;
}
@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}

.rank-badge {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.75rem;
}
.rank-1 { background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); color: white; }
.rank-2 { background: linear-gradient(135deg, #C0C0C0 0%, #A8A8A8 100%); color: white; }
.rank-3 { background: linear-gradient(135deg, #CD7F32 0%, #B8860B 100%); color: white; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-wa-dark mb-0">
        <i class="fas fa-chart-pie me-2"></i>Rekap Absensi
    </h2>
    <?php if ($semester_id && $kelas_id): ?>
    <div class="d-flex gap-2">
        <a href="export.php?semester_id=<?= $semester_id ?>&kelas_id=<?= $kelas_id ?>&tgl_awal=<?= $tgl_awal ?>&tgl_akhir=<?= $tgl_akhir ?>&type=excel" class="btn btn-success btn-export">
            <i class="fas fa-file-excel me-2"></i>Export Excel
        </a>
        <a href="export.php?semester_id=<?= $semester_id ?>&kelas_id=<?= $kelas_id ?>&tgl_awal=<?= $tgl_awal ?>&tgl_akhir=<?= $tgl_akhir ?>&type=pdf" class="btn btn-danger btn-export" target="_blank">
            <i class="fas fa-file-pdf me-2"></i>Export PDF
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- Filter Section -->
<form method="GET">
    <div class="filter-card mb-4">
        <div class="filter-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <i class="fas fa-sliders-h me-3"></i>
                <span class="fw-semibold" style="font-size: 1.1rem;">Filter & Pengaturan</span>
            </div>
            <div class="floating-filters">
                <i class="fas fa-magic" style="opacity: 0.7;"></i>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row g-4 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-bold text-muted small text-uppercase" style="letter-spacing: 1px;">
                        <i class="fas fa-calendar-alt me-1" style="color: #25D366;"></i> Semester
                    </label>
                    <select name="semester_id" class="form-select" required>
                        <option value="">Pilih Semester</option>
                        <?php
                        $semester = conn()->query("SELECT * FROM semester ORDER BY is_active DESC, tahun_ajaran_id DESC, semester ASC");
                        while ($row = $semester->fetch_assoc()):
                        ?>
                        <option value="<?= $row['id'] ?>" <?= ($row['id'] == $semester_id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($row['nama']) ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-bold text-muted small text-uppercase" style="letter-spacing: 1px;">
                        <i class="fas fa-school me-1" style="color: #128C7E;"></i> Kelas
                    </label>
                    <select name="kelas_id" class="form-select">
                        <option value="">Semua Kelas</option>
                        <?php
                        $kelas = conn()->query("SELECT * FROM kelas ORDER BY nama_kelas");
                        while ($row = $kelas->fetch_assoc()):
                        ?>
                        <option value="<?= $row['id'] ?>" <?= ($row['id'] == $kelas_id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($row['nama_kelas']) ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label fw-bold text-muted small text-uppercase" style="letter-spacing: 1px;">
                        <i class="fas fa-play me-1" style="color: #667eea;"></i> Tanggal Awal
                    </label>
                    <input type="date" name="tgl_awal" class="form-control" value="<?= $tgl_awal ?>">
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label fw-bold text-muted small text-uppercase" style="letter-spacing: 1px;">
                        <i class="fas fa-stop me-1" style="color: #f5576c;"></i> Tanggal Akhir
                    </label>
                    <input type="date" name="tgl_akhir" class="form-control" value="<?= $tgl_akhir ?>">
                </div>
                <div class="col-lg-2 col-md-12">
                    <button type="submit" class="btn btn-wa-primary w-100" style="padding: 0.85rem;">
                        <i class="fas fa-search me-2"></i>Tampilkan
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<?php if ($semester_id && $kelas_id):
    $kelas = conn()->query("SELECT nama_kelas, wali_kelas FROM kelas WHERE id = $kelas_id")->fetch_assoc();
    
    $stats = conn()->query("
        SELECT 
            SUM(CASE WHEN status = 'Hadir' THEN 1 ELSE 0 END) as hadir,
            SUM(CASE WHEN status = 'Terlambat' THEN 1 ELSE 0 END) as terlambat,
            SUM(CASE WHEN status = 'Sakit' THEN 1 ELSE 0 END) as sakit,
            SUM(CASE WHEN status = 'Izin' THEN 1 ELSE 0 END) as izin,
            SUM(CASE WHEN status = 'Alfa' THEN 1 ELSE 0 END) as alfa,
            COUNT(*) as total
        FROM absensi 
        WHERE semester_id = $semester_id 
        AND kelas_id = $kelas_id
        AND tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'
    ")->fetch_assoc();

    $total_siswa = conn()->query("SELECT COUNT(*) as total FROM siswa WHERE kelas_id = $kelas_id AND (status = 'aktif' OR status IS NULL)")->fetch_assoc()['total'];
    $total_hari = (strtotime($tgl_akhir) - strtotime($tgl_awal)) / (60*60*24) + 1;
    $total_seharusnya = $total_siswa * $total_hari;
    $kehadiran_persen = $total_seharusnya > 0 ? round(($stats['hadir'] / $total_seharusnya) * 100, 1) : 0;
    
    $siswa = conn()->query("
        SELECT s.id, s.nama, s.jenis_kelamin,
            COALESCE(SUM(CASE WHEN a.status = 'Hadir' THEN 1 ELSE 0 END), 0) as hadir,
            COALESCE(SUM(CASE WHEN a.status = 'Terlambat' THEN 1 ELSE 0 END), 0) as terlambat,
            COALESCE(SUM(CASE WHEN a.status = 'Sakit' THEN 1 ELSE 0 END), 0) as sakit,
            COALESCE(SUM(CASE WHEN a.status = 'Izin' THEN 1 ELSE 0 END), 0) as izin,
            COALESCE(SUM(CASE WHEN a.status = 'Alfa' THEN 1 ELSE 0 END), 0) as alfa,
            COUNT(a.id) as total_absen
        FROM siswa s
        LEFT JOIN absensi a ON s.id = a.siswa_id 
            AND a.semester_id = $semester_id
            AND a.kelas_id = $kelas_id
            AND a.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'
        WHERE s.kelas_id = $kelas_id AND (s.status = 'aktif' OR s.status IS NULL)
        GROUP BY s.id, s.nama, s.jenis_kelamin
        ORDER BY (alfa + sakit + izin) ASC, hadir DESC, nama ASC
    ");

    $daily_stats = conn()->query("
        SELECT tanggal,
            SUM(CASE WHEN status = 'Hadir' THEN 1 ELSE 0 END) as hadir,
            SUM(CASE WHEN status = 'Alfa' THEN 1 ELSE 0 END) as alfa
        FROM absensi
        WHERE semester_id = $semester_id AND kelas_id = $kelas_id
        AND tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'
        GROUP BY tanggal
        ORDER BY tanggal
    ");
    
    $labels = [];
    $data_hadir = [];
    $data_alfa = [];
    while ($d = $daily_stats->fetch_assoc()) {
        $labels[] = date('d M', strtotime($d['tanggal']));
        $data_hadir[] = $d['hadir'];
        $data_alfa[] = $d['alfa'];
    }
?>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="stat-card bg-white shadow-sm">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <div class="stat-label text-muted mb-1">Tingkat Kehadiran</div>
                    <div class="stat-value"><?= $kehadiran_persen ?>%</div>
                </div>
                <div class="stat-icon stat-hadir">
                    <i class="fas fa-check-double"></i>
                </div>
            </div>
            <div class="progress-custom">
                <div class="progress-bar-gradient" style="width: <?= $kehadiran_persen ?>%"></div>
            </div>
            <small class="text-muted mt-2 d-block">
                <i class="fas fa-info-circle me-1"></i>
                <?= $stats['hadir'] ?> dari <?= $total_seharusnya ?> kehadiran
            </small>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card bg-white shadow-sm">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label text-muted mb-1">Hadir</div>
                    <div class="stat-value" style="color: #128C7E;"><?= $stats['hadir'] ?></div>
                </div>
                <div class="stat-icon stat-hadir">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card bg-white shadow-sm">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label text-muted mb-1">Terlambat</div>
                    <div class="stat-value" style="color: #e6a800;"><?= $stats['terlambat'] ?></div>
                </div>
                <div class="stat-icon stat-terlambat">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card bg-white shadow-sm">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label text-muted mb-1">Alpha</div>
                    <div class="stat-value" style="color: #e53e3e;"><?= $stats['alfa'] ?></div>
                </div>
                <div class="stat-icon stat-alfa">
                    <i class="fas fa-user-times"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card bg-white shadow-sm">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label text-muted mb-1">Sakit</div>
                    <div class="stat-value" style="color: #0ea5e9;"><?= $stats['sakit'] ?></div>
                </div>
                <div class="stat-icon stat-sakit">
                    <i class="fas fa-user-injured"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-white shadow-sm">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label text-muted mb-1">Izin</div>
                    <div class="stat-value" style="color: #5a67d8;"><?= $stats['izin'] ?></div>
                </div>
                <div class="stat-icon stat-izin">
                    <i class="fas fa-envelope-open"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="kelas-info-card card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="kelas-icon me-3">
                    <i class="fas fa-school"></i>
                </div>
                <div class="flex-grow-1">
                    <h5 class="mb-1 fw-bold">
                        <?= htmlspecialchars($kelas['nama_kelas']) ?>
                        <span class="badge bg-success ms-2" style="font-size: 0.7rem;"><?= $total_siswa ?> Siswa</span>
                    </h5>
                    <p class="mb-0 text-muted small">
                        <i class="fas fa-user-tie me-1"></i>
                        <?= htmlspecialchars($kelas['wali_kelas'] ?? 'Belum ada wali kelas') ?>
                    </p>
                    <p class="mb-0 text-muted small">
                        <i class="fas fa-calendar-range me-1"></i>
                        <?= date('d M Y', strtotime($tgl_awal)) ?> - <?= date('d M Y', strtotime($tgl_akhir)) ?>
                        <span class="mx-2">•</span>
                        <i class="fas fa-calendar-day me-1"></i><?= $total_hari ?> Hari
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row g-3 mb-4">
    <div class="col-lg-4">
        <div class="chart-card">
            <div class="card-header d-flex align-items-center">
                <i class="fas fa-chart-pie me-2" style="color: #25D366;"></i>
                Distribusi Kehadiran
            </div>
            <div class="card-body" style="height: 280px;">
                <canvas id="pieChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="chart-card">
            <div class="card-header d-flex align-items-center">
                <i class="fas fa-chart-bar me-2" style="color: #667eea;"></i>
                Grafik Kehadiran Harian
            </div>
            <div class="card-body" style="height: 280px;">
                <canvas id="barChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Table Section -->
<div class="card-custom">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);">
        <h6 class="mb-0 fw-bold">
            <i class="fas fa-list me-2" style="color: #25D366;"></i>Detail Absensi per Siswa
        </h6>
        <span class="badge bg-secondary rounded-pill"><?= $siswa->num_rows ?> Siswa</span>
    </div>
    <div class="table-responsive">
        <table class="table table-rekap mb-0">
            <thead>
                <tr>
                    <th class="text-center" style="width: 60px;">Rank</th>
                    <th class="text-center" style="width: 50px;">No</th>
                    <th>Nama Siswa</th>
                    <th class="text-center"><span class="rekap-badge badge-hadir">Hadir</span></th>
                    <th class="text-center"><span class="rekap-badge badge-terlambat">Telat</span></th>
                    <th class="text-center"><span class="rekap-badge badge-sakit">Sakit</span></th>
                    <th class="text-center"><span class="rekap-badge badge-izin">Izin</span></th>
                    <th class="text-center"><span class="rekap-badge badge-alfa">Alfa</span></th>
                    <th class="text-center">% Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1; 
                $rank = 1;
                while ($row = $siswa->fetch_assoc()):
                    $persen = $total_hari > 0 ? round(($row['hadir'] / $total_hari) * 100, 1) : 0;
                    $persen_class = $persen >= 80 ? 'percentage-excellent' : ($persen >= 60 ? 'percentage-good' : 'percentage-poor');
                    $initial = strtoupper(substr($row['nama'], 0, 1));
                    $avatar_class = ($row['jenis_kelamin'] === 'Laki-laki') ? 'avatar-laki' : 'avatar-perempuan';
                    
                    $rank_class = '';
                    if ($rank == 1) $rank_class = 'rank-1';
                    elseif ($rank == 2) $rank_class = 'rank-2';
                    elseif ($rank == 3) $rank_class = 'rank-3';
                ?>
                <tr>
                    <td class="text-center">
                        <?php if ($rank <= 3): ?>
                        <span class="rank-badge <?= $rank_class ?>"><?= $rank ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center text-muted"><?= $no++ ?></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-mini <?= $avatar_class ?> me-3"><?= $initial ?></div>
                            <div>
                                <div class="fw-semibold"><?= htmlspecialchars($row['nama']) ?></div>
                                <small class="text-muted"><?= $row['jenis_kelamin'] ?></small>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="rekap-badge badge-hadir"><?= $row['hadir'] ?></span>
                    </td>
                    <td class="text-center">
                        <span class="rekap-badge badge-terlambat"><?= $row['terlambat'] ?></span>
                    </td>
                    <td class="text-center">
                        <span class="rekap-badge badge-sakit"><?= $row['sakit'] ?></span>
                    </td>
                    <td class="text-center">
                        <span class="rekap-badge badge-izin"><?= $row['izin'] ?></span>
                    </td>
                    <td class="text-center">
                        <span class="rekap-badge badge-alfa"><?= $row['alfa'] ?></span>
                    </td>
                    <td class="text-center">
                        <span class="<?= $persen_class ?>"><?= $persen ?>%</span>
                    </td>
                </tr>
                <?php $rank++; endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const pieCtx = document.getElementById('pieChart').getContext('2d');
new Chart(pieCtx, {
    type: 'doughnut',
    data: {
        labels: ['Hadir', 'Terlambat', 'Sakit', 'Izin', 'Alfa'],
        datasets: [{
            data: [<?= $stats['hadir'] ?>, <?= $stats['terlambat'] ?>, <?= $stats['sakit'] ?>, <?= $stats['izin'] ?>, <?= $stats['alfa'] ?>],
            backgroundColor: [
                '#25D366',
                '#ffc107',
                '#0ea5e9',
                '#667eea',
                '#f5576c'
            ],
            borderWidth: 0,
            hoverOffset: 10
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '60%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            }
        }
    }
});

const barCtx = document.getElementById('barChart').getContext('2d');
new Chart(barCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [
            {
                label: 'Hadir',
                data: <?= json_encode($data_hadir) ?>,
                backgroundColor: '#25D366',
                borderRadius: 6,
                borderSkipped: false
            },
            {
                label: 'Alfa',
                data: <?= json_encode($data_alfa) ?>,
                backgroundColor: '#f5576c',
                borderRadius: 6,
                borderSkipped: false
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
                align: 'end',
                labels: {
                    usePointStyle: true,
                    pointStyle: 'circle',
                    padding: 20
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: '#f1f5f9'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});
</script>

<?php elseif ($semester_id && !$kelas_id): ?>
<div class="alert alert-info d-flex align-items-center">
    <i class="fas fa-info-circle me-2" style="font-size: 1.5rem;"></i>
    <div>
        <strong>Silakan pilih kelas</strong> untuk melihat rekap absensi.
    </div>
</div>

<?php else: ?>
<div class="empty-state">
    <div class="empty-state-icon">
        <i class="fas fa-chart-pie"></i>
    </div>
    <h4 class="fw-bold text-muted mb-2">Rekap Absensi</h4>
    <p class="text-muted mb-4">Pilih semester dan kelas untuk melihat laporan absensi siswa</p>
    <div class="d-flex justify-content-center gap-3">
        <div class="text-center px-4">
            <div class="stat-icon stat-hadir mx-auto mb-2">
                <i class="fas fa-user-check"></i>
            </div>
            <small class="text-muted">Tracking Kehadiran</small>
        </div>
        <div class="text-center px-4">
            <div class="stat-icon stat-terlambat mx-auto mb-2">
                <i class="fas fa-chart-line"></i>
            </div>
            <small class="text-muted">Analisis Data</small>
        </div>
        <div class="text-center px-4">
            <div class="stat-icon stat-izin mx-auto mb-2">
                <i class="fas fa-file-export"></i>
            </div>
            <small class="text-muted">Export Laporan</small>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require_once '../views/layout.php';
