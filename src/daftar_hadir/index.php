<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Hadir - Tanda Tangan Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --glass-bg: rgba(255, 255, 255, 0.95);
            --shadow-elegant: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        * {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background: var(--primary-gradient);
            min-height: 100vh;
            padding: 20px 0;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
            animation: shimmer 15s infinite linear;
        }

        @keyframes shimmer {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .card {
            border: none;
            border-radius: 24px;
            box-shadow: var(--shadow-elegant);
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-header {
            background: var(--primary-gradient);
            color: white;
            border-radius: 24px 24px 0 0 !important;
            padding: 28px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .card-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.5), transparent);
            animation: shine 2s infinite;
        }

        @keyframes shine {
            0%, 100% { transform: translateX(-100%); }
            50% { transform: translateX(100%); }
        }

        .card-header h4 {
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 4px;
        }

        .card-header small {
            opacity: 0.9;
            font-weight: 500;
        }

        .card-body {
            padding: 32px;
        }

        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .form-control, .form-select {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: #fafafa;
        }

        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
            background: white;
            transform: translateY(-2px);
        }

        .form-control::placeholder {
            color: #9ca3af;
        }

        .signature-container {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            border: 2px dashed #e5e7eb;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            transition: all 0.3s ease;
        }

        .signature-container:focus-within {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
        }

        .signature-pad {
            border: none;
            border-radius: 14px;
            background: white;
            width: 100%;
            touch-action: none;
        }

        .signature-container small {
            display: block;
            padding: 8px 12px;
            color: #6b7280;
            font-size: 0.8rem;
            background: #f8fafc;
            border-top: 1px solid #e5e7eb;
        }

        .btn {
            padding: 14px 28px;
            border-radius: 14px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
        }

        .btn-outline-danger {
            border: 2px solid #ef4444;
            color: #ef4444;
            background: transparent;
        }

        .btn-outline-danger:hover {
            background: #ef4444;
            color: white;
            transform: translateY(-2px);
        }

        .links {
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.2);
        }

        .links a {
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            padding: 8px 16px;
            border-radius: 20px;
            background: rgba(255,255,255,0.1);
            margin: 0 4px;
        }

        .links a:hover {
            background: rgba(255,255,255,0.25);
            transform: translateY(-2px);
        }

        /* Toast Notification */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        .custom-toast {
            background: white;
            border-radius: 16px;
            padding: 16px 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 320px;
            animation: slideInRight 0.4s ease-out, fadeOut 0.4s ease-out 3.5s forwards;
            border-left: 5px solid;
        }

        .custom-toast.success {
            border-color: #10b981;
        }

        .custom-toast.error {
            border-color: #ef4444;
        }

        .custom-toast .icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .custom-toast.success .icon {
            background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
            color: white;
        }

        .custom-toast.error .icon {
            background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);
            color: white;
        }

        .custom-toast .content {
            flex: 1;
        }

        .custom-toast .title {
            font-weight: 700;
            font-size: 0.95rem;
            color: #1f2937;
        }

        .custom-toast .message {
            font-size: 0.85rem;
            color: #6b7280;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; transform: translateY(-10px); }
        }

        /* Loading Spinner */
        .btn.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .btn.loading .btn-text {
            opacity: 0;
        }

        .btn .spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            opacity: 0;
            animation: none;
        }

        .btn.loading .spinner {
            opacity: 1;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: translate(-50%, -50%) rotate(360deg); }
        }

        /* Success Animation */
        .success-checkmark {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9998;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(5px);
        }

        .success-checkmark.show {
            display: flex;
            animation: fadeIn 0.3s ease-out;
        }

        .success-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            animation: scaleIn 0.4s ease-out;
            box-shadow: 0 20px 60px rgba(16, 185, 129, 0.5);
        }

        .success-circle i {
            color: white;
            font-size: 3rem;
            animation: checkmark 0.3s ease-out 0.2s backwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes scaleIn {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }

        @keyframes checkmark {
            from { transform: scale(0) rotate(-45deg); opacity: 0; }
            to { transform: scale(1) rotate(0); opacity: 1; }
        }

        /* Floating particles */
        .floating-icon {
            position: absolute;
            color: rgba(255,255,255,0.1);
            font-size: 2rem;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(10deg); }
        }

        /* Responsive */
        @media (max-width: 576px) {
            .card-body { padding: 24px 20px; }
            .custom-toast { min-width: calc(100vw - 40px); }
        }

        /* Tablet */
        @media (min-width: 577px) and (max-width: 768px) {
            .card { margin: 0 10px; }
            .card-body { padding: 28px 24px; }
            .card-header h4 { font-size: 1.3rem; }
            .btn { padding: 12px 20px; }
        }

        /* Large phones */
        @media (min-width: 375px) and (max-width: 425px) {
            .card-header { padding: 20px 16px; }
            .card-header h4 { font-size: 1.2rem; }
            .form-control, .form-select { padding: 10px 14px; font-size: 0.9rem; }
            .btn { padding: 12px 20px; font-size: 0.9rem; }
            .links a { padding: 6px 10px; font-size: 0.75rem; }
        }

        /* Extra small phones */
        @media (max-width: 374px) {
            body { padding: 10px 0; }
            .card-header { padding: 16px 12px; }
            .card-header h4 { font-size: 1.1rem; }
            .card-header small { font-size: 0.75rem; }
            .card-body { padding: 20px 16px; }
            .form-label { font-size: 0.85rem; }
            .form-control, .form-select { padding: 8px 12px; font-size: 0.85rem; }
            .btn { padding: 10px 16px; font-size: 0.85rem; }
            .links a { padding: 5px 8px; font-size: 0.7rem; margin: 0 2px; }
            .signature-pad { height: 150px !important; }
        }

        /* Landscape orientation */
        @media (max-height: 500px) and (orientation: landscape) {
            body { padding: 10px; }
            .card { max-height: 95vh; overflow-y: auto; }
            .signature-pad { height: 120px !important; }
        }

        /* Touch devices */
        @media (hover: none) and (pointer: coarse) {
            .btn { min-height: 48px; }
            .form-control, .form-select { min-height: 48px; }
            .signature-pad { touch-action: none; }
        }
    </style>
</head>
<body>

<?php
include 'koneksi.php';
include 'security.php';

$events = [];
$result = $conn->query("SELECT * FROM events WHERE aktif = 'Y' ORDER BY nama_event");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}

$fields = [];
$result = $conn->query("SELECT * FROM form_fields WHERE aktif = 'Y' ORDER BY urutan ASC, id ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $fields[$row['nama_field']] = $row;
    }
}

$options = [];
$result = $conn->query("SELECT fo.* FROM field_options fo JOIN form_fields f ON fo.field_id = f.id WHERE f.aktif = 'Y'");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $options[$row['field_id']][] = $row;
    }
}
$conn->close();
?>

<div class="container position-relative">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Daftar Hadir Digital</h4>
                    <small>Silakan isi data dan tanda tangan di bawah</small>
                </div>
                <div class="card-body">
                    <form id="formAbsensi">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <div class="mb-4">
                            <label for="event_id" class="form-label">
                                <i class="fas fa-calendar-check me-1"></i>Pilih Acara <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="event_id" name="event_id" required>
                                <option value="" selected disabled>Pilih acara...</option>
                                <?php foreach ($events as $event): ?>
                                    <option value="<?= $event['id'] ?>"><?= htmlspecialchars($event['nama_event']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <?php foreach ($fields as $field): ?>
                            <?php 
                            $fieldId = $field['id'];
                            $isRequired = $field['wajib'] == 'Y';
                            $requiredAttr = $isRequired ? 'required' : '';
                            $icon = match($field['tipe']) {
                                'text' => 'fa-font',
                                'number' => 'fa-hashtag',
                                'date' => 'fa-calendar',
                                'select' => 'fa-chevron-down',
                                'textarea' => 'fa-align-left',
                                default => 'fa-pen'
                            };
                            ?>
                            
                            <?php if ($field['tipe'] == 'text'): ?>
                                <div class="mb-4">
                                    <label for="<?= $field['nama_field'] ?>" class="form-label">
                                        <i class="fas <?= $icon ?> me-1"></i><?= htmlspecialchars($field['label']) ?>
                                        <?php if($isRequired): ?><span class="text-danger">*</span><?php endif; ?>
                                    </label>
                                    <input type="text" class="form-control" 
                                           id="<?= $field['nama_field'] ?>" 
                                           name="<?= $field['nama_field'] ?>" 
                                           placeholder="<?= htmlspecialchars($field['placeholder'] ?? '') ?>"
                                           <?= $requiredAttr ?>>
                                </div>
                            
                            <?php elseif ($field['tipe'] == 'number'): ?>
                                <div class="mb-4">
                                    <label for="<?= $field['nama_field'] ?>" class="form-label">
                                        <i class="fas <?= $icon ?> me-1"></i><?= htmlspecialchars($field['label']) ?>
                                        <?php if($isRequired): ?><span class="text-danger">*</span><?php endif; ?>
                                    </label>
                                    <input type="number" class="form-control" 
                                           id="<?= $field['nama_field'] ?>" 
                                           name="<?= $field['nama_field'] ?>" 
                                           placeholder="<?= htmlspecialchars($field['placeholder'] ?? '') ?>"
                                           <?= $requiredAttr ?>>
                                </div>
                            
                            <?php elseif ($field['tipe'] == 'date'): ?>
                                <div class="mb-4">
                                    <label for="<?= $field['nama_field'] ?>" class="form-label">
                                        <i class="fas <?= $icon ?> me-1"></i><?= htmlspecialchars($field['label']) ?>
                                        <?php if($isRequired): ?><span class="text-danger">*</span><?php endif; ?>
                                    </label>
                                    <input type="date" class="form-control" 
                                           id="<?= $field['nama_field'] ?>" 
                                           name="<?= $field['nama_field'] ?>" 
                                           <?= $requiredAttr ?>>
                                </div>
                            
                            <?php elseif ($field['tipe'] == 'select'): ?>
                                <div class="mb-4">
                                    <label for="<?= $field['nama_field'] ?>" class="form-label">
                                        <i class="fas <?= $icon ?> me-1"></i><?= htmlspecialchars($field['label']) ?>
                                        <?php if($isRequired): ?><span class="text-danger">*</span><?php endif; ?>
                                    </label>
                                    <select class="form-select" id="<?= $field['nama_field'] ?>" name="<?= $field['nama_field'] ?>" <?= $requiredAttr ?>>
                                        <option value="" selected disabled><?= htmlspecialchars($field['placeholder'] ?? 'Pilih...') ?></option>
                                        <?php if (isset($options[$fieldId])): ?>
                                            <?php foreach ($options[$fieldId] as $opt): ?>
                                                <option value="<?= htmlspecialchars($opt['nilai']) ?>"><?= htmlspecialchars($opt['label']) ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            
                            <?php elseif ($field['tipe'] == 'textarea'): ?>
                                <div class="mb-4">
                                    <label for="<?= $field['nama_field'] ?>" class="form-label">
                                        <i class="fas <?= $icon ?> me-1"></i><?= htmlspecialchars($field['label']) ?>
                                        <?php if($isRequired): ?><span class="text-danger">*</span><?php endif; ?>
                                    </label>
                                    <textarea class="form-control" id="<?= $field['nama_field'] ?>" name="<?= $field['nama_field'] ?>" rows="3" placeholder="<?= htmlspecialchars($field['placeholder'] ?? '') ?>" <?= $requiredAttr ?>></textarea>
                                </div>
                            
                            <?php elseif ($field['tipe'] == 'signature'): ?>
                                <div class="mb-4">
                                    <label class="form-label">
                                        <i class="fas fa-signature me-1"></i><?= htmlspecialchars($field['label']) ?>
                                        <?php if($isRequired): ?><span class="text-danger">*</span><?php endif; ?>
                                    </label>
                                    <div class="signature-container">
                                        <canvas id="signaturePad" class="signature-pad"></canvas>
                                        <input type="hidden" name="signature_data" id="signatureData">
                                        <small><i class="fas fa-info-circle me-1"></i>Geser jari atau mouse untuk menandatangani</small>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>

                        <div class="d-grid gap-3">
                            <button type="button" class="btn btn-outline-danger" id="clearBtn">
                                <i class="fas fa-trash-alt me-2"></i>Hapus TTD
                            </button>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <span class="btn-text"><i class="fas fa-save me-2"></i>Simpan Kehadiran</span>
                                <span class="spinner"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="text-center links">
                <a href="rekap.php"><i class="fas fa-chart-bar me-1"></i> Rekap</a>
                <a href="dashboard.php"><i class="fas fa-tachometer-alt me-1"></i> Dashboard</a>
                <a href="admin.php"><i class="fas fa-cog me-1"></i> Admin</a>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<!-- Success Animation -->
<div class="success-checkmark" id="successCheckmark">
    <div class="success-circle">
        <i class="fas fa-check"></i>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
let signaturePad = null;

document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('signaturePad');
    
    if (canvas) {
        signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgba(255, 255, 255, 1)',
            penColor: '#1f2937',
            minWidth: 1.5,
            maxWidth: 3
        });

        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            const container = canvas.parentElement;
            canvas.width = container.offsetWidth * ratio;
            canvas.height = 180 * ratio;
            canvas.getContext('2d').scale(ratio, ratio);
        }

        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);

        document.getElementById('clearBtn').addEventListener('click', function() {
            signaturePad.clear();
            document.getElementById('signatureData').value = '';
            showToast('Tanda tangan dihapus', 'info');
        });
    } else {
        document.getElementById('clearBtn').style.display = 'none';
    }

    // AJAX Form Submit
    const formAbsensi = document.getElementById('formAbsensi');
    const signatureCanvas = document.getElementById('signaturePad');
    
    if (formAbsensi) {
        formAbsensi.addEventListener('submit', async function(e) {
            e.preventDefault();

            const submitBtn = document.getElementById('submitBtn');
            const signatureDataInput = document.getElementById('signatureData');
            
            // Validasi signature - cek baik dari variabel maupun input
            const hasSignature = signaturePad ? !signaturePad.isEmpty() : (signatureDataInput && signatureDataInput.value.length > 100);
            
            if (!hasSignature) {
                showToast('Mohon tanda tangan terlebih dahulu!', 'error');
                return;
            }

            // Set signature data dari canvas jika ada
            if (signaturePad && !signaturePad.isEmpty()) {
                const signatureData = signaturePad.toDataURL('image/png', 0.7);
                signatureDataInput.value = signatureData;
            }

            // Show loading
            submitBtn.classList.add('loading');

            try {
                const formData = new FormData(this);
                
                const response = await fetch('simpan.php', {
                    method: 'POST',
                    body: formData,
                    cache: 'no-store'
                });

                const result = await response.json();

                if (result.status === 'sukses') {
                    // Show success animation
                    document.getElementById('successCheckmark').classList.add('show');
                    
                    setTimeout(() => {
                        document.getElementById('successCheckmark').classList.remove('show');
                        // Reset form
                        document.getElementById('formAbsensi').reset();
                        if (signaturePad) signaturePad.clear();
                        showToast(result.message || 'Data kehadiran berhasil disimpan!', 'success');
                    }, 800);
                } else {
                    showToast(result.message || 'Gagal menyimpan data!', 'error');
                }
            } catch (error) {
                showToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
            } finally {
                submitBtn.classList.remove('loading');
            }
        });
    }

    function showToast(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = `custom-toast ${type}`;
        
        const iconClass = type === 'success' ? 'fa-check' : type === 'error' ? 'fa-times' : 'fa-info-circle';
        
        toast.innerHTML = `
            <div class="icon"><i class="fas ${iconClass}"></i></div>
            <div class="content">
                <div class="title">${type === 'success' ? 'Berhasil!' : type === 'error' ? 'Gagal!' : 'Informasi'}</div>
                <div class="message">${message}</div>
            </div>
        `;
        
        container.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 4000);
    }
});
</script>
</body>
</html>
