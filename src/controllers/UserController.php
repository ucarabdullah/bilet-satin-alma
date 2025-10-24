<?php
/**
 * UserController - Kullanıcı paneli işlemleri için kontrol sınıfı
 * 
 * Bu sınıf, kullanıcı paneli işlemlerini yönetir:
 * - Kullanıcı dashboard görüntüleme
 * - Bilet geçmişi listeleme
 * - Profil görüntüleme ve güncelleme
 * - Bilet satın alma
 */

class UserController {
    /**
     * Ensure user is logged in and has 'user' or 'company'/'company_admin' role
     * 
     * Normal kullanıcılar (role='user') ve Firma Admin'ler (role='company'/'company_admin') 
     * bu sayfalara erişebilir (bilet görüntüleme, profil, vs.)
     */
    private function ensureUser() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        // User ve company/company_admin erişebilir
        $allowedRoles = ['user', 'company_admin', 'company'];
        if (!in_array($_SESSION['user_role'] ?? '', $allowedRoles)) {
            $_SESSION['flash_messages']['error'] = 'Bu sayfaya erişim yetkiniz yok.';
            
            // Role göre yönlendir
            if (($_SESSION['user_role'] ?? '') === 'admin') {
                header('Location: /admin/dashboard');
            } else {
                header('Location: /');
            }
            exit;
        }
    }
    
    /**
     * Kullanıcı paneli ana sayfasını gösterir
     */
    public function dashboard() {
        $this->ensureUser();
        
        // Kullanıcı bilgilerini veritabanından alalım
        require_once __DIR__ . '/../models/User.php';
        require_once __DIR__ . '/../models/Ticket.php';
        
        $userModel = new User();
        $ticketModel = new Ticket();
        
        $user = $userModel->find($_SESSION['user_id']);
        
        if (!$user) {
            // Kullanıcı bulunamadıysa oturumu sonlandır
            session_destroy();
            header('Location: /login');
            exit;
        }
        
        // Kullanıcının aktif biletlerini getir
        $activeTickets = $ticketModel->getUserActiveTickets($_SESSION['user_id']);
        
        // Kullanıcının yaklaşan seyahatlerini getir (son 5)
        $upcomingTrips = $ticketModel->getUserUpcomingTrips($_SESSION['user_id'], 5);
        
        // Kullanıcının geçmiş seyahatlerini getir (son 5)
        $pastTrips = $ticketModel->getUserPastTrips($_SESSION['user_id'], 5);
        
        // Dashboard sayfasını göster
        $pageTitle = 'Kullanıcı Paneli - BiBilet';
        $content = __DIR__ . '/../views/user/dashboard.php';
        require_once __DIR__ . '/../views/layouts/app.php';
    }
    
    /**
     * Kullanıcının bilet geçmişini gösterir
     */
    public function ticketHistory() {
        $this->ensureUser();
        
        // Gerekli modelleri yükle
        require_once __DIR__ . '/../models/User.php';
        require_once __DIR__ . '/../models/Ticket.php';
        
        $userModel = new User();
        $ticketModel = new Ticket();
        
        // Sayfalama için parametreler
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        // Kullanıcının bilet geçmişini getir
        $tickets = $ticketModel->getUserTickets($_SESSION['user_id'], $limit, $offset);
        $totalTickets = $ticketModel->countUserTickets($_SESSION['user_id']);
        
        // Toplam sayfa sayısını hesapla
        $totalPages = ceil($totalTickets / $limit);
        
        // Bilet geçmişi sayfasını göster
        $pageTitle = 'Biletlerim - BiBilet';
        $content = __DIR__ . '/../views/user/ticket_history.php';
        require_once __DIR__ . '/../views/layouts/app.php';
    }
    
    /**
     * Kullanıcı profilini gösterir
     */
    public function profile() {
        $this->ensureUser();
        
        // Gerekli modelleri yükle
        require_once __DIR__ . '/../models/User.php';
        require_once __DIR__ . '/../models/Ticket.php';
        $userModel = new User();
        $ticketModel = new Ticket();
        
        // Kullanıcı bilgilerini getir
        $user = $userModel->find($_SESSION['user_id']);
        
        if (!$user) {
            session_destroy();
            header('Location: /login');
            exit;
        }
        
        // Profil güncelleme işlemi
        $errors = [];
        $success = false;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Form gönderilmiş, bilgileri güncelleyelim
            $fullName = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            // Validasyonlar
            if (empty($fullName)) {
                $errors[] = 'Ad Soyad alanı boş bırakılamaz.';
            }
            
            if (empty($email)) {
                $errors[] = 'E-posta alanı boş bırakılamaz.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Geçerli bir e-posta adresi giriniz.';
            }
            
            // E-posta adresi değiştirilmek isteniyorsa, kullanılabilir olup olmadığını kontrol et
            if ($email !== $user['email']) {
                if ($userModel->emailExists($email)) {
                    $errors[] = 'Bu e-posta adresi başka bir kullanıcı tarafından kullanılmaktadır.';
                }
            }
            
            // Şifre değiştirilecekse kontrol et
            if (!empty($currentPassword) || !empty($newPassword) || !empty($confirmPassword)) {
                // Mevcut şifre doğru mu kontrol et
                if (!Security::verifyPassword($currentPassword, $user['password'])) {
                    $errors[] = 'Mevcut şifreniz hatalı.';
                }
                
                // Yeni şifre kontrolü
                if (empty($newPassword)) {
                    $errors[] = 'Yeni şifre alanı boş bırakılamaz.';
                } elseif (strlen($newPassword) < 8) {
                    $errors[] = 'Yeni şifre en az 8 karakter olmalıdır.';
                }
                
                // Şifre onay kontrolü
                if ($newPassword !== $confirmPassword) {
                    $errors[] = 'Yeni şifre ve şifre onayı eşleşmiyor.';
                }
            }
            
            // Hata yoksa güncelleme yap
            if (empty($errors)) {
                $updateData = [
                    'full_name' => $fullName,
                    'email' => $email
                ];
                
                // Şifre değiştirilecekse ekle
                if (!empty($newPassword)) {
                    $updateData['password'] = $newPassword;
                }
                
                // Güncelleme işlemini yap
                if ($userModel->update($user['id'], $updateData)) {
                    $success = true;
                    
                    // Güncellenmiş kullanıcı bilgilerini getir
                    $user = $userModel->find($_SESSION['user_id']);
                } else {
                    $errors[] = 'Profil güncellenirken bir hata oluştu.';
                }
            }
        }
        
        // İptal edilen son biletler (profilde gösterim için)
        $canceledTickets = $ticketModel->getUserCanceledTickets($_SESSION['user_id'], 5);

        // Profil sayfasını göster
        $pageTitle = 'Profilim - BiBilet';
        $content = __DIR__ . '/../views/user/profile.php';
        require_once __DIR__ . '/../views/layouts/app.php';
    }
}
?>