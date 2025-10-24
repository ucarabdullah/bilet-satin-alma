<?php
$pageTitle = "Şifre Sıfırlama - BiBilet";
$pageDescription = "BiBilet hesap şifrenizi yenileyin";
require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center py-3">
                <h4 class="mb-0"><i class="fas fa-lock-open me-2"></i>Şifre Yenileme</h4>
            </div>
            <div class="card-body p-4">
                <p class="mb-4">
                    Lütfen hesabınız için yeni bir şifre belirleyin.
                </p>
                
                <form action="<?php echo BASE_PATH; ?>/reset-password" method="POST" class="needs-validation" novalidate>
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                    
                    <!-- Token Input -->
                    <input type="hidden" name="token" value="<?php echo $token; ?>">
                    
                    <!-- Password Input -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Yeni Şifre</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="En az 8 karakter, büyük/küçük harf ve rakam içeren bir şifre" 
                                   required>
                            <button type="button" class="btn btn-outline-secondary toggle-password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Şifreniz en az 8 karakter uzunluğunda olmalı ve en az bir büyük harf, bir küçük harf ve bir rakam içermelidir.
                        </div>
                        <div class="invalid-feedback">
                            Lütfen güvenli bir şifre giriniz.
                        </div>
                    </div>
                    
                    <!-- Confirm Password Input -->
                    <div class="mb-4">
                        <label for="confirm_password" class="form-label">Şifre (Tekrar)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                   placeholder="Şifrenizi tekrar giriniz" required>
                            <button type="button" class="btn btn-outline-secondary toggle-confirm-password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback">
                            Lütfen şifrenizi tekrar giriniz.
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="d-grid gap-2 mb-3">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Şifremi Güncelle
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Güvenlik Bilgisi -->
        <div class="mt-3 text-center">
            <small class="text-muted">
                <i class="fas fa-shield-alt me-1"></i>
                Şifrenizi kimseyle paylaşmayınız.
            </small>
        </div>
    </div>
</div>

<!-- Form validation and password toggle JavaScript -->
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
                
                // Check if passwords match
                const password = document.querySelector("#password");
                const confirmPassword = document.querySelector("#confirm_password");
                
                if (password.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity("Şifreler eşleşmiyor");
                    event.preventDefault();
                    event.stopPropagation();
                } else {
                    confirmPassword.setCustomValidity("");
                }
                
                form.classList.add("was-validated");
            }, false);
        });
        
        // Password toggle functions
        const setupPasswordToggle = (toggleSelector, passwordSelector) => {
            const toggle = document.querySelector(toggleSelector);
            const passwordField = document.querySelector(passwordSelector);
            
            if (toggle) {
                toggle.addEventListener("click", function() {
                    const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
                    passwordField.setAttribute("type", type);
                    
                    // Toggle eye icon
                    this.querySelector("i").classList.toggle("fa-eye");
                    this.querySelector("i").classList.toggle("fa-eye-slash");
                });
            }
        };
        
        // Setup both password fields
        setupPasswordToggle(".toggle-password", "#password");
        setupPasswordToggle(".toggle-confirm-password", "#confirm_password");
        
        // Password strength meter
        const passwordField = document.querySelector("#password");
        passwordField.addEventListener("input", function() {
            let strength = 0;
            const password = this.value;
            
            // Length check
            if (password.length >= 8) strength += 1;
            
            // Uppercase check
            if (password.match(/[A-Z]/)) strength += 1;
            
            // Lowercase check
            if (password.match(/[a-z]/)) strength += 1;
            
            // Number check
            if (password.match(/[0-9]/)) strength += 1;
            
            // Special character check
            if (password.match(/[^a-zA-Z0-9]/)) strength += 1;
            
            // Display strength
            let feedback = "";
            let color = "";
            
            switch (strength) {
                case 0:
                case 1:
                    feedback = "Çok zayıf";
                    color = "danger";
                    break;
                case 2:
                    feedback = "Zayıf";
                    color = "warning";
                    break;
                case 3:
                    feedback = "Orta";
                    color = "info";
                    break;
                case 4:
                    feedback = "Güçlü";
                    color = "primary";
                    break;
                case 5:
                    feedback = "Çok güçlü";
                    color = "success";
                    break;
            }
            
            // Add or update strength meter
            let meterExists = document.querySelector(".password-strength-meter");
            if (!meterExists && password.length > 0) {
                const meterHtml = `
                    <div class="password-strength-meter mt-1">
                        <div class="progress">
                            <div class="progress-bar bg-${color}" style="width: ${(strength/5) * 100}%"></div>
                        </div>
                        <small class="text-${color} mt-1 d-block">${feedback}</small>
                    </div>
                `;
                
                this.insertAdjacentHTML("afterend", meterHtml);
            } else if (meterExists && password.length > 0) {
                const progressBar = meterExists.querySelector(".progress-bar");
                const feedbackText = meterExists.querySelector("small");
                
                progressBar.className = `progress-bar bg-${color}`;
                progressBar.style.width = `${(strength/5) * 100}%`;
                feedbackText.className = `text-${color} mt-1 d-block`;
                feedbackText.textContent = feedback;
                
                meterExists.style.display = "block";
            } else if (meterExists) {
                meterExists.style.display = "none";
            }
        });
    })();
</script>
'; ?>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>