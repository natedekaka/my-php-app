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
    <title>Rekap Daftar Hadir - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f8f9fa; padding: 20px 0; }
        .card { border: none; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .card-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0 !important; }
        .table { margin-bottom: 0; }
        .table thead th { background: #f8f9fa; border-bottom: 2px solid #dee2e6; font-weight: 600; white-space: nowrap; }
        .table-hover tbody tr:hover { background: #f8f9fa; }
        .ttd-thumbnail { width: 80px; height: 40px; object-fit: contain; cursor: pointer; border: 1px solid #dee2e6; border-radius: 5px; transition: transform 0.2s; }
        .ttd-thumbnail:hover { transform: scale(2); border-color: #667eea; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; }
        @media print {
            .no-print { display: none !important; }
            .card { box-shadow: none; }
            .table { font-size: 12px; }
            body { background: white; }
        }
    </style>
</head>
<body>

<?php
// Ambil filter event jika ada
$filter_event = $_GET['filter_event'] ?? '';
$filter_tgl_awal = $_GET['filter_tgl_awal'] ?? '';
$filter_tgl_akhir = $_GET['filter_tgl_akhir'] ?? '';

// Ambil semua field aktif untuk header tabel (filter berdasarkan event jika dipilih)
$fields = [];
$fieldsQuery = "SELECT * FROM form_fields WHERE aktif = 'Y' AND tipe != 'signature'";
if (!empty($filter_event)) {
    $fieldsQuery .= " AND (event_id IS NULL OR event_id = '" . (int)$filter_event . "')";
}
$fieldsQuery .= " ORDER BY urutan ASC, id ASC";

$resultFields = $conn->query($fieldsQuery);
if ($resultFields) {
    while ($row = $resultFields->fetch_assoc()) {
        $fields[] = $row;
    }
}

// Build query
$whereClause = [];
$params = [];

if (!empty($filter_event)) {
    $whereClause[] = "p.event_id = ?";
    $params[] = $filter_event;
}

if (!empty($filter_tgl_awal)) {
    $whereClause[] = "DATE(p.waktu) >= ?";
    $params[] = $filter_tgl_awal;
}

if (!empty($filter_tgl_akhir)) {
    $whereClause[] = "DATE(p.waktu) <= ?";
    $params[] = $filter_tgl_akhir;
}

$whereStr = !empty($whereClause) ? "WHERE " . implode(" AND ", $whereClause) : "";

$sql = "SELECT p.id, p.event_id, p.data_json, p.ttd_file, p.waktu, e.nama_event FROM presensi p 
        JOIN events e ON p.event_id = e.id 
        $whereStr 
        ORDER BY p.waktu DESC";

$stmt = $conn->prepare($sql);
if ($stmt) {
    if (count($params) > 0) {
        $types = str_repeat("s", count($params));
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result() ?: false;
} else {
    $result = false;
}

// Ambil semua event untuk dropdown filter
$events = [];
$resultEvents = $conn->query("SELECT * FROM events WHERE aktif = 'Y' ORDER BY nama_event");
if ($resultEvents) {
    while ($row = $resultEvents->fetch_assoc()) {
        $events[] = $row;
    }
}
?>

<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-clipboard-list"></i> Rekap Daftar Kehadiran</h4>
            <div>
                <a href="index.php" class="btn btn-light btn-sm"><i class="fas fa-plus"></i> Tambah Data</a>
                <a href="admin.php" class="btn btn-light btn-sm"><i class="fas fa-cog"></i> Pengaturan</a>
            </div>
        </div>
        <div class="card-body">
            
            <!-- Filter & Tombol Cetak -->
            <div class="row mb-3 no-print">
                <div class="col-md-12">
                    <form method="GET" class="row g-2">
                        <div class="col-md-3">
                            <select class="form-select" name="filter_event">
                                <option value="">Semua Acara</option>
                                <?php foreach ($events as $event): ?>
                                    <option value="<?= $event['id'] ?>" <?= ($filter_event == $event['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($event['nama_event']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" name="filter_tgl_awal" value="<?= $_GET['filter_tgl_awal'] ?? '' ?>" placeholder="Tanggal Awal">
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" name="filter_tgl_akhir" value="<?= $_GET['filter_tgl_akhir'] ?? '' ?>" placeholder="Tanggal Akhir">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                            <?php if(!empty($filter_event) || !empty($_GET['filter_tgl_awal'])): ?>
                                <a href="rekap.php" class="btn btn-outline-secondary"><i class="fas fa-times"></i></a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row mb-3 no-print">
                <div class="col-md-8">
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="btn-group">
                        <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Cetak</button>
                        <a href="export.php?filter_event=<?= $filter_event ?>&filter_tgl_awal=<?= $_GET['filter_tgl_awal'] ?? '' ?>&filter_tgl_akhir=<?= $_GET['filter_tgl_akhir'] ?? '' ?>" class="btn btn-success"><i class="fas fa-file-excel"></i> Export Excel</a>
                        <a href="export.php?type=csv&filter_event=<?= $filter_event ?>&filter_tgl_awal=<?= $_GET['filter_tgl_awal'] ?? '' ?>&filter_tgl_akhir=<?= $_GET['filter_tgl_akhir'] ?? '' ?>" class="btn btn-warning"><i class="fas fa-file-csv"></i> Export CSV</a>
                    </div>
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Waktu</th>
                            <th>Acara</th>
                            <?php foreach ($fields as $field): ?>
                                <th><?= htmlspecialchars($field['label']) ?></th>
                            <?php endforeach; ?>
                            <th class="text-center">Tanda Tangan</th>
                            <th class="text-center">Aksi</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        if ($result && $result->num_rows > 0):
                            while ($row = $result->fetch_assoc()):
                                $data = json_decode($row['data_json'], true);
                        ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($row['waktu'])) ?></td>
                            <td><?= htmlspecialchars($row['nama_event']) ?></td>
                            <?php foreach ($fields as $field): ?>
                                <td><?= htmlspecialchars($data[$field['nama_field']] ?? '-') ?></td>
                            <?php endforeach; ?>
                            <td class="text-center">
                                <?php 
                                $ttdFile = $row['ttd_file'] ?? '';
                                if ($ttdFile) {
                                    $exists = false;
                                    $imgSrc = '';
                                    
                                    if (file_exists(__DIR__ . '/' . $ttdFile)) {
                                        $imgSrc = $ttdFile;
                                        $exists = true;
                                    } elseif (file_exists(__DIR__ . '/uploads/' . basename($ttdFile))) {
                                        $imgSrc = 'uploads/' . basename($ttdFile);
                                        $exists = true;
                                    }
                                    
                                    if ($exists) {
                                        echo '<img src="' . e($imgSrc) . '" alt="TTD" class="ttd-thumbnail" data-bs-toggle="modal" data-bs-target="#ttdModal" onclick="showTTD(this.src)">';
                                        echo '<a href="' . e($imgSrc) . '" download class="btn btn-sm btn-outline-primary ms-1" title="Download TTD"><i class="fas fa-download"></i></a>';
                                    } else {
                                        echo '<span class="text-muted">-</span>';
                                    }
                                } else {
                                    echo '<span class="text-muted">-</span>';
                                }
                                ?>
                            </td>
                            <td class="text-center">
                                <a href="admin_proses.php?aksi=hapus_presensi&id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus data ini?')" title="Hapus"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php 
                            endwhile;
                        else:
                        ?>
                        <tr>
                            <td colspan="<?= 5 + count($fields) ?>" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                <?= ($result && $result->num_rows > 0) ? 'Tidak ada data cocok dengan filter' : 'Belum ada data kehadiran' ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-3 text-muted">
                <small>Total: <?= ($result && $result->num_rows) ? $result->num_rows : 0 ?> data</small>
            </div>

            <?php 
            $stmt->close();
            $conn->close();
            ?>
        </div>
    </div>
</div>

<!-- Modal Preview TTD Besar -->
<div class="modal fade" id="ttdModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tanda Tangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="ttdPreview" src="" alt="Tanda Tangan" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function showTTD(src) {
    document.getElementById('ttdPreview').src = src;
}
</script>
</body>
</html>
