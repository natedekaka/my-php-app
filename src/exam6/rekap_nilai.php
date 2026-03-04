<?php
// rekap_nilai.php - Rekap Nilai

session_start();
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
if ($selected_ujian > 0) {
    $stmt = $conn->prepare("SELECT h.*, u.judul_ujian FROM hasil_ujian h JOIN ujian u ON h.id_ujian = u.id WHERE h.id_ujian = ? ORDER BY h.total_skor DESC");
    $stmt->bind_param("i", $selected_ujian);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $hasil_list[] = $row;
    }
    $stmt->close();
}

if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $stmt = $conn->prepare("DELETE FROM hasil_ujian WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Data dihapus!";
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
    <title>Rekap Nilai</title>
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
            <a href="admin_dashboard.php"><i class="bi bi-grid-1x2-fill me-2"></i> Manajemen Ujian</a>
            <a href="tambah_soal.php"><i class="bi bi-question-circle-fill me-2"></i> Bank Soal</a>
            <a href="rekap_nilai.php" class="active"><i class="bi bi-bar-chart-fill me-2"></i> Rekap Nilai</a>
            <a href="logout.php" class="text-warning mt-3"><i class="bi bi-box-arrow-right me-2"></i> Logout (<?= $_SESSION['admin_username'] ?>)</a>
        </div>

        <div class="col-md-10 p-4">
            <h3 class="mb-4">Rekap Nilai</h3>
            
            <?php if ($message): ?>
                <div class="alert alert-<?= $message_type ?> alert-dismissible fade show">
                    <?= $message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Pilih Ujian</label>
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
                        <div class="col-md-4 d-flex align-items-end">
                            <a href="ekspor_excel.php?ujian=<?= $selected_ujian ?>" class="btn btn-success">
                                <i class="bi bi-file-earmark-excel"></i> Ekspor ke Excel
                            </a>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <?php if ($selected_ujian > 0): ?>
            <div class="card">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Hasil Ujian</h5>
                    <span class="badge bg-light text-dark"><?= count($hasil_list) ?> peserta</span>
                </div>
                <div class="card-body">
                    <?php if (count($hasil_list) > 0): ?>
                    <table class="table table-hover table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>NIS</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>Skor</th>
                                <th>Waktu Submit</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            $total_skor = 0;
                            foreach ($hasil_list as $hasil): 
                                $total_skor += $hasil['total_skor'];
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($hasil['nis']) ?></td>
                                <td><?= htmlspecialchars($hasil['nama']) ?></td>
                                <td><?= htmlspecialchars($hasil['kelas']) ?></td>
                                <td><span class="badge bg-primary"><?= $hasil['total_skor'] ?></span></td>
                                <td><?= date('d/m/Y H:i:s', strtotime($hasil['waktu_submit'])) ?></td>
                                <td>
                                    <a href="?ujian=<?= $selected_ujian ?>&hapus=<?= $hasil['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="4" class="text-end">Rata-rata:</th>
                                <th><span class="badge bg-success"><?= count($hasil_list) > 0 ? round($total_skor / count($hasil_list), 1) : 0 ?></span></th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                    <?php else: ?>
                    <p class="text-center text-muted py-4">Belum ada peserta</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="alert alert-info">Pilih ujian untuk melihat rekap.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
