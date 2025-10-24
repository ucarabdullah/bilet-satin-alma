<?php
$pageTitle = "Şifremi Unuttum - BiBilet";
$pageDescription = "BiBilet hesap şifrenizi sıfırlayın";
require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center py-3">
                <h4 class="mb-0"><i class="fas fa-key me-2"></i>Şifremi Unuttum</h4>
            </div>
            <div class="card-body p-4">
                <p class="mb-4">
                    Hesabınıza bağlı email adresinizi girin. Size şifre sıfırlama bağlantısı içeren bir email göndereceğiz.
                </p>
                
                <form action="<?php echo BASE_PATH; ?>/forgot-password" method="POST" class="needs-validation" novalidate>
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                    
                    <!-- Email Input -->
                    <div class="mb-4">
                        <label for="email" class="form-label">Email Adresi</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="Email adresinizi giriniz" required>
                        </div>
                        <div class="invalid-feedback">
                            Lütfen geçerli bir email adresi giriniz.
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="d-grid gap-2 mb-3">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-paper-plane me-2"></i>Şifre Sıfırlama Linki Gönder
                        </button>
                    </div>
                    
                    <!-- Back to Login -->
                    <div class="text-center">
                        <a href="<?php echo BASE_PATH; ?>/login" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i>Giriş Sayfasına Dön
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Güvenlik Bilgisi -->
        <div class="mt-3 text-center">
            <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                Şifre sıfırlama bağlantısı 60 dakika geçerlidir.
            </small>
        </div>
    </div>
</div>

<!-- Form validation JavaScript -->
<?php $extraScripts = '
<script>
    // Form validation
    (function() {
        "use strict";
        
        const forms = document.querySelectorAll(".needs-validation");
        
        Array.from(forms).forEach(function (form) {
            form.addEventListener("submit", function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                
                form.classList.add("was-validated");
            }, false);
        });
    })();
</script>
'; ?>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>