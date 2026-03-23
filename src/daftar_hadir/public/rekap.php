<?php
/**
 * File: rekap.php
 * Deskripsi: Halaman rekap kehadiran publik (tanpa login)
 */

include '../includes/koneksi.php';
include '../includes/security.php';

$filter_event = $_GET['filter_event'] ?? '';
$filter_tgl_awal = $_GET['filter_tgl_awal'] ?? '';
$filter_tgl_akhir = $_GET['filter_tgl_akhir'] ?? '';

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

$allFields = [];
$allFieldsQuery = "SELECT * FROM form_fields WHERE aktif = 'Y' AND tipe != 'signature' ORDER BY COALESCE(event_id, 0) ASC, urutan ASC, id ASC";
$resultAllFields = $conn->query($allFieldsQuery);
if ($resultAllFields) {
    while ($row = $resultAllFields->fetch_assoc()) {
        $allFields[] = $row;
    }
}

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
$result = false;
if ($stmt) {
    if (count($params) > 0) {
        $types = str_repeat("s", count($params));
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result() ?: false;
}

$events = [];
$resultEvents = $conn->query("SELECT * FROM events WHERE aktif = 'Y' ORDER BY nama_event");
if ($resultEvents) {
    while ($row = $resultEvents->fetch_assoc()) {
        $events[] = $row;
    }
}

$eventFields = [];
$eventFieldsQuery = "SELECT COALESCE(event_id, 0) as evt_id, GROUP_CONCAT(nama_field ORDER BY urutan ASC, id ASC) as fields FROM form_fields WHERE aktif = 'Y' AND tipe != 'signature' GROUP BY COALESCE(event_id, 0)";
$resultEventFields = $conn->query($eventFieldsQuery);
if ($resultEventFields) {
    while ($row = $resultEventFields->fetch_assoc()) {
        $eventFields[$row['evt_id']] = explode(',', $row['fields']);
    }
}
if (!isset($eventFields[0])) {
    $eventFields[0] = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Daftar Hadir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px 0; }
        .card { border: none; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
        .card-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 20px 20px 0 0 !important; padding: 20px; }
        .table { margin-bottom: 0; }
        .table thead th { background: #f8f9fa; border-bottom: 2px solid #dee2e6; font-weight: 600; white-space: nowrap; }
        .table-hover tbody tr:hover { background: #f8f9fa; }
        .ttd-thumbnail { width: 80px; height: 40px; object-fit: contain; cursor: pointer; border: 1px solid #dee2e6; border-radius: 5px; transition: transform 0.2s; }
        .ttd-thumbnail:hover { transform: scale(2); border-color: #667eea; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; }
        .links { margin-top: 20px; text-align: center; }
        .links a { color: rgba(255,255,255,0.9); text-decoration: none; padding: 10px 20px; border-radius: 20px; background: rgba(255,255,255,0.15); margin: 0 5px; transition: all 0.3s; }
        .links a:hover { background: rgba(255,255,255,0.25); transform: translateY(-2px); }
        @media print { .no-print { display: none !important; } .card { box-shadow: none; } }
        @media (max-width: 576px) { .card-body { padding: 15px; } .table { font-size: 0.85rem; } }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h4 class="mb-0"><i class="fas fa-clipboard-list"></i> Rekap Daftar Kehadiran</h4>
            <div>
                <a href="index.php" class="btn btn-light btn-sm"><i class="fas fa-plus"></i> Tambah Data</a>
                <a href="admin.php" class="btn btn-light btn-sm"><i class="fas fa-cog"></i> Admin</a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3 no-print">
                <div class="col-12">
                    <form method="GET" class="row g-2">
                        <div class="col-md-3 col-12">
                            <select class="form-select" name="filter_event">
                                <option value="">Semua Acara</option>
                                <?php foreach ($events as $event): ?>
                                    <option value="<?= $event['id'] ?>" <?= ($filter_event == $event['id']) ? 'selected' : '' ?>>
                                        <?= e($event['nama_event']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 col-6">
                            <input type="date" class="form-control" name="filter_tgl_awal" value="<?= $_GET['filter_tgl_awal'] ?? '' ?>">
                        </div>
                        <div class="col-md-3 col-6">
                            <input type="date" class="form-control" name="filter_tgl_akhir" value="<?= $_GET['filter_tgl_akhir'] ?? '' ?>">
                        </div>
                        <div class="col-md-3 col-12">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                            <?php if(!empty($filter_event) || !empty($_GET['filter_tgl_awal'])): ?>
                                <a href="rekap.php" class="btn btn-outline-secondary"><i class="fas fa-times"></i></a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row mb-3 no-print">
                <div class="col-12 text-end">
                    <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Cetak</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Waktu</th>
                            <th>Acara</th>
                            <?php foreach ($allFields as $field): ?>
                                <th><?= e($field['label']) ?></th>
                            <?php endforeach; ?>
                            <th class="text-center">Tanda Tangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        if ($result && $result->num_rows > 0):
                            while ($row = $result->fetch_assoc()):
                                $data = json_decode($row['data_json'], true);
                                $eventId = $row['event_id'] ?? 0;
                                $rowFields = $eventFields[$eventId] ?? $eventFields[0] ?? [];
                        ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($row['waktu'])) ?></td>
                            <td><?= e($row['nama_event']) ?></td>
                            <?php foreach ($allFields as $field): ?>
                                <td><?= e($data[$field['nama_field']] ?? '-') ?></td>
                            <?php endforeach; ?>
                            <td class="text-center">
                                <?php 
                                $ttdFile = $row['ttd_file'] ?? '';
                                $imgSrc = '';
                                
                                if ($ttdFile) {
                                    // Try various path formats stored in DB
                                    $filename = basename($ttdFile);
                                    $possibleImgSrc = [
                                        $ttdFile,
                                        '../uploads/' . $filename,
                                        $filename
                                    ];
                                    
                                    foreach ($possibleImgSrc as $src) {
                                        $path = __DIR__ . '/' . $src;
                                        if (file_exists($path)) {
                                            $imgSrc = $src;
                                            break;
                                        }
                                    }
                                    
                                    // Also try absolute path (for temp files)
                                    if (!$imgSrc && file_exists($ttdFile)) {
                                        $imgSrc = $ttdFile;
                                    }
                                    
                                    if ($imgSrc) {
                                        echo '<img src="' . e($imgSrc) . '" alt="TTD" class="ttd-thumbnail" style="width:60px;height:30px;object-fit:contain;border:1px solid #ddd;border-radius:4px;" data-bs-toggle="modal" data-bs-target="#ttdModal" onclick="showTTD(this.src)">';
                                    } else {
                                        echo '<span class="text-danger" title="File: ' . e($ttdFile) . '">⚠️</span>';
                                    }
                                } else {
                                    echo '<span class="text-muted">-</span>';
                                }
                                ?>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td colspan="<?= 5 + count($allFields) ?>" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i><br>Belum ada data kehadiran
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-3 text-muted">
                <small>Total: <?= ($result && $result->num_rows) ? $result->num_rows : 0 ?> data</small>
            </div>
        </div>
    </div>
    <div class="links">
        <a href="index.php"><i class="fas fa-plus"></i> Daftar Hadir</a>
        <a href="admin.php"><i class="fas fa-cog"></i> Admin</a>
    </div>
</div>

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
<?php 
if ($stmt) $stmt->close();
$conn->close();
