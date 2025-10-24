<?php
/**
 * BiBilet - Auth Controller
 * Kullanıcı kimlik doğrulama işlemleri
 */

// Gerekli model dosyalarını yükle
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $user;
    
    public function __construct() {
        $this->user = new User();
    }
    
    /**
     * Login sayfasını göster
     */
    public function showLoginForm() {
        // Zaten giriş yapmış kullanıcıları ana sayfaya yönlendir
        if (Security::isLoggedIn()) {
            Security::redirect(BASE_PATH . '/');
            exit();
        }
        
        require_once VIEWS_PATH . '/auth/login.php';
    }
    
    /**
     * Register sayfasını göster
     */
    public function showRegisterForm() {
        // Zaten giriş yapmış kullanıcıları ana sayfaya yönlendir
        if (Security::isLoggedIn()) {
            Security::redirect(BASE_PATH . '/');
            exit();
        }
        
        require_once VIEWS_PATH . '/auth/register.php';
    }
    
    /**
     * Login işlemini gerçekleştir
     */
    public function login() {
        // CSRF kontrolü
        if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
            Security::setFlashMessage('error', 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.');
            Security::redirect(BASE_PATH . '/login');
            exit();
        }
        
        // Form verilerini al ve temizle
        $email = Security::sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? ''; // Şifreyi sanitize etmiyoruz
        
        // Veri doğrulama
        if (empty($email) || empty($password)) {
            Security::setFlashMessage('error', 'Lütfen email ve şifrenizi giriniz.');
            Security::redirect(BASE_PATH . '/login');
            exit();
        }
        
        // Brute force koruması
        if (!Security::checkLoginAttempts($email)) {
            Security::setFlashMessage('error', 'Çok fazla başarısız giriş denemesi. Lütfen daha sonra tekrar deneyin.');
            Security::redirect(BASE_PATH . '/login');
            exit();
        }
        
        // Login kontrolü
        $user = $this->user->login($email, $password);
        
        if (!$user) {
            Security::recordFailedLogin($email);
            Security::setFlashMessage('error', 'Email veya şifre hatalı.');
            Security::redirect(BASE_PATH . '/login');
            exit();
        }
        
        // Normal kullanıcı girişinde firma admin hesabı ile giriş engellenir
        // Firma admin girişi sadece /company/login üzerinden yapılmalıdır
        if ($user['role'] === ROLE_COMPANY_ADMIN) {
            Security::setFlashMessage('error', 'Firma hesabı ile giriş için Firma Girişi sayfasını kullanınız.');
            Security::redirect(BASE_PATH . '/company/login');
            exit();
        }
        
        // Başarılı giriş - session oluştur
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['company_id'] = $user['company_id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['last_login'] = time();
        
        // Login attempt sıfırla
        Security::resetLoginAttempts($email);
        
        // Eğer önceki sayfa kaydedilmişse oraya yönlendir
        if (isset($_SESSION['redirect_after_login'])) {
            $redirectUrl = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']);
            Security::redirect($redirectUrl);
            exit;
        }
        
        // Kullanıcı rolüne göre yönlendirme
        switch ($user['role']) {
            case ROLE_ADMIN:
                Security::redirect(BASE_PATH . '/admin/dashboard');
                break;
            default:
                // Normal kullanıcılar için
                Security::redirect(BASE_PATH . '/user/dashboard');
                break;
        }
    }
    
    /**
     * Register işlemini gerçekleştir
     */
    public function register() {
        // CSRF kontrolü
        if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
            Security::setFlashMessage('error', 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.');
            Security::redirect(BASE_PATH . '/register');
            exit();
        }
        
        // Form verilerini al ve temizle
        $fullName = Security::sanitizeInput($_POST['full_name'] ?? '');
        $email = Security::sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? ''; // Şifreyi sanitize etmiyoruz
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Veri doğrulama
        $errors = [];
        
        if (empty($fullName)) {
            $errors[] = "Ad Soyad alanı gereklidir.";
        }
        
        if (empty($email)) {
            $errors[] = "Email alanı gereklidir.";
        } elseif (!Security::validateEmail($email)) {
            $errors[] = "Geçerli bir email adresi giriniz.";
        }
        
        if (empty($password)) {
            $errors[] = "Şifre alanı gereklidir.";
        } elseif (!Security::validatePassword($password)) {
            $errors[] = "Şifre en az 8 karakter uzunluğunda olmalı ve en az bir büyük harf, bir küçük harf ve bir rakam içermelidir.";
        }
        
        if ($password !== $confirmPassword) {
            $errors[] = "Şifreler eşleşmiyor.";
        }
        
        // Hata varsa göster
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_data'] = ['full_name' => $fullName, 'email' => $email];
            Security::redirect(BASE_PATH . '/register');
            exit();
        }
        
        // Email kontrolü - kayıtlı mı?
        if ($this->user->findByEmail($email)) {
            Security::setFlashMessage('error', 'Bu email adresi zaten kayıtlı.');
            $_SESSION['form_data'] = ['full_name' => $fullName];
            Security::redirect(BASE_PATH . '/register');
            exit();
        }
        
        // Yeni kullanıcı oluştur
        $userId = $this->user->create($fullName, $email, $password);
        
        if (!$userId) {
            Security::setFlashMessage('error', 'Kayıt işlemi başarısız oldu. Lütfen tekrar deneyin.');
            $_SESSION['form_data'] = ['full_name' => $fullName, 'email' => $email];
            Security::redirect(BASE_PATH . '/register');
            exit();
        }
        
        // Başarılı kayıt mesajı
        Security::setFlashMessage('success', 'Kayıt işleminiz başarıyla tamamlandı. Şimdi giriş yapabilirsiniz.');
        Security::redirect(BASE_PATH . '/login');
    }
    
    /**
     * Çıkış yap
     */
    public function logout() {
        // Remember me token'ı sil
        if (isset($_COOKIE['remember_token'])) {
            $this->removeRememberMeToken($_COOKIE['remember_token']);
            setcookie('remember_token', '', time() - 3600, '/', '', true, true);
        }
        
        Security::destroySession();
        Security::setFlashMessage('success', 'Başarıyla çıkış yaptınız.');
        Security::redirect(BASE_PATH . '/login');
    }
    
    /**
     * Şifremi unuttum sayfasını göster
     */
    public function showForgotPasswordForm() {
        require_once VIEWS_PATH . '/auth/forgot_password.php';
    }
    
    /**
     * Şifre sıfırlama talebi gönder
     */
    public function forgotPassword() {
        // CSRF kontrolü
        if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
            Security::setFlashMessage('error', 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.');
            Security::redirect(BASE_PATH . '/forgot-password');
            exit();
        }
        
        $email = Security::sanitizeInput($_POST['email'] ?? '');
        
        if (empty($email) || !Security::validateEmail($email)) {
            Security::setFlashMessage('error', 'Lütfen geçerli bir email adresi giriniz.');
            Security::redirect(BASE_PATH . '/forgot-password');
            exit();
        }
        
        // Kullanıcı kontrolü
        $user = $this->user->findByEmail($email);
        
        if (!$user) {
            // Kullanıcı bulunamasa bile güvenlik için aynı mesajı göster
            Security::setFlashMessage('success', 'Şifre sıfırlama bağlantısı email adresinize gönderildi. Lütfen email kutunuzu kontrol ediniz.');
            Security::redirect(BASE_PATH . '/login');
            exit();
        }
        
        // Şifre sıfırlama token'ı oluştur
        $token = Security::generateUUID();
        $expiry = time() + 3600; // 1 saat
        
        // Token'ı veritabanına kaydet
        $this->saveResetToken($user['id'], $token, $expiry);
        
        // Email gönderme (gerçek uygulamada email gönderilecek)
        // Burada email gönderme kodu olacak
        
        // Başarılı mesaj göster
        Security::setFlashMessage('success', 'Şifre sıfırlama bağlantısı email adresinize gönderildi. Lütfen email kutunuzu kontrol ediniz.');
        Security::redirect(BASE_PATH . '/login');
    }
    
    /**
     * Şifre sıfırlama sayfasını göster
     */
    public function showResetPasswordForm($token) {
        // Token geçerliliği kontrolü
        if (!$this->validateResetToken($token)) {
            Security::setFlashMessage('error', 'Geçersiz veya süresi dolmuş şifre sıfırlama bağlantısı.');
            Security::redirect(BASE_PATH . '/login');
            exit();
        }
        
        require_once VIEWS_PATH . '/auth/reset_password.php';
    }
    
    /**
     * Şifre sıfırlama işlemi
     */
    public function resetPassword() {
        // CSRF kontrolü
        if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
            Security::setFlashMessage('error', 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.');
            Security::redirect(BASE_PATH . '/login');
            exit();
        }
        
        $token = Security::sanitizeInput($_POST['token'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Token geçerliliği kontrolü
        if (!$this->validateResetToken($token)) {
            Security::setFlashMessage('error', 'Geçersiz veya süresi dolmuş şifre sıfırlama bağlantısı.');
            Security::redirect(BASE_PATH . '/login');
            exit();
        }
        
        // Şifre doğrulama
        if (empty($password)) {
            Security::setFlashMessage('error', 'Lütfen yeni şifrenizi giriniz.');
            Security::redirect(BASE_PATH . '/reset-password/' . $token);
            exit();
        }
        
        if (!Security::validatePassword($password)) {
            Security::setFlashMessage('error', 'Şifre en az 8 karakter uzunluğunda olmalı ve en az bir büyük harf, bir küçük harf ve bir rakam içermelidir.');
            Security::redirect(BASE_PATH . '/reset-password/' . $token);
            exit();
        }
        
        if ($password !== $confirmPassword) {
            Security::setFlashMessage('error', 'Şifreler eşleşmiyor.');
            Security::redirect(BASE_PATH . '/reset-password/' . $token);
            exit();
        }
        
        // Token'dan kullanıcıyı bul
        $userId = $this->getUserIdFromToken($token);
        
        if (!$userId) {
            Security::setFlashMessage('error', 'Geçersiz token.');
            Security::redirect(BASE_PATH . '/login');
            exit();
        }
        
        // Şifreyi güncelle
        $this->user->update($userId, ['password' => $password]);
        
        // Token'ı sil
        $this->removeResetToken($token);
        
        // Başarılı mesaj göster
        Security::setFlashMessage('success', 'Şifreniz başarıyla güncellendi. Şimdi yeni şifrenizle giriş yapabilirsiniz.');
        Security::redirect(BASE_PATH . '/login');
    }
    
    /**
     * Remember me token'ını veritabanına kaydet
     * @param string $userId
     * @param string $token
     * @param int $expiry
     * @return bool
     */
    private function saveRememberMeToken($userId, $token, $expiry) {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO RememberToken (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)");
        return $stmt->execute([
            ':user_id' => $userId,
            ':token' => $token,
            ':expires_at' => date('Y-m-d H:i:s', $expiry)
        ]);
    }
    
    /**
     * Remember me token'ını sil
     * @param string $token
     * @return bool
     */
    private function removeRememberMeToken($token) {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM RememberToken WHERE token = :token");
        return $stmt->execute([':token' => $token]);
    }
    
    /**
     * Remember me token'ı ile kullanıcıyı bul
     * @param string $token
     * @return array|false
     */
    public function getUserByRememberToken($token) {
        $db = Database::getInstance();
        $sql = "SELECT u.* FROM User u 
                JOIN RememberToken rt ON u.id = rt.user_id 
                WHERE rt.token = :token AND rt.expires_at > CURRENT_TIMESTAMP";
        
        $stmt = $db->query($sql, [':token' => $token]);
        
        if ($stmt) {
            return $stmt->fetch();
        }
        
        return false;
    }
    
    /**
     * Şifre sıfırlama token'ını kaydet
     * @param string $userId
     * @param string $token
     * @param int $expiry
     * @return bool
     */
    private function saveResetToken($userId, $token, $expiry) {
        $db = Database::getInstance();
        $sql = "INSERT INTO PasswordReset (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)";
        $params = [
            ':user_id' => $userId,
            ':token' => $token,
            ':expires_at' => date('Y-m-d H:i:s', $expiry)
        ];
        
        return $db->execute($sql, $params);
    }
    
    /**
     * Şifre sıfırlama token'ı geçerliliğini kontrol et
     * @param string $token
     * @return bool
     */
    private function validateResetToken($token) {
        $db = Database::getInstance();
        $sql = "SELECT COUNT(*) as count FROM PasswordReset WHERE token = :token AND expires_at > CURRENT_TIMESTAMP";
        $stmt = $db->query($sql, [':token' => $token]);
        
        if ($stmt) {
            $result = $stmt->fetch();
            return (int) $result['count'] > 0;
        }
        
        return false;
    }
    
    /**
     * Şifre sıfırlama token'ından user ID'yi al
     * @param string $token
     * @return string|false
     */
    private function getUserIdFromToken($token) {
        $db = Database::getInstance();
        $sql = "SELECT user_id FROM PasswordReset WHERE token = :token AND expires_at > CURRENT_TIMESTAMP";
        $stmt = $db->query($sql, [':token' => $token]);
        
        if ($stmt) {
            $result = $stmt->fetch();
            return $result ? $result['user_id'] : false;
        }
        
        return false;
    }
    
    /**
     * Şifre sıfırlama token'ını sil
     * @param string $token
     * @return bool
     */
    private function removeResetToken($token) {
        $db = Database::getInstance();
        $sql = "DELETE FROM PasswordReset WHERE token = :token";
        return $db->execute($sql, [':token' => $token]);
    }
}