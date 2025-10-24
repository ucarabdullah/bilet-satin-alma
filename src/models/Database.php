<?php
/**
 * Database - Veritabanı bağlantısını yöneten sınıf
 * 
 * Bu sınıf, PDO kullanarak veritabanı bağlantısını yönetir.
 * Singleton pattern ile tek bir veritabanı bağlantısı kullanılır.
 */
class Database extends PDO
{
    private static $instance;
    
    /**
     * Database Constructor
     * 
     * @param string $dsn DSN bağlantı dizesi
     * @param string|null $username Kullanıcı adı
     * @param string|null $password Şifre
     * @param array $options PDO bağlantı seçenekleri
     */
    public function __construct($dsn = null, $username = null, $password = null, $options = [])
    {
        // Normal SQLite PDO bağlantısı
        if ($dsn === null) {
            // Use the canonical database file name present in the workspace
            $dsn = 'sqlite:' . dirname(__DIR__, 2) . '/database.sqlite';
        }
        
        $default_options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        $options = array_merge($default_options, $options);
        
        parent::__construct($dsn, $username, $password, $options);
        
        // SQLite için foreign key'leri aktif et
        if (strpos($dsn, 'sqlite') !== false) {
            $this->exec('PRAGMA foreign_keys = ON');
        }
    }
    
    /**
     * Prepared statement oluşturup çalıştırır
     * 
     * @param string $sql SQL sorgusu
     * @param array $params Parametre dizisi
     * @return PDOStatement|false
     */
    public function runQuery($sql, $params = [])
    {
        try {
            $stmt = $this->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log('SQL Hatası: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Singleton metodu ile veritabanı nesnesini döndürür
     * 
     * @param string $dsn DSN bağlantı dizesi
     * @param string|null $username Kullanıcı adı
     * @param string|null $password Şifre
     * @param array $options PDO bağlantı seçenekleri
     * @return Database
     */
    public static function getInstance($dsn = null, $username = null, $password = null, $options = [])
    {
        if (self::$instance === null) {
            self::$instance = new self($dsn, $username, $password, $options);
        }
        
        return self::$instance;
    }
}
?>
