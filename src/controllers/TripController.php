<?php
/**
 * TripController - Sefer işlemleri için kontrol sınıfı
 * 
 * Bu sınıf, sefer arama, sefer detaylarını görüntüleme ve bilet satın alma işlemlerini yönetir
 */

class TripController {
    /**
     * Sefer arama formunu gösterir
     */
    public function showSearchForm() {
        // Trip modelini yükle
        require_once __DIR__ . '/../models/Trip.php';
        $tripModel = new Trip();
        
        // Şehir listesini getir
        $cities = $tripModel->getUniqueCities();
        
        // Arama formu görünümünü göster
        require_once __DIR__ . '/../views/layouts/app.php';
        require_once __DIR__ . '/../views/trips/search_form.php';
    }
    
    /**
     * Sefer araması yapar (GET ve POST destekler)
     */
    public function search() {
        // Eğer arama parametreleri yoksa, formu göster
        $departureCity = $_GET['departure_city'] ?? $_POST['departure_city'] ?? '';
        $destinationCity = $_GET['destination_city'] ?? $_POST['destination_city'] ?? '';
        $departureDate = $_GET['departure_date'] ?? $_POST['departure_date'] ?? '';
        
        // Eğer hiç parametre yoksa, arama formunu göster
        if (empty($departureCity) || empty($destinationCity)) {
            $this->showSearchForm();
            return;
        }
        
        // KONTROL: Kalkış ve varış şehirleri aynı olamaz
        if ($departureCity === $destinationCity) {
            Security::setFlashMessage('error', 'Kalkış ve varış şehirleri aynı olamaz!');
            Security::redirect(BASE_PATH . '/');
            exit();
        }
        
        // Trip modelini yükle
        require_once __DIR__ . '/../models/Trip.php';
        $tripModel = new Trip();
        
        // Tarih yoksa bugün kullan
        if (empty($departureDate)) {
            $departureDate = date('Y-m-d');
        }
        
        // KONTROL: Geçmiş tarih seçilemez
        if (strtotime($departureDate) < strtotime(date('Y-m-d'))) {
            Security::setFlashMessage('error', 'Geçmiş tarihli sefer araması yapamazsınız!');
            Security::redirect(BASE_PATH . '/');
            exit();
        }
        
        // Arama yap
        $trips = $tripModel->searchTrips($departureCity, $destinationCity, $departureDate);
        
        // Şehir listesini getir (form için)
        $cities = $tripModel->getUniqueCities();
        
        // Arama sonuçlarını göster
        $pageTitle = "Sefer Ara: {$departureCity} - {$destinationCity}";
        $content = __DIR__ . '/../views/trips/search_results.php';
        require_once __DIR__ . '/../views/layouts/app.php';
    }
    
    /**
     * Sefer detaylarını gösterir
     * 
     * @param string $id Sefer ID
     */
    public function details($id) {
        // Trip modelini yükle
        require_once __DIR__ . '/../models/Trip.php';
        $tripModel = new Trip();
        
        // Sefer bilgilerini getir (firma bilgisi ve koltuk sayısı ile)
        $trip = $tripModel->getTripWithDetails($id);
        
        // Sefer bulunamadı ise hata ver
        if (!$trip) {
            $_SESSION['flash_messages']['error'] = 'Sefer bulunamadı.';
            header('Location: /trips/search');
            exit;
        }
        
        // Sefer detay görünümünü göster
        $pageTitle = "Sefer Detayı - {$trip['departure_city']} - {$trip['destination_city']}";
        $content = __DIR__ . '/../views/trips/details.php';
        require_once __DIR__ . '/../views/layouts/app.php';
    }
    
    /**
     * Rezervasyon formunu gösterir (Bilet satın alma sayfası)
     * 
     * @param string $id Sefer ID
     */
    public function showBookingForm($id) {
        // Admin ve Company Admin bilet satın alamaz
        if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'company_admin', 'company'])) {
            Security::setFlashMessage('error', 'Admin ve Firma Yöneticileri bilet satın alamaz.');
            Security::redirect('/trips/details/' . $id);
        }
        
        // Ziyaretçi ise girişten sonra bu sayfaya dönmesi için yönlendirme bilgisini kaydet
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['redirect_after_login'] = '/trips/book/' . $id;
        }
        
        // Trip modelini yükle
        require_once __DIR__ . '/../models/Trip.php';
        $tripModel = new Trip();
        
        // Sefer bilgilerini getir
        $trip = $tripModel->find($id);
        
        // Sefer bulunamadı ise hata ver
        if (!$trip) {
            $_SESSION['flash_messages']['error'] = 'Sefer bulunamadı.';
            header('Location: /trips/search');
            exit;
        }
        
        // KONTROL: Geçmiş tarihli sefer için bilet alınamaz
        $departureDateTime = strtotime($trip['departure_time']);
        $currentDateTime = time();
        
        if ($departureDateTime <= $currentDateTime) {
            Security::setFlashMessage('error', 'Bu sefer için kalkış saati geçmiştir. Geçmiş tarihli sefer için bilet alamazsınız.');
            Security::redirect('/');
            exit;
        }
        
        // Eğer boş koltuk yoksa hata ver
        if ($trip['available_seats'] <= 0) {
            $_SESSION['flash_messages']['error'] = 'Bu seferde boş koltuk bulunmamaktadır.';
            header('Location: /trips/details/' . $id);
            exit;
        }
        
        // Kullanıcı bilgilerini getir (bakiye için - sadece giriş yapmışsa)
        $user = null;
        if (isset($_SESSION['user_id'])) {
            require_once __DIR__ . '/../models/User.php';
            $userModel = new User();
            $user = $userModel->find($_SESSION['user_id']);
            // Kullanıcı bakiyesini session'a kaydet
            $_SESSION['user_balance'] = $user['balance'] ?? 0;
        }
        
        // Rezerve edilmiş koltukları al
        $bookedSeats = $trip['booked_seats'] ?? [];
        
        // Bilet satın alma formunu göster (PHP-only, NO JavaScript)
        $pageTitle = "Bilet Rezervasyonu - {$trip['departure_city']} → {$trip['destination_city']}";
        $content = __DIR__ . '/../views/trips/booking_no_js.php';
        require_once __DIR__ . '/../views/layouts/app.php';
    }
    
    /**
     * Bilet satın alır
     * 
     * @param string $id Sefer ID
     */
    public function book($id) {
        // Kullanıcının oturum açmış olduğundan emin olalım
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash_messages']['error'] = 'Bilet satın alabilmek için giriş yapmalısınız.';
            $_SESSION['redirect_after_login'] = '/trips/book/' . $id;
            header('Location: /login');
            exit;
        }
        
        // Admin ve Company Admin bilet satın alamaz
        if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'company_admin', 'company'])) {
            Security::setFlashMessage('error', 'Admin ve Firma Yöneticileri bilet satın alamaz.');
            Security::redirect('/trips/details/' . $id);
        }
        
        // Trip modelini yükle
        require_once __DIR__ . '/../models/Trip.php';
        $tripModel = new Trip();
        
        // Sefer bilgilerini getir
        $trip = $tripModel->find($id);
        
        // Sefer bulunamadı ise hata ver
        if (!$trip) {
            $_SESSION['flash_messages']['error'] = 'Sefer bulunamadı.';
            header('Location: /trips/search');
            exit;
        }
        
        // KONTROL: Geçmiş tarihli sefer için bilet alınamaz
        $departureDateTime = strtotime($trip['departure_time']);
        $currentDateTime = time();
        
        if ($departureDateTime <= $currentDateTime) {
            Security::setFlashMessage('error', 'Bu sefer için kalkış saati geçmiştir. Geçmiş tarihli sefer için bilet alamazsınız.');
            Security::redirect('/');
            exit;
        }
        
        // Eğer sadece koltuk seçimi veya kupon uygulama ise, formu tekrar göster
        if ((isset($_POST['select_seats']) || isset($_POST['apply_coupon'])) && !isset($_POST['confirm'])) {
            return $this->showBookingForm($id);
        }
        
        // Seçilen koltukları al (checkbox array veya string olabilir)
        $selectedSeats = [];
        if (isset($_POST['seats'])) {
            if (is_array($_POST['seats'])) {
                // Checkbox array'i
                $selectedSeats = array_map('intval', $_POST['seats']);
            } else {
                // String (eski format için backward compatibility)
                $seatsString = $_POST['seats'];
                $selectedSeats = !empty($seatsString) ? explode(',', $seatsString) : [];
                $selectedSeats = array_map('intval', $selectedSeats);
            }
        }
        
        // Koltuk seçilmemişse hata ver
        if (empty($selectedSeats)) {
            $_SESSION['flash_messages']['error'] = 'Lütfen en az bir koltuk seçiniz.';
            header('Location: /trips/book/' . $id);
            exit;
        }
        
        // Seçilen koltukların rezerve edilmediğinden emin ol
        foreach ($selectedSeats as $seat) {
            if (in_array($seat, $trip['booked_seats'])) {
                $_SESSION['flash_messages']['error'] = 'Seçtiğiniz koltuklardan biri başkası tarafından rezerve edilmiş.';
                header('Location: /trips/book/' . $id);
                exit;
            }
        }
        
        // Toplam fiyatı hesapla
        $totalPrice = $trip['price'] * count($selectedSeats);
        
        // Kupon kodu varsa kontrol et ve indirimi uygula
        $couponCode = $_POST['coupon_code'] ?? '';
        $discountAmount = 0;
        $appliedCouponId = null;
        
        if (!empty($couponCode)) {
            require_once __DIR__ . '/../models/Coupon.php';
            $couponModel = new Coupon();
            $couponValidation = $couponModel->validateCoupon($couponCode, $id, $_SESSION['user_id']);
            
            if ($couponValidation && $couponValidation['is_valid']) {
                $discountAmount = $totalPrice * ($couponValidation['discount_percent'] / 100);
                $totalPrice -= $discountAmount;
                $appliedCouponId = $couponValidation['id'];
            }
        }
        
        // Kullanıcı modelini yükle
        require_once __DIR__ . '/../models/User.php';
        $userModel = new User();
        $user = $userModel->find($_SESSION['user_id']);
        
        // Kullanıcının yeterli bakiyesi var mı kontrol et
        if ($user['balance'] < $totalPrice) {
            $_SESSION['flash_messages']['error'] = 'Yetersiz bakiye. Lütfen hesabınıza para yükleyin.';
            header('Location: /trips/book/' . $id);
            exit;
        }
        
        // Bilet oluştur
        require_once __DIR__ . '/../models/Ticket.php';
        $ticketModel = new Ticket();
        
        $ticketData = [
            'trip_id' => $trip['id'],
            'user_id' => $_SESSION['user_id'],
            'status' => 'active',
            'total_price' => $totalPrice,
            'seats' => $selectedSeats
        ];
        
        $ticketId = $ticketModel->create($ticketData);
        
        if ($ticketId) {
            // Kullanıcı bakiyesini güncelle
            $newBalance = $user['balance'] - $totalPrice;
            $userModel->updateBalance($_SESSION['user_id'], $newBalance);
            
            // Kupon kullanıldıysa kaydet
            if ($appliedCouponId) {
                require_once __DIR__ . '/../models/Coupon.php';
                $couponModel = new Coupon();
                $couponModel->recordUsage($appliedCouponId, $_SESSION['user_id']);
            }
            
            $_SESSION['flash_messages']['success'] = 'Biletiniz başarıyla oluşturuldu.' . 
                ($discountAmount > 0 ? ' (' . number_format($discountAmount, 2) . ' ₺ kupon indirimi uygulandı)' : '');
            header('Location: /tickets/view/' . $ticketId);
        } else {
            $_SESSION['flash_messages']['error'] = 'Bilet oluşturulurken bir hata oluştu.';
            header('Location: /trips/book/' . $id);
        }
        
        exit;
    }
}
?>