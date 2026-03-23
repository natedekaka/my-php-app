<?php
/**
 * E-Card Lebaran 1447 H
 * Form Input - Index Page
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

session_start();

$csrfToken = initCSRFToken();
$templates = getTemplates();

initializeDatabase();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Kirim Kartu Lebaran Digital 1447 H untuk keluarga dan sahabat">
    <title>E-Card Lebaran 1447 H</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            font-family: 'Lato', sans-serif;
            overflow-x: hidden;
        }
        
        .hero-section {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.1) 0%, rgba(212, 175, 55, 0.05) 100%);
            border-bottom: 1px solid rgba(212, 175, 55, 0.3);
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        
        .hero-title {
            font-family: 'Cinzel', serif;
            font-weight: 700;
            background: linear-gradient(135deg, #d4af37 0%, #f4e4ba 50%, #d4af37 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: none;
            letter-spacing: 3px;
        }
        
        .hero-subtitle {
            color: rgba(255, 255, 255, 0.7);
            font-weight: 300;
            letter-spacing: 1px;
        }
        
        .card-form {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .card-form .card-body {
            padding: 2.5rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #d4af37;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
        }
        
        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: #fff;
            padding: 0.8rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: #d4af37;
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
            color: #fff;
        }
        
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: none;
        }
        
        .template-section {
            margin-top: 2rem;
        }
        
        .template-section h5 {
            font-family: 'Cinzel', serif;
            color: #d4af37;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 1rem;
        }
        
        .template-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 15px;
        }
        
        @media (max-width: 768px) {
            .template-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        @media (max-width: 480px) {
            .template-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        .template-item {
            position: relative;
            aspect-ratio: 4/3;
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            border: 3px solid transparent;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }
        
        .template-item:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 15px 35px rgba(212, 175, 55, 0.2);
        }
        
        .template-item.selected {
            border-color: #d4af37;
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.3), 0 15px 35px rgba(212, 175, 55, 0.2);
        }
        
        .template-item.selected::after {
            content: '';
            position: absolute;
            top: 8px;
            right: 8px;
            width: 24px;
            height: 24px;
            background: #d4af37;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }
        
        .template-item.selected::before {
            content: '✓';
            position: absolute;
            top: 8px;
            right: 8px;
            width: 24px;
            height: 24px;
            color: #1a1a2e;
            font-size: 14px;
            font-weight: bold;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .template-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .template-item:hover img {
            transform: scale(1.1);
        }
        
        .template-number {
            position: absolute;
            bottom: 5px;
            left: 5px;
            background: rgba(0, 0, 0, 0.7);
            color: #d4af37;
            font-size: 0.7rem;
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: 600;
        }
        
        .btn-create {
            background: linear-gradient(135deg, #d4af37 0%, #c9a227 50%, #b8941f 100%);
            border: none;
            color: #1a1a2e;
            font-weight: 700;
            padding: 1rem 3rem;
            border-radius: 50px;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .btn-create::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }
        
        .btn-create:hover::before {
            left: 100%;
        }
        
        .btn-create:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(212, 175, 55, 0.4);
            color: #1a1a2e;
        }
        
        .btn-create:active {
            transform: translateY(0);
        }
        
        .btn-create svg {
            margin-right: 10px;
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.5rem;
        }
        
        .alert-danger {
            background: rgba(220, 53, 69, 0.15);
            color: #ff6b7a;
            border-left: 4px solid #dc3545;
        }
        
        .alert-success {
            background: rgba(40, 167, 69, 0.15);
            color: #6eea8b;
            border-left: 4px solid #28a745;
        }
        
        .footer {
            text-align: center;
            padding: 2rem;
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.85rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            margin-top: 3rem;
        }
        
        .footer span {
            color: #d4af37;
        }
        
        .mosque-icon {
            font-size: 3rem;
            color: #d4af37;
            margin-bottom: 1rem;
        }
        
        .decorative-line {
            width: 100px;
            height: 2px;
            background: linear-gradient(90deg, transparent, #d4af37, transparent);
            margin: 1rem auto;
        }
    </style>
</head>
<body>
    <div class="hero-section">
        <div class="container text-center py-4">
            <div class="mosque-icon">🕌</div>
            <h1 class="hero-title display-4 mb-2">E-Card Lebaran</h1>
            <div class="decorative-line"></div>
            <p class="hero-subtitle lead">Sebarkan Kehangatan Hari Raya Idulfitri 1447 H</p>
        </div>
    </div>
    
    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card-form">
                    <div class="card-body">
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php 
                                echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8');
                                unset($_SESSION['error']);
                                ?>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php 
                                echo htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8');
                                unset($_SESSION['success']);
                                ?>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form action="proses.php" method="POST" id="cardForm">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="selected_template" id="selectedTemplate" value="">
                            
                            <div class="mb-4">
                                <label for="pengirim" class="form-label">Nama Pengirim</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="pengirim" 
                                       name="pengirim" 
                                       placeholder="Contoh: Budi Santoso"
                                       maxlength="100"
                                       required
                                       value="<?php echo isset($_SESSION['old']['pengirim']) ? htmlspecialchars($_SESSION['old']['pengirim'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                            </div>
                            
                            <div class="mb-4">
                                <label for="penerima" class="form-label">Nama Penerima</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="penerima" 
                                       name="penerima" 
                                       placeholder="Contoh: Keluarga Jones"
                                       maxlength="100"
                                       required
                                       value="<?php echo isset($_SESSION['old']['penerima']) ? htmlspecialchars($_SESSION['old']['penerima'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                            </div>
                            
                            <div class="mb-4">
                                <label for="pesan" class="form-label">Pesan Hari Raya</label>
                                <textarea class="form-control" 
                                          id="pesan" 
                                          name="pesan" 
                                          rows="4" 
                                          maxlength="300"
                                          placeholder="Tulis pesan ucapan Idulfitri Anda di sini..."
                                          required><?php echo isset($_SESSION['old']['pesan']) ? htmlspecialchars($_SESSION['old']['pesan'], ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
                                <small class="text-muted mt-2 d-block" style="color: rgba(255,255,255,0.5) !important;">Maksimal 300 karakter</small>
                            </div>
                            
                            <div class="template-section">
                                <h5><span class="me-2">✨</span>Pilih Template</h5>
                                <div class="template-grid" id="templatePreview">
                                    <?php 
                                    $templateIndex = 0;
                                    foreach ($templates as $name => $file): 
                                        $templateIndex++;
                                        $imgPath = 'assets/templates/' . htmlspecialchars($file, ENT_QUOTES, 'UTF-8');
                                        $isSelected = $templateIndex === 1 ? 'selected' : '';
                                    ?>
                                        <div class="template-item <?php echo $isSelected; ?>" 
                                             data-template="<?php echo htmlspecialchars($file, ENT_QUOTES, 'UTF-8'); ?>"
                                             data-index="<?php echo $templateIndex; ?>"
                                             onclick="selectTemplate(this)">
                                            <?php if (file_exists(__DIR__ . '/assets/templates/' . $file)): ?>
                                                <img src="<?php echo $imgPath; ?>" alt="Template <?php echo $templateIndex; ?>" loading="lazy">
                                            <?php else: ?>
                                                <div class="d-flex align-items-center justify-content-center h-100 bg-secondary">
                                                    <span class="text-white">Template <?php echo $templateIndex; ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <span class="template-number"><?php echo sprintf('%02d', $templateIndex); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <div class="text-center mt-5">
                                <button type="submit" class="btn btn-create" id="btnCreate">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0Zm-2-6a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z"/>
                                        <path d="M2 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2H2Zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12Z"/>
                                    </svg>
                                    Buat Kartu
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="footer">
        <p>E-Card Lebaran 1447 H <span>•</span> natedekaka</p>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectTemplate(element) {
            document.querySelectorAll('.template-item').forEach(el => {
                el.classList.remove('selected');
            });
            element.classList.add('selected');
            document.getElementById('selectedTemplate').value = element.dataset.template;
        }
        
        // Auto-select first template
        document.addEventListener('DOMContentLoaded', function() {
            const firstTemplate = document.querySelector('.template-item');
            if (firstTemplate && !document.getElementById('selectedTemplate').value) {
                selectTemplate(firstTemplate);
            }
        });
        
        document.getElementById('cardForm').addEventListener('submit', function(e) {
            const template = document.getElementById('selectedTemplate').value;
            if (!template) {
                e.preventDefault();
                alert('Silakan pilih template terlebih dahulu');
            }
        });
        
        // Character counter for message
        const pesanInput = document.getElementById('pesan');
        pesanInput.addEventListener('input', function() {
            const count = this.value.length;
            const max = this.getAttribute('maxlength');
            let helper = this.nextElementSibling;
            if (helper && helper.classList.contains('text-muted')) {
                helper.textContent = `${count}/${max} karakter`;
            }
        });
    </script>
    <?php unset($_SESSION['old']); ?>
</body>
</html>
