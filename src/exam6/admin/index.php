<?php
// admin/index.php - Dashboard Admin (Manajemen Ujian)

session_start();

header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';

$message = '';
$message_type = '';

if (isset($_GET['toggle']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $status = $_GET['status'] === 'aktif' ? 'nonaktif' : 'aktif';
    
    $stmt = $conn->prepare("UPDATE ujian SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    if ($stmt->execute()) {
        $message = "Status ujian berhasil diubah!";
        $message_type = 'success';
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_ujian'])) {
    $judul = trim($_POST['judul_ujian']);
    $deskripsi = trim($_POST['deskripsi']);
    $status = in_array($_POST['status'], ['aktif', 'nonaktif']) ? $_POST['status'] : 'nonaktif';
    $edit_id = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;
    
    if (empty($judul)) {
        $message = "Judul ujian wajib diisi!";
        $message_type = 'danger';
    } else {
        if ($edit_id > 0) {
            $stmt = $conn->prepare("UPDATE ujian SET judul_ujian = ?, deskripsi = ?, status = ? WHERE id = ?");
            $stmt->bind_param("sssi", $judul, $deskripsi, $status, $edit_id);
            $message = "Ujian berhasil diperbarui!";
        } else {
            $stmt = $conn->prepare("INSERT INTO ujian (judul_ujian, deskripsi, status) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $judul, $deskripsi, $status);
            $message = "Ujian berhasil ditambahkan!";
        }
        
        if ($stmt->execute()) {
            $message_type = 'success';
        }
        $stmt->close();
    }
}

if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $stmt = $conn->prepare("DELETE FROM ujian WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Ujian berhasil dihapus!";
        $message_type = 'success';
    }
    $stmt->close();
}

$result = $conn->query("SELECT * FROM ujian ORDER BY tgl_dibuat DESC");

$edit_ujian = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM ujian WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_result = $stmt->get_result();
    $edit_ujian = $edit_result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Dashboard Admin - Manajemen Ujian</title>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
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
        .btn-primary:hover { background: var(--primary-hover); border-color: var(--primary-hover); }
        
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
        
        .action-buttons .btn { margin-bottom: 0.25rem; }
        
        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 4rem 1rem 1rem; }
            .mobile-toggle { display: flex; }
            .overlay.show { display: block; }
            .page-header { padding: 1rem; flex-direction: column; align-items: flex-start; }
        }
        
        @media (max-width: 768px) {
            .card-body { padding: 1rem; }
            .table { font-size: 0.875rem; }
            .table thead th, .table tbody td { padding: 0.5rem; }
            .btn { width: 100%; margin-bottom: 0.5rem; }
            .action-buttons .btn { width: auto; margin-bottom: 0; }
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
            <a href="index.php" class="active"><i class="bi bi-grid-1x2-fill"></i> Manajemen Ujian</a>
            <a href="tambah_soal.php"><i class="bi bi-question-circle-fill"></i> Bank Soal</a>
            <a href="rekap_nilai.php"><i class="bi bi-bar-chart-fill"></i> Rekap Nilai</a>
            <a href="logout.php" class="text-warning mt-3"><i class="bi bi-box-arrow-right"></i> Logout (<?= htmlspecialchars($_SESSION['admin_username']) ?>)</a>
        </div>
    </div>

    <div class="main-content">
        <div class="page-header animate-fade-in">
            <h3><i class="bi bi-clipboard-data me-2"></i>Manajemen Ujian</h3>
            <span class="badge bg-primary fs-6"><?= $result->num_rows ?> ujian</span>
        </div>
        
        <?php if ($message): ?>
        <div class="alert alert-<?= $message_type ?> alert-dismissible fade show animate-fade-in">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="card animate-fade-in">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-<?= $edit_ujian ? 'pencil-square' : 'plus-circle' ?> me-2"></i><?= $edit_ujian ? 'Edit Ujian' : 'Tambah Ujian Baru' ?></span>
                <?php if ($edit_ujian): ?>
                <a href="index.php" class="btn btn-sm btn-secondary">
                    <i class="bi bi-x-lg"></i> Batal
                </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <form method="POST" autocomplete="off">
                    <?php if ($edit_ujian): ?>
                        <input type="hidden" name="edit_id" value="<?= $edit_ujian['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Judul Ujian <span class="text-danger">*</span></label>
                            <input type="text" name="judul_ujian" class="form-control" required 
                                   value="<?= $edit_ujian ? htmlspecialchars($edit_ujian['judul_ujian']) : '' ?>"
                                   placeholder="Contoh: Ujian Matematika Semester 1">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="nonaktif" <?= $edit_ujian && $edit_ujian['status'] === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                                <option value="aktif" <?= $edit_ujian && $edit_ujian['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3" 
                                  placeholder="Masukkan deskripsi ujian..."><?= $edit_ujian ? htmlspecialchars($edit_ujian['deskripsi']) : '' ?></textarea>
                    </div>
                    
                    <button type="submit" name="simpan_ujian" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> <?= $edit_ujian ? 'Perbarui' : 'Simpan' ?>
                    </button>
                </form>
            </div>
        </div>

        <div class="card animate-fade-in">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-list-ol me-2"></i>Daftar Ujian</span>
                <span class="badge bg-primary"><?= $result->num_rows ?> ujian</span>
            </div>
            <div class="card-body p-0">
                <?php if ($result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;">No</th>
                                <th>Judul</th>
                                <th class="text-center" style="width: 100px;">Status</th>
                                <th class="text-center" style="width: 120px;">Tgl Dibuat</th>
                                <th style="min-width: 200px;">Link</th>
                                <th class="text-center" style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td>
                                    <div class="fw-semibold"><?= htmlspecialchars($row['judul_ujian']) ?></div>
                                    <small class="text-muted"><?= htmlspecialchars(mb_strimwidth($row['deskripsi'] ?? '', 0, 50, '...')) ?></small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $row['status'] === 'aktif' ? 'success' : 'secondary' ?>">
                                        <?= strtoupper($row['status']) ?>
                                    </span>
                                </td>
                                <td class="text-center text-muted"><?= date('d/m/Y', strtotime($row['tgl_dibuat'])) ?></td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control" value="<?= '../ujian.php?id=' . $row['id'] ?>" id="link<?= $row['id'] ?>" readonly>
                                        <button class="btn btn-outline-secondary" type="button" onclick="copyLink(<?= $row['id'] ?>)" title="Copy Link">
                                            <i class="bi bi-clipboard"></i>
                                        </button>
                                        <a href="../ujian.php?id=<?= $row['id'] ?>" target="_blank" class="btn btn-outline-primary" title="Buka">
                                            <i class="bi bi-box-arrow-up-right"></i>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex gap-1 action-buttons justify-content-center">
                                        <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="?toggle=1&id=<?= $row['id'] ?>&status=<?= $row['status'] ?>" 
                                           class="btn btn-sm btn-<?= $row['status'] === 'aktif' ? 'danger' : 'success' ?>" 
                                           title="<?= $row['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                            <i class="bi bi-toggle-<?= $row['status'] === 'aktif' ? 'on' : 'off' ?>"></i>
                                        </a>
                                        <a href="tambah_soal.php?ujian=<?= $row['id'] ?>" class="btn btn-sm btn-info" title="Bank Soal">
                                            <i class="bi bi-list-ol"></i>
                                        </a>
                                        <a href="?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-danger" title="Hapus"
                                           onclick="return confirm('Yakin hapus ujian ini? Semua soal juga akan dihapus.')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">Belum ada ujian</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('show');
            document.querySelector('.overlay').classList.toggle('show');
        }
        
        function copyLink(id) {
            var copyText = document.getElementById("link" + id);
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(window.location.origin + '/' + copyText.value).then(function() {
                alert("Link ujian copied!");
            }).catch(function() {
                copyText.select();
                document.execCommand('copy');
                alert("Link ujian copied!");
            });
        }
    </script>
</body>
</html>
