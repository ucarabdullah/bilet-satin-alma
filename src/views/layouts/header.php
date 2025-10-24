<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'BiBilet - Otobüs Bileti Satın Alma Platformu'; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/assets/css/custom.css">
    
    <!-- Meta description -->
    <meta name="description" content="<?php echo $pageDescription ?? 'BiBilet ile en uygun otobüs bileti satın alma platformu'; ?>">
    
    <!-- CSRF token meta tag -->
    <meta name="csrf-token" content="<?php echo Security::generateCSRFToken(); ?>">
    
    <!-- XSS protection header -->
    <meta http-equiv="X-XSS-Protection" content="1; mode=block">
    
    <!-- Prevent MIME sniffing -->
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    
    <!-- Clickjacking protection -->
    <meta http-equiv="X-Frame-Options" content="DENY">
    
    <!-- Additional headers -->
    <?php if (isset($extraHeaders)) echo $extraHeaders; ?>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_PATH; ?>/">
                <i class="fas fa-bus-alt me-2"></i>
                BiBilet
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_PATH; ?>/">Ana Sayfa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_PATH; ?>/search">Sefer Ara</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_PATH; ?>/companies">Firmalar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_PATH; ?>/about">Hakkımızda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_PATH; ?>/contact">İletişim</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if (Security::isLoggedIn()): ?>
                        <?php if (Security::getUserRole() === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_PATH; ?>/admin/dashboard">
                                    <i class="fas fa-tachometer-alt me-1"></i>Admin Panel
                                </a>
                            </li>
                        <?php elseif (Security::getUserRole() === 'company_admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_PATH; ?>/company/dashboard">
                                    <i class="fas fa-tachometer-alt me-1"></i>Firma Panel
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i><?php echo $_SESSION['user_name']; ?>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="<?php echo BASE_PATH; ?>/account/dashboard">
                                    <i class="fas fa-tachometer-alt me-2"></i>Hesabım
                                </a>
                                <a class="dropdown-item" href="<?php echo BASE_PATH; ?>/account/tickets">
                                    <i class="fas fa-ticket-alt me-2"></i>Biletlerim
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="<?php echo BASE_PATH; ?>/account/profile">
                                    <i class="fas fa-user-edit me-2"></i>Profil
                                </a>
                                <a class="dropdown-item" href="<?php echo BASE_PATH; ?>/logout">
                                    <i class="fas fa-sign-out-alt me-2"></i>Çıkış
                                </a>
                            </div>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_PATH; ?>/login">
                                <i class="fas fa-sign-in-alt me-1"></i>Giriş Yap
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_PATH; ?>/register">
                                <i class="fas fa-user-plus me-1"></i>Kayıt Ol
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Flash Message Display -->
    <?php $flashMessage = Security::getFlashMessage(); ?>
    <?php if ($flashMessage): ?>
        <div class="container mt-3">
            <div class="alert alert-<?php echo $flashMessage['type'] === 'error' ? 'danger' : $flashMessage['type']; ?> alert-dismissible fade show">
                <?php echo $flashMessage['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Main Content Container -->
    <main class="container mt-4">