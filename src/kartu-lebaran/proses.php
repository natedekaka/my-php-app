<?php
/**
 * E-Card Lebaran 1447 H
 * Processing Form & Image Generation
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$csrfToken = $_POST['csrf_token'] ?? '';
if (!validateCSRFToken($csrfToken)) {
    $_SESSION['error'] = 'Token keamanan tidak valid. Silakan coba lagi.';
    header('Location: index.php');
    exit;
}

$pengirim = validateInput($_POST['pengirim'] ?? '');
$penerima = validateInput($_POST['penerima'] ?? '');
$pesan = validateInput($_POST['pesan'] ?? '');
$templateFile = validateInput($_POST['selected_template'] ?? 'template1.jpg');

$_SESSION['old'] = [
    'pengirim' => $pengirim,
    'penerima' => $penerima,
    'pesan' => $pesan
];

if (empty($pengirim) || empty($penerima) || empty($pesan)) {
    $_SESSION['error'] = 'Semua field harus diisi.';
    header('Location: index.php');
    exit;
}

if (strlen($pengirim) > 100 || strlen($penerima) > 100 || strlen($pesan) > 500) {
    $_SESSION['error'] = 'Input terlalu panjang.';
    header('Location: index.php');
    exit;
}

$templatePath = __DIR__ . '/assets/templates/' . basename($templateFile);
if (!file_exists($templatePath)) {
    $templatePath = __DIR__ . '/assets/templates/template1.jpg';
    $templateFile = 'template1.jpg';
}

$fontPath = __DIR__ . '/assets/fonts/Roboto.ttf';
if (!file_exists($fontPath)) {
    $fontPath = __DIR__ . '/assets/fonts/NotoNaskhArabic.ttf';
}
if (!file_exists($fontPath)) {
    $fontPaths = glob(__DIR__ . '/assets/fonts/*.ttf');
    $fontPath = $fontPaths[0] ?? '';
}

$generatedDir = __DIR__ . '/assets/generated';
if (!ensureDirectoryExists($generatedDir)) {
    $_SESSION['error'] = 'Gagal membuat direktori output.';
    header('Location: index.php');
    exit;
}

if (!is_writable($generatedDir)) {
    $_SESSION['error'] = 'Direktori tidak memiliki izin tulis.';
    header('Location: index.php');
    exit;
}

try {
    $pdo = getDBConnection();
    
    $slug = generateSecureSlug(16);
    
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM kartu_ucapan WHERE slug = ?");
    $checkStmt->execute([$slug]);
    while ($checkStmt->fetchColumn() > 0) {
        $slug = generateSecureSlug(16);
        $checkStmt->execute([$slug]);
    }
    
    $insertStmt = $pdo->prepare("
        INSERT INTO kartu_ucapan (slug, pengirim, penerima, pesan, template_path) 
        VALUES (?, ?, ?, ?, ?)
    ");
    
    $insertStmt->execute([
        $slug,
        $pengirim,
        $penerima,
        $pesan,
        'templates/' . basename($templateFile)
    ]);
    
    $outputFileName = 'kartu_' . $slug . '.jpg';
    $outputPath = $generatedDir . '/' . $outputFileName;
    
    $imageCreated = createCardImage(
        $templatePath,
        $outputPath,
        $pengirim,
        $penerima,
        $pesan,
        $fontPath
    );
    
    if (!$imageCreated && !empty($fontPath)) {
        $_SESSION['error'] = 'Gagal membuat gambar kartu.';
        header('Location: index.php');
        exit;
    }
    
    unset($_SESSION['old']);
    regenerateCSRFToken();
    
    $_SESSION['success'] = 'Kartu berhasil dibuat!';
    $_SESSION['card_slug'] = $slug;
    $_SESSION['card_url'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . '/lihat.php?s=' . urlencode($slug);
    
    header('Location: lihat.php?s=' . urlencode($slug) . '&created=1');
    exit;
    
} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    $_SESSION['error'] = 'Terjadi kesalahan database. Silakan coba lagi.';
    header('Location: index.php');
    exit;
}
