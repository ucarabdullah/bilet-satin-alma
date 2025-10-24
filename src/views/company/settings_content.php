<?php
// Firma bilgilerini al
$companyModel = new Company();
$company = $companyModel->findById(Security::getCompanyId());

// Firma bulunamazsa (bu durumda session'da company_id yok demektir)
if (!$company) {
    // Hata mesajını ayarla ve çıkış yap (layout içinde olduğumuz için redirect yapamayız)
    echo '<div class="alert alert-danger">Firma bilgilerinize erişilemiyor. Lütfen tekrar giriş yapın.</div>';
    return;
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-building me-2"></i>Firma Ayarları</h1>
</div>

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
                        <label for="logo" class="form-label">Firma Logosu</label>
                        <?php if (!empty($company['logo_path'])): ?>
                            <div class="mb-2">
                                <img src="<?php echo BASE_PATH . '/uploads/logos/' . htmlspecialchars($company['logo_path']); ?>" 
                                     alt="Mevcut Logo" style="max-height: 100px;" class="img-thumbnail">
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                        <small class="text-muted">Maksimum dosya boyutu: 2MB. İzin verilen formatlar: JPG, PNG, GIF</small>
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
