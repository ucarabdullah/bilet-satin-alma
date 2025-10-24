<?php
/**
 * Trip - Sefer işlemlerini yöneten model sınıfı
 * 
 * Bu sınıf sefer işlemlerini ve sorgularını yönetir:
 * - Sefer listeleme
 * - Sefer detayları
 * - Sefer arama
 * - Koltuk durumu
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../helpers/Security.php';

class Trip {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Yeni sefer oluşturur
     * @param array $data {company_id, departure_city, destination_city, departure_time, arrival_time, price, capacity}
     * @return string|false Oluşturulan trip ID veya false
     */
    public function create($data) {
        try {
            $id = Security::generateUUID();
            $stmt = $this->db->prepare("
                INSERT INTO Trips (id, company_id, destination_city, arrival_time, departure_time, departure_city, price, capacity, created_date)
                VALUES (:id, :company_id, :destination_city, :arrival_time, :departure_time, :departure_city, :price, :capacity, CURRENT_TIMESTAMP)
            ");

            $ok = $stmt->execute([
                'id' => $id,
                'company_id' => $data['company_id'],
                'destination_city' => $data['destination_city'],
                'arrival_time' => $data['arrival_time'],
                'departure_time' => $data['departure_time'],
                'departure_city' => $data['departure_city'],
                'price' => (int)$data['price'],
                'capacity' => (int)$data['capacity']
            ]);
            return $ok ? $id : false;
        } catch (PDOException $e) {
            error_log('Trip create error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Seferi günceller
     * @param string $id
     * @param array $data Alanlar
     * @return bool
     */
    public function updateTrip($id, $data) {
        try {
            $fields = [];
            $params = ['id' => $id];
            $map = ['company_id','destination_city','arrival_time','departure_time','departure_city','price','capacity'];
            foreach ($map as $f) {
                if (isset($data[$f])) {
                    $fields[] = "$f = :$f";
                    $params[$f] = in_array($f, ['price','capacity']) ? (int)$data[$f] : $data[$f];
                }
            }
            if (empty($fields)) return false;
            $sql = 'UPDATE Trips SET ' . implode(', ', $fields) . ' WHERE id = :id';
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log('Trip update error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Seferi siler
     * İlgili tüm biletleri ve koltuk rezervasyonlarını da siler
     * Aktif biletler varsa kullanıcılara iade yapar
     */
    public function delete($id) {
        try {
            // Transaction başlat - tüm silme işlemleri başarılı olmazsa geri al
            $this->db->beginTransaction();
            
            // 1. Önce bu sefere ait aktif (canceled olmayan) biletleri al ve iade yap
            $stmt = $this->db->prepare('SELECT id, user_id, total_price FROM Tickets WHERE trip_id = :trip_id AND status != :status');
            $stmt->execute(['trip_id' => $id, 'status' => 'canceled']);
            $activeTickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($activeTickets as $ticket) {
                // Kullanıcıya para iade et
                $stmt = $this->db->prepare('UPDATE User SET balance = balance + :amount WHERE id = :user_id');
                $stmt->execute([
                    'amount' => $ticket['total_price'],
                    'user_id' => $ticket['user_id']
                ]);
            }
            
            // 2. Tüm biletleri al (iade yapıldıktan sonra)
            $stmt = $this->db->prepare('SELECT id FROM Tickets WHERE trip_id = :trip_id');
            $stmt->execute(['trip_id' => $id]);
            $ticketIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

            // 3. Biletlere ait koltuk rezervasyonlarını sil (sefer kaldırılacağı için koltuk kayıtlarına gerek yok)
            if (!empty($ticketIds)) {
                $placeholders = implode(',', array_fill(0, count($ticketIds), '?'));
                $stmt = $this->db->prepare("DELETE FROM Booked_Seats WHERE ticket_id IN ($placeholders)");
                $stmt->execute($ticketIds);
            }

            // 4. Biletleri tamamen sil
            $stmt = $this->db->prepare('DELETE FROM Tickets WHERE trip_id = :trip_id');
            $stmt->execute(['trip_id' => $id]);
            
            // 5. Seferi sil
            $stmt = $this->db->prepare('DELETE FROM Trips WHERE id = :id');
            $result = $stmt->execute(['id' => $id]);
            
            // Transaction tamamla
            $this->db->commit();
            return $result;
            
        } catch (PDOException $e) {
            // Hata durumunda transaction geri al
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('Trip delete error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Firmaya ait seferleri listeler
     */
    public function listByCompany($companyId) {
        try {
            $stmt = $this->db->prepare('SELECT * FROM Trips WHERE company_id = :cid ORDER BY departure_time DESC');
            $stmt->execute(['cid' => $companyId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Trip listByCompany error: ' . $e->getMessage());
            return [];
        }
    }
    /**
     * Popüler seferleri getirir
     * 
     * @param int $limit Kaç sefer getirileceği
     * @return array Sefer listesi
     */
    public function getPopularTrips($limit = 5) {
        try {
            // Popüler seferleri getir (en çok bilet satılan)
            $stmt = $this->db->prepare("
                SELECT t.*, bc.name as company_name, bc.logo_path as company_logo,
                       (SELECT COUNT(*) FROM Tickets WHERE trip_id = t.id) as ticket_count
                FROM Trips t
                JOIN Bus_Company bc ON t.company_id = bc.id
                WHERE t.departure_time > CURRENT_TIMESTAMP
                ORDER BY ticket_count DESC, t.departure_time ASC
                LIMIT :limit
            ");
            
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Popüler sefer sorgulama hatası: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Sefer ID'ye göre sefer bilgisini getirir
     * 
     * @param string $id Sefer ID
     * @return array|false Sefer bilgisi veya bulunamazsa false
     */
    public function find($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT t.*, bc.name as company_name, bc.logo_path as company_logo
                FROM Trips t
                JOIN Bus_Company bc ON t.company_id = bc.id
                WHERE t.id = :id
            ");
            
            $stmt->execute(['id' => $id]);
            $trip = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$trip) {
                return false;
            }
            
            // Rezerve edilmiş koltukları al
            $stmt = $this->db->prepare("
                SELECT bs.seat_number 
                FROM Booked_Seats bs
                JOIN Tickets tk ON bs.ticket_id = tk.id
                WHERE tk.trip_id = :trip_id AND tk.status = 'active'
            ");
            
            $stmt->execute(['trip_id' => $id]);
            $bookedSeats = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $trip['booked_seats'] = $bookedSeats;
            $trip['available_seats'] = $trip['capacity'] - count($bookedSeats);
            
            return $trip;
        } catch (PDOException $e) {
            error_log("Sefer bulma hatası: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Sefer detaylarını firma bilgisi ve rezerve koltuklarla getirir
     * 
     * @param string $id Sefer ID
     * @return array|false Sefer detay bilgisi veya bulunamazsa false
     */
    public function getTripWithDetails($id) {
        try {
            // Sefer ve firma bilgilerini getir
            $stmt = $this->db->prepare("
                SELECT t.*, bc.name as company_name, bc.logo_path as company_logo
                FROM Trips t
                JOIN Bus_Company bc ON t.company_id = bc.id
                WHERE t.id = :id
            ");
            
            $stmt->execute(['id' => $id]);
            $trip = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$trip) {
                return false;
            }
            
            // Rezerve edilmiş koltuk sayısını hesapla
            $stmt = $this->db->prepare("
                SELECT COUNT(DISTINCT bs.seat_number) as booked_count
                FROM Booked_Seats bs
                JOIN Tickets tk ON bs.ticket_id = tk.id
                WHERE tk.trip_id = :trip_id AND tk.status = 'active'
            ");
            
            $stmt->execute(['trip_id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $trip['booked_seats_count'] = (int)$result['booked_count'];
            $trip['available_seats'] = $trip['capacity'] - $trip['booked_seats_count'];
            
            return $trip;
        } catch (PDOException $e) {
            error_log("Sefer detayları getirme hatası: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Seferleri arar
     * 
     * @param string $departureCity Kalkış şehri
     * @param string $destinationCity Varış şehri
     * @param string $departureDate Kalkış tarihi (Y-m-d formatında)
     * @return array Sefer listesi
     */
    public function searchTrips($departureCity, $destinationCity, $departureDate) {
        try {
            // Tarih formatını kontrol et
            $date = new DateTime($departureDate);
            $formattedDate = $date->format('Y-m-d');
            
            $stmt = $this->db->prepare("
                SELECT t.*, bc.name as company_name, bc.logo_path as company_logo,
                       (SELECT COUNT(*) FROM Tickets tk JOIN Booked_Seats bs ON tk.id = bs.ticket_id 
                        WHERE tk.trip_id = t.id AND tk.status = 'active') as booked_seats_count
                FROM Trips t
                JOIN Bus_Company bc ON t.company_id = bc.id
                WHERE t.departure_city = :departure_city
                AND t.destination_city = :destination_city
                AND DATE(t.departure_time) = :departure_date
                AND t.departure_time > CURRENT_TIMESTAMP
                ORDER BY t.departure_time ASC
            ");
            
            $stmt->execute([
                'departure_city' => $departureCity,
                'destination_city' => $destinationCity,
                'departure_date' => $formattedDate
            ]);
            
            $trips = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Her sefer için boş koltuk sayısını hesapla
            foreach ($trips as $key => $trip) {
                $trips[$key]['available_seats'] = $trip['capacity'] - $trip['booked_seats_count'];
            }
            
            return $trips;
        } catch (PDOException $e) {
            error_log("Sefer arama hatası: " . $e->getMessage());
            return [];
        } catch (Exception $e) {
            error_log("Tarih formatı hatası: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Benzersiz şehir listesini getirir
     * 
     * @return array Şehir listesi
     */
    public function getUniqueCities() {
        try {
            $stmt = $this->db->prepare("
                SELECT DISTINCT departure_city as city FROM Trips
                UNION
                SELECT DISTINCT destination_city as city FROM Trips
                ORDER BY city ASC
            ");
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Şehir listesi hatası: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Benzersiz ID oluşturur (UUID v4)
     * 
     * @return string UUID
     */
    // UUID üretimi gerektiğinde Security::generateUUID ile merkezîleştirilecek.
}
?>