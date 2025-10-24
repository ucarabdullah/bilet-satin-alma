<?php
$pageTitle = "Profil Ayarları - Firma Admin Panel";
$pageDescription = "Profil bilgilerinizi düzenleyin";

Security::checkRole(ROLE_COMPANY_ADMIN);

// Kullanıcı bilgilerini al
$userModel = new User();
$user = $userModel->findById(Security::getUserId());

if (!$user) {
    Security::redirect(BASE_PATH . '/company/dashboard');
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/assets/css/custom.css">
</head>
<body>
    <?php require_once VIEWS_PATH . '/layouts/company_header.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <?php require_once VIEWS_PATH . '/layouts/company_sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><i class="bi bi-person me-2"></i>Profil Ayarları</h1>
                </div>

                <?php if ($message = Security::getFlashMessage()): ?>
                    <div class="alert alert-<?php echo $message['type']; ?> alert-dismissible fade show" role="alert">
                        <?php echo $message['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Profil Bilgileri</h5>
                            </div>
                            <div class="card-body">
                                <form action="<?php echo BASE_PATH; ?>/company/profile/update" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                                    
                                    <div class="mb-3">
                                        <label for="full_name" class="form-label">Ad Soyad</label>
                                        <input type="text" class="form-control" id="full_name" name="full_name" 
                                               value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Rol</label>
                                        <input type="text" class="form-control" value="Firma Yöneticisi" disabled>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Kayıt Tarihi</label>
                                        <input type="text" class="form-control" 
                                               value="<?php echo date('d.m.Y H:i', strtotime($user['created_at'])); ?>" disabled>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>Bilgileri Güncelle
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="card shadow-sm mt-4">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="bi bi-key me-2"></i>Şifre Değiştir</h5>
                            </div>
                            <div class="card-body">
                                <form action="<?php echo BASE_PATH; ?>/company/profile/change-password" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                                    
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Mevcut Şifre</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">Yeni Şifre</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" 
                                               pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required>
                                        <small class="text-muted">En az 8 karakter, 1 büyük harf, 1 küçük harf ve 1 rakam</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Yeni Şifre (Tekrar)</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    </div>

                                    <button type="submit" class="btn btn-warning">
                                        <i class="bi bi-shield-lock me-2"></i>Şifreyi Değiştir
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Bilgi</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Güvenlik İpuçları:</strong></p>
                                <ul class="small">
                                    <li>Güçlü bir şifre kullanın</li>
                                    <li>Şifrenizi kimseyle paylaşmayın</li>
                                    <li>Düzenli olarak şifrenizi değiştirin</li>
                                    <li>Email adresinizi güncel tutun</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
