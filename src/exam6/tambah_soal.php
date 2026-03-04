<?php
// tambah_soal.php - Bank Soal dengan Upload Gambar

session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'koneksi.php';

$upload_dir = 'uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

function uploadGambar($file, $prefix) {
    if ($file['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($ext, $allowed)) {
            $filename = $prefix . '_' . time() . '.' . $ext;
            $target = 'uploads/' . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $target)) {
                return $filename;
            }
        }
    }
    return null;
}

function hapusGambar($filename) {
    if ($filename && file_exists('uploads/' . $filename)) {
        unlink('uploads/' . $filename);
    }
}

$message = '';
$message_type = '';

$ujian_list = $conn->query("SELECT id, judul_ujian, status FROM ujian ORDER BY judul_ujian");

$selected_ujian = isset($_GET['ujian']) ? (int)$_GET['ujian'] : ($ujian_list->fetch_assoc()['id'] ?? 0);
if ($selected_ujian > 0) {
    $ujian_list->data_seek(0);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_soal'])) {
    $id_ujian = (int)$_POST['id_ujian'];
    $pertanyaan = trim($_POST['pertanyaan']);
    $opsi_a = trim($_POST['opsi_a']);
    $opsi_b = trim($_POST['opsi_b']);
    $opsi_c = trim($_POST['opsi_c']);
    $opsi_d = trim($_POST['opsi_d']);
    $opsi_e = trim($_POST['opsi_e']);
    $kunci = $_POST['kunci_jawaban'];
    $poin = (int)$_POST['poin'];
    $edit_id = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;
    
    $gambar_pertanyaan = null;
    $gambar_a = null;
    $gambar_b = null;
    $gambar_c = null;
    $gambar_d = null;
    $gambar_e = null;
    
    if ($edit_id > 0) {
        $stmt = $conn->prepare("SELECT * FROM soal WHERE id = ?");
        $stmt->bind_param("i", $edit_id);
        $stmt->execute();
        $old_soal = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        $gambar_pertanyaan = $old_soal['gambar_pertanyaan'] ?? null;
        $gambar_a = $old_soal['gambar_a'] ?? null;
        $gambar_b = $old_soal['gambar_b'] ?? null;
        $gambar_c = $old_soal['gambar_c'] ?? null;
        $gambar_d = $old_soal['gambar_d'] ?? null;
        $gambar_e = $old_soal['gambar_e'] ?? null;
    }
    
    if (!empty($_FILES['gambar_pertanyaan']['name'])) {
        $gambar_pertanyaan = uploadGambar($_FILES['gambar_pertanyaan'], 'soal');
    }
    if (!empty($_FILES['gambar_a']['name'])) {
        $gambar_a = uploadGambar($_FILES['gambar_a'], 'opsia');
    }
    if (!empty($_FILES['gambar_b']['name'])) {
        $gambar_b = uploadGambar($_FILES['gambar_b'], 'opsib');
    }
    if (!empty($_FILES['gambar_c']['name'])) {
        $gambar_c = uploadGambar($_FILES['gambar_c'], 'opsic');
    }
    if (!empty($_FILES['gambar_d']['name'])) {
        $gambar_d = uploadGambar($_FILES['gambar_d'], 'opsid');
    }
    if (!empty($_FILES['gambar_e']['name'])) {
        $gambar_e = uploadGambar($_FILES['gambar_e'], 'opsie');
    }
    
    if ($edit_id > 0) {
        $stmt = $conn->prepare("UPDATE soal SET pertanyaan=?, gambar_pertanyaan=?, opsi_a=?, gambar_a=?, opsi_b=?, gambar_b=?, opsi_c=?, gambar_c=?, opsi_d=?, gambar_d=?, opsi_e=?, gambar_e=?, kunci_jawaban=?, poin=? WHERE id=?");
        $stmt->bind_param("sssssssssssssii", $pertanyaan, $gambar_pertanyaan, $opsi_a, $gambar_a, $opsi_b, $gambar_b, $opsi_c, $gambar_c, $opsi_d, $gambar_d, $opsi_e, $gambar_e, $kunci, $poin, $edit_id);
        $message = "Soal berhasil diperbarui!";
    } else {
        $stmt = $conn->prepare("INSERT INTO soal (id_ujian, pertanyaan, gambar_pertanyaan, opsi_a, gambar_a, opsi_b, gambar_b, opsi_c, gambar_c, opsi_d, gambar_d, opsi_e, gambar_e, kunci_jawaban, poin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssssssssssi", $id_ujian, $pertanyaan, $gambar_pertanyaan, $opsi_a, $gambar_a, $opsi_b, $gambar_b, $opsi_c, $gambar_c, $opsi_d, $gambar_d, $opsi_e, $gambar_e, $kunci, $poin);
        $message = "Soal berhasil ditambahkan!";
    }
    
    if ($stmt->execute()) {
        $message_type = 'success';
    }
    $stmt->close();
}

if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    
    $stmt = $conn->prepare("SELECT * FROM soal WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $soal = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if ($soal) {
        hapusGambar($soal['gambar_pertanyaan']);
        hapusGambar($soal['gambar_a']);
        hapusGambar($soal['gambar_b']);
        hapusGambar($soal['gambar_c']);
        hapusGambar($soal['gambar_d']);
        hapusGambar($soal['gambar_e']);
        
        $stmt = $conn->prepare("DELETE FROM soal WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $message = "Soal berhasil dihapus!";
            $message_type = 'success';
        }
        $stmt->close();
    }
}

$soal_list = [];
if ($selected_ujian > 0) {
    $stmt = $conn->prepare("SELECT * FROM soal WHERE id_ujian = ? ORDER BY id");
    $stmt->bind_param("i", $selected_ujian);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $soal_list[] = $row;
    }
    $stmt->close();
}

$edit_soal = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM soal WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_result = $stmt->get_result();
    $edit_soal = $edit_result->fetch_assoc();
    $stmt->close();
    if ($edit_soal) {
        $selected_ujian = $edit_soal['id_ujian'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Soal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background: #2c3e50; }
        .sidebar a { color: #fff; text-decoration: none; padding: 15px 20px; display: block; }
        .sidebar a:hover, .sidebar a.active { background: #34495e; }
        .preview-img { max-width: 150px; max-height: 100px; object-fit: contain; border: 1px solid #ddd; border-radius: 4px; }
        .gambar-preview { max-width: 80px; max-height: 60px; object-fit: contain; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar p-0">
            <div class="p-3 text-center border-bottom">
                <h5 class="text-white mb-0"><i class="bi bi-mortarboard-fill"></i> Admin</h5>
            </div>
            <a href="admin_dashboard.php"><i class="bi bi-grid-1x2-fill me-2"></i> Manajemen Ujian</a>
            <a href="tambah_soal.php" class="active"><i class="bi bi-question-circle-fill me-2"></i> Bank Soal</a>
            <a href="rekap_nilai.php"><i class="bi bi-bar-chart-fill me-2"></i> Rekap Nilai</a>
            <a href="logout.php" class="text-warning mt-3"><i class="bi bi-box-arrow-right me-2"></i> Logout (<?= $_SESSION['admin_username'] ?>)</a>
        </div>

        <div class="col-md-10 p-4">
            <h3 class="mb-4">Bank Soal</h3>
            
            <?php if ($message): ?>
                <div class="alert alert-<?= $message_type === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
                    <?= $message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Pilih Ujian</label>
                            <select name="ujian" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Pilih Ujian --</option>
                                <?php 
                                $ujian_list->data_seek(0);
                                while ($ujian = $ujian_list->fetch_assoc()): 
                                ?>
                                <option value="<?= $ujian['id'] ?>" <?= $selected_ujian == $ujian['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($ujian['judul_ujian']) ?> (<?= $ujian['status'] ?>)
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <?php if ($selected_ujian > 0): ?>
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><?= $edit_soal ? 'Edit Soal' : 'Tambah Soal Baru' ?></h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <?php if ($edit_soal): ?>
                            <input type="hidden" name="edit_id" value="<?= $edit_soal['id'] ?>">
                        <?php else: ?>
                            <input type="hidden" name="id_ujian" value="<?= $selected_ujian ?>">
                        <?php endif; ?>
                        
                        <div class="mb-4 p-3 bg-light rounded">
                            <label class="form-label fw-bold">Pertanyaan</label>
                            <textarea name="pertanyaan" class="form-control mb-2" rows="3" required><?= $edit_soal ? htmlspecialchars($edit_soal['pertanyaan']) : '' ?></textarea>
                            <label class="form-label">Gambar Pertanyaan (opsional)</label>
                            <input type="file" name="gambar_pertanyaan" class="form-control" accept="image/*">
                            <?php if ($edit_soal && $edit_soal['gambar_pertanyaan']): ?>
                                <div class="mt-2">
                                    <img src="uploads/<?= $edit_soal['gambar_pertanyaan'] ?>" class="preview-img" alt="Gambar Pertanyaan">
                                    <small class="text-muted d-block">Gambar sudah ada</small>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="row">
                            <!-- Opsi A -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Opsi A</label>
                                <input type="text" name="opsi_a" class="form-control mb-2" required value="<?= $edit_soal ? htmlspecialchars($edit_soal['opsi_a']) : '' ?>">
                                <label class="form-label">Gambar Opsi A</label>
                                <input type="file" name="gambar_a" class="form-control" accept="image/*">
                                <?php if ($edit_soal && $edit_soal['gambar_a']): ?>
                                    <img src="uploads/<?= $edit_soal['gambar_a'] ?>" class="gambar-preview mt-1" alt="Gambar A">
                                <?php endif; ?>
                            </div>
                            
                            <!-- Opsi B -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Opsi B</label>
                                <input type="text" name="opsi_b" class="form-control mb-2" required value="<?= $edit_soal ? htmlspecialchars($edit_soal['opsi_b']) : '' ?>">
                                <label class="form-label">Gambar Opsi B</label>
                                <input type="file" name="gambar_b" class="form-control" accept="image/*">
                                <?php if ($edit_soal && $edit_soal['gambar_b']): ?>
                                    <img src="uploads/<?= $edit_soal['gambar_b'] ?>" class="gambar-preview mt-1" alt="Gambar B">
                                <?php endif; ?>
                            </div>
                            
                            <!-- Opsi C -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Opsi C</label>
                                <input type="text" name="opsi_c" class="form-control mb-2" required value="<?= $edit_soal ? htmlspecialchars($edit_soal['opsi_c']) : '' ?>">
                                <label class="form-label">Gambar Opsi C</label>
                                <input type="file" name="gambar_c" class="form-control" accept="image/*">
                                <?php if ($edit_soal && $edit_soal['gambar_c']): ?>
                                    <img src="uploads/<?= $edit_soal['gambar_c'] ?>" class="gambar-preview mt-1" alt="Gambar C">
                                <?php endif; ?>
                            </div>
                            
                            <!-- Opsi D -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Opsi D</label>
                                <input type="text" name="opsi_d" class="form-control mb-2" required value="<?= $edit_soal ? htmlspecialchars($edit_soal['opsi_d']) : '' ?>">
                                <label class="form-label">Gambar Opsi D</label>
                                <input type="file" name="gambar_d" class="form-control" accept="image/*">
                                <?php if ($edit_soal && $edit_soal['gambar_d']): ?>
                                    <img src="uploads/<?= $edit_soal['gambar_d'] ?>" class="gambar-preview mt-1" alt="Gambar D">
                                <?php endif; ?>
                            </div>
                            
                            <!-- Opsi E -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Opsi E</label>
                                <input type="text" name="opsi_e" class="form-control mb-2" required value="<?= $edit_soal ? htmlspecialchars($edit_soal['opsi_e']) : '' ?>">
                                <label class="form-label">Gambar Opsi E</label>
                                <input type="file" name="gambar_e" class="form-control" accept="image/*">
                                <?php if ($edit_soal && $edit_soal['gambar_e']): ?>
                                    <img src="uploads/<?= $edit_soal['gambar_e'] ?>" class="gambar-preview mt-1" alt="Gambar E">
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">Kunci Jawaban</label>
                                <select name="kunci_jawaban" class="form-select">
                                    <option value="a" <?= $edit_soal && $edit_soal['kunci_jawaban'] === 'a' ? 'selected' : '' ?>>A</option>
                                    <option value="b" <?= $edit_soal && $edit_soal['kunci_jawaban'] === 'b' ? 'selected' : '' ?>>B</option>
                                    <option value="c" <?= $edit_soal && $edit_soal['kunci_jawaban'] === 'c' ? 'selected' : '' ?>>C</option>
                                    <option value="d" <?= $edit_soal && $edit_soal['kunci_jawaban'] === 'd' ? 'selected' : '' ?>>D</option>
                                    <option value="e" <?= $edit_soal && $edit_soal['kunci_jawaban'] === 'e' ? 'selected' : '' ?>>E</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">Poin</label>
                                <input type="number" name="poin" class="form-control" value="<?= $edit_soal ? $edit_soal['poin'] : 10 ?>" min="1">
                            </div>
                        </div>
                        
                        <button type="submit" name="simpan_soal" class="btn btn-primary">
                            <i class="bi bi-save"></i> <?= $edit_soal ? 'Perbarui' : 'Simpan' ?>
                        </button>
                        <?php if ($edit_soal): ?>
                            <a href="tambah_soal.php?ujian=<?= $selected_ujian ?>" class="btn btn-secondary">Batal</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Soal</h5>
                    <span class="badge bg-light text-dark"><?= count($soal_list) ?> soal</span>
                </div>
                <div class="card-body">
                    <?php if (count($soal_list) > 0): ?>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Pertanyaan</th>
                                <th>Gambar</th>
                                <th>Kunci</th>
                                <th>Poin</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($soal_list as $soal): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars(mb_strimwidth($soal['pertanyaan'], 0, 60, '...')) ?></td>
                                <td>
                                    <?php if ($soal['gambar_pertanyaan'] || $soal['gambar_a'] || $soal['gambar_b'] || $soal['gambar_c'] || $soal['gambar_d'] || $soal['gambar_e']): ?>
                                        <i class="bi bi-image-fill text-success"></i>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-success"><?= strtoupper($soal['kunci_jawaban']) ?></span></td>
                                <td><?= $soal['poin'] ?></td>
                                <td>
                                    <a href="?ujian=<?= $selected_ujian ?>&edit=<?= $soal['id'] ?>" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="?ujian=<?= $selected_ujian ?>&hapus=<?= $soal['id'] ?>" class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Yakin hapus?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <p class="text-center text-muted">Belum ada soal</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php else: ?>
            <div class="alert alert-info">Pilih ujian terlebih dahulu.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
