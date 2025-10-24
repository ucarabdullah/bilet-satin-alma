<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Admin Panel' ?> - BiBilet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --admin-primary: #667eea;
            --admin-secondary: #764ba2;
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
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
        .admin-sidebar {
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
        
        .admin-sidebar-header {
            padding: 25px 20px;
            background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);
            text-align: center;
        }
        
        .admin-sidebar-header h3 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .admin-sidebar-header p {
            margin: 5px 0 0;
            font-size: 0.85rem;
            opacity: 0.9;
        }
        
        .admin-nav {
            padding: 20px 0;
        }
        
        .admin-nav-section {
            padding: 10px 20px;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #94a3b8;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .admin-nav-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #cbd5e1;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .admin-nav-item:hover {
            background: var(--sidebar-hover);
            color: white;
            border-left-color: var(--admin-primary);
        }
        
        .admin-nav-item.active {
            background: var(--sidebar-hover);
            color: white;
            border-left-color: var(--admin-primary);
        }
        
        .admin-nav-item i {
            width: 24px;
            margin-right: 12px;
            font-size: 1.1rem;
        }
        
        /* Topbar */
        .admin-topbar {
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
        
        .admin-topbar-left h1 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            color: #1e293b;
        }
        
        .admin-topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .admin-user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 15px;
            background: #f8fafc;
            border-radius: 50px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .admin-user-info:hover {
            background: #e2e8f0;
        }
        
        .admin-user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--admin-primary), var(--admin-secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        .admin-user-name {
            font-weight: 600;
            color: #1e293b;
        }
        
        /* Main Content */
        .admin-content {
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
        
        .stat-card-icon.primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .stat-card-icon.success {
            background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%);
            color: white;
        }
        
        .stat-card-icon.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        
        .stat-card-icon.info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
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
        .admin-table-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-top: 25px;
        }
        
        .admin-table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .admin-table-header h2 {
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
        .btn-admin-primary {
            background: linear-gradient(135deg, var(--admin-primary), var(--admin-secondary));
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .btn-admin-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
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
        .admin-alert {
            border-radius: 10px;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }
            
            .admin-sidebar.show {
                transform: translateX(0);
            }
            
            .admin-topbar {
                left: 0;
            }
            
            .admin-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="admin-sidebar">
        <div class="admin-sidebar-header">
            <h3><i class="bi bi-shield-check"></i> BiBilet</h3>
            <p>Admin Panel</p>
        </div>
        
        <div class="admin-nav">
            <div class="admin-nav-section">MENÜ</div>
            <a href="/admin/dashboard" class="admin-nav-item <?= strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') !== false ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
            
            <div class="admin-nav-section">YÖNETİM</div>
            <a href="/admin/companies" class="admin-nav-item <?= strpos($_SERVER['REQUEST_URI'], '/admin/companies') !== false ? 'active' : '' ?>">
                <i class="bi bi-building"></i>
                <span>Firmalar</span>
            </a>
            <a href="/admin/users" class="admin-nav-item <?= strpos($_SERVER['REQUEST_URI'], '/admin/users') !== false ? 'active' : '' ?>">
                <i class="bi bi-people"></i>
                <span>Kullanıcılar</span>
            </a>
            <a href="/admin/coupons" class="admin-nav-item <?= strpos($_SERVER['REQUEST_URI'], '/admin/coupons') !== false ? 'active' : '' ?>">
                <i class="bi bi-ticket-perforated"></i>
                <span>Kuponlar</span>
            </a>
            
            <div class="admin-nav-section">HESAP</div>
            <a href="/admin/profile" class="admin-nav-item <?= strpos($_SERVER['REQUEST_URI'], '/admin/profile') !== false ? 'active' : '' ?>">
                <i class="bi bi-person-circle"></i>
                <span>Profilim</span>
            </a>
            
            <div class="admin-nav-section">SİSTEM</div>
            <a href="/" class="admin-nav-item" target="_blank">
                <i class="bi bi-globe"></i>
                <span>Siteyi Görüntüle</span>
            </a>
            <a href="/logout" class="admin-nav-item">
                <i class="bi bi-box-arrow-right"></i>
                <span>Çıkış Yap</span>
            </a>
        </div>
    </div>
    
    <!-- Topbar -->
    <div class="admin-topbar">
        <div class="admin-topbar-left">
            <h1><?= $pageTitle ?? 'Dashboard' ?></h1>
        </div>
        <div class="admin-topbar-right">
            <div class="admin-user-info">
                <div class="admin-user-avatar">
                    <?= strtoupper(substr($_SESSION['user_email'] ?? 'A', 0, 1)) ?>
                </div>
                <div>
                    <div class="admin-user-name"><?= htmlspecialchars($_SESSION['user_email'] ?? 'Admin') ?></div>
                    <small class="text-muted">Administrator</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="admin-content">
        <!-- Flash Messages -->
        <?php if (isset($_SESSION['flash_messages']) && !empty($_SESSION['flash_messages'])): ?>
            <?php foreach ($_SESSION['flash_messages'] as $type => $message): ?>
                <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> admin-alert alert-dismissible fade show" role="alert">
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
