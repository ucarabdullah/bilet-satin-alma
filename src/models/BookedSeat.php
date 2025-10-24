<?php
/**
 * BookedSeat - Rezerve edilmiş koltuk işlemlerini yöneten model sınıfı
 * 
 * Bu sınıf koltuk rezervasyon işlemlerini yönetir:
 * - Koltuk rezervasyonu
 * - Koltuk durumu kontrolü
 * - Dolu koltuk listesi
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../helpers/Security.php';

class BookedSeat {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Belirli bir bilet için rezerve edilmiş koltukları getirir
     * 
     * @param string $ticketId Bilet ID
     * @return array Koltuk numaraları
     */
    public function getByTicket($ticketId) {
        try {
            $stmt = $this->db->prepare("
                SELECT seat_number FROM Booked_Seats
                WHERE ticket_id = :ticket_id
                ORDER BY seat_number ASC
            ");
            
            $stmt->execute(['ticket_id' => $ticketId]);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Bilet koltukları sorgulama hatası: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Belirli bir sefer için dolu koltukları getirir
     * 
     * @param string $tripId Sefer ID
     * @return array Dolu koltuk numaraları
     */
    public function getBookedSeatsForTrip($tripId) {
        try {
            $stmt = $this->db->prepare("
                SELECT bs.seat_number
                FROM Booked_Seats bs
                JOIN Tickets t ON bs.ticket_id = t.id
                WHERE t.trip_id = :trip_id AND t.status = 'active'
                ORDER BY bs.seat_number ASC
            ");
            
            $stmt->execute(['trip_id' => $tripId]);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Sefer dolu koltukları sorgulama hatası: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Koltukların dolu olup olmadığını kontrol eder
     * 
     * @param string $tripId Sefer ID
     * @param array $seatNumbers Kontrol edilecek koltuk numaraları
     * @return bool Tüm koltuklar boşsa true, herhangi biri doluysa false
     */
    public function areSeatsAvailable($tripId, $seatNumbers) {
        $bookedSeats = $this->getBookedSeatsForTrip($tripId);
        
        foreach ($seatNumbers as $seatNumber) {
            if (in_array($seatNumber, $bookedSeats)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Koltuk rezervasyonu oluşturur
     * 
     * @param string $ticketId Bilet ID
     * @param array $seatNumbers Rezerve edilecek koltuk numaraları
     * @return bool Başarılı ise true, değilse false
     */
    public function create($ticketId, $seatNumbers) {
        try {
            $this->db->beginTransaction();
            
            $stmt = $this->db->prepare("
                INSERT INTO Booked_Seats (id, ticket_id, seat_number, created_at)
                VALUES (:id, :ticket_id, :seat_number, CURRENT_TIMESTAMP)
            ");
            
            foreach ($seatNumbers as $seatNumber) {
                $id = Security::generateUUID();
                
                $stmt->execute([
                    'id' => $id,
                    'ticket_id' => $ticketId,
                    'seat_number' => $seatNumber
                ]);
            }
            
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollback();
            error_log("Koltuk rezervasyonu hatası: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Bilet iptali durumunda koltuk rezervasyonlarını siler
     * 
     * @param string $ticketId Bilet ID
     * @return bool Başarılı ise true, değilse false
     */
    public function deleteByTicket($ticketId) {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM Booked_Seats WHERE ticket_id = :ticket_id
            ");
            
            return $stmt->execute(['ticket_id' => $ticketId]);
        } catch (PDOException $e) {
            error_log("Koltuk rezervasyonu silme hatası: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Sefer için kapasite ve dolu koltuk bilgilerini getirir
     * 
     * @param string $tripId Sefer ID
     * @param int $capacity Sefer kapasitesi
     * @return array Kapasite bilgileri ['capacity' => int, 'booked' => int, 'available' => int, 'bookedSeats' => array]
     */
    public function getTripCapacityInfo($tripId, $capacity) {
        $bookedSeats = $this->getBookedSeatsForTrip($tripId);
        $bookedCount = count($bookedSeats);
        
        return [
            'capacity' => $capacity,
            'booked' => $bookedCount,
            'available' => $capacity - $bookedCount,
            'bookedSeats' => $bookedSeats
        ];
    }
    
    /**
     * Benzersiz ID oluşturur (UUID v4)
     * 
     * @return string UUID
     */
    // UUID üretimi Security::generateUUID ile merkezi hale getirildi.
}
?>
