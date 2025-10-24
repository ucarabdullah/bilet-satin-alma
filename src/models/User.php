<?php

require_once __DIR__ . '/../helpers/Security.php';

class User {
    private $db;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Email'e göre kullanıcı sorgulama
     * @param string $email
     * @return array|false
     */
    public function findByEmail($email) {
        $sql = "SELECT * FROM User WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }
    
    /**
     * ID'ye göre kullanıcı sorgulama
     * @param string $id
     * @return array|false
     */
    public function findById($id) {
        $sql = "SELECT * FROM User WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    /**
     * ID'ye göre kullanıcı sorgulama (alias)
     * @param string $id
     * @return array|false
     */
    public function find($id) {
        return $this->findById($id);
    }
    
    /**
     * Yeni kullanıcı kaydetme (register)
     * @param string $fullName
     * @param string $email
     * @param string $password
     * @param string $role Default: 'user'
     * @param string|null $companyId
     * @return bool|string User ID başarılı ise, false başarısız ise
     */
    public function create($fullName, $email, $password, $role = 'user', $companyId = null) {
        // Email kullanımda mı kontrol et
        if ($this->findByEmail($email)) {
            return false;
        }
        
        // Hash'lenmiş şifre oluştur
        $hashedPassword = Security::hashPassword($password);
        
    // UUID oluştur (Security üzerinden tek noktadan)
    $id = Security::generateUUID();
        
        // Yeni kullanıcı oluştur
        $sql = "INSERT INTO User (id, full_name, email, password, role, company_id, created_at) 
                VALUES (:id, :full_name, :email, :password, :role, :company_id, CURRENT_TIMESTAMP)";
        
        $params = [
            ':id' => $id,
            ':full_name' => $fullName,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':role' => $role,
            ':company_id' => $companyId
        ];
        
        $stmt = $this->db->prepare($sql);
        if ($stmt->execute($params)) {
            return $id;
        }
        
        return false;
    }
    
    /**
     * Login kontrolü
     * @param string $email
     * @param string $password
     * @return array|false User data başarılı ise, false başarısız ise
     */
    public function login($email, $password) {
        // Brute force koruması
        if (!Security::checkLoginAttempts($email)) {
            return false;
        }
        
        // Email'e göre kullanıcıyı bul
        $user = $this->findByEmail($email);
        
        if (!$user) {
            Security::recordFailedLogin($email);
            return false;
        }
        
        // Şifre kontrolü
        if (!Security::verifyPassword($password, $user['password'])) {
            Security::recordFailedLogin($email);
            return false;
        }
        
        // Login başarılı - attempt sayacını sıfırla
        Security::resetLoginAttempts($email);
        
        return $user;
    }
    
    /**
     * Kullanıcı bakiyesini belirli bir miktarda arttır/azalt
     * @param string $userId
     * @param float $amount
     * @return bool
     */
    public function adjustBalance($userId, $amount) {
        $sql = "UPDATE User SET balance = balance + :amount WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $userId, ':amount' => $amount]);
    }
    
    /**
     * Kullanıcı bakiyesi sorgulama
     * @param string $userId
     * @return float|false
     */
    public function getBalance($userId) {
        $sql = "SELECT balance FROM User WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $userId]);
        $result = $stmt->fetch();
        return $result ? (float) $result['balance'] : false;
    }
    
    /**
     * Kullanıcı bilgilerini güncelleme
     * @param string $userId
     * @param array $data
     * @return bool
     */
    public function update($userId, $data) {
        $allowedFields = ['full_name', 'email', 'password', 'company_id', 'role'];
        $fields = [];
        $params = [':id' => $userId];
        
        // Sadece izin verilen alanları güncelle
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                // Şifre güncelleniyorsa hash'le
                if ($key === 'password') {
                    $value = Security::hashPassword($value);
                }
                
                $fields[] = "$key = :$key";
                $params[":$key"] = $value;
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $sql = "UPDATE User SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Tüm kullanıcıları şirket bilgisi ile getir
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAllWithCompany($limit = 100, $offset = 0) {
        $sql = "SELECT u.*, c.name AS company_name
                FROM User u
                LEFT JOIN Bus_Company c ON u.company_id = c.id
                ORDER BY u.created_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Firma Admin kullanıcılarını listele
     * @return array|false
     */
    public function getCompanyAdmins() {
        $sql = "SELECT u.*, c.name as company_name 
                FROM User u 
                LEFT JOIN Bus_Company c ON u.company_id = c.id 
                WHERE u.role = :role
                ORDER BY u.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':role' => 'company']); // Veritabanı 'company' rolü kullanıyor
        return $stmt->fetchAll();
    }
    
    /**
     * Normal kullanıcıları listele
     * @param int $limit
     * @param int $offset
     * @return array|false
     */
    public function getUsers($limit = 10, $offset = 0) {
        $sql = "SELECT * FROM User WHERE role = :role ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':role', 'user');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Kullanıcı sayısını getir
     * @param string $role Belirli bir rolü filtrelemek için (opsiyonel)
     * @return int
     */
    public function count($role = null) {
        $sql = "SELECT COUNT(*) as total FROM User";
        $params = [];
        
        if ($role !== null) {
            $sql .= " WHERE role = :role";
            $params[':role'] = $role;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return (int) $result['total'];
    }
    
    /**
     * Kullanıcı bakiyesini günceller
     * @param string $userId Kullanıcı ID'si
     * @param int $newBalance Yeni bakiye
     * @return bool İşlem başarılı mı
     */
    public function updateBalance($userId, $newBalance) {
        try {
            $sql = "UPDATE User SET balance = :balance WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':balance' => $newBalance,
                ':id' => $userId
            ]);
        } catch (PDOException $e) {
            error_log("Bakiye güncelleme hatası: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kullanıcı sil
     * @param string $userId
     * @return bool
     */
    public function delete($userId) {
        try {
            // Admin kullanıcılar silinemez kontrolü
            $user = $this->findById($userId);
            if ($user && $user['role'] === 'admin') {
                return false;
            }
            
            // Transaction başlat
            $this->db->beginTransaction();
            
            // 1. Kullanıcının biletlerine ait koltukları sil
            $stmt = $this->db->prepare("
                DELETE FROM Booked_Seats 
                WHERE ticket_id IN (
                    SELECT id FROM Tickets WHERE user_id = :user_id
                )
            ");
            $stmt->execute([':user_id' => $userId]);
            
            // 2. Kullanıcının biletlerini sil
            $stmt = $this->db->prepare("DELETE FROM Tickets WHERE user_id = :user_id");
            $stmt->execute([':user_id' => $userId]);
            
            // 3. Kullanıcının kuponlarını sil
            $stmt = $this->db->prepare("DELETE FROM User_Coupons WHERE user_id = :user_id");
            $stmt->execute([':user_id' => $userId]);
            
            // 4. Eğer firma admini ise, firmaya ait seferleri ve biletleri sil
            if ($user['role'] === 'company' && $user['company_id']) { // Veritabanı 'company' rolü kullanıyor
                // Firmaya ait seferlerin biletlerindeki koltukları sil
                $stmt = $this->db->prepare("
                    DELETE FROM Booked_Seats 
                    WHERE ticket_id IN (
                        SELECT t.id FROM Tickets t
                        INNER JOIN Trips tr ON t.trip_id = tr.id
                        WHERE tr.company_id = :company_id
                    )
                ");
                $stmt->execute([':company_id' => $user['company_id']]);
                
                // Firmaya ait seferlerin biletlerini sil
                $stmt = $this->db->prepare("
                    DELETE FROM Tickets 
                    WHERE trip_id IN (
                        SELECT id FROM Trips WHERE company_id = :company_id
                    )
                ");
                $stmt->execute([':company_id' => $user['company_id']]);
                
                // Firmaya ait seferleri sil
                $stmt = $this->db->prepare("DELETE FROM Trips WHERE company_id = :company_id");
                $stmt->execute([':company_id' => $user['company_id']]);
            }
            
            // 5. Kullanıcıyı sil
            $stmt = $this->db->prepare("DELETE FROM User WHERE id = :id");
            $result = $stmt->execute([':id' => $userId]);
            
            // Transaction commit
            $this->db->commit();
            
            return $result;
        } catch (PDOException $e) {
            // Hata durumunda rollback
            $this->db->rollBack();
            error_log("Kullanıcı silme hatası: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Kullanıcıya bakiye ekle (pozitif tutar)
     * @param string $userId
     * @param float $amount
     * @return bool
     */
    public function addBalance($userId, $amount) {
        return $this->adjustBalance($userId, $amount);
    }
}