<?php
/**
 * CouponController - Kupon işlemleri için kontrol sınıfı
 * 
 * Bu sınıf kupon doğrulama ve uygulama işlemlerini yönetir
 */

class CouponController {
    /**
     * AJAX ile kupon kodunu doğrular
     * 
     * POST /api/coupons/validate
     * Body: { "code": "KUPONKODU", "trip_id": "sefer_id" }
     * 
     * Response:
     * - Success: { "valid": true, "discount": 20, "message": "%20 indirim uygulandı" }
     * - Error: { "valid": false, "message": "Geçersiz kupon kodu" }
     */
    public function validate() {
        // Sadece POST isteklerini kabul et
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['valid' => false, 'message' => 'Method not allowed']);
            exit;
        }

        // JSON response için header ayarla
        header('Content-Type: application/json');

        // Kullanıcının oturum açmış olduğundan emin ol
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['valid' => false, 'message' => 'Lütfen giriş yapın']);
            exit;
        }

        // POST verilerini al
        $input = json_decode(file_get_contents('php://input'), true);
        $code = strtoupper(trim($input['code'] ?? ''));
        $tripId = $input['trip_id'] ?? '';

        // Kupon kodu boş mu kontrol et
        if (empty($code)) {
            echo json_encode(['valid' => false, 'message' => 'Lütfen bir kupon kodu girin']);
            exit;
        }

        // Sefer ID boş mu kontrol et
        if (empty($tripId)) {
            echo json_encode(['valid' => false, 'message' => 'Geçersiz sefer']);
            exit;
        }

        // Coupon modelini yükle
        require_once __DIR__ . '/../models/Coupon.php';
        $couponModel = new Coupon();

        // Kupon kodunu doğrula
        $coupon = $couponModel->validateCoupon($code, $tripId, $_SESSION['user_id']);

        if ($coupon === false) {
            echo json_encode(['valid' => false, 'message' => 'Geçersiz kupon kodu']);
            exit;
        }

        // Kupon geçerliliğini kontrol et
        if (!$coupon['is_valid']) {
            echo json_encode([
                'valid' => false, 
                'message' => $coupon['error_message'] ?? 'Kupon kullanılamaz'
            ]);
            exit;
        }

        // Başarılı - kupon bilgilerini döndür
        echo json_encode([
            'valid' => true,
            'discount' => (int)$coupon['discount_percent'],
            'coupon_id' => $coupon['id'],
            'message' => '%' . $coupon['discount_percent'] . ' indirim uygulandı!'
        ]);
        exit;
    }

    /**
     * Kullanıcının kullanılabilir kuponlarını listeler
     * 
     * GET /api/coupons/available?trip_id=xxx
     */
    public function available() {
        // JSON response için header ayarla
        header('Content-Type: application/json');

        // Kullanıcının oturum açmış olduğundan emin ol
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Lütfen giriş yapın']);
            exit;
        }

        $tripId = $_GET['trip_id'] ?? '';

        // Coupon modelini yükle
        require_once __DIR__ . '/../models/Coupon.php';
        $couponModel = new Coupon();

        // Kullanılabilir kuponları getir
        $coupons = $couponModel->getAvailableCouponsForUser($_SESSION['user_id'], $tripId);

        echo json_encode([
            'success' => true,
            'coupons' => $coupons
        ]);
        exit;
    }
}
?>
