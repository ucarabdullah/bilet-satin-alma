<?php
/**
 * BiBilet - Genel Yapılandırma Dosyası
 * Tüm uygulama ayarları burada tanımlanır
 */

// Hata raporlama (development için açık, production'da kapatılmalı)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Temel yol ayarları
define('BASE_PATH', '');
define('ROOT_PATH', dirname(__DIR__));
define('SITE_NAME', 'BiBilet - Otobüs Bileti Satış Platformu');

// View yolları
define('VIEWS_PATH', ROOT_PATH . '/src/views');
define('LAYOUTS_PATH', VIEWS_PATH . '/layouts');
define('PARTIALS_PATH', VIEWS_PATH . '/partials');

// Veritabanı yolu (workspace contains database.sqlite)
define('DB_PATH', ROOT_PATH . '/database.sqlite');

// Session güvenlik ayarları
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Development için 0, production'da 1 olmalı
ini_set('session.cookie_samesite', 'Strict');

// Session timeout (30 dakika)
ini_set('session.gc_maxlifetime', 1800);

// Uygulama gizli anahtarı (session, token, vb. için)
define('APP_SECRET', 'bibilet_secret_key_change_in_production');

// Timezone
date_default_timezone_set('Europe/Istanbul');

// Uygulama ayarları
define('DEFAULT_USER_BALANCE', 800); // Yeni kullanıcıların başlangıç kredisi (kullanıcı isteği)
define('TICKET_CANCEL_LIMIT_HOURS', 1); // Kalkışa kaç saat kala iptal edilebilir
define('MAX_LOGIN_ATTEMPTS', 10); // Maksimum giriş denemesi
define('LOGIN_LOCKOUT_TIME', 900); // Hesap kilitleme süresi (15 dakika)

// Dosya yükleme ayarları
define('UPLOAD_DIR', ROOT_PATH . '/public/assets/uploads');
define('MAX_UPLOAD_SIZE', 2097152); // 2MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Sayfalama ayarları
define('ITEMS_PER_PAGE', 10);

// Roller
define('ROLE_ADMIN', 'admin');
define('ROLE_COMPANY_ADMIN', 'company');
define('ROLE_USER', 'user');
