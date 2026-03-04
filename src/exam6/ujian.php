<?php
// ujian.php - Halaman Ujian Siswa (Tampilan Baru)

require_once 'koneksi.php';

$message = '';
$message_type = '';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID ujian tidak valid");
}

$id_ujian = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT * FROM ujian WHERE id = ?");
$stmt->bind_param("i", $id_ujian);
$stmt->execute();
$result = $stmt->get_result();
$ujian = $result->fetch_assoc();
$stmt->close();

if (!$ujian) {
    die("Ujian tidak ditemukan");
}

if ($ujian['status'] !== 'aktif') {
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ujian Ditutup</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>
            * { font-family: 'Poppins', sans-serif; }
            body { background: linear-gradient(135deg, #ff6b6b 0%, #ffa500 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
            .card { border: none; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card p-5 text-center">
                        <i class="bi bi-x-circle-fill text-danger" style="font-size: 5rem;"></i>
                        <h2 class="mt-4 fw-bold">Maaf, Ujian Ditutup</h2>
                        <p class="text-muted"><?= htmlspecialchars($ujian['judul_ujian']) ?></p>
                        <p class="text-muted">Silakan hubungi guru untuk informasi lebih lanjut.</p>
                        <a href="index.php" class="btn btn-secondary mt-3">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

$stmt = $conn->prepare("SELECT * FROM soal WHERE id_ujian = ? ORDER BY id");
$stmt->bind_param("i", $id_ujian);
$stmt->execute();
$result = $stmt->get_result();
$soal_list = [];
while ($row = $result->fetch_assoc()) {
    $soal_list[] = $row;
}
$stmt->close();

if (count($soal_list) === 0) {
    die("Belum ada soal. Hubungi guru.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_ujian'])) {
    $nis = trim($_POST['nis']);
    $nama = trim($_POST['nama']);
    $kelas = trim($_POST['kelas']);
    
    if (empty($nis) || empty($nama) || empty($kelas)) {
        $message = "Mohon lengkapi data identitas!";
        $message_type = 'danger';
    } else {
        $total_skor = 0;
        foreach ($soal_list as $soal) {
            $jawaban = isset($_POST['jawaban_' . $soal['id']]) ? $_POST['jawaban_' . $soal['id']] : '';
            if ($jawaban === $soal['kunci_jawaban']) {
                $total_skor += $soal['poin'];
            }
        }
        
        $stmt = $conn->prepare("INSERT INTO hasil_ujian (id_ujian, nis, nama, kelas, total_skor) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isssi", $id_ujian, $nis, $nama, $kelas, $total_skor);
        
        if ($stmt->execute()) {
            ?>
            <!DOCTYPE html>
            <html lang="id">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Ujian Selesai</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
                <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
                <style>
                    * { font-family: 'Poppins', sans-serif; }
                    body { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
                    .card { border: none; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
                    .skor-box { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-size: 4rem; font-weight: 700; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="card p-5 text-center">
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                                <h2 class="mt-4 fw-bold">Terima Kasih!</h2>
                                <p class="text-muted">Jawaban Anda telah disubmit.</p>
                                
                                <div class="my-4">
                                    <p class="text-muted mb-1">Skor Anda</p>
                                    <div class="skor-box"><?= $total_skor ?></div>
                                </div>
                                
                                <div class="bg-light rounded p-3 mb-4">
                                    <p class="mb-1"><strong><?= htmlspecialchars($nama) ?></strong></p>
                                    <p class="mb-1 text-muted">NIS: <?= htmlspecialchars($nis) ?></p>
                                    <p class="mb-0 text-muted">Kelas: <?= htmlspecialchars($kelas) ?></p>
                                </div>
                                
                                <a href="index.php" class="btn btn-success">
                                    <i class="bi bi-house-door me-2"></i>Halaman Utama
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </body>
            </html>
            <?php
            exit;
        } else {
            $message = "Terjadi kesalahan. Coba lagi.";
            $message_type = 'danger';
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($ujian['judul_ujian']) ?> - Ujian Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        body { background: #f8f9fa; }
        
        .ujian-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px 0;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }
        
        .ujian-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
        }
        
        .soal-card { 
            background: white; 
            border-radius: 16px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.08); 
            margin-bottom: 25px;
            padding: 25px;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }
        
        .soal-card:hover {
            border-color: rgba(102, 126, 234, 0.3);
        }
        
        .soal-number {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            margin-right: 15px;
        }
        
        .option-label {
            cursor: pointer;
            padding: 15px 20px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            margin-bottom: 12px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
        }
        
        .option-label:hover { 
            background: #f8f9fa; 
            border-color: #667eea;
            transform: translateX(5px);
        }
        
        .option-label input:checked + .option-content {
            font-weight: 600;
        }
        
        .option-label:has(input:checked) {
            background: rgba(102, 126, 234, 0.1);
            border-color: #667eea;
        }
        
        .option-letter {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 12px;
            flex-shrink: 0;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 15px 50px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }
        
        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
        }
        
        .identitas-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 25px;
            margin-bottom: 25px;
        }
        
        .soal-img {
            max-width: 100%;
            max-height: 300px;
            border-radius: 12px;
            margin: 10px 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .opsi-img {
            max-width: 80px;
            max-height: 60px;
            border-radius: 8px;
            margin-left: 10px;
            object-fit: contain;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="ujian-header">
        <div class="container position-relative">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <a href="index.php" class="text-white text-decoration-none mb-2 d-inline-block">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <h2 class="text-white fw-bold mb-1"><?= htmlspecialchars($ujian['judul_ujian']) ?></h2>
                    <p class="text-white-50 mb-0"><?= htmlspecialchars($ujian['deskripsi']) ?></p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="badge bg-white bg-opacity-25 text-white fs-6 px-3 py-2">
                        <i class="bi bi-question-circle me-2"></i><?= count($soal_list) ?> Soal
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        <?php if ($message): ?>
            <div class="alert alert-<?= $message_type ?> alert-dismissible fade show">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" id="formUjian">
            <!-- Identitas -->
            <div class="identitas-card">
                <h5 class="fw-bold mb-4">
                    <i class="bi bi-person-badge me-2 text-primary"></i>Identitas Siswa
                </h5>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">NIS <span class="text-danger">*</span></label>
                        <input type="text" name="nis" class="form-control form-control-lg" required placeholder="Masukkan NIS">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control form-control-lg" required placeholder="Masukkan nama">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Kelas <span class="text-danger">*</span></label>
                        <input type="text" name="kelas" class="form-control form-control-lg" required placeholder="Contoh: X IPA 1">
                    </div>
                </div>
            </div>

            <!-- Daftar Soal -->
            <?php $no = 1; foreach ($soal_list as $soal): ?>
            <div class="soal-card">
                <div class="d-flex align-items-start mb-3">
                    <span class="soal-number"><?= $no ?></span>
                    <div class="flex-grow-1">
                        <p class="mb-2 fw-medium fs-5"><?= nl2br(htmlspecialchars($soal['pertanyaan'])) ?></p>
                        <?php if ($soal['gambar_pertanyaan']): ?>
                            <img src="uploads/<?= $soal['gambar_pertanyaan'] ?>" class="soal-img" alt="Gambar Pertanyaan">
                        <?php endif; ?>
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-star me-1"></i>Poin: <?= $soal['poin'] ?>
                        </small>
                    </div>
                </div>
                
                <div class="ms-5">
                    <?php 
                    $options = [
                        'a' => ['text' => $soal['opsi_a'], 'img' => $soal['gambar_a']],
                        'b' => ['text' => $soal['opsi_b'], 'img' => $soal['gambar_b']],
                        'c' => ['text' => $soal['opsi_c'], 'img' => $soal['gambar_c']],
                        'd' => ['text' => $soal['opsi_d'], 'img' => $soal['gambar_d']],
                        'e' => ['text' => $soal['opsi_e'], 'img' => $soal['gambar_e']]
                    ];
                    
                    foreach ($options as $key => $opt): 
                    ?>
                    <label class="option-label">
                        <input type="radio" name="jawaban_<?= $soal['id'] ?>" value="<?= $key ?>" required class="d-none">
                        <span class="option-letter"><?= strtoupper($key) ?></span>
                        <span class="option-content">
                            <?php if ($opt['img']): ?>
                                <img src="uploads/<?= $opt['img'] ?>" class="opsi-img" alt="Gambar <?= strtoupper($key) ?>">
                            <?php else: ?>
                                <?= htmlspecialchars($opt['text']) ?>
                            <?php endif; ?>
                        </span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php $no++; endforeach; ?>

            <!-- Submit -->
            <div class="text-center mb-5">
                <button type="submit" name="submit_ujian" class="btn btn-primary btn-submit text-white" 
                        onclick="return confirm('Apakah Anda yakin ingin Submit jawaban?')">
                    <i class="bi bi-send-fill me-2"></i>Kirim Jawaban
                </button>
            </div>
        </form>
        
        <footer class="text-center text-muted py-4">
            <small>&copy; <?= date('Y') ?> Sistem Ujian Online</small>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
