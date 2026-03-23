<?php
/**
 * File: admin_proses.php
 * Deskripsi: Memproses data dari admin panel
 */

session_start();
include '../includes/koneksi.php';
include '../includes/security.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../public/login.php');
    exit;
}

$aksi = $_GET['aksi'] ?? $_POST['aksi'] ?? '';

if ($aksi == 'simpan_event') {
    $id = $_POST['id'] ?? '';
    $nama_event = trim($_POST['nama_event']);
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $aktif = $_POST['aktif'] ?? 'Y';
    
    if (empty($nama_event)) {
        header('Location: ../public/admin.php?status=gagal&msg=nama_kosong');
        exit;
    }
    
    if ($id) {
        $stmt = $conn->prepare("UPDATE events SET nama_event = ?, deskripsi = ?, aktif = ? WHERE id = ?");
        $stmt->bind_param("ssi", $nama_event, $deskripsi, $aktif, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO events (nama_event, deskripsi, aktif) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nama_event, $deskripsi, $aktif);
    }
    
    if ($stmt->execute()) {
        header('Location: ../public/admin.php?status=sukses&tab=events');
    } else {
        header('Location: ../public/admin.php?status=gagal&msg=' . urlencode($stmt->error));
    }
    $stmt->close();
}

elseif ($aksi == 'hapus_event') {
    $id = $_GET['id'] ?? 0;
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header('Location: ../public/admin.php?status=sukses&tab=events');
    } else {
        header('Location: ../public/admin.php?status=gagal&msg=' . urlencode($stmt->error));
    }
    $stmt->close();
}

elseif ($aksi == 'simpan_field') {
    $id = $_POST['id'] ?? '';
    $nama_field_raw = trim($_POST['nama_field']);
    $nama_field = strtolower($nama_field_raw);
    $nama_field = preg_replace('/[^a-z0-9_]/', '_', $nama_field);
    $nama_field = preg_replace('/_+/', '_', $nama_field);
    $nama_field = trim($nama_field, '_');
    $label = trim($_POST['label']);
    $tipe = $_POST['tipe'] ?? 'text';
    $placeholder = trim($_POST['placeholder'] ?? '');
    $urutan = (int)($_POST['urutan'] ?? 0);
    $wajib = $_POST['wajib'] ?? 'N';
    $aktif = $_POST['aktif'] ?? 'Y';
    $event_id = !empty($_POST['event_id']) ? (int)$_POST['event_id'] : null;
    
    if (empty($nama_field) || empty($label)) {
        header('Location: ../public/admin.php?status=gagal&msg=field_kosong&tab=fields');
        exit;
    }
    
    if (empty($nama_field) || !preg_match('/^[a-z][a-z0-9_]*$/', $nama_field)) {
        header('Location: ../public/admin.php?status=gagal&msg=nama_field_invalid&tab=fields');
        exit;
    }
    
    $nama_field_esc = $conn->real_escape_string($nama_field);
    $label_esc = $conn->real_escape_string($label);
    $tipe_esc = $conn->real_escape_string($tipe);
    $placeholder_esc = $conn->real_escape_string($placeholder);
    $wajib_esc = $conn->real_escape_string($wajib);
    $aktif_esc = $conn->real_escape_string($aktif);
    
    $event_check = $event_id !== null ? " AND event_id = $event_id" : " AND event_id IS NULL";
    
    if (!$id) {
        $check_sql = "SELECT id FROM form_fields WHERE nama_field = '$nama_field_esc' $event_check";
        $check_result = @$conn->query($check_sql);
        if ($check_result && $check_result->num_rows > 0) {
            header('Location: ../public/admin.php?status=gagal&msg=nama_field_sudah_ada&tab=fields');
            exit;
        }
    }
    
    if ($id) {
        $id_esc = (int)$id;
        if ($event_id !== null) {
            $sql = "UPDATE form_fields SET nama_field = '$nama_field_esc', label = '$label_esc', tipe = '$tipe_esc', placeholder = '$placeholder_esc', urutan = $urutan, wajib = '$wajib_esc', aktif = '$aktif_esc', event_id = $event_id WHERE id = $id_esc";
        } else {
            $sql = "UPDATE form_fields SET nama_field = '$nama_field_esc', label = '$label_esc', tipe = '$tipe_esc', placeholder = '$placeholder_esc', urutan = $urutan, wajib = '$wajib_esc', aktif = '$aktif_esc', event_id = NULL WHERE id = $id_esc";
        }
    } else {
        if ($event_id !== null) {
            $sql = "INSERT INTO form_fields (nama_field, label, tipe, placeholder, urutan, wajib, aktif, event_id) VALUES ('$nama_field_esc', '$label_esc', '$tipe_esc', '$placeholder_esc', $urutan, '$wajib_esc', '$aktif_esc', $event_id)";
        } else {
            $sql = "INSERT INTO form_fields (nama_field, label, tipe, placeholder, urutan, wajib, aktif, event_id) VALUES ('$nama_field_esc', '$label_esc', '$tipe_esc', '$placeholder_esc', $urutan, '$wajib_esc', '$aktif_esc', NULL)";
        }
    }
    
    $result = @$conn->query($sql);
    if ($result) {
        header('Location: ../public/admin.php?status=sukses&tab=fields');
    } else {
        header('Location: ../public/admin.php?status=gagal&msg=' . urlencode($conn->error) . '&tab=fields');
    }
}

elseif ($aksi == 'hapus_field') {
    $id = $_GET['id'] ?? 0;
    $stmt = $conn->prepare("DELETE FROM form_fields WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header('Location: ../public/admin.php?status=sukses&tab=fields');
    } else {
        header('Location: ../public/admin.php?status=gagal&msg=' . urlencode($stmt->error));
    }
    $stmt->close();
}

elseif ($aksi == 'hapus_presensi') {
    $id = (int)($_GET['id'] ?? 0);
    
    if ($id > 0) {
        $stmt = $conn->prepare("SELECT ttd_file FROM presensi WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $ttdFile = $row['ttd_file'];
            
            $stmt2 = $conn->prepare("DELETE FROM presensi WHERE id = ?");
            $stmt2->bind_param("i", $id);
            $stmt2->execute();
            $stmt2->close();
            
            if ($ttdFile) {
                $paths = [
                    __DIR__ . '/' . $ttdFile,
                    __DIR__ . '/uploads/' . basename($ttdFile)
                ];
                foreach ($paths as $path) {
                    if (file_exists($path)) {
                        @unlink($path);
                    }
                }
            }
        }
        $stmt->close();
    }
    
    header('Location: ../public/rekap_admin.php?status=hapus_ok');
    exit;
}

else {
    header('Location: ../public/admin.php');
}

$conn->close();
exit;
