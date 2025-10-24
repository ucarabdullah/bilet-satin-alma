<?php
/**
 * Ticket - Bilet işlemlerini yöneten model sınıfı
 * 
 * Bu sınıf bilet işlemlerini ve sorgularını yönetir:
 * - Bilet oluşturma
 * - Bilet sorgulama
 * - Bilet durumu güncelleme
 * - Koltuk rezervasyonu
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../helpers/Security.php';

class Ticket {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Yeni bilet oluşturur
     * 
     * @param array $data Bilet verileri
     * @return string|bool Başarılıysa bilet ID'si, değilse false döner
     */
    public function create($data) {
        try {
            // Bilet ID'sini UUID olarak oluştur
            $ticketId = Security::generateUUID();
            
            // Bilet kaydı ekle
            $stmt = $this->db->prepare("
                INSERT INTO Tickets (id, trip_id, user_id, status, total_price, created_at)
                VALUES (:id, :trip_id, :user_id, :status, :total_price, CURRENT_TIMESTAMP)
            ");
            
            $result = $stmt->execute([
                'id' => $ticketId,
                'trip_id' => $data['trip_id'],
                'user_id' => $data['user_id'],
                'status' => $data['status'] ?? 'active',
                'total_price' => $data['total_price']
            ]);
            
            if (!$result) {
                return false;
            }
            
            // Koltuk rezervasyonlarını ekle
            if (isset($data['seats']) && is_array($data['seats'])) {
                foreach ($data['seats'] as $seatNumber) {
                    $seatId = Security::generateUUID();
                    
                    $stmt = $this->db->prepare("
                        INSERT INTO Booked_Seats (id, ticket_id, seat_number, created_at)
                        VALUES (:id, :ticket_id, :seat_number, CURRENT_TIMESTAMP)
                    ");
                    
                    $stmt->execute([
                        'id' => $seatId,
                        'ticket_id' => $ticketId,
                        'seat_number' => $seatNumber
                    ]);
                }
            }
            
            return $ticketId;
        } catch (PDOException $e) {
            error_log("Bilet oluşturma hatası: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Bilet ID'ye göre bilet bilgisini getirir
     * 
     * @param string $id Bilet ID
     * @return array|false Bilet bilgisi veya bulunamazsa false
     */
    public function find($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT t.*, tr.departure_city, tr.destination_city, tr.departure_time, tr.arrival_time,
                       bc.name as company_name, bc.logo_path as company_logo
                FROM Tickets t
                JOIN Trips tr ON t.trip_id = tr.id
                JOIN Bus_Company bc ON tr.company_id = bc.id
                WHERE t.id = :id
            ");
            
            $stmt->execute(['id' => $id]);
            $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$ticket) {
                return false;
            }
            
            // Bilete ait koltuk bilgilerini al
            $stmt = $this->db->prepare("
                SELECT seat_number FROM Booked_Seats
                WHERE ticket_id = :ticket_id
                ORDER BY seat_number ASC
            ");
            
            $stmt->execute(['ticket_id' => $id]);
            $seats = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $ticket['seats'] = $seats;
            
            return $ticket;
        } catch (PDOException $e) {
            error_log("Bilet bulma hatası: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Kullanıcının aktif biletlerini getirir
     * 
     * @param string $userId Kullanıcı ID
     * @return array Aktif biletler listesi
     */
    public function getUserActiveTickets($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT t.*, tr.departure_city, tr.destination_city, tr.departure_time, tr.arrival_time,
                       bc.name as company_name, bc.logo_path as company_logo
                FROM Tickets t
                JOIN Trips tr ON t.trip_id = tr.id
                JOIN Bus_Company bc ON tr.company_id = bc.id
                WHERE t.user_id = :user_id 
                  AND t.status = 'active' 
                  AND datetime(tr.departure_time) > datetime('now', 'localtime')
                ORDER BY tr.departure_time ASC
            ");
            
            $stmt->execute(['user_id' => $userId]);
            $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Her bilet için koltuk bilgilerini al
            foreach ($tickets as $key => $ticket) {
                $stmt = $this->db->prepare("
                    SELECT seat_number FROM Booked_Seats
                    WHERE ticket_id = :ticket_id
                    ORDER BY seat_number ASC
                ");
                
                $stmt->execute(['ticket_id' => $ticket['id']]);
                $seats = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                $tickets[$key]['seats'] = $seats;
            }
            
            return $tickets;
        } catch (PDOException $e) {
            error_log("Kullanıcı aktif bilet sorgulama hatası: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Kullanıcının yaklaşan seyahatlerini getirir
     * 
     * @param string $userId Kullanıcı ID
     * @param int $limit Kaç adet getirileceği
     * @return array Yaklaşan seyahatler listesi
     */
    public function getUserUpcomingTrips($userId, $limit = 5) {
        try {
            $stmt = $this->db->prepare("
                SELECT t.id as ticket_id, t.created_at as purchase_date, t.total_price,
                       tr.id as trip_id, tr.departure_city, tr.destination_city, 
                       tr.departure_time, tr.arrival_time,
                       bc.name as company_name, bc.logo_path as company_logo
                FROM Tickets t
                JOIN Trips tr ON t.trip_id = tr.id
                JOIN Bus_Company bc ON tr.company_id = bc.id
                WHERE t.user_id = :user_id 
                  AND t.status = 'active' 
                  AND datetime(tr.departure_time) > datetime('now', 'localtime')
                ORDER BY tr.departure_time ASC
                LIMIT :limit
            ");
            
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_STR);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Kullanıcı yaklaşan seyahat sorgulama hatası: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Kullanıcının geçmiş seyahatlerini getirir (tamamlanmış)
     * 
     * @param string $userId Kullanıcı ID
     * @param int $limit Gösterilecek bilet sayısı
     * @return array Geçmiş seyahatler listesi
     */
    public function getUserPastTrips($userId, $limit = 5) {
        try {
            $stmt = $this->db->prepare("
                SELECT t.id as ticket_id, t.created_at as purchase_date, t.total_price,
                       tr.id as trip_id, tr.departure_city, tr.destination_city, 
                       tr.departure_time, tr.arrival_time,
                       bc.name as company_name, bc.logo_path as company_logo
                FROM Tickets t
                JOIN Trips tr ON t.trip_id = tr.id
                JOIN Bus_Company bc ON tr.company_id = bc.id
                WHERE t.user_id = :user_id 
                  AND t.status = 'active' 
                  AND datetime(tr.departure_time) <= datetime('now', 'localtime')
                ORDER BY tr.departure_time DESC
                LIMIT :limit
            ");
            
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_STR);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Kullanıcı geçmiş seyahat sorgulama hatası: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Kullanıcının iptal edilen biletlerini getirir
     *
     * @param string $userId Kullanıcı ID
     * @param int $limit Gösterilecek bilet sayısı
     * @return array İptal edilen biletler listesi
     */
        public function getUserCanceledTickets($userId, $limit = 5) {
        try {
                        $stmt = $this->db->prepare("
                                SELECT t.id as ticket_id, t.created_at as purchase_date, t.total_price,
                                             tr.id as trip_id, tr.departure_city, tr.destination_city,
                                             tr.departure_time, tr.arrival_time,
                                             bc.name as company_name, bc.logo_path as company_logo
                                FROM Tickets t
                                LEFT JOIN Trips tr ON t.trip_id = tr.id
                                LEFT JOIN Bus_Company bc ON tr.company_id = bc.id
                                WHERE t.user_id = :user_id
                                    AND t.status = 'canceled'
                                ORDER BY t.created_at DESC
                                LIMIT :limit
                        ");
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_STR);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Kullanıcı iptal edilen biletler sorgulama hatası: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Kullanıcının tüm biletlerini getirir
     * 
     * @param string $userId Kullanıcı ID
     * @param int $limit Sayfa başına gösterilecek bilet sayısı
     * @param int $offset Sayfalama için offset değeri
     * @return array Biletler listesi
     */
    public function getUserTickets($userId, $limit = 10, $offset = 0) {
        try {
            $stmt = $this->db->prepare("
                SELECT t.*, tr.departure_city, tr.destination_city, tr.departure_time, tr.arrival_time,
                       bc.name as company_name, bc.logo_path as company_logo
                FROM Tickets t
                JOIN Trips tr ON t.trip_id = tr.id
                JOIN Bus_Company bc ON tr.company_id = bc.id
                WHERE t.user_id = :user_id
                ORDER BY tr.departure_time DESC
                LIMIT :limit OFFSET :offset
            ");
            
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_STR);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Her bilet için koltuk bilgilerini al
            foreach ($tickets as $key => $ticket) {
                $stmt = $this->db->prepare("
                    SELECT seat_number FROM Booked_Seats
                    WHERE ticket_id = :ticket_id
                    ORDER BY seat_number ASC
                ");
                
                $stmt->execute(['ticket_id' => $ticket['id']]);
                $seats = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                $tickets[$key]['seats'] = $seats;
            }
            
            return $tickets;
        } catch (PDOException $e) {
            error_log("Kullanıcı bilet geçmişi sorgulama hatası: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Kullanıcının toplam bilet sayısını döner (sayfalama için)
     * 
     * @param string $userId Kullanıcı ID
     * @return int Toplam bilet sayısı
     */
    public function countUserTickets($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM Tickets 
                WHERE user_id = :user_id
            ");
            
            $stmt->execute(['user_id' => $userId]);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Kullanıcı bilet sayısı sorgulama hatası: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Bileti iptal eder
     * 
     * @param string $ticketId Bilet ID
     * @param string $userId Kullanıcı ID (güvenlik kontrolü için)
     * @return bool İşlem başarılı mı
     */
    public function cancelTicket($ticketId, $userId) {
        try {
            // Önce bilet bilgilerini al
            $stmt = $this->db->prepare("
                SELECT t.*, tr.departure_time
                FROM Tickets t
                JOIN Trips tr ON t.trip_id = tr.id
                WHERE t.id = :id AND t.user_id = :user_id AND t.status = 'active'
            ");
            $stmt->execute(['id' => $ticketId, 'user_id' => $userId]);
            $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$ticket) {
                return false; // Bilet bulunamadı veya zaten iptal edilmiş
            }
            
            // 1 saat kuralını kontrol et
            $departureTime = strtotime($ticket['departure_time']);
            $currentTime = time();
            $timeUntilDeparture = $departureTime - $currentTime;
            
            if ($timeUntilDeparture < 3600) { // 1 saat = 3600 saniye
                return false; // Seyahatten 1 saatten az kaldı, iptal edilemez
            }
            
            // Transaction başlat
            $this->db->beginTransaction();
            
            try {
                // Bileti iptal et
                $stmt = $this->db->prepare("
                    UPDATE Tickets 
                    SET status = 'canceled' 
                    WHERE id = :id
                ");
                $stmt->execute(['id' => $ticketId]);
                
                // Para iadesini yap (kullanıcının bakiyesine ekle)
                $stmt = $this->db->prepare("
                    UPDATE User 
                    SET balance = balance + :refund_amount 
                    WHERE id = :user_id
                ");
                $stmt->execute([
                    'refund_amount' => $ticket['total_price'],
                    'user_id' => $userId
                ]);
                
                // Transaction'ı tamamla
                $this->db->commit();
                return true;
                
            } catch (PDOException $e) {
                // Hata durumunda rollback yap
                $this->db->rollBack();
                error_log("Bilet iptal transaction hatası: " . $e->getMessage());
                return false;
            }
            
        } catch (PDOException $e) {
            error_log("Bilet iptal hatası: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Benzersiz ID oluşturur (UUID v4)
     * 
     * @return string UUID
     */
    // UUID üretimi Security::generateUUID ile merkezi hale getirildi.
    
    /**
     * Firma ID'ye göre tüm biletleri getirir (Firma Admin için)
     * 
     * @param string $companyId Firma ID
     * @param int $limit Limit
     * @param int $offset Offset
     * @return array Biletler listesi
     */
    public function getTicketsByCompanyId($companyId, $limit = 50, $offset = 0) {
        try {
            $stmt = $this->db->prepare("
                SELECT t.*, tr.departure_city, tr.destination_city, tr.departure_time, tr.arrival_time,
                       tr.price as trip_price,
                       u.full_name as customer_name, u.email as customer_email,
                       bc.name as company_name
                FROM Tickets t
                JOIN Trips tr ON t.trip_id = tr.id
                JOIN Bus_Company bc ON tr.company_id = bc.id
                JOIN User u ON t.user_id = u.id
                WHERE bc.id = :company_id
                ORDER BY tr.departure_time DESC, t.created_at DESC
                LIMIT :limit OFFSET :offset
            ");
            
            $stmt->bindParam(':company_id', $companyId, PDO::PARAM_STR);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Her bilet için koltuk bilgilerini al
            foreach ($tickets as $key => $ticket) {
                $stmt = $this->db->prepare("
                    SELECT seat_number FROM Booked_Seats
                    WHERE ticket_id = :ticket_id
                    ORDER BY seat_number ASC
                ");
                
                $stmt->execute(['ticket_id' => $ticket['id']]);
                $seats = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                $tickets[$key]['seats'] = $seats;
            }
            
            return $tickets;
        } catch (PDOException $e) {
            error_log("Firma bilet sorgulama hatası: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Firma ID'ye göre toplam bilet sayısını döner
     * 
     * @param string $companyId Firma ID
     * @return int Toplam bilet sayısı
     */
    public function countTicketsByCompanyId($companyId) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM Tickets t
                JOIN Trips tr ON t.trip_id = tr.id
                WHERE tr.company_id = :company_id
            ");
            
            $stmt->execute(['company_id' => $companyId]);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Firma bilet sayısı sorgulama hatası: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Firma admin bilet iptal eder (kendi firmasının biletlerini)
     * 
     * @param string $ticketId Bilet ID
     * @param string $companyId Firma ID (güvenlik kontrolü için)
     * @return bool İşlem başarılı mı
     */
    public function cancelTicketByCompany($ticketId, $companyId) {
        try {
            // Önce bilet bilgilerini al
            $stmt = $this->db->prepare("
                SELECT t.*, tr.departure_time
                FROM Tickets t
                JOIN Trips tr ON t.trip_id = tr.id
                WHERE t.id = :id 
                  AND tr.company_id = :company_id 
                  AND t.status = 'active'
            ");
            $stmt->execute(['id' => $ticketId, 'company_id' => $companyId]);
            $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$ticket) {
                return false; // Bilet bulunamadı veya bu firmaya ait değil
            }
            
            // 1 saat kuralını kontrol et
            $departureTime = strtotime($ticket['departure_time']);
            $currentTime = time();
            $timeUntilDeparture = $departureTime - $currentTime;
            
            if ($timeUntilDeparture < 3600) { // 1 saat = 3600 saniye
                return false; // Seyahatten 1 saatten az kaldı, iptal edilemez
            }
            
            // Transaction başlat
            $this->db->beginTransaction();
            
            try {
                // Bileti iptal et
                $stmt = $this->db->prepare("
                    UPDATE Tickets 
                    SET status = 'canceled' 
                    WHERE id = :id
                ");
                $stmt->execute(['id' => $ticketId]);
                
                // Para iadesini yap (kullanıcının bakiyesine ekle)
                $stmt = $this->db->prepare("
                    UPDATE User 
                    SET balance = balance + :refund_amount 
                    WHERE id = :user_id
                ");
                $stmt->execute([
                    'refund_amount' => $ticket['total_price'],
                    'user_id' => $ticket['user_id']
                ]);
                
                // Transaction'ı tamamla
                $this->db->commit();
                return true;
                
            } catch (PDOException $e) {
                // Hata durumunda rollback yap
                $this->db->rollBack();
                error_log("Firma bilet iptal transaction hatası: " . $e->getMessage());
                return false;
            }
            
        } catch (PDOException $e) {
            error_log("Firma bilet iptal hatası: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Belirli bir sefer için tüm biletleri getirir (Firma Admin için)
     * 
     * @param string $tripId Sefer ID
     * @param string $companyId Firma ID (güvenlik kontrolü)
     * @return array Biletler listesi
     */
    public function getTicketsByTripId($tripId, $companyId) {
        try {
            $stmt = $this->db->prepare("
                SELECT t.*, 
                       u.full_name as customer_name, 
                       u.email as customer_email,
                       u.balance as customer_balance
                FROM Tickets t
                JOIN Trips tr ON t.trip_id = tr.id
                JOIN User u ON t.user_id = u.id
                WHERE tr.id = :trip_id
                  AND tr.company_id = :company_id
                ORDER BY t.created_at DESC
            ");
            
            $stmt->execute([
                'trip_id' => $tripId,
                'company_id' => $companyId
            ]);
            
            $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Her bilet için koltuk bilgilerini al
            foreach ($tickets as $key => $ticket) {
                $stmt = $this->db->prepare("
                    SELECT seat_number FROM Booked_Seats
                    WHERE ticket_id = :ticket_id
                    ORDER BY seat_number ASC
                ");
                
                $stmt->execute(['ticket_id' => $ticket['id']]);
                $seats = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                $tickets[$key]['seats'] = $seats;
            }
            
            return $tickets;
        } catch (PDOException $e) {
            error_log("Sefer bilet sorgulama hatası: " . $e->getMessage());
            return [];
        }
    }
}
?>