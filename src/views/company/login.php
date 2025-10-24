<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Firma Girişi - BiBilet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #7e22ce 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .company-login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }
        .company-login-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        .company-login-header i {
            font-size: 4rem;
            margin-bottom: 15px;
            opacity: 0.9;
        }
        .company-login-header h2 {
            margin: 0;
            font-weight: 600;
            font-size: 1.75rem;
        }
        .company-login-header p {
            margin: 5px 0 0;
            opacity: 0.9;
            font-size: 0.95rem;
        }
        .company-login-body {
            padding: 40px 35px;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #2a5298;
            box-shadow: 0 0 0 0.2rem rgba(42, 82, 152, 0.15);
        }
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        .btn-company-login {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-weight: 600;
            font-size: 1rem;
            color: white;
            transition: transform 0.2s, box-shadow 0.2s;
            width: 100%;
        }
        .btn-company-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(42, 82, 152, 0.3);
            color: white;
        }
        .btn-company-login:active {
            transform: translateY(0);
        }
        .back-to-site {
            text-align: center;
            margin-top: 20px;
        }
        .back-to-site a {
            color: #2a5298;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        .back-to-site a:hover {
            color: #1e3c72;
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .company-badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 8px 15px;
            border-radius: 20px;
            margin-top: 10px;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="company-login-card">
        <div class="company-login-header">
            <i class="bi bi-building"></i>
            <h2>Firma Paneli</h2>
            <p>Firma Yöneticisi Girişi</p>
            <div class="company-badge">
                <i class="bi bi-bus-front me-2"></i>Sefer & Kupon Yönetimi
            </div>
        </div>
        <div class="company-login-body">
            <?php if (isset($_SESSION['flash_messages']['error'])): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <?= htmlspecialchars($_SESSION['flash_messages']['error']) ?>
                </div>
                <?php unset($_SESSION['flash_messages']['error']); ?>
            <?php endif; ?>
            
            <form method="post" action="/company/login">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                
                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="bi bi-envelope me-2"></i>E-posta
                    </label>
                    <input type="email" class="form-control" id="email" name="email" 
                           placeholder="firma@ornek.com" required autofocus>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock me-2"></i>Şifre
                    </label>
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="••••••••" required>
                </div>
                
                <button type="submit" class="btn btn-company-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Firma Paneline Giriş Yap
                </button>
            </form>
            
            <div class="back-to-site">
                <a href="/">
                    <i class="bi bi-arrow-left me-1"></i>Ana Sayfaya Dön
                </a>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
