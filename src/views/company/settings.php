<?php
$pageTitle = "Firma Ayarları - Firma Admin Panel";
$pageDescription = "Firma bilgilerinizi düzenleyin";

Security::checkRole(ROLE_COMPANY_ADMIN);

// Firma bilgilerini al
$companyModel = new Company();
$company = $companyModel->findById(Security::getCompanyId());

if (!$company) {
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
                    <h1 class="h2"><i class="bi bi-building me-2"></i>Firma Ayarları</h1>
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
                                <h5 class="mb-0"><i class="bi bi-building-gear me-2"></i>Firma Bilgileri</h5>
                            </div>
                            <div class="card-body">
                                <form action="<?php echo BASE_PATH; ?>/company/settings/update" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                                    
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Firma Adı</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="<?php echo htmlspecialchars($company['name']); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Açıklama</label>
                                        <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($company['description'] ?? ''); ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Telefon</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" 
                                               value="<?php echo htmlspecialchars($company['phone'] ?? ''); ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?php echo htmlspecialchars($company['email'] ?? ''); ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label for="address" class="form-label">Adres</label>
                                        <textarea class="form-control" id="address" name="address" rows="2"><?php echo htmlspecialchars($company['address'] ?? ''); ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="logo" class="form-label">Firma Logosu</label>
                                        <?php if (!empty($company['logo'])): ?>
                                            <div class="mb-2">
                                                <img src="<?php echo BASE_PATH . '/uploads/logos/' . htmlspecialchars($company['logo']); ?>" 
                                                     alt="Mevcut Logo" style="max-height: 100px;" class="img-thumbnail">
                                            </div>
                                        <?php endif; ?>
                                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                        <small class="text-muted">Maksimum dosya boyutu: 2MB. İzin verilen formatlar: JPG, PNG, GIF</small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Durum</label>
                                        <input type="text" class="form-control" 
                                               value="<?php echo $company['is_active'] ? 'Aktif' : 'Pasif'; ?>" disabled>
                                        <small class="text-muted">Firma durumunu sadece sistem yöneticisi değiştirebilir.</small>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>Bilgileri Güncelle
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
                                <p><strong>Firma ID:</strong> <?php echo htmlspecialchars($company['id']); ?></p>
                                <p><strong>Kayıt Tarihi:</strong> <?php echo date('d.m.Y', strtotime($company['created_at'])); ?></p>
                                <hr>
                                <p class="small text-muted mb-0">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    Logo değişiklikleri anında yayına alınır. Lütfen uygun görsel kullanın.
                                </p>
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
