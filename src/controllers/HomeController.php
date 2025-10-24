<?php
/**
 * HomeController - Ana sayfa ve genel site işlemleri için kontrol sınıfı
 * 
 * Bu sınıf, ana sayfa görüntüleme ve genel site işlemlerini yönetir
 */

class HomeController {
    /**
     * Ana sayfayı gösterir
     */
    public function index() {
        // Popüler seferleri getir
        require_once __DIR__ . '/../models/Trip.php';
        $tripModel = new Trip();
        $popularTrips = $tripModel->getPopularTrips(5);
        
        // View değişkenlerini hazırla
        $pageTitle = 'Ana Sayfa - BiBilet';
        
        // Ana sayfa görünümünü göster
        // Layout içinde home.php include edilecek
        $content = __DIR__ . '/../views/home.php';
        require_once __DIR__ . '/../views/layouts/app.php';
    }
}
?>