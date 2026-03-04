<?php
// rekap_nilai.php - Rekap Nilai

session_start();

header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data:;");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'koneksi.php';

$message = '';
$message_type = '';

$ujian_list = $conn->query("SELECT id, judul_ujian FROM ujian ORDER BY judul_ujian");
$selected_ujian = isset($_GET['ujian']) ? (int)$_GET['ujian'] : 0;

$hasil_list = [];
$stats = ['total' => 0, 'rata' => 0, 'tertinggi' => 0, 'terendah' => 0];
if ($selected_ujian > 0) {
    $stmt = $conn->prepare("SELECT h.*, u.judul_ujian FROM hasil_ujian h JOIN ujian u ON h.id_ujian = u.id WHERE h.id_ujian = ? ORDER BY h.total_skor DESC");
    $stmt->bind_param("i", $selected_ujian);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $hasil_list[] = $row;
    }
    $stmt->close();
    
    if (count($hasil_list) > 0) {
        $scores = array_column($hasil_list, 'total_skor');
        $stats['total'] = count($hasil_list);
        $stats['rata'] = round(array_sum($scores) / count($scores), 1);
        $stats['tertinggi'] = max($scores);
        $stats['terendah'] = min($scores);
    }
}

if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $stmt = $conn->prepare("DELETE FROM hasil_ujian WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Data berhasil dihapus!";
        $message_type = 'success';
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Rekap Nilai - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --secondary: #64748b;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --dark: #1e293b;
            --light: #f8fafc;
            --border: #e2e8f0;
            --sidebar-width: 260px;
        }
        
        * { font-family: 'Inter', sans-serif; }
        
        body { background-color: #f1f5f9; min-height: 100vh; }
        
        .sidebar { 
            width: var(--sidebar-width); 
            min-height: 100vh; 
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: transform 0.3s ease;
        }
        
        .sidebar-brand { padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-brand h5 { color: #fff; font-weight: 600; margin: 0; }
        
        .sidebar a { 
            color: rgba(255,255,255,0.7); 
            text-decoration: none; 
            padding: 0.875rem 1.5rem; 
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
            font-size: 0.9375rem;
        }
        
        .sidebar a:hover { background: rgba(255,255,255,0.05); color: #fff; }
        .sidebar a.active { background: rgba(79, 70, 229, 0.2); color: #fff; border-left-color: var(--primary); }
        
        .main-content { margin-left: var(--sidebar-width); padding: 2rem; transition: margin-left 0.3s ease; }
        
        .page-header {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        
        .page-header h3 { margin: 0; font-weight: 600; color: var(--dark); }
        
        .card { border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 1.5rem; }
        .card-header { background: #fff; border-bottom: 1px solid var(--border); padding: 1.25rem 1.5rem; font-weight: 600; color: var(--dark); }
        .card-body { padding: 1.5rem; }
        
        .form-control, .form-select {
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 0.625rem 0.875rem;
            font-size: 0.9375rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        
        .btn { border-radius: 8px; padding: 0.625rem 1.25rem; font-weight: 500; transition: all 0.2s ease; }
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-success { background: var(--success); border-color: var(--success); }
        
        .stat-card {
            background: linear-gradient(135deg, var(--primary) 0%, #6366f1 100%);
            border-radius: 12px;
            padding: 1.25rem;
            color: white;
            text-align: center;
        }
        
        .stat-card.success { background: linear-gradient(135deg, var(--success) 0%, #34d399 100%); }
        .stat-card.warning { background: linear-gradient(135deg, var(--warning) 0%, #fbbf24 100%); }
        .stat-card.danger { background: linear-gradient(135deg, var(--danger) 0%, #f87171 100%); }
        
        .stat-value { font-size: 2rem; font-weight: 700; }
        .stat-label { font-size: 0.875rem; opacity: 0.9; }
        
        .table thead th { background: #f8fafc; border-bottom: 2px solid var(--border); color: var(--secondary); font-weight: 600; font-size: 0.8125rem; text-transform: uppercase; letter-spacing: 0.5px; padding: 1rem; }
        .table tbody td { padding: 1rem; vertical-align: middle; border-bottom: 1px solid var(--border); }
        .table tbody tr:hover { background: #f8fafc; }
        
        .badge { font-weight: 500; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.75rem; }
        
        .mobile-toggle {
            display: none;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1001;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.625rem;
            font-size: 1.25rem;
        }
        
        .overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 999; }
        
        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 4rem 1rem 1rem; }
            .mobile-toggle { display: flex; }
            .overlay.show { display: block; }
        }
        
        @media (max-width: 768px) {
            .card-body { padding: 1rem; }
            .stat-card { margin-bottom: 1rem; }
            .table { font-size: 0.875rem; }
            .btn { width: 100%; margin-bottom: 0.5rem; }
        }
        
        .animate-fade-in { animation: fadeIn 0.3s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>
    <button class="mobile-toggle" onclick="toggleSidebar()">
        <i class="bi bi-list"></i>
    </button>
    
    <div class="overlay" onclick="toggleSidebar()"></div>

    <div class="sidebar">
        <div class="sidebar-brand">
            <h5><i class="bi bi-mortarboard-fill me-2"></i>Admin Panel</h5>
        </div>
        <div class="sidebar-menu">
            <a href="admin_dashboard.php"><i class="bi bi-grid-1x2-fill"></i> Manajemen Ujian</a>
            <a href="tambah_soal.php"><i class="bi bi-question-circle-fill"></i> Bank Soal</a>
            <a href="rekap_nilai.php" class="active"><i class="bi bi-bar-chart-fill"></i> Rekap Nilai</a>
            <a href="logout.php" class="text-warning mt-3"><i class="bi bi-box-arrow-right"></i> Logout (<?= htmlspecialchars($_SESSION['admin_username']) ?>)</a>
        </div>
    </div>

    <div class="main-content">
        <div class="page-header animate-fade-in">
            <h3><i class="bi bi-bar-chart-line me-2"></i>Rekap Nilai</h3>
        </div>
        
        <?php if ($message): ?>
        <div class="alert alert-<?= $message_type ?> alert-dismissible fade show animate-fade-in">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="card animate-fade-in">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold"><i class="bi bi-file-earmark-text me-1"></i>Pilih Ujian</label>
                        <select name="ujian" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Pilih Ujian --</option>
                            <?php 
                            $ujian_list->data_seek(0);
                            while ($ujian = $ujian_list->fetch_assoc()): 
                            ?>
                            <option value="<?= $ujian['id'] ?>" <?= $selected_ujian == $ujian['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ujian['judul_ujian']) ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <?php if ($selected_ujian > 0): ?>
                    <div class="col-md-4">
                        <a href="ekspor_excel.php?ujian=<?= $selected_ujian ?>" class="btn btn-success w-100">
                            <i class="bi bi-file-earmark-excel me-1"></i> Ekspor Excel
                        </a>
                    </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <?php if ($selected_ujian > 0): ?>
        
        <?php if ($stats['total'] > 0): ?>
        <div class="row animate-fade-in">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-value"><?= $stats['total'] ?></div>
                    <div class="stat-label">Total Peserta</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card success">
                    <div class="stat-value"><?= $stats['rata'] ?></div>
                    <div class="stat-label">Rata-rata Skor</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card warning">
                    <div class="stat-value"><?= $stats['tertinggi'] ?></div>
                    <div class="stat-label">Skor Tertinggi</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card danger">
                    <div class="stat-value"><?= $stats['terendah'] ?></div>
                    <div class="stat-label">Skor Terendah</div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="card animate-fade-in">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-people me-2"></i>Hasil Ujian</span>
                <span class="badge bg-primary"><?= $stats['total'] ?> peserta</span>
            </div>
            <div class="card-body p-0">
                <?php if ($stats['total'] > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;">No</th>
                                <th>NIS</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th class="text-center" style="width: 80px;">Skor</th>
                                <th class="text-center" style="width: 140px;">Waktu Submit</th>
                                <th class="text-center" style="width: 70px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach ($hasil_list as $hasil): 
                            ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td><?= htmlspecialchars($hasil['nis']) ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($hasil['nama']) ?></td>
                                <td><?= htmlspecialchars($hasil['kelas']) ?></td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $hasil['total_skor'] >= $stats['rata'] ? 'success' : 'warning' ?>">
                                        <?= $hasil['total_skor'] ?>
                                    </span>
                                </td>
                                <td class="text-center text-muted"><?= date('d/m/Y H:i', strtotime($hasil['waktu_submit'])) ?></td>
                                <td class="text-center">
                                    <a href="?ujian=<?= $selected_ujian ?>&hapus=<?= $hasil['id'] ?>" class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Yakin hapus data ini?')" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">Belum ada peserta yang mengerjakan</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php else: ?>
        <div class="card animate-fade-in">
            <div class="card-body text-center py-5">
                <i class="bi bi-folder2-open text-muted" style="font-size: 4rem;"></i>
                <p class="text-muted mt-3">Silakan pilih ujian untuk melihat rekap nilai</p>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('show');
            document.querySelector('.overlay').classList.toggle('show');
        }
    </script>
</body>
</html>
