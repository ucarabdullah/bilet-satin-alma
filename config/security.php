<?php
/**
 * BiBilet - Güvenlik Sınıfı
 * Tüm güvenlik fonksiyonları burada tanımlanır
 */

class Security {
    
    /**
     * Güvenli session başlatma ve kontrol
     * @return bool
     */
    public static function startSecureSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // İlk session başlatma
        if (!isset($_SESSION['initiated'])) {
            session_regenerate_id(true);
            $_SESSION['initiated'] = true;
            $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        }
        
        // Session hijacking koruması - IP kontrolü
        if (isset($_SESSION['ip']) && $_SESSION['ip'] !== $_SERVER['REMOTE_ADDR']) {
            self::destroySession();
            return false;
        }
        
        // Session hijacking koruması - User agent kontrolü
        if (isset($_SESSION['user_agent']) && $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
            self::destroySession();
            return false;
        }
        
        // Session timeout kontrolü (30 dakika)
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
            self::destroySession();
            return false;
        }
        
        $_SESSION['last_activity'] = time();
        return true;
    }
    
    /**
     * Session'ı güvenli bir şekilde yok etme
     */
    public static function destroySession() {
        $_SESSION = [];
        
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        session_destroy();
        session_start();
        session_regenerate_id(true);
    }
    
    /**
     * CSRF token üretme
     * @return string
     */
    public static function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * CSRF token doğrulama
     * @param string $token
     * @return bool
     */
    public static function validateCSRFToken($token) {
        if (!isset($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Input sanitization - XSS koruması
     * @param mixed $data
     * @return mixed
     */
    public static function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitizeInput'], $data);
        }
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Kullanıcı giriş kontrolü
     * @return bool
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Kullanıcı rolünü döndür
     * @return string|null
     */
    public static function getUserRole() {
        return $_SESSION['user_role'] ?? null;
    }
    
    /**
     * Kullanıcı ID'sini döndür
     * @return string|null
     */
    public static function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Firma ID'sini döndür (Firma Admin için)
     * @return string|null
     */
    public static function getCompanyId() {
        return $_SESSION['company_id'] ?? null;
    }
    
    /**
     * Rol kontrolü ve yetkilendirme
     * @param string $requiredRole
     * @return bool
     */
    public static function checkRole($requiredRole) {
        if (!self::isLoggedIn()) {
            header('Location: ' . BASE_PATH . '/login');
            exit();
        }
        
        $userRole = self::getUserRole();
        
        // Admin her yere erişebilir
        if ($userRole === ROLE_ADMIN) {
            return true;
        }
        
        // Firma Admin kendi yetkilerine erişebilir
        if ($userRole === ROLE_COMPANY_ADMIN && in_array($requiredRole, [ROLE_COMPANY_ADMIN, ROLE_USER])) {
            return true;
        }
        
        // User sadece user sayfalarına erişebilir
        if ($userRole === ROLE_USER && $requiredRole === ROLE_USER) {
            return true;
        }
        
        // Yetkisiz erişim
        http_response_code(403);
        die('Bu sayfaya erişim yetkiniz yok!');
    }
    
    /**
     * Firma sahipliği kontrolü (Firma Admin için)
     * @param string $companyId
     * @return bool
     */
    public static function checkCompanyOwnership($companyId) {
        $userRole = self::getUserRole();
        
        // Admin her firmayı yönetebilir
        if ($userRole === ROLE_ADMIN) {
            return true;
        }
        
        // Firma Admin sadece kendi firmasını yönetebilir
        if ($userRole === ROLE_COMPANY_ADMIN) {
            return self::getCompanyId() === $companyId;
        }
        
        return false;
    }
    
    /**
     * Şifre hash'leme
     * @param string $password
     * @return string
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }
    
    /**
     * Şifre doğrulama
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Email validation
     * @param string $email
     * @return bool
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Password complexity check
     * En az 8 karakter, 1 büyük harf, 1 küçük harf, 1 rakam
     * @param string $password
     * @return bool
     */
    public static function validatePassword($password) {
        return strlen($password) >= 8 
            && preg_match('/[A-Z]/', $password) 
            && preg_match('/[a-z]/', $password) 
            && preg_match('/[0-9]/', $password);
    }
    
    /**
     * UUID oluşturma
     * @return string
     */
    public static function generateUUID() {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
    
    /**
     * Login attempt kontrolü - Brute force koruması
     * @param string $email
     * @return bool
     */
    public static function checkLoginAttempts($email) {
        $key = 'login_attempts_' . md5($email);
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [
                'count' => 0,
                'time' => time()
            ];
        }
        
        // Kilitleme süresi dolmuşsa sıfırla
        if (time() - $_SESSION[$key]['time'] > LOGIN_LOCKOUT_TIME) {
            $_SESSION[$key] = [
                'count' => 0,
                'time' => time()
            ];
        }
        
        // Maksimum deneme sayısı aşıldıysa false döndür
        if ($_SESSION[$key]['count'] >= MAX_LOGIN_ATTEMPTS) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Başarısız login denemesini kaydet
     * @param string $email
     */
    public static function recordFailedLogin($email) {
        $key = 'login_attempts_' . md5($email);
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [
                'count' => 0,
                'time' => time()
            ];
        }
        
        $_SESSION[$key]['count']++;
        $_SESSION[$key]['time'] = time();
    }
    
    /**
     * Başarılı login sonrası attempt'leri sıfırla
     * @param string $email
     */
    public static function resetLoginAttempts($email) {
        $key = 'login_attempts_' . md5($email);
        unset($_SESSION[$key]);
    }
    
    /**
     * Dosya yükleme güvenlik kontrolü
     * @param array $file $_FILES array'i
     * @return array ['success' => bool, 'message' => string, 'filename' => string]
     */
    public static function validateFileUpload($file) {
        // Dosya yüklendi mi kontrolü
        if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return ['success' => false, 'message' => 'Dosya yüklenmedi'];
        }
        
        // Yükleme hatası kontrolü
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Dosya yükleme hatası'];
        }
        
        // Dosya boyutu kontrolü
        if ($file['size'] > MAX_UPLOAD_SIZE) {
            return ['success' => false, 'message' => 'Dosya boyutu çok büyük (max 2MB)'];
        }
        
        // Dosya uzantısı kontrolü
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ALLOWED_EXTENSIONS)) {
            return ['success' => false, 'message' => 'Geçersiz dosya formatı'];
        }
        
        // MIME type kontrolü
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($mimeType, $allowedMimes)) {
            return ['success' => false, 'message' => 'Geçersiz dosya tipi'];
        }
        
        // Güvenli dosya adı oluştur
        $newFilename = self::generateUUID() . '.' . $extension;
        
        return ['success' => true, 'message' => 'Geçerli dosya', 'filename' => $newFilename];
    }
    
    /**
     * SQL Injection koruması için string escape
     * @param string $string
     * @return string
     */
    public static function escapeString($string) {
        return addslashes(trim($string));
    }
    
    /**
     * Redirect helper
     * @param string $url
     */
    public static function redirect($url) {
        header('Location: ' . $url);
        exit();
    }
    
    /**
     * Flash message ekle
     * @param string $type (success, error, warning, info)
     * @param string $message
     */
    public static function setFlashMessage($type, $message) {
        $_SESSION['flash_message'] = [
            'type' => $type,
            'message' => $message
        ];
    }
    
    /**
     * Flash message'ı al ve sil
     * @return array|null
     */
    public static function getFlashMessage() {
        if (isset($_SESSION['flash_message'])) {
            $message = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
            return $message;
        }
        return null;
    }
}
