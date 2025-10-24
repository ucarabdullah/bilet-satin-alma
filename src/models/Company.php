<?php
/**
 * Company - Otobüs firması işlemlerini yöneten model sınıfı
 * 
 * Bu sınıf firma işlemlerini ve sorgularını yönetir:
 * - Firma listeleme
 * - Firma detayları
 * - Firma CRUD işlemleri
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../helpers/Security.php';

class Company {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Tüm firmaları getirir
     * 
     * @return array Firma listesi
     */
    public function getAll() {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM Bus_Company
                ORDER BY name ASC
            ");
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Firma listesi sorgulama hatası: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * ID'ye göre firma bilgisini getirir
     * 
     * @param string $id Firma ID
     * @return array|false Firma bilgisi veya bulunamazsa false
     */
    public function find($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM Bus_Company
                WHERE id = :id
            ");
            
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Firma bulma hatası: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ID'ye göre firma bilgisini getirir (alias)
     * 
     * @param string $id Firma ID
     * @return array|false Firma bilgisi veya bulunamazsa false
     */
    public function findById($id) {
        return $this->find($id);
    }
    
    /**
     * Yeni firma oluşturur
     * 
     * @param array $data Firma bilgileri (name, logo_path)
     * @return string|false Oluşturulan firma ID veya hata durumunda false
     */
    public function create($data) {
        try {
            $id = Security::generateUUID();
            
            $stmt = $this->db->prepare("
                INSERT INTO Bus_Company (id, name, logo_path, created_at)
                VALUES (:id, :name, :logo_path, CURRENT_TIMESTAMP)
            ");
            
            $stmt->execute([
                'id' => $id,
                'name' => $data['name'],
                'logo_path' => $data['logo_path'] ?? null
            ]);
            
            return $id;
        } catch (PDOException $e) {
            error_log("Firma oluşturma hatası: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Firma bilgilerini günceller
     * 
     * @param string $id Firma ID
     * @param array $data Güncellenecek firma bilgileri
     * @return bool Başarılı ise true, değilse false
     */
    public function update($id, $data) {
        try {
            $fields = [];
            $params = ['id' => $id];
            
            if (isset($data['name'])) {
                $fields[] = "name = :name";
                $params['name'] = $data['name'];
            }
            
            if (isset($data['logo_path'])) {
                $fields[] = "logo_path = :logo_path";
                $params['logo_path'] = $data['logo_path'];
            }
            
            if (empty($fields)) {
                return false;
            }
            
            $sql = "UPDATE Bus_Company SET " . implode(', ', $fields) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Firma güncelleme hatası: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Firmayı siler
     * 
     * @param string $id Firma ID
     * @return bool Başarılı ise true, değilse false
     */
    public function delete($id) {
        try {
            // Transaction başlat - tüm silme işlemleri başarılı olmazsa geri al
            $this->db->beginTransaction();
            
            // 1. Önce bu firmaya ait seferlerdeki aktif biletleri al ve para iadesi yap
            $stmt = $this->db->prepare("
                SELECT t.id, t.user_id, t.total_price 
                FROM Tickets t
                INNER JOIN Trips tr ON t.trip_id = tr.id
                WHERE tr.company_id = :company_id 
                  AND t.status != 'canceled'
            ");
            $stmt->execute(['company_id' => $id]);
            $activeTickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Aktif biletler için kullanıcılara para iadesi yap
            foreach ($activeTickets as $ticket) {
                $stmt = $this->db->prepare('UPDATE User SET balance = balance + :amount WHERE id = :user_id');
                $stmt->execute([
                    'amount' => $ticket['total_price'],
                    'user_id' => $ticket['user_id']
                ]);
            }
            
            // 2. Firmaya ait seferlerin tüm biletlerini al (iade yapıldıktan sonra)
            $stmt = $this->db->prepare("
                SELECT t.id 
                FROM Tickets t
                INNER JOIN Trips tr ON t.trip_id = tr.id
                WHERE tr.company_id = :company_id
            ");
            $stmt->execute(['company_id' => $id]);
            $ticketIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // 3. Biletlere ait koltuk rezervasyonlarını sil
            if (!empty($ticketIds)) {
                $placeholders = implode(',', array_fill(0, count($ticketIds), '?'));
                $stmt = $this->db->prepare("DELETE FROM Booked_Seats WHERE ticket_id IN ($placeholders)");
                $stmt->execute($ticketIds);
            }
            
            // 4. Firmaya ait seferlerin biletlerini sil
            $stmt = $this->db->prepare("
                DELETE FROM Tickets 
                WHERE trip_id IN (
                    SELECT id FROM Trips WHERE company_id = :company_id
                )
            ");
            $stmt->execute(['company_id' => $id]);
            
            // 5. Firmaya ait seferleri sil
            $stmt = $this->db->prepare("DELETE FROM Trips WHERE company_id = :company_id");
            $stmt->execute(['company_id' => $id]);
            
            // 6. Firmaya ait kullanıcıların company_id'sini NULL yap
            $stmt = $this->db->prepare("UPDATE User SET company_id = NULL WHERE company_id = :company_id");
            $stmt->execute(['company_id' => $id]);
            
            // 7. Önce firmaya ait kuponların kullanımlarını sil (User_Coupons)
            $stmt = $this->db->prepare("
                DELETE FROM User_Coupons 
                WHERE coupon_id IN (
                    SELECT id FROM Coupons WHERE company_id = :company_id
                )
            ");
            $stmt->execute(['company_id' => $id]);
            
            // 8. Firmaya ait kuponları sil
            $stmt = $this->db->prepare("DELETE FROM Coupons WHERE company_id = :company_id");
            $stmt->execute(['company_id' => $id]);
            
            // 9. Firmayı sil
            $stmt = $this->db->prepare("DELETE FROM Bus_Company WHERE id = :id");
            $result = $stmt->execute(['id' => $id]);
            
            // Transaction commit
            $this->db->commit();
            
            return $result;
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }
    
    /**
     * Firma adının benzersiz olup olmadığını kontrol eder
     * 
     * @param string $name Firma adı
     * @param string|null $excludeId Hariç tutulacak firma ID (güncelleme için)
     * @return bool Benzersiz ise true, değilse false
     */
    public function isNameUnique($name, $excludeId = null) {
        try {
            $sql = "SELECT COUNT(*) FROM Bus_Company WHERE name = :name";
            $params = ['name' => $name];
            
            if ($excludeId) {
                $sql .= " AND id != :exclude_id";
                $params['exclude_id'] = $excludeId;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchColumn() == 0;
        } catch (PDOException $e) {
            error_log("Firma adı kontrol hatası: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Firmaya ait seferleri getirir
     * 
     * @param string $companyId Firma ID
     * @return array Sefer listesi
     */
    public function getTrips($companyId) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM Trips
                WHERE company_id = :company_id
                ORDER BY departure_time DESC
            ");
            
            $stmt->execute(['company_id' => $companyId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Firma seferleri sorgulama hatası: " . $e->getMessage());
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
