<?php
session_start();
include 'koneksi.php';
include 'security.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Daftar Hadir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background: #f8f9fa; padding: 20px 0; }
        .card { border: none; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .card-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0 !important; }
        .stat-card { border-radius: 15px; color: white; transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon { font-size: 2.5rem; opacity: 0.8; }
    </style>
</head>
<body>
<?php
include 'koneksi.php';

// Statistik umum
$total_kehadiran = $conn->query("SELECT COUNT(*) as total FROM presensi")->fetch_assoc()['total'];
$total_event = $conn->query("SELECT COUNT(*) as total FROM events WHERE aktif = 'Y'")->fetch_assoc()['total'];
$total_today = $conn->query("SELECT COUNT(*) as total FROM presensi WHERE DATE(waktu) = CURDATE()")->fetch_assoc()['total'];

// Kehadiran per event
$event_stats = [];
$result = $conn->query("
    SELECT e.nama_event, COUNT(p.id) as total 
    FROM events e 
    LEFT JOIN presensi p ON e.id = p.event_id 
    WHERE e.aktif = 'Y' 
    GROUP BY e.id, e.nama_event 
    ORDER BY total DESC
");
while ($row = $result->fetch_assoc()) {
    $event_stats[] = $row;
}

// Kehadiran 7 hari terakhir
$daily_stats = [];
$result = $conn->query("
    SELECT DATE(waktu) as tanggal, COUNT(*) as total 
    FROM presensi 
    WHERE waktu >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
    GROUP BY DATE(waktu) 
    ORDER BY tanggal
");
while ($row = $result->fetch_assoc()) {
    $daily_stats[] = $row;
}

// Recent data
$recent = $conn->query("
    SELECT p.*, e.nama_event 
    FROM presensi p 
    JOIN events e ON p.event_id = e.id 
    ORDER BY p.waktu DESC LIMIT 5
");

$conn->close();
?>

<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-chart-bar"></i> Dashboard Statistik Kehadiran</h4>
            <div>
                <a href="index.php" class="btn btn-light btn-sm"><i class="fas fa-home"></i> Beranda</a>
                <a href="rekap.php" class="btn btn-light btn-sm"><i class="fas fa-list"></i> Rekap</a>
                <a href="admin.php" class="btn btn-light btn-sm"><i class="fas fa-cog"></i> Admin</a>
            </div>
        </div>
        <div class="card-body">
            <!-- Statistik Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="card-body text-center py-4">
                            <i class="fas fa-users stat-icon"></i>
                            <h2 class="mt-2 mb-0"><?= $total_kehadiran ?></h2>
                            <small>Total Kehadiran</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <div class="card-body text-center py-4">
                            <i class="fas fa-calendar stat-icon"></i>
                            <h2 class="mt-2 mb-0"><?= $total_event ?></h2>
                            <small>Jenis Acara Aktif</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <div class="card-body text-center py-4">
                            <i class="fas fa-clock stat-icon"></i>
                            <h2 class="mt-2 mb-0"><?= $total_today ?></h2>
                            <small>Hari Ini</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-chart-pie"></i> Kehadiran per Acara</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="eventChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-chart-line"></i> Trend 7 Hari Terakhir</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="dailyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Data -->
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-history"></i> Data Terbaru</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>Acara</th>
                                    <th>Nama</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $recent->fetch_assoc()): ?>
                                <?php $data = json_decode($row['data_json'], true); ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($row['waktu'])) ?></td>
                                    <td><?= htmlspecialchars($row['nama_event']) ?></td>
                                    <td><?= htmlspecialchars($data['nama_lengkap'] ?? '-') ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const eventLabels = <?= json_encode(array_column($event_stats, 'nama_event')) ?>;
const eventData = <?= json_encode(array_column($event_stats, 'total')) ?>;

new Chart(document.getElementById('eventChart'), {
    type: 'doughnut',
    data: {
        labels: eventLabels,
        datasets: [{
            data: eventData,
            backgroundColor: ['#667eea', '#f093fb', '#4facfe', '#00f2fe', '#43e97b', '#fa709a']
        }]
    },
    options: { responsive: true }
});

const dailyLabels = <?= json_encode(array_map(fn($d) => date('d/m', strtotime($d['tanggal'])), $daily_stats)) ?>;
const dailyData = <?= json_encode(array_column($daily_stats, 'total')) ?>;

new Chart(document.getElementById('dailyChart'), {
    type: 'bar',
    data: {
        labels: dailyLabels,
        datasets: [{
            label: 'Jumlah Kehadiran',
            data: dailyData,
            backgroundColor: '#667eea',
            borderRadius: 5
        }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
});
</script>
</body>
</html>