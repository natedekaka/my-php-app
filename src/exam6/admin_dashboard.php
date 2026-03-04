<?php
// admin_dashboard.php - Dashboard Admin (Manajemen Ujian)

session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'koneksi.php';

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
    $status = $_POST['status'];
    $edit_id = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;
    
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
    <title>Dashboard Admin - Manajemen Ujian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background: #2c3e50; }
        .sidebar a { color: #fff; text-decoration: none; padding: 15px 20px; display: block; }
        .sidebar a:hover, .sidebar a.active { background: #34495e; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar p-0">
            <div class="p-3 text-center border-bottom">
                <h5 class="text-white mb-0"><i class="bi bi-mortarboard-fill"></i> Admin</h5>
            </div>
            <a href="admin_dashboard.php" class="active"><i class="bi bi-grid-1x2-fill me-2"></i> Manajemen Ujian</a>
            <a href="tambah_soal.php"><i class="bi bi-question-circle-fill me-2"></i> Bank Soal</a>
            <a href="rekap_nilai.php"><i class="bi bi-bar-chart-fill me-2"></i> Rekap Nilai</a>
            <a href="logout.php" class="text-warning mt-3"><i class="bi bi-box-arrow-right me-2"></i> Logout (<?= $_SESSION['admin_username'] ?>)</a>
        </div>

        <div class="col-md-10 p-4">
            <h3 class="mb-4">Manajemen Ujian</h3>
            
            <?php if ($message): ?>
                <div class="alert alert-<?= $message_type === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
                    <?= $message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><?= $edit_ujian ? 'Edit Ujian' : 'Tambah Ujian Baru' ?></h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <?php if ($edit_ujian): ?>
                            <input type="hidden" name="edit_id" value="<?= $edit_ujian['id'] ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Judul Ujian</label>
                            <input type="text" name="judul_ujian" class="form-control" required 
                                   value="<?= $edit_ujian ? htmlspecialchars($edit_ujian['judul_ujian']) : '' ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3"><?= $edit_ujian ? htmlspecialchars($edit_ujian['deskripsi']) : '' ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="nonaktif" <?= $edit_ujian && $edit_ujian['status'] === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                                <option value="aktif" <?= $edit_ujian && $edit_ujian['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                            </select>
                        </div>
                        
                        <button type="submit" name="simpan_ujian" class="btn btn-primary">
                            <i class="bi bi-save"></i> <?= $edit_ujian ? 'Perbarui' : 'Simpan' ?>
                        </button>
                        <?php if ($edit_ujian): ?>
                            <a href="admin_dashboard.php" class="btn btn-secondary">Batal</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Daftar Ujian</h5>
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul Ujian</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                                <th>Tgl Dibuat</th>
                                <th>Link Ujian</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['judul_ujian']) ?></td>
                                <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $row['status'] === 'aktif' ? 'success' : 'secondary' ?>">
                                        <?= strtoupper($row['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($row['tgl_dibuat'])) ?></td>
                                <td>
                                    <div class="input-group input-group-sm" style="max-width: 250px;">
                                        <input type="text" class="form-control" value="<?= 'ujian.php?id=' . $row['id'] ?>" id="link<?= $row['id'] ?>" readonly>
                                        <button class="btn btn-outline-secondary" type="button" onclick="copyLink(<?= $row['id'] ?>)">
                                            <i class="bi bi-clipboard"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted"> Atau </small>
                                    <a href="ujian.php?id=<?= $row['id'] ?>" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="bi bi-box-arrow-up-right"></i> Buka
                                    </a>
                                </td>
                                <td>
                                    <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                    <a href="?toggle=1&id=<?= $row['id'] ?>&status=<?= $row['status'] ?>" 
                                       class="btn btn-sm btn-<?= $row['status'] === 'aktif' ? 'danger' : 'success' ?>">
                                        <i class="bi bi-toggle-<?= $row['status'] === 'aktif' ? 'on' : 'off' ?>"></i>
                                    </a>
                                    <a href="tambah_soal.php?ujian=<?= $row['id'] ?>" class="btn btn-sm btn-info">
                                        <i class="bi bi-list-ol"></i>
                                    </a>
                                    <a href="?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Yakin hapus?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if ($result->num_rows === 0): ?>
                            <tr><td colspan="7" class="text-center text-muted">Belum ada ujian</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function copyLink(id) {
    var copyText = document.getElementById("link" + id);
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value).then(function() {
        alert("Link ujian copied!");
    });
}
</script>
</body>
</html>
