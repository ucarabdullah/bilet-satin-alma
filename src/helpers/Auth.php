<?php

class Auth {
    
    /**
     * Kullanıcı giriş yapmış mı kontrolü
     */
    public static function check() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Giriş yapmış kullanıcı ID'sini al
     */
    public static function id() {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Giriş yapmış kullanıcı bilgilerini al
     */
    public static function user() {
        if (!self::check()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'] ?? null,
            'email' => $_SESSION['user_email'] ?? null,
            'full_name' => $_SESSION['user_name'] ?? null,
            'role' => $_SESSION['user_role'] ?? 'user',
            'company_id' => $_SESSION['company_id'] ?? null
        ];
    }
    
    /**
     * Kullanıcı rolünü al
     */
    public static function role() {
        return $_SESSION['user_role'] ?? 'guest';
    }
    
    /**
     * Kullanıcı giriş işlemi
     */
    public static function login($user) {
        // Session fixation koruması
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role'];
        
        if (isset($user['company_id'])) {
            $_SESSION['company_id'] = $user['company_id'];
        }
        
        // Session güvenlik bilgilerini güncelle
        $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $_SESSION['last_activity'] = time();
        
        return true;
    }
    
    /**
     * Kullanıcı çıkış işlemi
     */
    public static function logout() {
        Security::destroySession();
    }
    
    /**
     * Giriş yapmış kullanıcı kontrolü - Middleware
     */
    public static function requireLogin() {
        if (!self::check()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            header('Location: /login');
            exit;
        }
        
        // Session hijacking kontrolü
        if (!Security::validateSession()) {
            self::logout();
            $_SESSION['error'] = 'Oturumunuz güvenlik nedeniyle sonlandırıldı. Lütfen tekrar giriş yapın.';
            header('Location: /login');
            exit;
        }
    }
    
    /**
     * Admin rolü kontrolü
     */
    public static function requireAdmin() {
        self::requireLogin();
        
        if (self::role() !== 'admin') {
            $_SESSION['error'] = 'Bu sayfaya erişim yetkiniz yok.';
            header('Location: /');
            exit;
        }
    }
    
    /**
     * Company admin rolü kontrolü
     */
    public static function requireCompanyAdmin() {
        self::requireLogin();
        
        if (self::role() !== 'company') { // Veritabanı 'company' rolü kullanıyor
            $_SESSION['error'] = 'Bu sayfaya erişim yetkiniz yok.';
            header('Location: /');
            exit;
        }
    }
    
    /**
     * User rolü kontrolü
     */
    public static function requireUser() {
        self::requireLogin();
        
        if (self::role() !== 'user') {
            $_SESSION['error'] = 'Bu sayfaya erişim yetkiniz yok.';
            header('Location: /');
            exit;
        }
    }
    
    /**
     * Belirli bir role sahip mi kontrolü
     */
    public static function hasRole($role) {
        return self::role() === $role;
    }
    
    /**
     * Belirli rollerden birine sahip mi kontrolü
     */
    public static function hasAnyRole($roles) {
        return in_array(self::role(), $roles);
    }
    
    /**
     * Ziyaretçi mi kontrolü (giriş yapmamış)
     */
    public static function guest() {
        return !self::check();
    }
    
    /**
     * Şirket ID kontrolü - Company admin için
     */
    public static function companyId() {
        return $_SESSION['company_id'] ?? null;
    }
    
    /**
     * Kullanıcının belirli bir şirkete ait olup olmadığını kontrol et
     */
    public static function belongsToCompany($companyId) {
        if (self::role() !== 'company') { // Veritabanı 'company' rolü kullanıyor
            return false;
        }
        
        return self::companyId() === $companyId;
    }
}
