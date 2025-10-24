<?php
/**
 * Coupon - Kupon işlemlerini yöneten model sınıfı
 * 
 * Bu sınıf kupon işlemlerini ve sorgularını yönetir:
 * - Kupon listeleme
 * - Kupon doğrulama
 * - Kupon CRUD işlemleri
 * - Kupon kullanım takibi
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../helpers/Security.php';

class Coupon {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Tüm kuponları getirir
     * 
     * @return array Kupon listesi
     */
    public function getAll() {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM Coupons
                ORDER BY created_at DESC
            ");
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Kupon listesi sorgulama hatası: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Belirli firmaya ait kuponları getirir
     */
    public function getByCompany($companyId) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM Coupons
                WHERE company_id = :company_id
                ORDER BY created_at DESC
            ");
            $stmt->execute(['company_id' => $companyId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Kupon liste (firma) hatası: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * ID'ye göre kupon bilgisini getirir
     * 
     * @param string $id Kupon ID
     * @return array|false Kupon bilgisi veya bulunamazsa false
     */
    public function find($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM Coupons
                WHERE id = :id
            ");
            
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Kupon bulma hatası: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Kupon koduna göre kupon bilgisini getirir
     * 
     * @param string $code Kupon kodu
     * @return array|false Kupon bilgisi veya bulunamazsa false
     */
    public function findByCode($code) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM Coupons
                WHERE code = :code
            ");
            
            $stmt->execute(['code' => strtoupper($code)]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Kupon kodu bulma hatası: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Kupon kodunu doğrular ve kullanılabilir mi kontrol eder
     * 
     * @param string $code Kupon kodu
     * @param string $userId Kullanıcı ID
     * @return array Doğrulama sonucu ['valid' => bool, 'message' => string, 'discount' => float]
     */
    public function validate($code, $userId) {
        $coupon = $this->findByCode($code);
        
        if (!$coupon) {
            return [
                'valid' => false,
                'message' => 'Geçersiz kupon kodu.',
                'discount' => 0
            ];
        }
        
        // Son kullanma tarihi kontrolü
        if (strtotime($coupon['expire_date']) < time()) {
            return [
                'valid' => false,
                'message' => 'Bu kupon kodunun kullanım süresi dolmuştur.',
                'discount' => 0
            ];
        }
        
        // Kullanım limiti kontrolü
        $usageCount = $this->getUsageCount($coupon['id']);
        if ($usageCount >= $coupon['usage_limit']) {
            return [
                'valid' => false,
                'message' => 'Bu kupon kodunun kullanım limiti dolmuştur.',
                'discount' => 0
            ];
        }
        
        // Kullanıcı daha önce bu kuponu kullanmış mı kontrol et
        if ($this->hasUserUsedCoupon($coupon['id'], $userId)) {
            return [
                'valid' => false,
                'message' => 'Bu kupon kodunu daha önce kullandınız.',
                'discount' => 0
            ];
        }
        
        return [
            'valid' => true,
            'message' => 'Kupon kodu başarıyla uygulandı!',
            'discount' => $coupon['discount'],
            'coupon_id' => $coupon['id']
        ];
    }
    
    /**
     * Kuponun kaç kez kullanıldığını getirir
     * 
     * @param string $couponId Kupon ID
     * @return int Kullanım sayısı
     */
    public function getUsageCount($couponId) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM User_Coupons
                WHERE coupon_id = :coupon_id
            ");
            
            $stmt->execute(['coupon_id' => $couponId]);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Kupon kullanım sayısı hatası: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Kullanıcının kuponu daha önce kullanıp kullanmadığını kontrol eder
     * 
     * @param string $couponId Kupon ID
     * @param string $userId Kullanıcı ID
     * @return bool Kullanmışsa true, değilse false
     */
    public function hasUserUsedCoupon($couponId, $userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM User_Coupons
                WHERE coupon_id = :coupon_id AND user_id = :user_id
            ");
            
            $stmt->execute([
                'coupon_id' => $couponId,
                'user_id' => $userId
            ]);
            
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Kupon kullanım kontrolü hatası: " . $e->getMessage());
            return true; // Güvenlik için true döndür
        }
    }
    
    /**
     * Kullanıcının kupon kullanımını kaydeder
     * 
     * @param string $couponId Kupon ID
     * @param string $userId Kullanıcı ID
     * @return bool Başarılı ise true, değilse false
     */
    public function recordUsage($couponId, $userId) {
        try {
            $id = Security::generateUUID();
            
            $stmt = $this->db->prepare("
                INSERT INTO User_Coupons (id, coupon_id, user_id, created_at)
                VALUES (:id, :coupon_id, :user_id, CURRENT_TIMESTAMP)
            ");
            
            return $stmt->execute([
                'id' => $id,
                'coupon_id' => $couponId,
                'user_id' => $userId
            ]);
        } catch (PDOException $e) {
            error_log("Kupon kullanım kaydı hatası: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Yeni kupon oluşturur
     * 
     * @param array $data Kupon bilgileri (code, discount, usage_limit, expire_date)
     * @return string|false Oluşturulan kupon ID veya hata durumunda false
     */
    public function create($data) {
        try {
            $id = Security::generateUUID();
            
            $stmt = $this->db->prepare("
                INSERT INTO Coupons (id, code, discount, company_id, usage_limit, expire_date, created_at)
                VALUES (:id, :code, :discount, :company_id, :usage_limit, :expire_date, CURRENT_TIMESTAMP)
            ");
            
            $stmt->execute([
                'id' => $id,
                'code' => strtoupper($data['code']),
                'discount' => $data['discount'],
                'company_id' => $data['company_id'] ?? null,
                'usage_limit' => $data['usage_limit'],
                'expire_date' => $data['expire_date']
            ]);
            
            return $id;
        } catch (PDOException $e) {
            error_log("Kupon oluşturma hatası: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Kupon bilgilerini günceller
     * 
     * @param string $id Kupon ID
     * @param array $data Güncellenecek kupon bilgileri
     * @return bool Başarılı ise true, değilse false
     */
    public function update($id, $data) {
        try {
            $fields = [];
            $params = ['id' => $id];
            
            if (isset($data['code'])) {
                $fields[] = "code = :code";
                $params['code'] = strtoupper($data['code']);
            }
            
            if (isset($data['discount'])) {
                $fields[] = "discount = :discount";
                $params['discount'] = $data['discount'];
            }
            
            if (isset($data['usage_limit'])) {
                $fields[] = "usage_limit = :usage_limit";
                $params['usage_limit'] = $data['usage_limit'];
            }
            
            if (isset($data['expire_date'])) {
                $fields[] = "expire_date = :expire_date";
                $params['expire_date'] = $data['expire_date'];
            }
            
            if (empty($fields)) {
                return false;
            }
            
            $sql = "UPDATE Coupons SET " . implode(', ', $fields) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Kupon güncelleme hatası: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Kuponu siler
     * 
     * @param string $id Kupon ID
     * @return bool Başarılı ise true, değilse false
     */
    public function delete($id) {
        try {
            // Önce User_Coupons tablosundan bu kupona ait kayıtları sil
            $stmt = $this->db->prepare("DELETE FROM User_Coupons WHERE coupon_id = :id");
            $stmt->execute(['id' => $id]);
            
            // Sonra kuponu sil
            $stmt = $this->db->prepare("DELETE FROM Coupons WHERE id = :id");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log("Kupon silme hatası: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Kupon kodunun benzersiz olup olmadığını kontrol eder
     * 
     * @param string $code Kupon kodu
     * @param string|null $excludeId Hariç tutulacak kupon ID (güncelleme için)
     * @return bool Benzersiz ise true, değilse false
     */
    public function isCodeUnique($code, $excludeId = null) {
        try {
            $sql = "SELECT COUNT(*) FROM Coupons WHERE code = :code";
            $params = ['code' => strtoupper($code)];
            
            if ($excludeId) {
                $sql .= " AND id != :exclude_id";
                $params['exclude_id'] = $excludeId;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchColumn() == 0;
        } catch (PDOException $e) {
            error_log("Kupon kodu kontrol hatası: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Kupon kodunu doğrular ve kullanılabilirliğini kontrol eder
     * 
     * @param string $code Kupon kodu
     * @param string $tripId Sefer ID
     * @param string $userId Kullanıcı ID
     * @return array|false Kupon bilgileri veya false
     */
    public function validateCoupon($code, $tripId, $userId) {
        try {
            // Kuponu getir
            $coupon = $this->findByCode($code);
            
            if (!$coupon || !is_array($coupon)) {
                return [
                    'is_valid' => false,
                    'error_message' => 'Kupon bulunamadı'
                ];
            }
            
            $result = [
                'id' => $coupon['id'] ?? null,
                'code' => $coupon['code'] ?? '',
                'discount_percent' => $coupon['discount'] ?? 0,  // Database column is 'discount' not 'discount_percent'
                'is_valid' => false,
                'error_message' => ''
            ];
            
            // Son kullanma tarihini kontrol et
            if ($coupon['expire_date'] && strtotime($coupon['expire_date']) < time()) {
                $result['error_message'] = 'Kuponun süresi dolmuş';
                return $result;
            }
            
            // Kullanım limiti kontrolü
            if ($coupon['usage_limit'] !== null) {
                $stmt = $this->db->prepare("
                    SELECT COUNT(*) as usage_count
                    FROM User_Coupons
                    WHERE coupon_id = :coupon_id
                ");
                $stmt->execute(['coupon_id' => $coupon['id']]);
                $usage = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($usage['usage_count'] >= $coupon['usage_limit']) {
                    $result['error_message'] = 'Kupon kullanım limiti dolmuş';
                    return $result;
                }
            }
            
            // Kullanıcının bu kuponu daha önce kullanıp kullanmadığını kontrol et
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as used_count
                FROM User_Coupons
                WHERE coupon_id = :coupon_id AND user_id = :user_id
            ");
            $stmt->execute([
                'coupon_id' => $coupon['id'],
                'user_id' => $userId
            ]);
            $userUsage = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($userUsage['used_count'] > 0) {
                $result['error_message'] = 'Bu kuponu daha önce kullandınız';
                return $result;
            }
            
            // Firmaya özel kupon kontrolü (eğer company_id varsa)
            if ($coupon['company_id']) {
                // Seferin firma ID'sini kontrol et
                $stmt = $this->db->prepare("SELECT company_id FROM Trips WHERE id = :trip_id");
                $stmt->execute(['trip_id' => $tripId]);
                $trip = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$trip || $trip['company_id'] !== $coupon['company_id']) {
                    $result['error_message'] = 'Bu kupon sadece belirli firmalar için geçerlidir';
                    return $result;
                }
            }
            
            // Tüm kontroller başarılı
            $result['is_valid'] = true;
            return $result;
            
        } catch (PDOException $e) {
            error_log("Kupon doğrulama hatası: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Kullanıcının kullanabileceği kuponları getirir
     * 
     * @param string $userId Kullanıcı ID
     * @param string|null $tripId Sefer ID (opsiyonel)
     * @return array Kullanılabilir kupon listesi
     */
    public function getAvailableCouponsForUser($userId, $tripId = null) {
        try {
            $sql = "
                SELECT c.*,
                       CASE WHEN uc.coupon_id IS NOT NULL THEN 1 ELSE 0 END as is_used
                FROM Coupons c
                LEFT JOIN User_Coupons uc ON c.id = uc.coupon_id AND uc.user_id = :user_id
                WHERE (c.expire_date IS NULL OR c.expire_date > CURRENT_TIMESTAMP)
                AND uc.coupon_id IS NULL
            ";
            
            $params = ['user_id' => $userId];
            
            // Eğer sefer ID verilmişse, firma kontrolü yap
            if ($tripId) {
                $sql .= " AND (c.company_id IS NULL OR c.company_id = (SELECT company_id FROM Trips WHERE id = :trip_id))";
                $params['trip_id'] = $tripId;
            }
            
            $sql .= " ORDER BY c.discount DESC";  // Column name is 'discount' not 'discount_percent'
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Kullanılabilir kupon listesi hatası: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Benzersiz ID oluşturur (UUID v4)
     * 
     * @return string UUID
     */
    // UUID üretimi Security::generateUUID ile merkezi hale getirildi.
}
?>
