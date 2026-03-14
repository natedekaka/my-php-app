<?php
/**
 * File: admin_proses.php
 * Deskripsi: Memproses data dari admin panel
 */

session_start();
include 'koneksi.php';
include 'security.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$aksi = $_GET['aksi'] ?? $_POST['aksi'] ?? '';

if ($aksi == 'simpan_event') {
    $id = $_POST['id'] ?? '';
    $nama_event = trim($_POST['nama_event']);
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $aktif = $_POST['aktif'] ?? 'Y';
    
    if (empty($nama_event)) {
        header('Location: admin.php?status=gagal&msg=nama_kosong');
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
        header('Location: admin.php?status=sukses&tab=events');
    } else {
        header('Location: admin.php?status=gagal&msg=' . urlencode($stmt->error));
    }
    $stmt->close();
}

elseif ($aksi == 'hapus_event') {
    $id = $_GET['id'] ?? 0;
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header('Location: admin.php?status=sukses&tab=events');
    } else {
        header('Location: admin.php?status=gagal&msg=' . urlencode($stmt->error));
    }
    $stmt->close();
}

elseif ($aksi == 'simpan_field') {
    $id = $_POST['id'] ?? '';
    $nama_field = trim($_POST['nama_field']);
    $label = trim($_POST['label']);
    $tipe = $_POST['tipe'] ?? 'text';
    $placeholder = trim($_POST['placeholder'] ?? '');
    $urutan = (int)($_POST['urutan'] ?? 0);
    $wajib = $_POST['wajib'] ?? 'N';
    $aktif = $_POST['aktif'] ?? 'Y';
    
    if (empty($nama_field) || empty($label)) {
        header('Location: admin.php?status=gagal&msg=field_kosong&tab=fields');
        exit;
    }
    
    // Validasi nama_field (hanya alphanumeric dan underscore)
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $nama_field)) {
        header('Location: admin.php?status=gagal&msg=nama_field_invalid&tab=fields');
        exit;
    }
    
    if ($id) {
        $stmt = $conn->prepare("UPDATE form_fields SET nama_field = ?, label = ?, tipe = ?, placeholder = ?, urutan = ?, wajib = ?, aktif = ? WHERE id = ?");
        $stmt->bind_param("ssssissi", $nama_field, $label, $tipe, $placeholder, $urutan, $wajib, $aktif, $id);
    } else {
        // Cek duplikat
        $check = $conn->prepare("SELECT id FROM form_fields WHERE nama_field = ?");
        $check->bind_param("s", $nama_field);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            header('Location: admin.php?status=gagal&msg=field_duplikat&tab=fields');
            exit;
        }
        $check->close();
        
        $stmt = $conn->prepare("INSERT INTO form_fields (nama_field, label, tipe, placeholder, urutan, wajib, aktif) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssiss", $nama_field, $label, $tipe, $placeholder, $urutan, $wajib, $aktif);
    }
    
    if ($stmt->execute()) {
        header('Location: admin.php?status=sukses&tab=fields');
    } else {
        header('Location: admin.php?status=gagal&msg=' . urlencode($stmt->error) . '&tab=fields');
    }
    $stmt->close();
}

elseif ($aksi == 'hapus_field') {
    $id = $_GET['id'] ?? 0;
    $stmt = $conn->prepare("DELETE FROM form_fields WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header('Location: admin.php?status=sukses&tab=fields');
    } else {
        header('Location: admin.php?status=gagal&msg=' . urlencode($stmt->error));
    }
    $stmt->close();
}

else {
    header('Location: admin.php');
}

$conn->close();
exit;
