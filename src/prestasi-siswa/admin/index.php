<?php
session_start();
require_once '../config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$db = getDB();

// Get data for tabs
$siswa = $db->query("SELECT * FROM siswa ORDER BY nama_siswa");
$prestasis = $db->query("SELECT p.*, s.nama_siswa, s.kelas FROM prestasi p LEFT JOIN siswa s ON p.siswa_id = s.id ORDER BY p.created_at DESC");
$guru = $db->query("SELECT * FROM guru ORDER BY nama_guru");

// Get stats
$totalPrestasi = $db->query("SELECT COUNT(*) as total FROM prestasi")->fetch_assoc()['total'];
$totalSiswa = $db->query("SELECT COUNT(*) as total FROM siswa")->fetch_assoc()['total'];
$totalPending = $db->query("SELECT COUNT(*) as total FROM prestasi WHERE status_publikasi = 'draft'")->fetch_assoc()['total'];
$totalGuru = $db->query("SELECT COUNT(*) as total FROM guru")->fetch_assoc()['total'];
$totalPrestasiGuru = $db->query("SELECT COUNT(*) as total FROM prestasi_guru")->fetch_assoc()['total'];
$totalPrestasiSekolah = $db->query("SELECT COUNT(*) as total FROM prestasi_sekolah")->fetch_assoc()['total'];
$totalAlumniPTN = $db->query("SELECT COUNT(*) as total FROM alumni_ptn")->fetch_assoc()['total'];

// Handle add/edit/delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'delete_siswa') {
        $id = (int)$_POST['id'];
        $db->query("DELETE FROM prestasi WHERE siswa_id = $id");
        $db->query("DELETE FROM siswa WHERE id = $id");
        header('Location: index.php');
        exit;
    }
    
    if ($action === 'delete_guru') {
        $id = (int)$_POST['id'];
        $db->query("DELETE FROM prestasi_guru WHERE guru_id = $id");
        $db->query("DELETE FROM guru WHERE id = $id");
        header('Location: index.php');
        exit;
    }
    
    if ($action === 'edit_siswa') {
        $id = (int)$_POST['id'];
        $nis = $_POST['nis'];
        $nama = $_POST['nama_siswa'];
        $kelas = $_POST['kelas'];
        
        $stmt = $db->prepare("UPDATE siswa SET nis = ?, nama_siswa = ?, kelas = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nis, $nama, $kelas, $id);
        $stmt->execute();
        header('Location: index.php');
        exit;
    }
    
    if ($action === 'edit_guru') {
        $id = (int)$_POST['id'];
        $nip = $_POST['nip'];
        $nama = $_POST['nama_guru'];
        $mapel = $_POST['mapel'];
        
        $stmt = $db->prepare("UPDATE guru SET nip = ?, nama_guru = ?, mapel = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nip, $nama, $mapel, $id);
        $stmt->execute();
        header('Location: index.php');
        exit;
    }
    
    if ($action === 'edit_alumni_ptn') {
        $id = (int)$_POST['id'];
        $siswa_id = (int)$_POST['siswa_id'];
        $jenis = $_POST['jenis'];
        $nama_perguruan = $_POST['nama_perguruan'];
        $nama_perusahaan = $_POST['nama_perusahaan'];
        $fakultas = $_POST['fakultas'];
        $prodi = $_POST['prodi'];
        $tahun_ajaran = $_POST['tahun_ajaran'];
        
        $stmt = $db->prepare("UPDATE alumni_ptn SET siswa_id = ?, jenis = ?, nama_perguruan = ?, nama_perusahaan = ?, fakultas = ?, prodi = ?, tahun_ajaran = ? WHERE id = ?");
        $stmt->bind_param("issssssi", $siswa_id, $jenis, $nama_perguruan, $nama_perusahaan, $fakultas, $prodi, $tahun_ajaran, $id);
        $stmt->execute();
        header('Location: index.php');
        exit;
    }
    
    if ($action === 'edit_prestasi') {
        $id = (int)$_POST['id'];
        $siswa_id = (int)$_POST['siswa_id'];
        $nama_lomba = $_POST['nama_lomba'];
        $jenis_prestasi = $_POST['jenis_prestasi'];
        $jenis_peserta = $_POST['jenis_peserta'];
        $nama_tim = $_POST['nama_tim'] ?? null;
        $tingkat = $_POST['tingkat'];
        $peringkat = $_POST['peringkat'];
        $tanggal = $_POST['tanggal'];
        $penyelenggara = $_POST['penyelenggara'] ?? '';
        $deskripsi = $_POST['deskripsi'] ?? '';
        
        $stmt = $db->prepare("UPDATE prestasi SET siswa_id = ?, nama_lomba = ?, jenis_prestasi = ?, jenis_peserta = ?, nama_tim = ?, tingkat = ?, peringkat = ?, tanggal = ?, Penyelenggara = ?, deskripsi = ? WHERE id = ?");
        $stmt->bind_param("isssssssssi", $siswa_id, $nama_lomba, $jenis_prestasi, $jenis_peserta, $nama_tim, $tingkat, $peringkat, $tanggal, $penyelenggara, $deskripsi, $id);
        $stmt->execute();
        header('Location: index.php');
        exit;
    }
    
    if ($action === 'add_siswa') {
        $nis = $_POST['nis'];
        $nama = $_POST['nama_siswa'];
        $kelas = $_POST['kelas'];
        
        $stmt = $db->prepare("INSERT INTO siswa (nis, nama_siswa, kelas) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nis, $nama, $kelas);
        $stmt->execute();
        header('Location: index.php');
        exit;
    }
    
    if ($action === 'add_guru') {
        $nip = $_POST['nip'];
        $nama = $_POST['nama_guru'];
        $mapel = $_POST['mapel'];
        
        $stmt = $db->prepare("INSERT INTO guru (nip, nama_guru, mapel) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nip, $nama, $mapel);
        $stmt->execute();
        header('Location: index.php');
        exit;
    }
    
    if ($action === 'add_prestasi') {
        $jenis_peserta = $_POST['jenis_peserta'];
        
        if ($jenis_peserta === 'kelompok') {
            $siswa_id = null;
        } else {
            $siswa_id = $_POST['siswa_id'] ? (int)$_POST['siswa_id'] : null;
        }
        
        $nama_lomba = $_POST['nama_lomba'];
        $jenis = $_POST['jenis_prestasi'];
        $tingkat = $_POST['tingkat'];
        $peringkat = $_POST['peringkat'];
        $tanggal = $_POST['tanggal'];
        $penyelenggara = $_POST['penyelenggara'];
        $deskripsi = $_POST['deskripsi'];
        
        $foto_sertifikat = '';
        if (isset($_FILES['foto_sertifikat']) && $_FILES['foto_sertifikat']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/';
            $ext = strtolower(pathinfo($_FILES['foto_sertifikat']['name'], PATHINFO_EXTENSION));
            $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($ext, $allowedExt)) {
                $newFilename = uniqid('prestasi_') . '.' . $ext;
                if (move_uploaded_file($_FILES['foto_sertifikat']['tmp_name'], $uploadDir . $newFilename)) {
                    $foto_sertifikat = $newFilename;
                }
            }
        }
        
        $nama_tim = $_POST['nama_tim'] ?? null;
        
        if ($siswa_id === null) {
            $stmt = $db->prepare("INSERT INTO prestasi (siswa_id, nama_lomba, jenis_prestasi, jenis_peserta, nama_tim, tingkat, peringkat, tanggal, Penyelenggara, foto_sertifikat, deskripsi) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssssss", $nama_lomba, $jenis, $jenis_peserta, $nama_tim, $tingkat, $peringkat, $tanggal, $penyelenggara, $foto_sertifikat, $deskripsi);
        } else {
            $stmt = $db->prepare("INSERT INTO prestasi (siswa_id, nama_lomba, jenis_prestasi, jenis_peserta, nama_tim, tingkat, peringkat, tanggal, Penyelenggara, foto_sertifikat, deskripsi) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssssssss", $siswa_id, $nama_lomba, $jenis, $jenis_peserta, $nama_tim, $tingkat, $peringkat, $tanggal, $penyelenggara, $foto_sertifikat, $deskripsi);
        }
        $stmt->execute();
        header('Location: index.php');
        exit;
    }
    
    if ($action === 'add_alumni_ptn') {
        $siswa_id = (int)$_POST['siswa_id'];
        $jenis = $_POST['jenis'];
        $nama_perguruan = $_POST['nama_perguruan'] ?? '';
        $nama_perusahaan = $_POST['nama_perusahaan'] ?? '';
        $fakultas = $_POST['fakultas'] ?? '';
        $prodi = $_POST['prodi'] ?? '';
        $tahun_ajaran = $_POST['tahun_ajaran'];
        
        $foto = '';
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/';
            $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($ext, $allowedExt)) {
                $newFilename = uniqid('alumni_') . '.' . $ext;
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadDir . $newFilename)) {
                    $foto = $newFilename;
                }
            }
        }
        
        $stmt = $db->prepare("INSERT INTO alumni_ptn (siswa_id, jenis, nama_perguruan, nama_perusahaan, fakultas, prodi, tahun_ajaran, foto) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssss", $siswa_id, $jenis, $nama_perguruan, $nama_perusahaan, $fakultas, $prodi, $tahun_ajaran, $foto);
        $stmt->execute();
        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Prestasi SMA Negeri 6 Cimahi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e40af',
                        secondary: '#3b82f6',
                        accent: '#f59e0b'
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <i class="fas fa-trophy text-2xl text-primary mr-2"></i>
                    <span class="font-bold text-xl">Admin Panel</span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-gray-600"><?= $_SESSION['admin_nama'] ?></span>
                    <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-6 gap-6 mb-8">
            <div class="bg-white rounded-xl p-6 shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500">Prestasi Siswa</p>
                        <p class="text-3xl font-bold text-primary"><?= $totalPrestasi ?></p>
                    </div>
                    <i class="fas fa-user-graduate text-4xl text-primary/20"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl p-6 shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500">Siswa</p>
                        <p class="text-3xl font-bold text-green-600"><?= $totalSiswa ?></p>
                    </div>
                    <i class="fas fa-users text-4xl text-green-600/20"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl p-6 shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500">Prestasi Guru</p>
                        <p class="text-3xl font-bold text-blue-600"><?= $totalPrestasiGuru ?></p>
                    </div>
                    <i class="fas fa-chalkboard-teacher text-4xl text-blue-600/20"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl p-6 shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500">Guru</p>
                        <p class="text-3xl font-bold text-indigo-600"><?= $totalGuru ?></p>
                    </div>
                    <i class="fas fa-user-tie text-4xl text-indigo-600/20"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl p-6 shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500">Prestasi Sekolah</p>
                        <p class="text-3xl font-bold text-purple-600"><?= $totalPrestasiSekolah ?></p>
                    </div>
                    <i class="fas fa-school text-4xl text-purple-600/20"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl p-6 shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500">Alumni PTN</p>
                        <p class="text-3xl font-bold text-teal-600"><?= $totalAlumniPTN ?></p>
                    </div>
                    <i class="fas fa-graduation-cap text-4xl text-teal-600/20"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl p-6 shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500">Draft</p>
                        <p class="text-3xl font-bold text-yellow-600"><?= $totalPending ?></p>
                    </div>
                    <i class="fas fa-clock text-4xl text-yellow-600/20"></i>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white rounded-xl shadow mb-6">
            <div class="border-b">
                <nav class="flex">
                    <button onclick="showTab('data-siswa')" class="tab-btn px-6 py-4 text-primary border-b-2 border-primary font-medium" data-tab="data-siswa">
                        <i class="fas fa-users mr-2"></i> Data Siswa
                    </button>
                    <button onclick="showTab('data-prestasi')" class="tab-btn px-6 py-4 text-gray-500 hover:text-primary" data-tab="data-prestasi">
                        <i class="fas fa-user-graduate mr-2"></i> Prestasi Siswa
                    </button>
                    <button onclick="showTab('data-guru')" class="tab-btn px-6 py-4 text-gray-500 hover:text-primary" data-tab="data-guru">
                        <i class="fas fa-chalkboard-teacher mr-2"></i> Data Guru
                    </button>
                    <button onclick="showTab('data-prestasi-guru')" class="tab-btn px-6 py-4 text-gray-500 hover:text-primary" data-tab="data-prestasi-guru">
                        <i class="fas fa-award mr-2"></i> Prestasi Guru
                    </button>
                    <button onclick="showTab('data-prestasi-sekolah')" class="tab-btn px-6 py-4 text-gray-500 hover:text-primary" data-tab="data-prestasi-sekolah">
                        <i class="fas fa-school mr-2"></i> Prestasi Sekolah
                    </button>
                    <button onclick="showTab('data-alumni-ptn')" class="tab-btn px-6 py-4 text-gray-500 hover:text-primary" data-tab="data-alumni-ptn">
                        <i class="fas fa-graduation-cap mr-2"></i> Alumni PTN
                    </button>
                </nav>
            </div>

            <!-- Tab: Data Siswa -->
            <div id="tab-data-siswa" class="tab-content p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold">Data Siswa</h3>
                    <button onclick="showModal('modal-siswa')" class="bg-primary text-white px-4 py-2 rounded hover:bg-blue-700">
                        <i class="fas fa-plus mr-1"></i> Tambah Siswa
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left">NIS</th>
                                <th class="px-4 py-3 text-left">Nama</th>
                                <th class="px-4 py-3 text-left">Kelas</th>
                                <th class="px-4 py-3 text-center">Prestasi</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($s = $siswa->fetch_assoc()): ?>
                            <?php 
                                $jml = $db->query("SELECT COUNT(*) as total FROM prestasi WHERE siswa_id = ".$s['id'])->fetch_assoc()['total'];
                            ?>
                            <tr class="border-t">
                                <td class="px-4 py-3"><?= htmlspecialchars($s['nis']) ?></td>
                                <td class="px-4 py-3 font-medium"><?= htmlspecialchars($s['nama_siswa']) ?></td>
                                <td class="px-4 py-3"><?= htmlspecialchars($s['kelas']) ?></td>
                                <td class="px-4 py-3 text-center">
                                    <span class="bg-primary/10 text-primary px-3 py-1 rounded-full text-sm">
                                        <?= $jml ?> prestasi
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" onclick="openEditSiswa(<?= $s['id'] ?>, '<?= htmlspecialchars($s['nis'] ?? '') ?>', '<?= htmlspecialchars($s['nama_siswa']) ?>', '<?= htmlspecialchars($s['kelas']) ?>')" class="text-blue-500 hover:text-blue-700 mr-2">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" class="inline" onsubmit="return confirm('Yakin hapus? Data prestasi siswa ini juga akan dihapus.')">
                                        <input type="hidden" name="action" value="delete_siswa">
                                        <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                        <button type="submit" class="text-red-500 hover:text-red-700">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab: Data Prestasi -->
            <div id="tab-data-prestasi" class="tab-content p-6 hidden">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold">Data Prestasi</h3>
                    <button onclick="showModal('modal-prestasi')" class="bg-primary text-white px-4 py-2 rounded hover:bg-blue-700">
                        <i class="fas fa-plus mr-1"></i> Tambah Prestasi
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left">Siswa</th>
                                <th class="px-4 py-3 text-left">Lomba</th>
                                <th class="px-4 py-3 text-center">Jenis</th>
                                <th class="px-4 py-3 text-center">Tingkat</th>
                                <th class="px-4 py-3 text-center">Peringkat</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($p = $prestasis->fetch_assoc()): ?>
                            <tr class="border-t">
                                <td class="px-4 py-3">
                                    <div class="font-medium"><?= htmlspecialchars($p['nama_siswa']) ?></div>
                                    <div class="text-sm text-gray-500"><?= htmlspecialchars($p['kelas']) ?></div>
                                </td>
                                <td class="px-4 py-3"><?= htmlspecialchars($p['nama_lomba']) ?></td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 rounded text-xs <?= $p['jenis_prestasi'] === 'akademik' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' ?>">
                                        <?= $p['jenis_prestasi'] ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center capitalize"><?= $p['tingkat'] ?></td>
                                <td class="px-4 py-3 text-center">
                                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-bold">
                                        <?= $p['peringkat'] ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 rounded text-xs <?= $p['status_publikasi'] === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                                        <?= $p['status_publikasi'] ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" onclick="openEditPrestasi(<?= $p['id'] ?>, <?= $p['siswa_id'] ?>, '<?= addslashes($p['nama_siswa'].' - '.$p['kelas']) ?>', '<?= addslashes($p['nama_lomba']) ?>', '<?= $p['jenis_prestasi'] ?>', '<?= $p['jenis_peserta'] ?>', '<?= addslashes($p['nama_tim'] ?? '') ?>', '<?= $p['tingkat'] ?>', '<?= $p['peringkat'] ?>', '<?= $p['tanggal'] ?>', '<?= addslashes($p['penyelenggara'] ?? '') ?>', '<?= addslashes($p['deskripsi'] ?? '') ?>')" class="text-blue-500 hover:text-blue-700 mr-2">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" class="inline" onsubmit="return confirm('Yakin hapus?')">
                                        <input type="hidden" name="action" value="delete_prestasi">
                                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                        <button type="submit" class="text-red-500 hover:text-red-700">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab: Data Guru -->
            <div id="tab-data-guru" class="tab-content p-6 hidden">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold">Data Guru</h3>
                    <button onclick="showModal('modal-guru')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        <i class="fas fa-plus mr-1"></i> Tambah Guru
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left">NIP</th>
                                <th class="px-4 py-3 text-left">Nama</th>
                                <th class="px-4 py-3 text-left">Mata Pelajaran</th>
                                <th class="px-4 py-3 text-center">Prestasi</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $guru->data_seek(0);
                            while ($g = $guru->fetch_assoc()): 
                                $jml_guru = $db->query("SELECT COUNT(*) as total FROM prestasi_guru WHERE guru_id = ".$g['id'])->fetch_assoc()['total'];
                            ?>
                            <tr class="border-t">
                                <td class="px-4 py-3"><?= htmlspecialchars($g['nip'] ?? '-') ?></td>
                                <td class="px-4 py-3 font-medium"><?= htmlspecialchars($g['nama_guru']) ?></td>
                                <td class="px-4 py-3"><?= htmlspecialchars($g['mapel'] ?? '-') ?></td>
                                <td class="px-4 py-3 text-center">
                                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                                        <?= $jml_guru ?> prestasi
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" onclick="openEditGuru(<?= $g['id'] ?>, '<?= htmlspecialchars($g['nip'] ?? '') ?>', '<?= htmlspecialchars($g['nama_guru']) ?>', '<?= htmlspecialchars($g['mapel'] ?? '') ?>')" class="text-blue-500 hover:text-blue-700 mr-2">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" class="inline" onsubmit="return confirm('Yakin hapus? Data prestasi guru ini juga akan dihapus.')">
                                        <input type="hidden" name="action" value="delete_guru">
                                        <input type="hidden" name="id" value="<?= $g['id'] ?>">
                                        <button type="submit" class="text-red-500 hover:text-red-700">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab: Prestasi Guru -->
            <div id="tab-data-prestasi-guru" class="tab-content p-6 hidden">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold">Prestasi Guru</h3>
                    <button onclick="showModal('modal-prestasi-guru')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        <i class="fas fa-plus mr-1"></i> Tambah Prestasi Guru
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left">Guru</th>
                                <th class="px-4 py-3 text-left">Lomba</th>
                                <th class="px-4 py-3 text-center">Jenis</th>
                                <th class="px-4 py-3 text-center">Tingkat</th>
                                <th class="px-4 py-3 text-center">Peringkat</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $pg_result = $db->query("SELECT pg.*, g.nama_guru FROM prestasi_guru pg JOIN guru g ON pg.guru_id = g.id ORDER BY pg.created_at DESC");
                            while ($pg = $pg_result->fetch_assoc()): 
                            ?>
                            <tr class="border-t">
                                <td class="px-4 py-3">
                                    <div class="font-medium"><?= htmlspecialchars($pg['nama_guru']) ?></div>
                                    <div class="text-sm text-gray-500"><?= htmlspecialchars($pg['mapel'] ?? '-') ?></div>
                                </td>
                                <td class="px-4 py-3"><?= htmlspecialchars($pg['nama_lomba']) ?></td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-800">
                                        <?= $pg['jenis_prestasi'] ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center capitalize"><?= $pg['tingkat'] ?></td>
                                <td class="px-4 py-3 text-center">
                                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-bold">
                                        <?= $pg['peringkat'] ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" onclick="openEditPrestasiGuru(<?= $pg['id'] ?>, <?= $pg['guru_id'] ?>, '<?= htmlspecialchars($pg['nama_lomba']) ?>', '<?= $pg['jenis_prestasi'] ?>', '<?= $pg['tingkat'] ?>', '<?= $pg['peringkat'] ?>', '<?= $pg['tanggal'] ?>', '<?= htmlspecialchars($pg['penyelenggara'] ?? '') ?>', '<?= htmlspecialchars($pg['deskripsi'] ?? '') ?>')" class="text-blue-500 hover:text-blue-700 mr-2">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" class="inline" onsubmit="return confirm('Yakin hapus?')">
                                        <input type="hidden" name="action" value="delete_prestasi_guru">
                                        <input type="hidden" name="id" value="<?= $pg['id'] ?>">
                                        <button type="submit" class="text-red-500 hover:text-red-700">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab: Prestasi Sekolah -->
            <div id="tab-data-prestasi-sekolah" class="tab-content p-6 hidden">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold">Prestasi Sekolah</h3>
                    <button onclick="showModal('modal-prestasi-sekolah')" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                        <i class="fas fa-plus mr-1"></i> Tambah Prestasi Sekolah
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left">Prestasi</th>
                                <th class="px-4 py-3 text-left">Kategori</th>
                                <th class="px-4 py-3 text-center">Tingkat</th>
                                <th class="px-4 py-3 text-center">Peringkat</th>
                                <th class="px-4 py-3 text-center">Tanggal</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $ps_result = $db->query("SELECT * FROM prestasi_sekolah ORDER BY created_at DESC");
                            while ($ps = $ps_result->fetch_assoc()): 
                            ?>
                            <tr class="border-t">
                                <td class="px-4 py-3 font-medium"><?= htmlspecialchars($ps['nama_prestasi']) ?></td>
                                <td class="px-4 py-3"><?= htmlspecialchars($ps['kategori'] ?? '-') ?></td>
                                <td class="px-4 py-3 text-center capitalize"><?= $ps['tingkat'] ?></td>
                                <td class="px-4 py-3 text-center">
                                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-bold">
                                        <?= $ps['peringkat'] ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center"><?= date('d/m/Y', strtotime($ps['tanggal'])) ?></td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" onclick="openEditPrestasiSekolah(<?= $ps['id'] ?>, '<?= htmlspecialchars($ps['nama_prestasi']) ?>', '<?= htmlspecialchars($ps['kategori'] ?? '') ?>', '<?= $ps['tingkat'] ?>', '<?= $ps['peringkat'] ?>', '<?= $ps['tanggal'] ?>', '<?= htmlspecialchars($ps['penyelenggara'] ?? '') ?>', '<?= htmlspecialchars($ps['deskripsi'] ?? '') ?>')" class="text-blue-500 hover:text-blue-700 mr-2">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" class="inline" onsubmit="return confirm('Yakin hapus?')">
                                        <input type="hidden" name="action" value="delete_prestasi_sekolah">
                                        <input type="hidden" name="id" value="<?= $ps['id'] ?>">
                                        <button type="submit" class="text-red-500 hover:text-red-700">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab: Alumni -->
            <div id="tab-data-alumni-ptn" class="tab-content p-6 hidden">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold">Data Alumni</h3>
                    <button onclick="showModal('modal-alumni-ptn')" class="bg-teal-600 text-white px-4 py-2 rounded hover:bg-teal-700">
                        <i class="fas fa-plus mr-1"></i> Tambah Alumni
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left">Siswa</th>
                                <th class="px-4 py-3 text-center">Jenis</th>
                                <th class="px-4 py-3 text-left">Perguruan/Perusahaan</th>
                                <th class="px-4 py-3 text-left">Fakultas/Posisi</th>
                                <th class="px-4 py-3 text-left">Prodi</th>
                                <th class="px-4 py-3 text-center">Tahun</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $alumni_result = $db->query("SELECT a.*, s.nama_siswa, s.kelas FROM alumni_ptn a JOIN siswa s ON a.siswa_id = s.id ORDER BY a.tahun_ajaran DESC, s.nama_siswa");
                            while ($alumni = $alumni_result->fetch_assoc()): 
                                $jenisLabel = ['ptn' => 'PTN', 'pts' => 'PTS', 'kerja' => 'Bekerja'][$alumni['jenis']] ?? $alumni['jenis'];
                                $jenisColor = ['ptn' => 'bg-blue-100 text-blue-800', 'pts' => 'bg-purple-100 text-purple-800', 'kerja' => 'bg-green-100 text-green-800'][$alumni['jenis']] ?? 'bg-gray-100';
                                $namaTujuan = $alumni['jenis'] === 'kerja' ? ($alumni['nama_perusahaan'] ?? '-') : ($alumni['nama_perguruan'] ?? '-');
                            ?>
                            <tr class="border-t">
                                <td class="px-4 py-3">
                                    <div class="font-medium"><?= htmlspecialchars($alumni['nama_siswa']) ?></div>
                                    <div class="text-sm text-gray-500"><?= htmlspecialchars($alumni['kelas']) ?></div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 rounded text-xs font-medium <?= $jenisColor ?>"><?= $jenisLabel ?></span>
                                </td>
                                <td class="px-4 py-3 font-medium text-teal-600"><?= htmlspecialchars($namaTujuan) ?></td>
                                <td class="px-4 py-3"><?= htmlspecialchars($alumni['fakultas'] ?? '-') ?></td>
                                <td class="px-4 py-3"><?= htmlspecialchars($alumni['prodi'] ?? '-') ?></td>
                                <td class="px-4 py-3 text-center"><?= htmlspecialchars($alumni['tahun_ajaran']) ?></td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" onclick="openEditAlumni(<?= $alumni['id'] ?>, <?= $alumni['siswa_id'] ?>, '<?= htmlspecialchars($alumni['nama_siswa'].' - '.$alumni['kelas']) ?>', '<?= $alumni['jenis'] ?>', '<?= htmlspecialchars($alumni['nama_perguruan'] ?? '') ?>', '<?= htmlspecialchars($alumni['nama_perusahaan'] ?? '') ?>', '<?= htmlspecialchars($alumni['fakultas'] ?? '') ?>', '<?= htmlspecialchars($alumni['prodi'] ?? '') ?>', '<?= htmlspecialchars($alumni['tahun_ajaran'] ?? '') ?>')" class="text-blue-500 hover:text-blue-700 mr-2">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" class="inline" onsubmit="return confirm('Yakin hapus?')">
                                        <input type="hidden" name="action" value="delete_alumni_ptn">
                                        <input type="hidden" name="id" value="<?= $alumni['id'] ?>">
                                        <button type="submit" class="text-red-500 hover:text-red-700">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Tambah Siswa -->
    <div id="modal-siswa" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-xl p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Tambah Siswa</h3>
                <button onclick="hideModal('modal-siswa')" class="text-2xl">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add_siswa">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">NIS</label>
                        <input type="text" name="nis" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Nama Siswa</label>
                        <input type="text" name="nama_siswa" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Kelas</label>
                        <input type="text" name="kelas" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <button type="submit" class="w-full bg-primary text-white py-2 rounded-lg hover:bg-blue-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Tambah Prestasi -->
    <div id="modal-prestasi" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Tambah Prestasi</h3>
                <button onclick="hideModal('modal-prestasi')" class="text-2xl">&times;</button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_prestasi">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Jenis Peserta</label>
                        <select name="jenis_peserta" id="jenis_peserta" required class="w-full px-3 py-2 border rounded-lg" onchange="toggleTimField()">
                            <option value="perorangan">Perorangan</option>
                            <option value="kelompok">Kelompok/Tim</option>
                        </select>
                    </div>
                    <div id="tim_field" class="hidden">
                        <label class="block text-sm font-medium mb-1">Nama Tim/kelompok</label>
                        <input type="text" name="nama_tim" class="w-full px-3 py-2 border rounded-lg" placeholder="contoh: Paskibra, Basket, Band">
                    </div>
                    <div id="siswa_field">
                        <label class="block text-sm font-medium mb-1">Siswa</label>
                        <input type="text" name="siswa_search" id="siswa_search" class="w-full px-3 py-2 border rounded-lg" placeholder="Cari nama siswa..." list="siswa_list" autocomplete="off">
                        <datalist id="siswa_list">
                            <?php 
                            $siswa->data_seek(0);
                            while ($s = $siswa->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($s['nama_siswa'].' - '.$s['kelas']) ?>" data-id="<?= $s['id'] ?>"><?= htmlspecialchars($s['nama_siswa'].' - '.$s['kelas']) ?></option>
                            <?php endwhile; ?>
                        </datalist>
                        <input type="hidden" name="siswa_id" id="siswa_id" required>
                        <script>
                            document.getElementById('siswa_search').addEventListener('input', function() {
                                const options = document.querySelectorAll('#siswa_list option');
                                const value = this.value;
                                let found = false;
                                options.forEach(option => {
                                    if (option.value === value) {
                                        document.getElementById('siswa_id').value = option.getAttribute('data-id');
                                        found = true;
                                    }
                                });
                                if (!found) {
                                    document.getElementById('siswa_id').value = '';
                                }
                            });
                        </script>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Nama Lomba</label>
                        <input type="text" name="nama_lomba" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Jenis Prestasi</label>
                        <select name="jenis_prestasi" id="jenis_prestasi" required class="w-full px-3 py-2 border rounded-lg" onchange="handleJenisPrestasi()">
                            <option value="akademik">Akademik</option>
                            <option value="non-akademik">Non-Akademik</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Tingkat</label>
                        <select name="tingkat" required class="w-full px-3 py-2 border rounded-lg">
                            <option value="sekolah">Sekolah</option>
                            <option value="kecamatan">Kecamatan</option>
                            <option value="kota">Kota</option>
                            <option value="provinsi">Provinsi</option>
                            <option value="nasional">Nasional</option>
                            <option value="internasional">Internasional</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Peringkat</label>
                        <select name="peringkat" required class="w-full px-3 py-2 border rounded-lg">
                            <option value="1">Juara 1</option>
                            <option value="2">Juara 2</option>
                            <option value="3">Juara 3</option>
                            <option value="harapan">Harapan</option>
                            <option value="finalis">Finalis</option>
                            <option value="peserta">Peserta</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Tanggal</label>
                        <input type="date" name="tanggal" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Penyelenggara</label>
                        <input type="text" name="penyelenggara" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Foto Sertifikat/Foto</label>
                        <input type="file" name="foto_sertifikat" accept="image/*" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Deskripsi</label>
                        <textarea name="deskripsi" rows="3" class="w-full px-3 py-2 border rounded-lg"></textarea>
                    </div>
                </div>
                <button type="submit" class="w-full bg-primary text-white py-3 rounded-lg hover:bg-blue-700 mt-4">
                    Simpan Prestasi
                </button>
            </form>
        </div>
    </div>

    <!-- Modal: Tambah Guru -->
    <div id="modal-guru" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-xl p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Tambah Guru</h3>
                <button onclick="hideModal('modal-guru')" class="text-2xl">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add_guru">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">NIP</label>
                        <input type="text" name="nip" class="w-full px-3 py-2 border rounded-lg" placeholder="Opsional">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Nama Guru</label>
                        <input type="text" name="nama_guru" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Mata Pelajaran</label>
                        <input type="text" name="mapel" class="w-full px-3 py-2 border rounded-lg" placeholder="Opsional">
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Tambah Prestasi Guru -->
    <div id="modal-prestasi-guru" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Tambah Prestasi Guru</h3>
                <button onclick="hideModal('modal-prestasi-guru')" class="text-2xl">&times;</button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_prestasi_guru">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Guru</label>
                        <input type="text" name="guru_search" id="guru_search" class="w-full px-3 py-2 border rounded-lg" placeholder="Cari nama guru..." list="guru_list" autocomplete="off">
                        <datalist id="guru_list">
                            <?php 
                            $guru->data_seek(0);
                            while ($g = $guru->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($g['nama_guru'].($g['mapel'] ? ' - '.$g['mapel'] : '')) ?>" data-id="<?= $g['id'] ?>"><?= htmlspecialchars($g['nama_guru'].($g['mapel'] ? ' - '.$g['mapel'] : '')) ?></option>
                            <?php endwhile; ?>
                        </datalist>
                        <input type="hidden" name="guru_id" id="guru_id" required>
                        <script>
                            document.getElementById('guru_search').addEventListener('input', function() {
                                const options = document.querySelectorAll('#guru_list option');
                                const value = this.value;
                                let found = false;
                                options.forEach(option => {
                                    if (option.value === value) {
                                        document.getElementById('guru_id').value = option.getAttribute('data-id');
                                        found = true;
                                    }
                                });
                                if (!found) {
                                    document.getElementById('guru_id').value = '';
                                }
                            });
                        </script>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Nama Lomba</label>
                        <input type="text" name="nama_lomba" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Jenis Prestasi</label>
                        <select name="jenis_prestasi" required class="w-full px-3 py-2 border rounded-lg">
                            <option value="akademik">Akademik</option>
                            <option value="non-akademik">Non-Akademik</option>
                            <option value="penelitian">Penelitian</option>
                            <option value="kompetisi">Kompetisi</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Tingkat</label>
                        <select name="tingkat" required class="w-full px-3 py-2 border rounded-lg">
                            <option value="sekolah">Sekolah</option>
                            <option value="kecamatan">Kecamatan</option>
                            <option value="kota">Kota</option>
                            <option value="provinsi">Provinsi</option>
                            <option value="nasional">Nasional</option>
                            <option value="internasional">Internasional</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Peringkat</label>
                        <select name="peringkat" required class="w-full px-3 py-2 border rounded-lg">
                            <option value="1">Juara 1</option>
                            <option value="2">Juara 2</option>
                            <option value="3">Juara 3</option>
                            <option value="harapan">Harapan</option>
                            <option value="finalis">Finalis</option>
                            <option value="peserta">Peserta</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Tanggal</label>
                        <input type="date" name="tanggal" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Penyelenggara</label>
                        <input type="text" name="penyelenggara" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Foto Sertifikat</label>
                        <input type="file" name="foto_sertifikat" accept="image/*" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Deskripsi</label>
                        <textarea name="deskripsi" rows="3" class="w-full px-3 py-2 border rounded-lg"></textarea>
                    </div>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 mt-4">
                    Simpan Prestasi Guru
                </button>
            </form>
        </div>
    </div>

    <!-- Modal: Tambah Prestasi Sekolah -->
    <div id="modal-prestasi-sekolah" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Tambah Prestasi Sekolah</h3>
                <button onclick="hideModal('modal-prestasi-sekolah')" class="text-2xl">&times;</button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_prestasi_sekolah">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Nama Prestasi</label>
                        <input type="text" name="nama_prestasi" required class="w-full px-3 py-2 border rounded-lg" placeholder="contoh: Sekolah Adiwiyata, Sekolah RSBI, dll">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Kategori</label>
                        <select name="kategori" class="w-full px-3 py-2 border rounded-lg">
                            <option value="">- Pilih Kategori -</option>
                            <option value="akademik">Akademik</option>
                            <option value="non-akademik">Non-Akademik</option>
                            <option value="kesiswaan">Kesiswaan</option>
                            <option value="fasilitas">Fasilitas/Infrastruktur</option>
                            <option value="lingkungan">Lingkungan</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Tingkat</label>
                        <select name="tingkat" required class="w-full px-3 py-2 border rounded-lg">
                            <option value="sekolah">Sekolah</option>
                            <option value="kecamatan">Kecamatan</option>
                            <option value="kota">Kota</option>
                            <option value="provinsi">Provinsi</option>
                            <option value="nasional">Nasional</option>
                            <option value="internasional">Internasional</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Peringkat</label>
                        <select name="peringkat" required class="w-full px-3 py-2 border rounded-lg">
                            <option value="1">Juara 1</option>
                            <option value="2">Juara 2</option>
                            <option value="3">Juara 3</option>
                            <option value="harapan">Harapan</option>
                            <option value="sertifikasi">Sertifikasi</option>
                            <option value="akreditasi">Akreditasi</option>
                            <option value="peserta">Peserta</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Tanggal</label>
                        <input type="date" name="tanggal" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Penyelenggara</label>
                        <input type="text" name="penyelenggara" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Foto Bukti</label>
                        <input type="file" name="foto_bukti" accept="image/*" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Deskripsi</label>
                        <textarea name="deskripsi" rows="3" class="w-full px-3 py-2 border rounded-lg"></textarea>
                    </div>
                </div>
                <button type="submit" class="w-full bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700 mt-4">
                    Simpan Prestasi Sekolah
                </button>
            </form>
        </div>
    </div>

    <!-- Modal: Tambah Alumni -->
    <div id="modal-alumni-ptn" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Tambah Alumni</h3>
                <button onclick="hideModal('modal-alumni-ptn')" class="text-2xl">&times;</button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_alumni_ptn">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Siswa</label>
                        <input type="text" name="alumni_siswa_search" id="alumni_siswa_search" class="w-full px-3 py-2 border rounded-lg" placeholder="Cari nama siswa..." list="alumni_siswa_list" autocomplete="off">
                        <datalist id="alumni_siswa_list">
                            <?php 
                            $siswa->data_seek(0);
                            while ($s = $siswa->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($s['nama_siswa'].' - '.$s['kelas']) ?>" data-id="<?= $s['id'] ?>"><?= htmlspecialchars($s['nama_siswa'].' - '.$s['kelas']) ?></option>
                            <?php endwhile; ?>
                        </datalist>
                        <input type="hidden" name="siswa_id" id="alumni_siswa_id" required>
                        <script>
                            document.getElementById('alumni_siswa_search').addEventListener('input', function() {
                                const options = document.querySelectorAll('#alumni_siswa_list option');
                                const value = this.value;
                                let found = false;
                                options.forEach(option => {
                                    if (option.value === value) {
                                        document.getElementById('alumni_siswa_id').value = option.getAttribute('data-id');
                                        found = true;
                                    }
                                });
                                if (!found) {
                                    document.getElementById('alumni_siswa_id').value = '';
                                }
                            });
                        </script>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Jenis</label>
                        <select name="jenis" id="add_alumni_jenis" required class="w-full px-3 py-2 border rounded-lg" onchange="toggleAlumniFields()">
                            <option value="ptn">Perguruan Tinggi Negeri (PTN)</option>
                            <option value="pts">Perguruan Tinggi Swasta (PTS)</option>
                            <option value="kerja">Bekerja</option>
                        </select>
                    </div>
                    <div id="perguruan_field">
                        <label class="block text-sm font-medium mb-1">Nama Perguruan Tinggi</label>
                        <input type="text" name="nama_perguruan" id="add_nama_perguruan" class="w-full px-3 py-2 border rounded-lg" placeholder="contoh: Universitas Indonesia">
                    </div>
                    <div id="perusahaan_field" class="hidden">
                        <label class="block text-sm font-medium mb-1">Nama Perusahaan/Instansi</label>
                        <input type="text" name="nama_perusahaan" id="add_nama_perusahaan" class="w-full px-3 py-2 border rounded-lg" placeholder="contoh: PT Maju Jaya">
                    </div>
                    <div id="fakultas_field">
                        <label class="block text-sm font-medium mb-1">Fakultas</label>
                        <input type="text" name="fakultas" class="w-full px-3 py-2 border rounded-lg" placeholder="contoh: Kedokteran">
                    </div>
                    <div id="prodi_field">
                        <label class="block text-sm font-medium mb-1">Program Studi</label>
                        <input type="text" name="prodi" class="w-full px-3 py-2 border rounded-lg" placeholder="contoh: Kedokteran Umum">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Tahun Ajaran/Tahun</label>
                        <input type="text" name="tahun_ajaran" required class="w-full px-3 py-2 border rounded-lg" placeholder="contoh: 2024/2025">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Foto</label>
                        <input type="file" name="foto" accept="image/*" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Deskripsi</label>
                        <textarea name="deskripsi" rows="3" class="w-full px-3 py-2 border rounded-lg"></textarea>
                    </div>
                </div>
                <button type="submit" class="w-full bg-teal-600 text-white py-3 rounded-lg hover:bg-teal-700 mt-4">
                    Simpan Alumni
                </button>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Alumni -->
    <div id="modal-edit-alumni" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Edit Alumni</h3>
                <button onclick="hideModal('modal-edit-alumni')" class="text-2xl">&times;</button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit_alumni_ptn">
                <input type="hidden" name="id" id="edit_alumni_id">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Siswa</label>
                        <input type="text" name="edit_alumni_siswa_search" id="edit_alumni_siswa_search" class="w-full px-3 py-2 border rounded-lg" placeholder="Cari nama siswa..." list="edit_alumni_siswa_list" autocomplete="off">
                        <datalist id="edit_alumni_siswa_list">
                            <?php 
                            $siswa->data_seek(0);
                            while ($s = $siswa->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($s['nama_siswa'].' - '.$s['kelas']) ?>" data-id="<?= $s['id'] ?>"><?= htmlspecialchars($s['nama_siswa'].' - '.$s['kelas']) ?></option>
                            <?php endwhile; ?>
                        </datalist>
                        <input type="hidden" name="siswa_id" id="edit_alumni_siswa_id" required>
                        <script>
                            document.getElementById('edit_alumni_siswa_search').addEventListener('input', function() {
                                const options = document.querySelectorAll('#edit_alumni_siswa_list option');
                                const value = this.value;
                                let found = false;
                                options.forEach(option => {
                                    if (option.value === value) {
                                        document.getElementById('edit_alumni_siswa_id').value = option.getAttribute('data-id');
                                        found = true;
                                    }
                                });
                                if (!found) {
                                    document.getElementById('edit_alumni_siswa_id').value = '';
                                }
                            });
                        </script>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Jenis</label>
                        <select name="jenis" id="edit_alumni_jenis" required class="w-full px-3 py-2 border rounded-lg" onchange="toggleEditAlumniFields()">
                            <option value="ptn">Perguruan Tinggi Negeri (PTN)</option>
                            <option value="pts">Perguruan Tinggi Swasta (PTS)</option>
                            <option value="kerja">Bekerja</option>
                        </select>
                    </div>
                    <div id="edit_perguruan_field">
                        <label class="block text-sm font-medium mb-1">Nama Perguruan Tinggi</label>
                        <input type="text" name="nama_perguruan" id="edit_nama_perguruan" class="w-full px-3 py-2 border rounded-lg" placeholder="contoh: Universitas Indonesia">
                    </div>
                    <div id="edit_perusahaan_field" class="hidden">
                        <label class="block text-sm font-medium mb-1">Nama Perusahaan/Instansi</label>
                        <input type="text" name="nama_perusahaan" id="edit_nama_perusahaan" class="w-full px-3 py-2 border rounded-lg" placeholder="contoh: PT Maju Jaya">
                    </div>
                    <div id="edit_fakultas_field">
                        <label class="block text-sm font-medium mb-1">Fakultas</label>
                        <input type="text" name="fakultas" id="edit_fakultas" class="w-full px-3 py-2 border rounded-lg" placeholder="contoh: Kedokteran">
                    </div>
                    <div id="edit_prodi_field">
                        <label class="block text-sm font-medium mb-1">Program Studi</label>
                        <input type="text" name="prodi" id="edit_prodi" class="w-full px-3 py-2 border rounded-lg" placeholder="contoh: Kedokteran Umum">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Tahun Ajaran/Tahun</label>
                        <input type="text" name="tahun_ajaran" id="edit_tahun_ajaran" required class="w-full px-3 py-2 border rounded-lg" placeholder="contoh: 2024/2025">
                    </div>
                </div>
                <button type="submit" class="w-full bg-teal-600 text-white py-3 rounded-lg hover:bg-teal-700 mt-4">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Siswa -->
    <div id="modal-edit-siswa" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-xl p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Edit Siswa</h3>
                <button onclick="hideModal('modal-edit-siswa')" class="text-2xl">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="edit_siswa">
                <input type="hidden" name="id" id="edit_siswa_id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">NIS</label>
                        <input type="text" name="nis" id="edit_siswa_nis" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Nama Siswa</label>
                        <input type="text" name="nama_siswa" id="edit_siswa_nama" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Kelas</label>
                        <input type="text" name="kelas" id="edit_siswa_kelas" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <button type="submit" class="w-full bg-primary text-white py-2 rounded-lg hover:bg-blue-700">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Guru -->
    <div id="modal-edit-guru" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-xl p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Edit Guru</h3>
                <button onclick="hideModal('modal-edit-guru')" class="text-2xl">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="edit_guru">
                <input type="hidden" name="id" id="edit_guru_id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">NIP</label>
                        <input type="text" name="nip" id="edit_guru_nip" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Nama Guru</label>
                        <input type="text" name="nama_guru" id="edit_guru_nama" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Mata Pelajaran</label>
                        <input type="text" name="mapel" id="edit_guru_mapel" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Prestasi Siswa -->
    <div id="modal-edit-prestasi" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Edit Prestasi Siswa</h3>
                <button onclick="hideModal('modal-edit-prestasi')" class="text-2xl">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="edit_prestasi">
                <input type="hidden" name="id" id="edit_prestasi_id">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Jenis Peserta</label>
                        <select name="jenis_peserta" id="edit_jenis_peserta" required class="w-full px-3 py-2 border rounded-lg" onchange="toggleEditTimField()">
                            <option value="perorangan">Perorangan</option>
                            <option value="kelompok">Kelompok/Tim</option>
                        </select>
                    </div>
                    <div id="edit_tim_field" class="hidden">
                        <label class="block text-sm font-medium mb-1">Nama Tim/kelompok</label>
                        <input type="text" name="nama_tim" id="edit_prestasi_nama_tim" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div id="edit_siswa_field">
                        <label class="block text-sm font-medium mb-1">Siswa</label>
                        <input type="text" name="edit_siswa_search" id="edit_siswa_search" class="w-full px-3 py-2 border rounded-lg" placeholder="Cari nama siswa..." list="edit_siswa_list" autocomplete="off">
                        <datalist id="edit_siswa_list">
                            <?php 
                            $siswa->data_seek(0);
                            while ($s = $siswa->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($s['nama_siswa'].' - '.$s['kelas']) ?>" data-id="<?= $s['id'] ?>"><?= htmlspecialchars($s['nama_siswa'].' - '.$s['kelas']) ?></option>
                            <?php endwhile; ?>
                        </datalist>
                        <input type="hidden" name="siswa_id" id="edit_prestasi_siswa_id">
                        <script>
                            document.getElementById('edit_siswa_search').addEventListener('input', function() {
                                const options = document.querySelectorAll('#edit_siswa_list option');
                                const value = this.value;
                                let found = false;
                                options.forEach(option => {
                                    if (option.value === value) {
                                        document.getElementById('edit_prestasi_siswa_id').value = option.getAttribute('data-id');
                                        found = true;
                                    }
                                });
                                if (!found) {
                                    document.getElementById('edit_prestasi_siswa_id').value = '';
                                }
                            });
                        </script>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Nama Lomba</label>
                        <input type="text" name="nama_lomba" id="edit_prestasi_nama_lomba" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Jenis Prestasi</label>
                        <select name="jenis_prestasi" id="edit_prestasi_jenis" required class="w-full px-3 py-2 border rounded-lg">
                            <option value="akademik">Akademik</option>
                            <option value="non-akademik">Non-Akademik</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Tingkat</label>
                        <select name="tingkat" id="edit_prestasi_tingkat" required class="w-full px-3 py-2 border rounded-lg">
                            <option value="sekolah">Sekolah</option>
                            <option value="kecamatan">Kecamatan</option>
                            <option value="kota">Kota</option>
                            <option value="provinsi">Provinsi</option>
                            <option value="nasional">Nasional</option>
                            <option value="internasional">Internasional</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Peringkat</label>
                        <select name="peringkat" id="edit_prestasi_peringkat" required class="w-full px-3 py-2 border rounded-lg">
                            <option value="1">Juara 1</option>
                            <option value="2">Juara 2</option>
                            <option value="3">Juara 3</option>
                            <option value="harapan">Harapan</option>
                            <option value="finalis">Finalis</option>
                            <option value="peserta">Peserta</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Tanggal</label>
                        <input type="date" name="tanggal" id="edit_prestasi_tanggal" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Penyelenggara</label>
                        <input type="text" name="penyelenggara" id="edit_prestasi_penyelenggara" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Deskripsi</label>
                        <textarea name="deskripsi" id="edit_prestasi_deskripsi" rows="3" class="w-full px-3 py-2 border rounded-lg"></textarea>
                    </div>
                </div>
                <button type="submit" class="w-full bg-primary text-white py-3 rounded-lg hover:bg-blue-700 mt-4">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Prestasi Guru -->
    <div id="modal-edit-prestasi-guru" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Edit Prestasi Guru</h3>
                <button onclick="hideModal('modal-edit-prestasi-guru')" class="text-2xl">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="edit_prestasi_guru">
                <input type="hidden" name="id" id="edit_prestasi_guru_id">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Guru</label>
                        <select name="guru_id" id="edit_prestasi_guru_guru_id" required class="w-full px-3 py-2 border rounded-lg">
                            <?php 
                            $guru->data_seek(0);
                            while ($g = $guru->fetch_assoc()): ?>
                            <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['nama_guru'].($g['mapel'] ? ' - '.$g['mapel'] : '')) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Nama Lomba</label>
                        <input type="text" name="nama_lomba" id="edit_prestasi_guru_nama_lomba" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Jenis Prestasi</label>
                        <select name="jenis_prestasi" id="edit_prestasi_guru_jenis" required class="w-full px-3 py-2 border rounded-lg">
                            <option value="akademik">Akademik</option>
                            <option value="non-akademik">Non-Akademik</option>
                            <option value="penelitian">Penelitian</option>
                            <option value="kompetisi">Kompetisi</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Tingkat</label>
                        <select name="tingkat" id="edit_prestasi_guru_tingkat" required class="w-full px-3 py-2 border rounded-lg">
                            <option value="sekolah">Sekolah</option>
                            <option value="kecamatan">Kecamatan</option>
                            <option value="kota">Kota</option>
                            <option value="provinsi">Provinsi</option>
                            <option value="nasional">Nasional</option>
                            <option value="internasional">Internasional</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Peringkat</label>
                        <select name="peringkat" id="edit_prestasi_guru_peringkat" required class="w-full px-3 py-2 border rounded-lg">
                            <option value="1">Juara 1</option>
                            <option value="2">Juara 2</option>
                            <option value="3">Juara 3</option>
                            <option value="harapan">Harapan</option>
                            <option value="finalis">Finalis</option>
                            <option value="peserta">Peserta</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Tanggal</label>
                        <input type="date" name="tanggal" id="edit_prestasi_guru_tanggal" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Penyelenggara</label>
                        <input type="text" name="penyelenggara" id="edit_prestasi_guru_penyelenggara" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Deskripsi</label>
                        <textarea name="deskripsi" id="edit_prestasi_guru_deskripsi" rows="3" class="w-full px-3 py-2 border rounded-lg"></textarea>
                    </div>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 mt-4">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Prestasi Sekolah -->
    <div id="modal-edit-prestasi-sekolah" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Edit Prestasi Sekolah</h3>
                <button onclick="hideModal('modal-edit-prestasi-sekolah')" class="text-2xl">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="edit_prestasi_sekolah">
                <input type="hidden" name="id" id="edit_prestasi_sekolah_id">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Nama Prestasi</label>
                        <input type="text" name="nama_prestasi" id="edit_prestasi_sekolah_nama" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Kategori</label>
                        <select name="kategori" id="edit_prestasi_sekolah_kategori" class="w-full px-3 py-2 border rounded-lg">
                            <option value="">- Pilih Kategori -</option>
                            <option value="akademik">Akademik</option>
                            <option value="non-akademik">Non-Akademik</option>
                            <option value="kesiswaan">Kesiswaan</option>
                            <option value="fasilitas">Fasilitas/Infrastruktur</option>
                            <option value="lingkungan">Lingkungan</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Tingkat</label>
                        <select name="tingkat" id="edit_prestasi_sekolah_tingkat" required class="w-full px-3 py-2 border rounded-lg">
                            <option value="sekolah">Sekolah</option>
                            <option value="kecamatan">Kecamatan</option>
                            <option value="kota">Kota</option>
                            <option value="provinsi">Provinsi</option>
                            <option value="nasional">Nasional</option>
                            <option value="internasional">Internasional</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Peringkat</label>
                        <select name="peringkat" id="edit_prestasi_sekolah_peringkat" required class="w-full px-3 py-2 border rounded-lg">
                            <option value="1">Juara 1</option>
                            <option value="2">Juara 2</option>
                            <option value="3">Juara 3</option>
                            <option value="harapan">Harapan</option>
                            <option value="sertifikasi">Sertifikasi</option>
                            <option value="akreditasi">Akreditasi</option>
                            <option value="peserta">Peserta</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Tanggal</label>
                        <input type="date" name="tanggal" id="edit_prestasi_sekolah_tanggal" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Penyelenggara</label>
                        <input type="text" name="penyelenggara" id="edit_prestasi_sekolah_penyelenggara" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Deskripsi</label>
                        <textarea name="deskripsi" id="edit_prestasi_sekolah_deskripsi" rows="3" class="w-full px-3 py-2 border rounded-lg"></textarea>
                    </div>
                </div>
                <button type="submit" class="w-full bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700 mt-4">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.getElementById('tab-' + tabName).classList.remove('hidden');
            
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('text-primary', 'border-b-2', 'border-primary');
                el.classList.add('text-gray-500');
            });
            document.querySelector(`[data-tab="${tabName}"]`).classList.add('text-primary', 'border-b-2', 'border-primary');
            document.querySelector(`[data-tab="${tabName}"]`).classList.remove('text-gray-500');
        }

        function showModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        function hideModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        function handleJenisPrestasi() {
            const jenisPrestasi = document.getElementById('jenis_prestasi').value;
            const jenisPeserta = document.getElementById('jenis_peserta');
            
            if (jenisPrestasi === 'non-akademik') {
                jenisPeserta.value = 'kelompok';
                toggleTimField();
            }
        }

        function filterSiswa(searchTerm) {
            const select = document.getElementById('siswa_id');
            const options = select.querySelectorAll('option');
            const term = searchTerm.toLowerCase();
            
            options.forEach(option => {
                if (option.value === '') {
                    option.style.display = '';
                    return;
                }
                const nama = option.getAttribute('data-nama') || '';
                const kelas = option.getAttribute('data-kelas') || '';
                if (nama.includes(term) || kelas.includes(term)) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            });
        }

        function filterGuru(searchTerm) {
            const select = document.getElementById('guru_id');
            const options = select.querySelectorAll('option');
            const term = searchTerm.toLowerCase();
            
            options.forEach(option => {
                if (option.value === '') {
                    option.style.display = '';
                    return;
                }
                const nama = option.getAttribute('data-nama') || '';
                const mapel = option.getAttribute('data-mapel') || '';
                if (nama.includes(term) || mapel.includes(term)) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            });
        }

        function filterAlumniSiswa(searchTerm) {
            const select = document.getElementById('alumni_siswa_id');
            const options = select.querySelectorAll('option');
            const term = searchTerm.toLowerCase();
            
            options.forEach(option => {
                if (option.value === '') {
                    option.style.display = '';
                    return;
                }
                const nama = option.getAttribute('data-nama') || '';
                const kelas = option.getAttribute('data-kelas') || '';
                if (nama.includes(term) || kelas.includes(term)) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            });
        }

        function filterEditAlumniSiswa(searchTerm) {
            const select = document.getElementById('edit_alumni_siswa_id');
            const options = select.querySelectorAll('option');
            const term = searchTerm.toLowerCase();
            
            options.forEach(option => {
                if (option.value === '') {
                    option.style.display = '';
                    return;
                }
                const nama = option.getAttribute('data-nama') || '';
                const kelas = option.getAttribute('data-kelas') || '';
                if (nama.includes(term) || kelas.includes(term)) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            });
        }

        function toggleTimField() {
            const jenis = document.getElementById('jenis_peserta').value;
            const timField = document.getElementById('tim_field');
            const siswaField = document.getElementById('siswa_field');
            const siswaSelect = siswaField.querySelector('select');
            
            if (jenis === 'kelompok') {
                timField.classList.remove('hidden');
                siswaField.classList.add('hidden');
                siswaSelect.removeAttribute('required');
            } else {
                timField.classList.add('hidden');
                siswaField.classList.remove('hidden');
                siswaSelect.setAttribute('required', 'required');
            }
        }

        function openEditSiswa(id, nis, nama, kelas) {
            document.getElementById('edit_siswa_id').value = id;
            document.getElementById('edit_siswa_nis').value = nis;
            document.getElementById('edit_siswa_nama').value = nama;
            document.getElementById('edit_siswa_kelas').value = kelas;
            showModal('modal-edit-siswa');
        }

        function openEditGuru(id, nip, nama, mapel) {
            document.getElementById('edit_guru_id').value = id;
            document.getElementById('edit_guru_nip').value = nip;
            document.getElementById('edit_guru_nama').value = nama;
            document.getElementById('edit_guru_mapel').value = mapel;
            showModal('modal-edit-guru');
        }

        function openEditAlumni(id, siswa_id, nama_siswa, jenis, nama_perguruan, nama_perusahaan, fakultas, prodi, tahun_ajaran) {
            document.getElementById('edit_alumni_id').value = id;
            document.getElementById('edit_alumni_siswa_id').value = siswa_id;
            document.getElementById('edit_alumni_siswa_search').value = nama_siswa;
            document.getElementById('edit_alumni_jenis').value = jenis;
            document.getElementById('edit_nama_perguruan').value = nama_perguruan;
            document.getElementById('edit_nama_perusahaan').value = nama_perusahaan;
            document.getElementById('edit_fakultas').value = fakultas;
            document.getElementById('edit_prodi').value = prodi;
            document.getElementById('edit_tahun_ajaran').value = tahun_ajaran;
            toggleEditAlumniFields();
            showModal('modal-edit-alumni');
        }

        function toggleEditAlumniFields() {
            const jenis = document.getElementById('edit_alumni_jenis').value;
            document.getElementById('edit_perguruan_field').classList.toggle('hidden', jenis === 'kerja');
            document.getElementById('edit_perusahaan_field').classList.toggle('hidden', jenis !== 'kerja');
            document.getElementById('edit_fakultas_field').classList.toggle('hidden', jenis === 'kerja');
            document.getElementById('edit_prodi_field').classList.toggle('hidden', jenis === 'kerja');
        }

        function openEditPrestasi(id, siswa_id, nama_siswa, nama_lomba, jenis_prestasi, jenis_peserta, nama_tim, tingkat, peringkat, tanggal, penyelenga, deskripsi) {
            document.getElementById('edit_prestasi_id').value = id;
            document.getElementById('edit_prestasi_siswa_id').value = siswa_id;
            document.getElementById('edit_siswa_search').value = nama_siswa;
            document.getElementById('edit_prestasi_nama_lomba').value = nama_lomba;
            document.getElementById('edit_prestasi_jenis').value = jenis_prestasi;
            document.getElementById('edit_jenis_peserta').value = jenis_peserta;
            document.getElementById('edit_prestasi_nama_tim').value = nama_tim;
            document.getElementById('edit_prestasi_tingkat').value = tingkat;
            document.getElementById('edit_prestasi_peringkat').value = peringkat;
            document.getElementById('edit_prestasi_tanggal').value = tanggal;
            document.getElementById('edit_prestasi_penyelenggara').value = penyelenga;
            document.getElementById('edit_prestasi_deskripsi').value = deskripsi;
            
            toggleEditTimField();
            showModal('modal-edit-prestasi');
        }

        function openEditPrestasiGuru(id, guru_id, nama_lomba, jenis_prestasi, tingkat, peringkat, tanggal, penyelenga, deskripsi) {
            document.getElementById('edit_prestasi_guru_id').value = id;
            document.getElementById('edit_prestasi_guru_guru_id').value = guru_id;
            document.getElementById('edit_prestasi_guru_nama_lomba').value = nama_lomba;
            document.getElementById('edit_prestasi_guru_jenis').value = jenis_prestasi;
            document.getElementById('edit_prestasi_guru_tingkat').value = tingkat;
            document.getElementById('edit_prestasi_guru_peringkat').value = peringkat;
            document.getElementById('edit_prestasi_guru_tanggal').value = tanggal;
            document.getElementById('edit_prestasi_guru_penyelenggara').value = penyelenga;
            document.getElementById('edit_prestasi_guru_deskripsi').value = deskripsi;
            showModal('modal-edit-prestasi-guru');
        }

        function openEditPrestasiSekolah(id, nama_prestasi, kategori, tingkat, peringkat, tanggal, penyelenga, deskripsi) {
            document.getElementById('edit_prestasi_sekolah_id').value = id;
            document.getElementById('edit_prestasi_sekolah_nama').value = nama_prestasi;
            document.getElementById('edit_prestasi_sekolah_kategori').value = kategori;
            document.getElementById('edit_prestasi_sekolah_tingkat').value = tingkat;
            document.getElementById('edit_prestasi_sekolah_peringkat').value = peringkat;
            document.getElementById('edit_prestasi_sekolah_tanggal').value = tanggal;
            document.getElementById('edit_prestasi_sekolah_penyelenggara').value = penyelenga;
            document.getElementById('edit_prestasi_sekolah_deskripsi').value = deskripsi;
            showModal('modal-edit-prestasi-sekolah');
        }

        function toggleEditTimField() {
            const jenis = document.getElementById('edit_jenis_peserta').value;
            const timField = document.getElementById('edit_tim_field');
            const siswaField = document.getElementById('edit_siswa_field');
            const siswaSelect = siswaField.querySelector('select');
            
            if (jenis === 'kelompok') {
                timField.classList.remove('hidden');
                siswaField.classList.add('hidden');
                siswaSelect.removeAttribute('required');
            } else {
                timField.classList.add('hidden');
                siswaField.classList.remove('hidden');
                siswaSelect.setAttribute('required', 'required');
            }
        }

        function toggleAlumniFields() {
            const jenis = document.getElementById('add_alumni_jenis').value;
            const perguruanField = document.getElementById('perguruan_field');
            const perusahaanField = document.getElementById('perusahaan_field');
            const fakultasField = document.getElementById('fakultas_field');
            const prodiField = document.getElementById('prodi_field');
            const namaPerguruan = document.getElementById('add_nama_perguruan');
            const namaPerusahaan = document.getElementById('add_nama_perusahaan');
            
            if (jenis === 'kerja') {
                perguruanField.classList.add('hidden');
                perusahaanField.classList.remove('hidden');
                fakultasField.classList.add('hidden');
                prodiField.classList.add('hidden');
                namaPerguruan.removeAttribute('required');
                namaPerusahaan.setAttribute('required', 'required');
            } else {
                perguruanField.classList.remove('hidden');
                perusahaanField.classList.add('hidden');
                fakultasField.classList.remove('hidden');
                prodiField.classList.remove('hidden');
                namaPerguruan.setAttribute('required', 'required');
                namaPerusahaan.removeAttribute('required');
            }
        }
    </script>
</body>
</html>
