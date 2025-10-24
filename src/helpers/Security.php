<?php

class Security {
    
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    public static function validateCSRFToken($token) {
        if (!isset($_SESSION['csrf_token']) || !isset($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    public static function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    
    public static function sanitize($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::sanitize($value);
            }
            return $data;
        }
        
        $data = trim($data);
        $data = stripslashes($data);
        return $data;
    }

    public static function sanitizeInput($data) {
        return self::sanitize($data);
    }
    
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    public static function validatePasswordStrength($password) {
        if (strlen($password) < 8) {
            return false;
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            return false;
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            return false;
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            return false;
        }
        
        return true;
    }
    
    // Alias for validatePasswordStrength
    public static function validatePassword($password) {
        return self::validatePasswordStrength($password);
    }
    
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }
    
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Session hijacking koruması
     */
    public static function validateSession() {
        // IP adresi kontrolü
        if (isset($_SESSION['user_ip'])) {
            if ($_SESSION['user_ip'] !== $_SERVER['REMOTE_ADDR']) {
                return false;
            }
        } else {
            $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
        }
        
        // User agent kontrolü
        if (isset($_SESSION['user_agent'])) {
            if ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
                return false;
            }
        } else {
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        }
        
        // Session timeout kontrolü (30 dakika)
        if (isset($_SESSION['last_activity'])) {
            if (time() - $_SESSION['last_activity'] > 1800) { // 30 dakika = 1800 saniye
                return false;
            }
        }
        $_SESSION['last_activity'] = time();
        
        return true;
    }
    
    /**
     * Session başlatma - Güvenli yapılandırma
     */
    public static function startSecureSession() {
        if (session_status() === PHP_SESSION_NONE) {
            // Session yapılandırması
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', 0); // HTTPS kullanıyorsanız 1 yapın
            ini_set('session.use_only_cookies', 1);
            ini_set('session.use_strict_mode', 1);
            ini_set('session.cookie_samesite', 'Lax');
            
            session_start();
            
            // Session fixation koruması
            if (!isset($_SESSION['initiated'])) {
                session_regenerate_id(true);
                $_SESSION['initiated'] = true;
                $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
                $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
                $_SESSION['last_activity'] = time();
            }
        }
    }
    
    /**
     * Kullanıcının giriş yapmış olup olmadığını kontrol et
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    // Kullanıcı rolü (header vb. için pratik yardımcı)
    public static function getUserRole() {
        return $_SESSION['user_role'] ?? 'guest';
    }
    
    /**
     * Oturumdaki kullanıcının ID'sini döndürür
     * @return int|null Kullanıcı ID'si veya null
     */
    public static function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Oturumdaki kullanıcının firma ID'sini döndürür
     * @return string|null Firma ID'si veya null
     */
    public static function getCompanyId() {
        return $_SESSION['company_id'] ?? null;
    }
    
    /**
     * Session sonlandırma - Güvenli çıkış
     */
    public static function destroySession() {
        $_SESSION = array();
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
    }
    
    /**
     * Login attempt limiti kontrolü (Brute force koruması)
     * 5 başarısız deneme sonrası 15 dakika bekleme
     */
    public static function checkLoginAttempts($email) {
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = [];
        }
        
        // Eski girişimleri temizle (15 dakikadan eski)
        $currentTime = time();
        $_SESSION['login_attempts'] = array_filter($_SESSION['login_attempts'], function($attempt) use ($currentTime) {
            return isset($attempt['timestamp']) && ($currentTime - $attempt['timestamp']) < 900; // 15 dakika = 900 saniye
        });
        
        // Belirli email için giriş sayısı
        $emailAttempts = array_filter($_SESSION['login_attempts'], function($attempt) use ($email) {
            return isset($attempt['email']) && $attempt['email'] === $email;
        });
        
        if (count($emailAttempts) >= 5) {
            return false; // Çok fazla deneme
        }
        
        return true;
    }
    
    /**
     * Başarısız login denemesi kaydet
     */
    public static function recordFailedLogin($email) {
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = [];
        }
        
        $_SESSION['login_attempts'][] = [
            'email' => $email,
            'timestamp' => time()
        ];
    }
    
    /**
     * Başarılı login sonrası kayıtları temizle
     */
    public static function clearLoginAttempts($email) {
        if (isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = array_filter($_SESSION['login_attempts'], function($item) use ($email) {
                return !isset($item['email']) || $item['email'] !== $email;
            });
        }
    }

    // Geriye dönük uyumluluk: resetLoginAttempts -> clearLoginAttempts
    public static function resetLoginAttempts($email) {
        return self::clearLoginAttempts($email);
    }
    
    /**
     * Random token oluşturma (Remember me, reset password vb.)
     */
    public static function generateRandomToken($length = 32) {
        return bin2hex(random_bytes($length));
    }

    // Geriye dönük uyumluluk: generateToken -> generateRandomToken
    public static function generateToken($length = 32) {
        return self::generateRandomToken($length);
    }

    // Geriye dönük uyumluluk: validateCSRF -> validateCSRFToken
    public static function validateCSRF($token) {
        return self::validateCSRFToken($token);
    }

    // Basit yetkilendirme yardımcıları (doküman uyumu)
    public static function checkAuth() {
        return self::isLoggedIn();
    }

    public static function checkPermission($requiredRole) {
        $role = self::getUserRole();
        if ($requiredRole === 'user') {
            return in_array($role, ['user','admin','company'], true); // Veritabanı 'company' rolü kullanıyor
        }
        if ($requiredRole === 'company') { // Veritabanı 'company' rolü kullanıyor
            return in_array($role, ['company','admin'], true);
        }
        if ($requiredRole === 'admin') {
            return $role === 'admin';
        }
        return false;
    }
    
    /**
     * Kullanıcının belirli bir role sahip olup olmadığını kontrol eder
     * @param string $role Kontrol edilecek rol ('admin', 'company', 'user')
     * @return bool Kullanıcı bu role sahip mi?
     */
    public static function checkRole($role) {
        if (!self::isLoggedIn()) {
            return false;
        }
        
        $userRole = self::getUserRole();
        
        // Admin her yere erişebilir
        if ($userRole === 'admin') {
            return true;
        }
        
        // Kullanıcının rolü istenen rol ile eşleşiyor mu?
        return $userRole === $role;
    }
    
    /**
     * Rate limiting - API istekleri için
     */
    public static function checkRateLimit($identifier, $maxRequests = 60, $timeWindow = 60) {
        if (!isset($_SESSION['rate_limit'])) {
            $_SESSION['rate_limit'] = [];
        }
        
        $currentTime = time();
        
        // Eski kayıtları temizle
        if (isset($_SESSION['rate_limit'][$identifier])) {
            $_SESSION['rate_limit'][$identifier] = array_filter(
                $_SESSION['rate_limit'][$identifier],
                function($timestamp) use ($currentTime, $timeWindow) {
                    return ($currentTime - $timestamp) < $timeWindow;
                }
            );
        } else {
            $_SESSION['rate_limit'][$identifier] = [];
        }
        
        // Limit kontrolü
        if (count($_SESSION['rate_limit'][$identifier]) >= $maxRequests) {
            return false;
        }
        
        // Yeni isteği kaydet
        $_SESSION['rate_limit'][$identifier][] = $currentTime;
        return true;
    }
    
    /**
     * Flash mesaj oluşturma
     * @param string $type Mesaj tipi (success, error, warning, info)
     * @param string $message Mesaj içeriği
     */
    public static function setFlashMessage($type, $message) {
        if (!isset($_SESSION['flash_messages'])) {
            $_SESSION['flash_messages'] = [];
        }
        $_SESSION['flash_messages'][$type] = $message;
    }
    
    /**
     * Flash mesaj alma ve temizleme
     * - type verilirse: o tipe ait mesajı ['type'=>..., 'message'=>...] olarak döner
     * - type verilmezse: mevcut ilk mesajı ['type'=>..., 'message'=>...] olarak döner
     * @param string|null $type Mesaj tipi (opsiyonel)
     * @return array|null ['type' => string, 'message' => string] veya null
     */
    public static function getFlashMessage($type = null) {
        // Hiç mesaj yoksa
        if (!isset($_SESSION['flash_messages']) || empty($_SESSION['flash_messages'])) {
            return null;
        }

        // Belirli tip istendiğinde
        if ($type !== null) {
            if (isset($_SESSION['flash_messages'][$type])) {
                $message = $_SESSION['flash_messages'][$type];
                unset($_SESSION['flash_messages'][$type]);
                return [
                    'type' => $type,
                    'message' => $message,
                ];
            }
            return null;
        }

        // İlk mevcut mesajı döndür (FIFO basit yaklaşım)
        foreach ($_SESSION['flash_messages'] as $t => $msg) {
            unset($_SESSION['flash_messages'][$t]);
            return [
                'type' => $t,
                'message' => $msg,
            ];
        }

        return null;
    }
    
    /**
     * UUID v4 oluşturur
     * @return string UUID
     */
    public static function generateUUID() {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
    
    public static function redirect($path) {
        header('Location: ' . BASE_PATH . $path);
        exit;
    }
}

