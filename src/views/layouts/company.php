<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Firma Panel' ?> - BiBilet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --company-primary: #1e3c72;
            --company-secondary: #2a5298;
            --company-accent: #3b82f6;
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --topbar-height: 70px;
            --sidebar-width: 260px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f1f5f9;
            overflow-x: hidden;
        }
        
        /* Sidebar */
        .company-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            color: white;
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s;
        }
        
        .company-sidebar-header {
            padding: 25px 20px;
            background: linear-gradient(135deg, var(--company-primary) 0%, var(--company-secondary) 100%);
            text-align: center;
        }
        
        .company-sidebar-header h3 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .company-sidebar-header p {
            margin: 5px 0 0;
            font-size: 0.85rem;
            opacity: 0.9;
        }
        
        .company-name-badge {
            background: rgba(255,255,255,0.15);
            padding: 8px 12px;
            border-radius: 8px;
            margin-top: 10px;
            font-size: 0.8rem;
            display: inline-block;
        }
        
        .company-nav {
            padding: 20px 0;
        }
        
        .company-nav-section {
            padding: 10px 20px;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #94a3b8;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .company-nav-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #cbd5e1;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .company-nav-item:hover {
            background: var(--sidebar-hover);
            color: white;
            border-left-color: var(--company-accent);
        }
        
        .company-nav-item.active {
            background: var(--sidebar-hover);
            color: white;
            border-left-color: var(--company-accent);
        }
        
        .company-nav-item i {
            width: 24px;
            margin-right: 12px;
            font-size: 1.1rem;
        }
        
        /* Topbar */
        .company-topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--topbar-height);
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            z-index: 999;
        }
        
        .company-topbar-left h1 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            color: #1e293b;
        }
        
        .company-topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .company-user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 15px;
            background: #f8fafc;
            border-radius: 50px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .company-user-info:hover {
            background: #e2e8f0;
        }
        
        .company-user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--company-primary), var(--company-secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        .company-user-name {
            font-weight: 600;
            color: #1e293b;
        }
        
        /* Main Content */
        .company-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--topbar-height);
            padding: 30px;
            min-height: calc(100vh - var(--topbar-height));
        }
        
        /* Cards */
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .stat-card-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 15px;
        }
        
        .stat-card-icon.blue {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }
        
        .stat-card-icon.green {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        
        .stat-card-icon.orange {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }
        
        .stat-card-icon.purple {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
        }
        
        .stat-card-value {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }
        
        .stat-card-label {
            color: #64748b;
            font-size: 0.95rem;
            margin: 5px 0 0;
        }
        
        /* Table Enhancements */
        .company-table-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-top: 25px;
        }
        
        .company-table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .company-table-header h2 {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 600;
            color: #1e293b;
        }
        
        .table {
            margin: 0;
        }
        
        .table thead th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            border: none;
            padding: 15px;
        }
        
        .table tbody td {
            padding: 15px;
            vertical-align: middle;
        }
        
        /* Buttons */
        .btn-company-primary {
            background: linear-gradient(135deg, var(--company-primary), var(--company-secondary));
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .btn-company-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(42, 82, 152, 0.3);
            color: white;
        }
        
        .btn-sm {
            padding: 6px 14px;
            font-size: 0.875rem;
        }
        
        /* Badge */
        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 500;
        }
        
        /* Flash Messages */
        .company-alert {
            border-radius: 10px;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .company-sidebar {
                transform: translateX(-100%);
            }
            
            .company-sidebar.show {
                transform: translateX(0);
            }
            
            .company-topbar {
                left: 0;
            }
            
            .company-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="company-sidebar">
        <div class="company-sidebar-header">
            <h3><i class="bi bi-building"></i> BiBilet</h3>
            <p>Firma Paneli</p>
            <?php if (isset($_SESSION['company_name'])): ?>
                <div class="company-name-badge">
                    <i class="bi bi-star-fill me-1"></i><?= htmlspecialchars($_SESSION['company_name']) ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="company-nav">
            <div class="company-nav-section">MENÜ</div>
            <a href="/company/dashboard" class="company-nav-item <?= strpos($_SERVER['REQUEST_URI'], '/company/dashboard') !== false ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
            
            <div class="company-nav-section">YÖNETİM</div>
            <a href="/company/trips" class="company-nav-item <?= strpos($_SERVER['REQUEST_URI'], '/company/trips') !== false ? 'active' : '' ?>">
                <i class="bi bi-bus-front"></i>
                <span>Seferler</span>
            </a>
            <a href="/company/tickets" class="company-nav-item <?= strpos($_SERVER['REQUEST_URI'], '/company/tickets') !== false ? 'active' : '' ?>">
                <i class="bi bi-receipt"></i>
                <span>Biletler</span>
            </a>
            <a href="/company/coupons" class="company-nav-item <?= strpos($_SERVER['REQUEST_URI'], '/company/coupons') !== false ? 'active' : '' ?>">
                <i class="bi bi-ticket-perforated"></i>
                <span>Kuponlar</span>
            </a>
            
            <div class="company-nav-section">AYARLAR</div>
            <a href="/company/profile" class="company-nav-item <?= strpos($_SERVER['REQUEST_URI'], '/company/profile') !== false ? 'active' : '' ?>">
                <i class="bi bi-person-circle"></i>
                <span>Profilim</span>
            </a>
            <a href="/company/settings" class="company-nav-item <?= strpos($_SERVER['REQUEST_URI'], '/company/settings') !== false ? 'active' : '' ?>">
                <i class="bi bi-gear"></i>
                <span>Firma Ayarları</span>
            </a>
            
            <div class="company-nav-section">SİSTEM</div>
            <a href="/" class="company-nav-item" target="_blank">
                <i class="bi bi-globe"></i>
                <span>Siteyi Görüntüle</span>
            </a>
            <a href="/logout" class="company-nav-item">
                <i class="bi bi-box-arrow-right"></i>
                <span>Çıkış Yap</span>
            </a>
        </div>
    </div>
    
    <!-- Topbar -->
    <div class="company-topbar">
        <div class="company-topbar-left">
            <h1><?= $pageTitle ?? 'Dashboard' ?></h1>
        </div>
        <div class="company-topbar-right">
            <div class="company-user-info">
                <div class="company-user-avatar">
                    <?= strtoupper(substr($_SESSION['user_email'] ?? 'F', 0, 1)) ?>
                </div>
                <div>
                    <div class="company-user-name"><?= htmlspecialchars($_SESSION['user_email'] ?? 'Firma Admin') ?></div>
                    <small class="text-muted">Firma Yöneticisi</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="company-content">
        <!-- Flash Messages -->
        <?php if (isset($_SESSION['flash_messages']) && !empty($_SESSION['flash_messages'])): ?>
            <?php foreach ($_SESSION['flash_messages'] as $type => $message): ?>
                <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> company-alert alert-dismissible fade show" role="alert">
                    <i class="bi bi-<?= $type === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
                    <?= htmlspecialchars($message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['flash_messages'][$type]); ?>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <!-- Page Content -->
        <?php 
        if (isset($content) && file_exists($content)) {
            require_once $content;
        }
        ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
