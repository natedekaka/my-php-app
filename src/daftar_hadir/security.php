<?php
/**
 * File: security.php
 * Deskripsi: Keamanan dasar aplikasi untuk mencegah serangan umum
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Session Security
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');

if (isset($_SESSION['created']) && time() - $_SESSION['created'] > 3600) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

// Generate CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function csrf_token() {
    return $_SESSION['csrf_token'];
}

function verify_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// XSS Protection - Escape HTML
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// Rate Limiting (sederhana)
function check_rate_limit($identifier, $max_attempts = 10, $time_window = 60) {
    $file = sys_get_temp_dir() . '/rate_limit_' . md5($identifier);
    $now = time();
    
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        if ($data['time'] > $now - $time_window) {
            if ($data['attempts'] >= $max_attempts) {
                return false;
            }
            $data['attempts']++;
        } else {
            $data = ['attempts' => 1, 'time' => $now];
        }
    } else {
        $data = ['attempts' => 1, 'time' => $now];
    }
    
    file_put_contents($file, json_encode($data));
    return true;
}

// Security Headers
function security_headers() {
    if (!headers_sent()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
}

security_headers();

// Skip CSRF check untuk API endpoints
$current_file = basename($_SERVER['PHP_SELF'] ?? '');
$api_endpoints = ['simpan.php', 'export.php'];

if (!in_array($current_file, $api_endpoints)) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $csrf_token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!verify_csrf($csrf_token)) {
            http_response_code(403);
            echo json_encode(['status' => 'gagal', 'message' => 'Token keamanan tidak valid']);
            exit;
        }
    }
}

// Rate Limiting untuk form submission (hanya simpan.php)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $current_file === 'simpan.php') {
    $rate_key = ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . '_simpan';
    if (!check_rate_limit($rate_key, 10, 60)) {
        http_response_code(429);
        echo json_encode(['status' => 'gagal', 'message' => 'Terlalu banyak percobaan. Silakan coba lagi nanti.']);
        exit;
    }
}
