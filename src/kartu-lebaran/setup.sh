#!/bin/bash
# Setup script untuk E-Card Lebaran 1447 H

echo "=========================================="
echo "  E-Card Lebaran 1447 H - Setup Script"
echo "=========================================="
echo ""

APP_DIR="/home/daniarsyah/my-php-app/src/kartu-lebaran"
cd "$APP_DIR" || exit 1

echo "[1/4] Setting permissions..."
chmod -R 777 assets/generated 2>/dev/null
chmod -R 755 assets/templates 2>/dev/null
chmod -R 755 assets/fonts 2>/dev/null
echo "  ✓ Permissions set"

echo ""
echo "[2/4] Checking fonts..."
if [ ! -f "assets/fonts/Roboto.ttf" ]; then
    echo "  Downloading Roboto font..."
    mkdir -p assets/fonts
    curl -sL -o assets/fonts/Roboto.ttf "https://github.com/googlefonts/roboto/raw/main/src/hinted/Roboto-Regular.ttf" 2>/dev/null
    if [ -f "assets/fonts/Roboto.ttf" ]; then
        echo "  ✓ Roboto font downloaded"
    else
        echo "  ⚠ Font download failed, will use system font"
    fi
else
    echo "  ✓ Roboto font already exists"
fi

echo ""
echo "[3/4] Generating sample template..."
php setup-template.php 2>/dev/null
if [ -f "assets/templates/template1.jpg" ]; then
    echo "  ✓ Sample template created"
else
    echo "  ⚠ Template generation skipped"
fi

echo ""
echo "[4/4] Starting containers..."
cd "$APP_DIR"
podman-compose up -d 2>/dev/null || docker-compose up -d 2>/dev/null

if [ $? -eq 0 ]; then
    echo ""
    echo "=========================================="
    echo "  ✓ Setup Complete!"
    echo ""
    echo "  Akses aplikasi di:"
    echo "  http://localhost:8080"
    echo ""
    echo "  Database: kartu_lebaran_db"
    echo "  User: root"
    echo "  Password: root_password"
    echo "=========================================="
else
    echo ""
    echo "  ⚠ Podman/Docker not available"
    echo "  Jalankan secara manual:"
    echo "  podman-compose up -d"
fi
