<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-lg border-0" style="border-top: 4px solid var(--brand-secondary) !important;">
            <div class="card-header text-center py-4" style="background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary)); border: none;">
                <h4 class="mb-0 text-white">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Giriş Yap
                </h4>
            </div>
            <div class="card-body p-4">
                <form action="<?php echo BASE_PATH; ?>/login" method="POST" class="needs-validation" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Adresi</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?php echo $_SESSION['form_data']['email'] ?? ''; ?>"
                                   placeholder="Email adresinizi giriniz" required>
                        </div>
                        <div class="invalid-feedback">Lütfen geçerli bir email adresi giriniz.</div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Şifre</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Şifrenizi giriniz" required>
                        </div>
                        <div class="invalid-feedback">Lütfen şifrenizi giriniz.</div>
                    </div>
                    <div class="d-grid gap-2 mb-3">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>Giriş Yap
                        </button>
                    </div>
                    <div class="text-center">
                        <a href="<?php echo BASE_PATH; ?>/forgot-password" class="text-decoration-none">Şifremi Unuttum</a>
                        <div class="mt-3">Henüz hesabınız yok mu?
                            <a href="<?php echo BASE_PATH; ?>/register" class="text-decoration-none fw-bold">Kayıt Ol</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="mt-3 text-center">
            <small class="text-muted">
                <i class="fas fa-lock me-1"></i>Tüm bilgileriniz SSL ile şifrelenmektedir.
            </small>
        </div>
    </div>
</div>