<?php
/**
 * GD Library Functions for Image Generation
 * E-Card Lebaran 1447 H - Elegant Design
 */

function generateSecureSlug(int $length = 16): string {
    return bin2hex(random_bytes($length / 2));
}

function validateInput(string $data): string {
    return trim(htmlspecialchars($data, ENT_QUOTES, 'UTF-8'));
}

function ensureDirectoryExists(string $path): bool {
    if (!is_dir($path)) {
        return mkdir($path, 0755, true);
    }
    return is_writable($path);
}

function createCardImage(
    string $templatePath,
    string $outputPath,
    string $pengirim,
    string $penerima,
    string $pesan,
    string $fontPath
): bool {
    if (!file_exists($templatePath)) {
        error_log("Template not found: $templatePath");
        return false;
    }
    
    $image = imagecreatefromjpeg($templatePath);
    if (!$image) {
        $image = imagecreatefrompng($templatePath);
    }
    if (!$image) {
        $image = imagecreatefromstring(file_get_contents($templatePath));
    }
    
    if (!$image) {
        error_log("Failed to create image from template");
        return false;
    }
    
    $width = imagesx($image);
    $height = imagesy($image);
    
    $overlay = imagecreatetruecolor($width, $height);
    imagesavealpha($overlay, true);
    $transparent = imagecolorallocatealpha($overlay, 0, 0, 0, 95);
    imagefill($overlay, 0, 0, $transparent);
    imagecopymerge($overlay, $image, 0, 0, 0, 0, $width, $height, 100);
    
    $goldColor = imagecolorallocate($overlay, 212, 175, 55);
    $whiteColor = imagecolorallocate($overlay, 255, 255, 255);
    $darkColor = imagecolorallocate($overlay, 30, 30, 30);
    $shadowColor = imagecolorallocate($overlay, 0, 0, 0);
    
    $fontSizeTitle = (int)max(12, min(20, $width / 30));
    $fontSizeYear = (int)max(10, min(16, $width / 40));
    $fontSizeName = (int)max(8, min(14, $width / 45));
    $fontSizeMsg = (int)max(7, min(12, $width / 50));
    $fontSizeBottom = (int)max(6, min(10, $width / 60));
    
    $framePadding = (int)($width * 0.08);
    $frameWidth = $width - (2 * $framePadding);
    $frameHeight = $height - (2 * $framePadding);
    $frameX1 = $framePadding;
    $frameY1 = $framePadding;
    $frameX2 = $width - $framePadding;
    $frameY2 = $height - $framePadding;
    
    $borderWidth = (int)max(2, $width / 200);
    $innerBorderWidth = (int)max(1, $width / 400);
    
    for ($i = 0; $i < $borderWidth; $i++) {
        imagerectangle($overlay, $frameX1 + $i, $frameY1 + $i, $frameX2 - $i, $frameY2 - $i, $goldColor);
    }
    
    for ($i = 0; $i < $innerBorderWidth; $i++) {
        $offset = $borderWidth + 3 + $i;
        imagerectangle($overlay, $frameX1 + $offset, $frameY1 + $offset, $frameX2 - $offset, $frameY2 - $offset, $goldColor);
    }
    
    $cornerSize = (int)($width * 0.02);
    $cornerThick = (int)max(3, $width / 150);
    
    // Top-left corner
    imageline($overlay, $frameX1, $frameY1 + $cornerSize, $frameX1, $frameY1, $goldColor);
    imageline($overlay, $frameX1, $frameY1, $frameX1 + $cornerSize, $frameY1, $goldColor);
    imageline($overlay, $frameX1, $frameY1 + $cornerSize, $frameX1, $frameY1, $whiteColor);
    imageline($overlay, $frameX1, $frameY1, $frameX1 + $cornerSize, $frameY1, $whiteColor);
    
    // Top-right corner
    imageline($overlay, $frameX2 - $cornerSize, $frameY1, $frameX2, $frameY1, $goldColor);
    imageline($overlay, $frameX2, $frameY1, $frameX2, $frameY1 + $cornerSize, $goldColor);
    
    // Bottom-left corner
    imageline($overlay, $frameX1, $frameY2 - $cornerSize, $frameX1, $frameY2, $goldColor);
    imageline($overlay, $frameX1, $frameY2, $frameX1 + $cornerSize, $frameY2, $goldColor);
    
    // Bottom-right corner
    imageline($overlay, $frameX2 - $cornerSize, $frameY2, $frameX2, $frameY2, $goldColor);
    imageline($overlay, $frameX2, $frameY2 - $cornerSize, $frameX2, $frameY2, $goldColor);
    
    $contentTop = $frameY1 + $borderWidth + 30;
    $contentBottom = $frameY2 - $borderWidth - 30;
    $contentHeight = $contentBottom - $contentTop;
    $contentWidth = $frameWidth - 60;
    $centerX = (int)($width / 2);
    
    $titleY = (int)($contentTop + ($contentHeight * 0.12));
    
    if (file_exists($fontPath)) {
        imagettftext($overlay, $fontSizeTitle, 0, $centerX + 4, $titleY + 4, $shadowColor, $fontPath, 'Eid Mubarak');
        imagettftext($overlay, $fontSizeTitle, 0, $centerX + 2, $titleY + 2, $shadowColor, $fontPath, 'Eid Mubarak');
        imagettftext($overlay, $fontSizeTitle, 0, $centerX, $titleY, $goldColor, $fontPath, 'Eid Mubarak');
        
        imagettftext($overlay, $fontSizeYear, 0, $centerX + 3, $titleY + $fontSizeTitle + 18, $shadowColor, $fontPath, '1447 H');
        imagettftext($overlay, $fontSizeYear, 0, $centerX, $titleY + $fontSizeTitle + 15, $whiteColor, $fontPath, '1447 H');
        
        $fromY = (int)($contentTop + ($contentHeight * 0.38));
        imagettftext($overlay, $fontSizeName, 0, $centerX + 3, $fromY + 3, $shadowColor, $fontPath, "Dari: $pengirim");
        imagettftext($overlay, $fontSizeName, 0, $centerX, $fromY, $whiteColor, $fontPath, "Dari: $pengirim");
        
        $toY = (int)($contentTop + ($contentHeight * 0.52));
        imagettftext($overlay, $fontSizeName, 0, $centerX + 3, $toY + 3, $shadowColor, $fontPath, "Untuk: $penerima");
        imagettftext($overlay, $fontSizeName, 0, $centerX, $toY, $whiteColor, $fontPath, "Untuk: $penerima");
        
        $msgStartY = (int)($contentTop + ($contentHeight * 0.68));
        $lines = wrapText($pesan, $fontPath, $fontSizeMsg, $contentWidth);
        $lineHeight = (int)($fontSizeMsg * 2.0);
        
        $y = $msgStartY;
        foreach ($lines as $line) {
            if ($y < $contentBottom - $lineHeight) {
                imagettftext($overlay, $fontSizeMsg, 0, $centerX + 3, $y + 3, $shadowColor, $fontPath, $line);
                imagettftext($overlay, $fontSizeMsg, 0, $centerX, $y, $whiteColor, $fontPath, $line);
                $y += $lineHeight;
            }
        }
        
        $bottomY = $contentBottom - 10;
        imagettftext($overlay, $fontSizeBottom, 0, $centerX + 3, $bottomY + 3, $shadowColor, $fontPath, 'Mohon Maaf Lahir dan Batin');
        imagettftext($overlay, $fontSizeBottom, 0, $centerX, $bottomY, $goldColor, $fontPath, 'Mohon Maaf Lahir dan Batin');
    } else {
        $textColor = $whiteColor;
        imagestring($overlay, 5, (int)$centerX - 50, $titleY - 20, 'Eid Mubarak', $goldColor);
        imagestring($overlay, 4, (int)$centerX - 20, $titleY + 10, '1447 H', $whiteColor);
        imagestring($overlay, 3, (int)$centerX - 60, $contentTop + 80, "Dari: $pengirim", $textColor);
        imagestring($overlay, 3, (int)$centerX - 60, $contentTop + 100, "Untuk: $penerima", $textColor);
        imagestring($overlay, 2, (int)$centerX - 80, $contentTop + 130, substr($pesan, 0, 50), $textColor);
    }
    
    $result = imagejpeg($overlay, $outputPath, 95);
    imagedestroy($image);
    imagedestroy($overlay);
    
    return $result;
}

function wrapText(string $text, string $fontPath, float $fontSize, float $maxWidth): array {
    if (!file_exists($fontPath)) {
        return str_split($text, 40);
    }
    
    $words = explode(' ', $text);
    $lines = [];
    $currentLine = '';
    
    foreach ($words as $word) {
        $testLine = empty($currentLine) ? $word : $currentLine . ' ' . $word;
        $bbox = imagettfbbox($fontSize, 0, $fontPath, $testLine);
        $testWidth = abs($bbox[2] - $bbox[0]);
        
        if ($testWidth > $maxWidth && !empty($currentLine)) {
            $lines[] = $currentLine;
            $currentLine = $word;
        } else {
            $currentLine = $testLine;
        }
    }
    
    if (!empty($currentLine)) {
        $lines[] = $currentLine;
    }
    
    return $lines ?: [$text];
}

function initCSRFToken(): string {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

function validateCSRFToken(?string $token): bool {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

function regenerateCSRFToken(): string {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}

function getTemplates(): array {
    $templateDir = __DIR__ . '/../assets/templates';
    $templates = [];
    
    if (is_dir($templateDir)) {
        $files = glob($templateDir . '/*.{jpg,jpeg,png}', GLOB_BRACE);
        foreach ($files as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);
            $templates[$name] = basename($file);
        }
    }
    
    if (empty($templates)) {
        $templates['template1'] = 'template1.jpg';
    }
    
    return $templates;
}

function getWhatsAppShareUrl(string $slug): string {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $basePath = dirname($_SERVER['SCRIPT_NAME']);
    $cardUrl = "$protocol://$host$basePath/lihat.php?s=" . urlencode($slug);
    $text = urlencode("Saya mengirim Kartu Lebaran untuk Anda! 🎉\n\nKlik link berikut untuk melihat:\n$cardUrl");
    return "https://wa.me/?text=$text";
}
