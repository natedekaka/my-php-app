<?php
/**
 * E-Card Lebaran 1447 H
 * View Card Page
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

session_start();

$slug = $_GET['s'] ?? '';
$slug = validateInput($slug);

if (empty($slug)) {
    header('HTTP/1.1 404 Not Found');
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Kartu Tidak Ditemukan</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
        <style>
            body { 
                background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
                min-height: 100vh;
                font-family: 'Lato', sans-serif;
            }
            .card { 
                border: none; 
                border-radius: 20px;
                background: rgba(255, 255, 255, 0.05);
                backdrop-filter: blur(10px);
            }
        </style>
    </head>
    <body class="d-flex align-items-center justify-content-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card p-5 text-center">
                        <h2 class="text-danger">Kartu Tidak Ditemukan</h2>
                        <p class="mt-3 text-white-50">Maaf, kartu yang Anda cari tidak tersedia.</p>
                        <a href="index.php" class="btn btn-warning mt-3">Buat Kartu Baru</a>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

try {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("SELECT * FROM kartu_ucapan WHERE slug = ?");
    $stmt->execute([$slug]);
    $card = $stmt->fetch();
    
    if (!$card) {
        header('HTTP/1.1 404 Not Found');
        ?>
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Kartu Tidak Ditemukan</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body { 
                    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
                    min-height: 100vh;
                    font-family: 'Lato', sans-serif;
                }
                .card { 
                    border: none; 
                    border-radius: 20px;
                    background: rgba(255, 255, 255, 0.05);
                    backdrop-filter: blur(10px);
                }
            </style>
        </head>
        <body class="d-flex align-items-center justify-content-center">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card p-5 text-center">
                            <h2 class="text-danger">Kartu Tidak Ditemukan</h2>
                            <p class="mt-3 text-white-50">Maaf, kartu dengan slug "<?php echo htmlspecialchars($slug, ENT_QUOTES, 'UTF-8'); ?>" tidak ditemukan.</p>
                            <a href="index.php" class="btn btn-warning mt-3">Buat Kartu Baru</a>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
    
    $cardImagePath = __DIR__ . '/assets/generated/kartu_' . htmlspecialchars($card['slug'], ENT_QUOTES, 'UTF-8') . '.jpg';
    $cardImageUrl = 'assets/generated/kartu_' . htmlspecialchars($card['slug'], ENT_QUOTES, 'UTF-8') . '.jpg';
    
    $whatsappUrl = getWhatsAppShareUrl($card['slug']);
    
    $created = isset($_GET['created']) && $_GET['created'] === '1';
    
} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    $card = null;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Kartu Lebaran dari <?php echo htmlspecialchars($card['pengirim'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?> untuk <?php echo htmlspecialchars($card['penerima'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:title" content="Kartu Lebaran 1447 H - <?php echo htmlspecialchars($card['pengirim'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($card['pesan'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($cardImageUrl, ENT_QUOTES, 'UTF-8'); ?>">
    <title>Kartu Lebaran - <?php echo htmlspecialchars($card['pengirim'] ?? 'E-Card', ENT_QUOTES, 'UTF-8'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            font-family: 'Lato', sans-serif;
        }
        
        .hero-section {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.1) 0%, rgba(212, 175, 55, 0.05) 100%);
            border-bottom: 1px solid rgba(212, 175, 55, 0.3);
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        
        .hero-title {
            font-family: 'Cinzel', serif;
            font-weight: 700;
            background: linear-gradient(135deg, #d4af37 0%, #f4e4ba 50%, #d4af37 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .card-container {
            max-width: 700px;
            margin: 0 auto;
        }
        
        .card-image {
            border-radius: 20px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.5);
            width: 100%;
            height: auto;
            border: 3px solid rgba(212, 175, 55, 0.3);
        }
        
        .success-banner {
            background: linear-gradient(135deg, #d4af37 0%, #c9a227 100%);
            color: #1a1a2e;
            padding: 1rem 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            text-align: center;
            font-weight: 700;
            box-shadow: 0 10px 40px rgba(212, 175, 55, 0.3);
        }
        
        .btn-whatsapp {
            background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-size: 1.1rem;
            box-shadow: 0 10px 30px rgba(37, 211, 102, 0.3);
        }
        
        .btn-whatsapp:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(37, 211, 102, 0.4);
            color: white;
        }
        
        .btn-new {
            background: transparent;
            border: 2px solid #d4af37;
            color: #d4af37;
            font-weight: 600;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-size: 1.1rem;
        }
        
        .btn-new:hover {
            background: rgba(212, 175, 55, 0.1);
            color: #d4af37;
        }
        
        .footer {
            text-align: center;
            padding: 2rem;
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.85rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            margin-top: 3rem;
        }
        
        .footer span { color: #d4af37; }
        
        .decorative-line {
            width: 100px;
            height: 2px;
            background: linear-gradient(90deg, transparent, #d4af37, transparent);
            margin: 1rem auto;
        }
        
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <div class="hero-section">
        <div class="container text-center py-3">
            <h1 class="hero-title display-6 mb-1">Kartu Lebaran</h1>
            <div class="decorative-line"></div>
        </div>
    </div>
    
    <div class="container pb-5">
        <?php if ($created): ?>
            <div class="success-banner">
                ✨ Kartu berhasil dibuat! ✨
            </div>
        <?php endif; ?>
        
        <div class="card-container">
            <?php if (file_exists($cardImagePath)): ?>
                <img src="<?php echo htmlspecialchars($cardImageUrl, ENT_QUOTES, 'UTF-8'); ?>" 
                     alt="Kartu Lebaran dari <?php echo htmlspecialchars($card['pengirim'], ENT_QUOTES, 'UTF-8'); ?>" 
                     class="card-image mb-4">
            <?php else: ?>
                <div class="card p-5 text-center mb-4" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(212,175,55,0.2); border-radius: 20px;">
                    <h3 class="text-warning mb-3" style="font-family: 'Cinzel', serif;">
                        🕌 <?php echo htmlspecialchars($card['pengirim'], ENT_QUOTES, 'UTF-8'); ?>
                    </h3>
                    <p class="text-white-50">mengucapkan:</p>
                    <h2 class="text-warning mb-4" style="font-family: 'Cinzel', serif;">Eid Mubarak</h2>
                    <p class="text-white-50">untuk</p>
                    <h4 class="text-light mb-4"><?php echo htmlspecialchars($card['penerima'], ENT_QUOTES, 'UTF-8'); ?></h4>
                    <div class="p-4 rounded" style="background: rgba(255,255,255,0.05);">
                        <p class="mb-0 text-white"><?php echo nl2br(htmlspecialchars($card['pesan'], ENT_QUOTES, 'UTF-8')); ?></p>
                    </div>
                    <p class="text-muted mt-4 small">
                        <em>Gambar kartu tidak tersedia</em>
                    </p>
                </div>
            <?php endif; ?>
            
            <div class="action-buttons">
                <a href="<?php echo htmlspecialchars($whatsappUrl, ENT_QUOTES, 'UTF-8'); ?>" 
                   target="_blank" 
                   class="btn-whatsapp">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.326-.518-.64-.653-.305-.133-.663-.103-.962-.173.527-.161 1.033-.373 1.49-.568.187-.076.346-.119.494-.126.36-.013.691.02.98.196.082.05.158.078.217.071.396-.008.864-.042 1.288-.411.082-.072.189-.156.257-.132.196.072.781.648.903 1.759.061.51.121 1.05.26 1.56.134.48.57.879.987 1.166.42.288.878.497 1.329.634.45.138.865.124 1.216-.055.18-.092.5-.22.696-.412.098-.097.197-.22.285-.36.088-.132.044-.249-.02-.357z"/>
                    </svg>
                    Bagikan ke WhatsApp
                </a>
                
                <a href="index.php" class="btn-new">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                        <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
                    </svg>
                    Buat Kartu Baru
                </a>
            </div>
        </div>
    </div>
    
    <footer class="footer">
        <p>E-Card Lebaran 1447 H <span>•</span> natedekaka</p>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
