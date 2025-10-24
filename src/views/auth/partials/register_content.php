<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center py-3">
                <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i>Yeni Hesap Oluştur</h4>
            </div>
            <div class="card-body p-4">
                <?php if (!empty($formErrors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($formErrors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?php echo BASE_PATH; ?>/register" method="POST" class="needs-validation" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">

                    <div class="mb-3">
                        <label for="full_name" class="form-label">Ad Soyad</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="full_name" name="full_name"
                                   value="<?php echo $formData['full_name'] ?? ''; ?>"
                                   placeholder="Adınızı ve soyadınızı giriniz" required>
                        </div>
                        <div class="invalid-feedback">Lütfen adınızı ve soyadınızı giriniz.</div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Adresi</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?php echo $formData['email'] ?? ''; ?>"
                                   placeholder="Email adresinizi giriniz" required>
                        </div>
                        <div class="invalid-feedback">Lütfen geçerli bir email adresi giriniz.</div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Şifre</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password"
                                   placeholder="En az 8 karakter, büyük/küçük harf ve rakam içeren bir şifre" required>
                        </div>
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Şifreniz en az 8 karakter uzunluğunda olmalı ve en az bir büyük harf, bir küçük harf ve bir rakam içermelidir.
                        </div>
                        <div class="invalid-feedback">Lütfen güvenli bir şifre giriniz.</div>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Şifre (Tekrar)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                   placeholder="Şifrenizi tekrar giriniz" required>
                        </div>
                        <div class="invalid-feedback">Lütfen şifrenizi tekrar giriniz.</div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                        <label class="form-check-label" for="terms">
                            <a href="<?php echo BASE_PATH; ?>/terms" target="_blank">Kullanım Şartları</a>'nı ve
                            <a href="<?php echo BASE_PATH; ?>/privacy" target="_blank">Gizlilik Politikası</a>'nı okudum ve kabul ediyorum.
                        </label>
                        <div class="invalid-feedback">Devam etmek için kullanım şartlarını kabul etmelisiniz.</div>
                    </div>

                    <div class="d-grid gap-2 mb-3">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-user-plus me-2"></i>Kayıt Ol
                        </button>
                    </div>

                    <div class="text-center">
                        Zaten hesabınız var mı?
                        <a href="<?php echo BASE_PATH; ?>/login" class="text-decoration-none fw-bold">Giriş Yap</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-3 text-center">
            <small class="text-muted">
                <i class="fas fa-shield-alt me-1"></i>Kişisel bilgileriniz SSL ile korunmaktadır.
            </small>
        </div>
    </div>
</div>