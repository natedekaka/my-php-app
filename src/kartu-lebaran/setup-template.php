<?php
/**
 * Create a sample template image using GD Library
 * This script generates a sample template if none exists
 */

$templateDir = __DIR__ . '/assets/templates';
$generatedDir = __DIR__ . '/assets/generated';

if (!is_dir($templateDir)) {
    mkdir($templateDir, 0755, true);
}

if (!is_dir($generatedDir)) {
    mkdir($generatedDir, 0755, true);
}

$sampleTemplatePath = $templateDir . '/template1.jpg';

if (!file_exists($sampleTemplatePath)) {
    $width = 800;
    $height = 600;
    
    $image = imagecreatetruecolor($width, $height);
    
    $bgColors = [
        imagecolorallocate($image, 26, 71, 42),
        imagecolorallocate($image, 45, 90, 63),
        imagecolorallocate($image, 34, 62, 52),
        imagecolorallocate($image, 22, 55, 38),
    ];
    
    imagefill($image, 0, 0, $bgColors[0]);
    
    for ($i = 0; $i < 10; $i++) {
        $x1 = rand(0, $width);
        $y1 = rand(0, $height);
        $x2 = rand(0, $width);
        $y2 = rand(0, $height);
        $color = imagecolorallocate($image, rand(20, 50), rand(80, 120), rand(40, 70));
        imagesetthickness($image, rand(1, 3));
        imageline($image, $x1, $y1, $x2, $y2, $color);
    }
    
    $centerX = $width / 2;
    $centerY = $height / 2;
    
    $circleColor = imagecolorallocate($image, 201, 162, 39);
    imagearc($image, $centerX, $centerY, 300, 300, 0, 360, $circleColor);
    imagearc($image, $centerX, $centerY, 290, 290, 0, 360, $circleColor);
    
    $goldColor = imagecolorallocate($image, 201, 162, 39);
    $whiteColor = imagecolorallocate($image, 255, 255, 255);
    
    $mosqueIcon = '🕌';
    $starIcon = '⭐';
    
    $rectColor = imagecolorallocatealpha($image, 201, 162, 39, 80);
    imagefilledrectangle($image, $centerX - 250, $centerY - 180, $centerX + 250, $centerY + 180, $rectColor);
    
    imagejpeg($image, $sampleTemplatePath, 90);
    imagedestroy($image);
    
    echo "Sample template created at: $sampleTemplatePath\n";
} else {
    echo "Template already exists at: $sampleTemplatePath\n";
}

$fontPath = __DIR__ . '/assets/fonts';
if (!is_dir($fontPath)) {
    mkdir($fontPath, 0755, true);
}

echo "Setup complete!\n";
echo "Please download a TTF font and place it in: $fontPath/\n";
echo "Recommended font: Roboto or NotoNaskhArabic\n";
