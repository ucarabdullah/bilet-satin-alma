<!-- src/views/layouts/app.php -->
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'BiBilet - Online Otobüs Bileti') ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription ?? 'BiBilet ile en uygun otobüs biletlerini bulun ve satın alın') ?>">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS - Brand Colors -->
    <style>
        :root {
            --brand-primary: #1F2E64;
            --brand-secondary: #3DE0C4;
            --brand-primary-dark: #151f47;
            --brand-secondary-light: #6FEAD9;
            --bg-dark: #0F1419;
            --bg-medium: #1a1f2e;
            --text-light: #e8eaed;
        }
        
        body {
            background-color: #f8f9fb;
        }
        
        /* Bootstrap Override */
        .btn-primary {
            background-color: var(--brand-primary) !important;
            border-color: var(--brand-primary) !important;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: var(--brand-primary-dark) !important;
            border-color: var(--brand-primary-dark) !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(31, 46, 100, 0.3);
        }
        .btn-outline-primary {
            color: var(--brand-primary) !important;
            border-color: var(--brand-primary) !important;
            font-weight: 600;
        }
        .btn-outline-primary:hover {
            background-color: var(--brand-primary) !important;
            border-color: var(--brand-primary) !important;
            color: white !important;
        }
        
        .text-primary {
            color: var(--brand-primary) !important;
        }
        .bg-primary {
            background-color: var(--brand-primary) !important;
        }
        .border-primary {
            border-color: var(--brand-primary) !important;
        }
        
        /* Secondary Color (Turkuaz) */
        .btn-secondary {
            background-color: var(--brand-secondary) !important;
            border-color: var(--brand-secondary) !important;
            color: var(--brand-primary) !important;
            font-weight: 600;
        }
        .btn-secondary:hover {
            background-color: var(--brand-secondary-light) !important;
            border-color: var(--brand-secondary-light) !important;
        }
        
        .text-secondary-brand {
            color: var(--brand-secondary) !important;
        }
        .bg-secondary-brand {
            background-color: var(--brand-secondary) !important;
        }
        
        /* Badge */
        .badge.bg-primary {
            background-color: var(--brand-primary) !important;
        }
        
        /* Links */
        a {
            color: var(--brand-primary);
            text-decoration: none;
            transition: all 0.3s;
        }
        a:hover {
            color: var(--brand-primary-dark);
        }
        
        /* Navbar */
        header {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        
        /* Navbar Brand Logo */
        .navbar-brand {
            display: flex;
            align-items: center;
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--brand-primary) !important;
            transition: all 0.3s;
        }
        .navbar-brand:hover {
            color: var(--brand-primary-dark) !important;
            transform: scale(1.05);
        }
        .navbar-brand .logo-icon {
            width: 45px;
            height: 45px;
            margin-right: 10px;
            background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.3rem;
            box-shadow: 0 4px 12px rgba(61, 224, 196, 0.3);
        }
        
        /* Gradient Accents */
        .gradient-primary {
            background: linear-gradient(135deg, var(--brand-primary) 0%, #2a3f7a 50%, var(--brand-primary-dark) 100%);
        }
        
        .gradient-text {
            background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Card Styling */
        .card {
            border: none;
            border-radius: 12px;
            transition: all 0.3s;
        }
        .card:hover {
            border-color: var(--brand-secondary) !important;
            box-shadow: 0 8px 24px rgba(31, 46, 100, 0.15) !important;
            transform: translateY(-4px);
        }
        
        /* Form Controls Focus */
        .form-control:focus, .form-select:focus {
            border-color: var(--brand-secondary) !important;
            box-shadow: 0 0 0 0.25rem rgba(61, 224, 196, 0.25) !important;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s;
        }
        
        .form-control:hover, .form-select:hover {
            border-color: var(--brand-secondary);
        }
        
        /* Swap Button */
        .swap-cities {
            transition: all 0.3s ease;
        }
        .swap-cities:hover {
            transform: scale(1.1);
        }
        
        /* Custom Select Styling */
        .form-select, .custom-select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%231F2E64' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
            padding-right: 2.5rem;
            font-weight: 500;
        }
        
        .custom-select {
            background-color: white;
            box-shadow: 0 2px 8px rgba(31, 46, 100, 0.08);
        }
        
        .custom-select:hover {
            box-shadow: 0 4px 12px rgba(61, 224, 196, 0.25);
            border-color: var(--brand-secondary);
        }
        
        .form-select option {
            padding: 12px;
            font-size: 1rem;
            background-color: white;
            color: var(--brand-primary);
        }
        
        /* Custom Date Input Styling */
        input[type="date"] {
            position: relative;
            padding-right: 2.5rem;
            cursor: pointer;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(31, 46, 100, 0.08);
        }
        
        input[type="date"]:hover {
            box-shadow: 0 4px 12px rgba(61, 224, 196, 0.25);
            border-color: var(--brand-secondary);
        }
        
        /* Modern Date Picker */
        input[type="date"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
            border-radius: 4px;
            margin-right: 2px;
            opacity: 0.8;
            filter: invert(27%) sepia(51%) saturate(2878%) hue-rotate(204deg) brightness(95%) contrast(93%);
            transition: all 0.3s;
        }
        
        input[type="date"]::-webkit-calendar-picker-indicator:hover {
            opacity: 1;
            transform: scale(1.15);
        }
        
        /* Quick Date Buttons */
        .quick-date {
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            white-space: nowrap;
        }
        
        .quick-date.active, .quick-date:active {
            background: var(--brand-secondary) !important;
            border-color: var(--brand-secondary) !important;
            color: var(--brand-primary) !important;
        }
        
        /* Date Input Focus */
        input[type="date"]:focus::-webkit-datetime-edit {
            color: var(--brand-primary);
        }
        
        /* Custom Scrollbar for Select Options (Firefox) */
        select {
            scrollbar-width: thin;
            scrollbar-color: var(--brand-secondary) #f1f1f1;
        }
        
        /* Validation Styling */
        .form-control.is-invalid, .form-select.is-invalid {
            border-color: #dc3545 !important;
        }
        
        .form-control.is-valid, .form-select.is-valid {
            border-color: var(--brand-secondary) !important;
        }
        
        /* Error Message */
        .city-error {
            display: none;
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            animation: shake 0.5s;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        
        /* Select Dropdown Animation */
        .form-select {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .form-select:focus {
            transform: translateY(-2px);
        }
        
        /* Social Media Links */
        .social-link {
            transition: color 0.3s ease;
        }
        
        .social-link:hover {
            color: var(--brand-secondary) !important;
        }
    </style>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    
    <!-- Güvenlik Meta -->
    <meta name="csrf-token" content="<?= Security::generateCSRFToken(); ?>">
    <meta http-equiv="X-XSS-Protection" content="1; mode=block">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="DENY">
    
    <?php if (isset($extraHeaders)) echo $extraHeaders; ?>
</head>
<body>
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <a class="navbar-brand" href="/">
                    <div class="logo-icon">
                        <i class="bi bi-ticket-perforated"></i>
                    </div>
                    <span>BiBilet</span>
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                        data-bs-target="#navbarNav" aria-controls="navbarNav" 
                        aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="/">Ana Sayfa</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/#search-form">Bilet Ara</a>
                        </li>
                    </ul>
                    
                    <div class="d-flex">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                <!-- Admin User Navigation - Sadece Panel Butonu -->
                                <a href="<?php echo BASE_PATH; ?>/admin/dashboard" class="btn btn-outline-primary">
                                    <i class="bi bi-shield-check me-1"></i>Admin Paneli
                                </a>
                            <?php elseif (isset($_SESSION['user_role']) && $_SESSION['user_role'] === ROLE_COMPANY_ADMIN): ?>
                                <!-- Company Admin Navigation -->
                                <a href="<?php echo BASE_PATH; ?>/company/dashboard" class="btn btn-outline-primary">
                                    <i class="bi bi-building me-1"></i>Firma Admin Paneli
                                </a>
                            <?php else: ?>
                                <!-- Regular User Navigation -->
                                <div class="dropdown">
                                    <button class="btn btn-outline-primary dropdown-toggle" type="button" 
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-person-circle me-1"></i>Hesabım
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="<?php echo BASE_PATH; ?>/user/dashboard"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                                        <li><a class="dropdown-item" href="<?php echo BASE_PATH; ?>/user/tickets"><i class="bi bi-ticket-detailed me-2"></i>Biletlerim</a></li>
                                        <li><a class="dropdown-item" href="<?php echo BASE_PATH; ?>/user/profile"><i class="bi bi-person me-2"></i>Profilim</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="<?php echo BASE_PATH; ?>/logout"><i class="bi bi-box-arrow-right me-2"></i>Çıkış Yap</a></li>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="/login" class="btn btn-outline-primary me-2">Giriş Yap</a>
                            <a href="/register" class="btn btn-primary">Kayıt Ol</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    
    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash_messages']) && !empty($_SESSION['flash_messages'])): ?>
        <?php foreach ($_SESSION['flash_messages'] as $type => $message): ?>
            <div class="container mt-3">
                <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
            <?php unset($_SESSION['flash_messages'][$type]); ?>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Main Content -->
    <main>
        <?php 
        // Content değişkeni tanımlıysa, o dosyayı include et
        if (isset($content) && file_exists($content)) {
            require_once $content;
        }
        ?>
    </main>
    
    <!-- Footer -->
    <footer class="py-5 mt-5" style="background: linear-gradient(135deg, var(--brand-primary), #152347); color: white;">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="logo-icon me-2" style="background: var(--brand-secondary);">
                            <i class="bi bi-ticket-perforated"></i>
                        </div>
                        <h5 class="mb-0 text-white fw-bold">BiBilet</h5>
                    </div>
                    <p class="text-white-50">Online otobüs bileti satın alma platformu</p>
                    <p class="text-white-50 small">
                        <i class="bi bi-envelope me-2"></i> info@bibilet.com<br>
                        <i class="bi bi-telephone me-2"></i> 0850 123 45 67
                    </p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 class="text-white fw-bold">Hızlı Linkler</h5>
                    <ul class="list-unstyled mt-3">
                        <li class="mb-2"><a href="/" class="text-decoration-none text-white-50">Ana Sayfa</a></li>
                        <li class="mb-2"><a href="/#search-form" class="text-decoration-none text-white-50">Bilet Ara</a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li class="mb-2"><a href="/user/dashboard" class="text-decoration-none text-white-50">Dashboard</a></li>
                            <li class="mb-2"><a href="/user/tickets" class="text-decoration-none text-white-50">Biletlerim</a></li>
                        <?php else: ?>
                            <li class="mb-2"><a href="/login" class="text-decoration-none text-white-50">Giriş Yap</a></li>
                            <li class="mb-2"><a href="/register" class="text-decoration-none text-white-50">Kayıt Ol</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 class="text-white fw-bold">Bizi Takip Edin</h5>
                    <div class="d-flex gap-3 mt-3">
                        <a href="#" class="social-link text-white-50 fs-4">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="#" class="social-link text-white-50 fs-4">
                            <i class="bi bi-twitter"></i>
                        </a>
                        <a href="#" class="social-link text-white-50 fs-4">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="#" class="social-link text-white-50 fs-4">
                            <i class="bi bi-linkedin"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="border-top border-secondary pt-3 mt-3">
                <p class="text-center text-white-50 mb-0 small">
                    <i class="bi bi-c-circle me-1"></i>
                    <?= date('Y') ?> BiBilet - Tüm Hakları Saklıdır.
                </p>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>