<?php
session_start();
include 'koneksi.php';
include 'security.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$events = [];
$resultEvents = $conn->query("SELECT * FROM events WHERE aktif = 'Y' ORDER BY nama_event");
if ($resultEvents) {
    while ($row = $resultEvents->fetch_assoc()) {
        $events[] = $row;
    }
}
?>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Daftar Hadir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f8f9fa; padding: 20px 0; }
        .card { border: none; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .card-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0 !important; }
        .nav-tabs .nav-link.active { border-color: #dee2e6 #dee2e6 #fff; }
        .nav-tabs .nav-link { color: #495057; }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-cog"></i> Pengaturan Admin</h4>
            <div>
                <a href="index.php" class="btn btn-light btn-sm"><i class="fas fa-home"></i> Beranda</a>
                <a href="rekap.php" class="btn btn-light btn-sm"><i class="fas fa-list"></i> Rekap</a>
                <a href="rekap_admin.php" class="btn btn-warning btn-sm"><i class="fas fa-list"></i> Rekap Admin</a>
                <a href="dashboard.php" class="btn btn-light btn-sm"><i class="fas fa-chart-bar"></i> Dashboard</a>
                <a href="login.php?logout=1" class="btn btn-danger btn-sm"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs" id="adminTab" role="tablist">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#events"><i class="fas fa-calendar"></i> Kelola Jenis Acara</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#fields"><i class="fas fa-th-list"></i> Kelola Form</button></li>
            </ul>
            
            <div class="tab-content mt-3">
                <!-- Tab Kelola Jenis Acara -->
                <div class="tab-pane fade show active" id="events">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-light"><h6 class="mb-0">Tambah/Edit Acara</h6></div>
                                <div class="card-body">
                                    <form method="POST" action="admin_proses.php">
                                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                        <input type="hidden" name="aksi" value="simpan_event">
                                        <input type="hidden" name="id" id="event_id">
                                        <div class="mb-3">
                                            <label class="form-label">Nama Acara</label>
                                            <input type="text" class="form-control" name="nama_event" id="nama_event" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Deskripsi</label>
                                            <textarea class="form-control" name="deskripsi" id="deskripsi" rows="2"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select class="form-select" name="aktif">
                                                <option value="Y">Aktif</option>
                                                <option value="N">Nonaktif</option>
                                            </select>
                                        </div>
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                                            <button type="button" class="btn btn-outline-secondary" onclick="resetEventForm()">Batal</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr><th>No</th><th>Nama Acara</th><th>Deskripsi</th><th>Status</th><th>Aksi</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        include 'koneksi.php';
                                        $result = $conn->query("SELECT * FROM events ORDER BY id DESC");
                                        $no = 1;
                                        while ($row = $result->fetch_assoc()):
                                        ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= htmlspecialchars($row['nama_event']) ?></td>
                                            <td><?= htmlspecialchars($row['deskripsi'] ?? '-') ?></td>
                                            <td>
                                                <?php if($row['aktif'] == 'Y'): ?>
                                                    <span class="badge bg-success">Aktif</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Nonaktif</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" onclick="editEvent(<?= $row['id'] ?>, '<?= htmlspecialchars($row['nama_event']) ?>', '<?= htmlspecialchars($row['deskripsi'] ?? '') ?>', '<?= $row['aktif'] ?>')"><i class="fas fa-edit"></i></button>
                                                <a href="admin_proses.php?aksi=hapus_event&id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tab Kelola Form Fields -->
                <div class="tab-pane fade" id="fields">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-light"><h6 class="mb-0">Tambah/Edit Field</h6></div>
                                <div class="card-body">
                                    <form method="POST" action="admin_proses.php">
                                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                        <input type="hidden" name="aksi" value="simpan_field">
                                        <input type="hidden" name="id" id="field_id">
                                        <div class="mb-3">
                                            <label class="form-label">Pilih Acara</label>
                                            <select class="form-select" name="event_id" id="event_id_field" required>
                                                <option value="">Pilih acara...</option>
                                                <?php foreach ($events as $evt): ?>
                                                    <option value="<?= $evt['id'] ?>"><?= htmlspecialchars($evt['nama_event']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <small class="text-muted">Field ini akan ditampilkan hanya untuk acara yang dipilih</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nama Field</label>
                                            <input type="text" class="form-control" name="nama_field" id="nama_field" placeholder="contoh: nama_lengkap" required>
                                            <small class="text-muted">Tanpa spasi, gunakan underscore</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Label</label>
                                            <input type="text" class="form-control" name="label" id="label" placeholder="contoh: Nama Lengkap" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Tipe Input</label>
                                            <select class="form-select" name="tipe" id="tipe">
                                                <option value="text">Text</option>
                                                <option value="number">Number</option>
                                                <option value="date">Date</option>
                                                <option value="select">Select (Dropdown)</option>
                                                <option value="textarea">Textarea</option>
                                                <option value="signature">Signature (Tanda Tangan)</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Placeholder</label>
                                            <input type="text" class="form-control" name="placeholder" id="placeholder">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Urutan</label>
                                            <input type="number" class="form-control" name="urutan" id="urutan" value="0">
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="wajib" value="Y" id="wajib">
                                                <label class="form-check-label" for="wajib">Wajib diisi</label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select class="form-select" name="aktif">
                                                <option value="Y">Aktif</option>
                                                <option value="N">Nonaktif</option>
                                            </select>
                                        </div>
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                                            <button type="button" class="btn btn-outline-secondary" onclick="resetFieldForm()">Batal</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Filter berdasarkan Acara</label>
                                <select class="form-select" id="filter_event" onchange="filterTableByEvent()">
                                    <option value="">Semua Acara</option>
                                    <?php foreach ($events as $evt): ?>
                                        <option value="<?= $evt['id'] ?>"><?= htmlspecialchars($evt['nama_event']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr><th>Urutan</th><th>Nama Field</th><th>Label</th><th>Tipe</th><th>Wajib</th><th>Acara</th><th>Status</th><th>Aksi</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $result = $conn->query("SELECT f.*, e.nama_event FROM form_fields f LEFT JOIN events e ON f.event_id = e.id ORDER BY f.urutan ASC, f.id ASC");
                                        while ($row = $result->fetch_assoc()):
                                        ?>
                                        <tr data-event-id="<?= $row['event_id'] ?? '' ?>">
                                            <td><?= $row['urutan'] ?></td>
                                            <td><code><?= htmlspecialchars($row['nama_field']) ?></code></td>
                                            <td><?= htmlspecialchars($row['label']) ?></td>
                                            <td><?= htmlspecialchars($row['tipe']) ?></td>
                                            <td><?= $row['wajib'] == 'Y' ? '<span class="badge bg-danger">Ya</span>' : '-' ?></td>
                                            <td><?= $row['event_id'] ? '<span class="badge bg-info">' . htmlspecialchars($row['nama_event']) . '</span>' : '<span class="badge bg-warning">Belum dipilih</span>' ?></td>
                                            <td><?= $row['aktif'] == 'Y' ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Nonaktif</span>' ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" onclick="editField(<?= $row['id'] ?>, '<?= $row['nama_field'] ?>', '<?= $row['label'] ?>', '<?= $row['tipe'] ?>', '<?= $row['placeholder'] ?? '' ?>', '<?= $row['urutan'] ?>', '<?= $row['wajib'] ?>', '<?= $row['aktif'] ?>', '<?= $row['event_id'] ?? '' ?>')"><i class="fas fa-edit"></i></button>
                                                <a href="admin_proses.php?aksi=hapus_field&id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                        <?php endwhile; $conn->close(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function editEvent(id, nama, desc, aktif) {
    document.getElementById('event_id').value = id;
    document.getElementById('nama_event').value = nama;
    document.getElementById('deskripsi').value = desc;
    document.querySelector('select[name="aktif"]').value = aktif;
}
function resetEventForm() {
    document.getElementById('event_id').value = '';
    document.getElementById('nama_event').value = '';
    document.getElementById('deskripsi').value = '';
}
function editField(id, nama, label, tipe, placeholder, urutan, wajib, aktif, eventId) {
    document.getElementById('field_id').value = id;
    document.getElementById('nama_field').value = nama;
    document.getElementById('label').value = label;
    document.getElementById('tipe').value = tipe;
    document.getElementById('placeholder').value = placeholder;
    document.getElementById('urutan').value = urutan;
    document.getElementById('wajib').checked = (wajib == 'Y');
    document.querySelector('select[name="aktif"]:last-of-type').value = aktif;
    document.getElementById('event_id_field').value = eventId || '';
}
function resetFieldForm() {
    document.getElementById('field_id').value = '';
    document.getElementById('event_id_field').value = '';
    document.getElementById('nama_field').value = '';
    document.getElementById('label').value = '';
    document.getElementById('tipe').value = 'text';
    document.getElementById('placeholder').value = '';
    document.getElementById('urutan').value = '0';
    document.getElementById('wajib').checked = false;
}
function filterTableByEvent() {
    const filterEvent = document.getElementById('filter_event').value;
    const rows = document.querySelectorAll('#fields tbody tr');
    
    rows.forEach(row => {
        const rowEventId = row.dataset.eventId;
        if (!filterEvent || rowEventId === filterEvent) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
</body>
</html>
