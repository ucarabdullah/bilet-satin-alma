<?php
class CompanyController {
    private function ensureCompanyAdmin() {
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== ROLE_COMPANY_ADMIN) {
            header('Location: /company/login'); exit;
        }
    }

    /**
     * Company admin login page
     */
    public function login() {
        // If already logged in as company admin, redirect to dashboard
        if (isset($_SESSION['user_id']) && ($_SESSION['user_role'] ?? '') === ROLE_COMPANY_ADMIN) {
            header('Location: /company/dashboard'); exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
                $_SESSION['flash_messages']['error'] = 'CSRF doğrulaması başarısız.';
                header('Location: /company/login'); exit;
            }
            
            require_once __DIR__ . '/../models/User.php';
            require_once __DIR__ . '/../models/Company.php';
            $userModel = new User();
            $companyModel = new Company();
            
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            
            $user = $userModel->login($email, $password);
            
            if ($user && $user['role'] === ROLE_COMPANY_ADMIN) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['company_id'] = $user['company_id'];
                
                // Get company name
                if ($user['company_id']) {
                    $company = $companyModel->find($user['company_id']);
                    $_SESSION['company_name'] = $company['name'] ?? 'Firma';
                }
                
                $_SESSION['flash_messages']['success'] = 'Hoş geldiniz, ' . $user['full_name'];
                header('Location: /company/dashboard'); exit;
            } else {
                $_SESSION['flash_messages']['error'] = 'Geçersiz firma admin bilgileri veya yetkiniz yok.';
                header('Location: /company/login'); exit;
            }
        }
        
        // Show company login view (without layout)
        require_once __DIR__ . '/../views/company/login.php';
    }

    public function dashboard() {
        $this->ensureCompanyAdmin();
        
        // Gather statistics
        require_once __DIR__ . '/../models/Trip.php';
        require_once __DIR__ . '/../models/Coupon.php';
        
        $tripModel = new Trip();
        $couponModel = new Coupon();
        $companyId = $_SESSION['company_id'] ?? null;
        
        $stats = [
            'trips' => $companyId ? count($tripModel->listByCompany($companyId)) : 0,
            'coupons' => $companyId ? count($couponModel->getByCompany($companyId)) : 0,
            'bookings' => 0,
            'revenue' => 0
        ];
        
        $recentTrips = $companyId ? array_slice($tripModel->listByCompany($companyId), 0, 5) : [];
        
        $pageTitle = 'Dashboard';
        $content = __DIR__ . '/../views/company/dashboard.php';
        require_once __DIR__ . '/../views/layouts/company.php';
    }

    public function trips() {
        $this->ensureCompanyAdmin();
        require_once __DIR__ . '/../models/Trip.php';
        $tripModel = new Trip();
        $companyId = $_SESSION['company_id'] ?? null;
        $trips = $companyId ? $tripModel->listByCompany($companyId) : [];
        $pageTitle = 'Seferler';
        $content = __DIR__ . '/../views/company/trips.php';
        require_once __DIR__ . '/../views/layouts/company.php';
    }

    public function createTrip() {
        $this->ensureCompanyAdmin();
        require_once __DIR__ . '/../models/Trip.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
                $_SESSION['flash_messages']['error'] = 'CSRF doğrulaması başarısız.'; 
                header('Location: /company/trips/new'); 
                exit;
            }
            $tripModel = new Trip();
            // 24 saatlik sistem: select kutularından saat-dakika al ve birleştir
            $depDate = trim($_POST['departure_date'] ?? '');
            $depHour = trim($_POST['departure_hour'] ?? '');
            $depMin = trim($_POST['departure_minute'] ?? '');
            $arrDate = trim($_POST['arrival_date'] ?? '');
            $arrHour = trim($_POST['arrival_hour'] ?? '');
            $arrMin = trim($_POST['arrival_minute'] ?? '');
            
            $departure_dt = ($depDate && $depHour !== '' && $depMin !== '') 
                ? ($depDate . ' ' . $depHour . ':' . $depMin . ':00') 
                : '';
            $arrival_dt = ($arrDate && $arrHour !== '' && $arrMin !== '') 
                ? ($arrDate . ' ' . $arrHour . ':' . $arrMin . ':00') 
                : '';

            $data = [
                'company_id' => $_SESSION['company_id'] ?? '',
                'departure_city' => trim($_POST['departure_city'] ?? ''),
                'destination_city' => trim($_POST['destination_city'] ?? ''),
                'departure_time' => $departure_dt,
                'arrival_time' => $arrival_dt,
                'price' => (int)($_POST['price'] ?? 0),
                'capacity' => (int)($_POST['capacity'] ?? 0),
            ];
            if (!$data['company_id'] || !$data['departure_city'] || !$data['destination_city'] || !$data['departure_time'] || !$data['arrival_time'] || $data['price'] <= 0 || $data['capacity'] <= 0) {
                $_SESSION['flash_messages']['error'] = 'Zorunlu alanlar eksik.'; 
                header('Location: /company/trips/new'); 
                exit;
            }
            // Zaman doğrulaması: varış > kalkış olmalı
            if (strtotime($data['arrival_time']) <= strtotime($data['departure_time'])) {
                $_SESSION['flash_messages']['error'] = 'Varış zamanı, kalkış zamanından sonra olmalıdır.';
                header('Location: /company/trips/new');
                exit;
            }
            $id = $tripModel->create($data);
            $_SESSION['flash_messages'][$id ? 'success' : 'error'] = $id ? 'Sefer oluşturuldu.' : 'Sefer oluşturulamadı.';
            header('Location: /company/trips'); exit;
        }
        $pageTitle = 'Yeni Sefer';
        $content = __DIR__ . '/../views/company/trip_form.php';
        require_once __DIR__ . '/../views/layouts/company.php';
    }

    public function editTrip($id) {
        $this->ensureCompanyAdmin();
        require_once __DIR__ . '/../models/Trip.php';
        $tripModel = new Trip();
        $trip = $tripModel->find($id);
        if (!$trip || ($trip['company_id'] ?? null) !== ($_SESSION['company_id'] ?? null)) { 
            $_SESSION['flash_messages']['error'] = 'Sefer bulunamadı.'; 
            header('Location: /company/trips'); 
            exit; 
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
                $_SESSION['flash_messages']['error'] = 'CSRF doğrulaması başarısız.'; 
                header('Location: /company/trips/edit/' . $id); 
                exit;
            }
            // 24 saatlik sistem: select kutularından saat-dakika al ve birleştir
            $depDate = trim($_POST['departure_date'] ?? '');
            $depHour = trim($_POST['departure_hour'] ?? '');
            $depMin = trim($_POST['departure_minute'] ?? '');
            $arrDate = trim($_POST['arrival_date'] ?? '');
            $arrHour = trim($_POST['arrival_hour'] ?? '');
            $arrMin = trim($_POST['arrival_minute'] ?? '');
            
            $departure_dt = ($depDate && $depHour !== '' && $depMin !== '') 
                ? ($depDate . ' ' . $depHour . ':' . $depMin . ':00') 
                : '';
            $arrival_dt = ($arrDate && $arrHour !== '' && $arrMin !== '') 
                ? ($arrDate . ' ' . $arrHour . ':' . $arrMin . ':00') 
                : '';

            $data = [
                'departure_city' => trim($_POST['departure_city'] ?? ''),
                'destination_city' => trim($_POST['destination_city'] ?? ''),
                'departure_time' => $departure_dt,
                'arrival_time' => $arrival_dt,
                'price' => (int)($_POST['price'] ?? 0),
                'capacity' => (int)($_POST['capacity'] ?? 0),
            ];
            if (!$data['departure_city'] || !$data['destination_city'] || !$data['departure_time'] || !$data['arrival_time'] || $data['price'] <= 0 || $data['capacity'] <= 0) {
                $_SESSION['flash_messages']['error'] = 'Zorunlu alanlar eksik.'; 
                header('Location: /company/trips/edit/' . $id); 
                exit;
            }
            if (strtotime($data['arrival_time']) <= strtotime($data['departure_time'])) {
                $_SESSION['flash_messages']['error'] = 'Varış zamanı, kalkış zamanından sonra olmalıdır.';
                header('Location: /company/trips/edit/' . $id);
                exit;
            }
            $ok = $tripModel->updateTrip($id, $data);
            $_SESSION['flash_messages'][$ok ? 'success' : 'error'] = $ok ? 'Sefer güncellendi.' : 'Güncelleme başarısız.';
            header('Location: /company/trips'); 
            exit;
        }
        $pageTitle = 'Sefer Düzenle';
        $content = __DIR__ . '/../views/company/trip_form.php';
        require_once __DIR__ . '/../views/layouts/company.php';
    }

    public function deleteTrip($id) {
        $this->ensureCompanyAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
                $_SESSION['flash_messages']['error'] = 'CSRF doğrulaması başarısız.'; header('Location: /company/trips'); exit;
            }
        }
        require_once __DIR__ . '/../models/Trip.php';
        $tripModel = new Trip();
        $trip = $tripModel->find($id);
        if (!$trip || ($trip['company_id'] ?? null) !== ($_SESSION['company_id'] ?? null)) {
            $_SESSION['flash_messages']['error'] = 'Sefer bulunamadı.'; header('Location: /company/trips'); exit;
        }
        $ok = $tripModel->delete($id);
        $_SESSION['flash_messages'][$ok ? 'success' : 'error'] = $ok ? 'Sefer silindi.' : 'Silme başarısız.';
        header('Location: /company/trips'); exit;
    }

    public function coupons() {
        $this->ensureCompanyAdmin();
        require_once __DIR__ . '/../models/Coupon.php';
        $couponModel = new Coupon();
        $companyId = $_SESSION['company_id'] ?? null;
        $coupons = $companyId ? $couponModel->getByCompany($companyId) : [];
        $pageTitle = 'Kuponlar';
        $content = __DIR__ . '/../views/company/coupons.php';
        require_once __DIR__ . '/../views/layouts/company.php';
    }

    public function createCoupon() {
        $this->ensureCompanyAdmin();
        require_once __DIR__ . '/../models/Coupon.php';
        $couponModel = new Coupon();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
                $_SESSION['flash_messages']['error'] = 'CSRF doğrulaması başarısız.'; header('Location: /company/coupons'); exit;
            }
            $data = [
                'company_id' => $_SESSION['company_id'] ?? null,
                'code' => trim($_POST['code'] ?? ''),
                'discount' => (float)($_POST['discount'] ?? 0),
                'usage_limit' => (int)($_POST['usage_limit'] ?? 1),
                'expire_date' => trim($_POST['expire_date'] ?? '')
            ];
            if (!$data['code'] || $data['discount'] <= 0 || $data['usage_limit'] <= 0 || !$data['expire_date']) {
                $_SESSION['flash_messages']['error'] = 'Zorunlu alanlar eksik.'; header('Location: /company/coupons'); exit;
            }
            $id = $couponModel->create($data);
            $_SESSION['flash_messages'][$id ? 'success' : 'error'] = $id ? 'Kupon oluşturuldu.' : 'Kupon oluşturulamadı.';
            header('Location: /company/coupons'); exit;
        }
        $pageTitle = 'Yeni Kupon';
        $content = __DIR__ . '/../views/company/coupon_form.php';
        require_once __DIR__ . '/../views/layouts/company.php';
    }

    public function editCoupon($id) {
        $this->ensureCompanyAdmin();
        require_once __DIR__ . '/../models/Coupon.php';
        $couponModel = new Coupon();
        $coupon = $couponModel->find($id);
        
        if (!$coupon) {
            $_SESSION['flash_messages']['error'] = 'Kupon bulunamadı.';
            header('Location: /company/coupons');
            exit;
        }
        
        // Admin tarafından oluşturulan kuponlar (company_id = NULL) firma adminleri tarafından düzenlenemez
        if ($coupon['company_id'] === null) {
            $_SESSION['flash_messages']['error'] = 'Admin tarafından oluşturulan kuponlar düzenlenemez.';
            header('Location: /company/coupons');
            exit;
        }
        
        // Firma admini sadece kendi firmasının kuponlarını düzenleyebilir
        if ($coupon['company_id'] !== ($_SESSION['company_id'] ?? null)) {
            $_SESSION['flash_messages']['error'] = 'Bu kuponu düzenleme yetkiniz yok.';
            header('Location: /company/coupons');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
                $_SESSION['flash_messages']['error'] = 'CSRF doğrulaması başarısız.'; header('Location: /company/coupons'); exit;
            }
            $data = [
                'code' => trim($_POST['code'] ?? ''),
                'discount' => (float)($_POST['discount'] ?? 0),
                'usage_limit' => (int)($_POST['usage_limit'] ?? 1),
                'expire_date' => trim($_POST['expire_date'] ?? '')
            ];
            $ok = $couponModel->update($id, $data);
            $_SESSION['flash_messages'][$ok ? 'success' : 'error'] = $ok ? 'Kupon güncellendi.' : 'Güncelleme başarısız.';
            header('Location: /company/coupons'); exit;
        }
        $pageTitle = 'Kupon Düzenle';
        $content = __DIR__ . '/../views/company/coupon_form.php';
        require_once __DIR__ . '/../views/layouts/company.php';
    }

    public function deleteCoupon($id) {
        $this->ensureCompanyAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
                $_SESSION['flash_messages']['error'] = 'CSRF doğrulaması başarısız.'; 
                header('Location: /company/coupons'); 
                exit;
            }
        }
        require_once __DIR__ . '/../models/Coupon.php';
        $couponModel = new Coupon();
        $coupon = $couponModel->find($id);
        
        if (!$coupon) {
            $_SESSION['flash_messages']['error'] = 'Kupon bulunamadı.';
            header('Location: /company/coupons');
            exit;
        }
        
        // Admin tarafından oluşturulan kuponlar (company_id = NULL) firma adminleri tarafından silinemez
        if ($coupon['company_id'] === null) {
            $_SESSION['flash_messages']['error'] = 'Admin tarafından oluşturulan kuponlar silinemez.';
            header('Location: /company/coupons');
            exit;
        }
        
        // Firma admini sadece kendi firmasının kuponlarını silebilir
        if ($coupon['company_id'] !== ($_SESSION['company_id'] ?? null)) {
            $_SESSION['flash_messages']['error'] = 'Bu kuponu silme yetkiniz yok.';
            header('Location: /company/coupons');
            exit;
        }
        
        $ok = $couponModel->delete($id);
        $_SESSION['flash_messages'][$ok ? 'success' : 'error'] = $ok ? 'Kupon silindi.' : 'Silme başarısız.';
        header('Location: /company/coupons'); exit;
    }
    
    /**
     * Firma biletlerini listeler (müşteri bilgileri ile)
     */
    public function tickets() {
        $this->ensureCompanyAdmin();
        
        require_once __DIR__ . '/../models/Ticket.php';
        $ticketModel = new Ticket();
        $companyId = $_SESSION['company_id'] ?? null;
        
        if (!$companyId) {
            $_SESSION['flash_messages']['error'] = 'Firma bilgisi bulunamadı.';
            header('Location: /company/dashboard');
            exit;
        }
        
        // Sayfalama
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        // Firma biletlerini getir
        $tickets = $ticketModel->getTicketsByCompanyId($companyId, $limit, $offset);
        $totalTickets = $ticketModel->countTicketsByCompanyId($companyId);
        $totalPages = ceil($totalTickets / $limit);
        
        $pageTitle = 'Firma Biletleri';
        $content = __DIR__ . '/../views/company/tickets.php';
        require_once __DIR__ . '/../views/layouts/company.php';
    }
    
    /**
     * Firma admin bilet iptal eder
     */
    public function cancelTicket($id) {
        $this->ensureCompanyAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['flash_messages']['error'] = 'Geçersiz istek.';
            header('Location: /company/tickets');
            exit;
        }
        
        if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
            $_SESSION['flash_messages']['error'] = 'CSRF doğrulaması başarısız.';
            header('Location: /company/tickets');
            exit;
        }
        
        require_once __DIR__ . '/../models/Ticket.php';
        
        $ticketModel = new Ticket();
        $companyId = $_SESSION['company_id'] ?? null;
        
        // Bileti iptal et (para iadesi otomatik yapılıyor)
        $success = $ticketModel->cancelTicketByCompany($id, $companyId);
        
        if ($success) {
            $_SESSION['flash_messages']['success'] = 'Bilet iptal edildi ve müşteriye iade yapıldı.';
        } else {
            $_SESSION['flash_messages']['error'] = 'Bilet iptal edilemedi. Kalkışa 1 saatten az süre kalmış olabilir veya bilet zaten iptal edilmiş olabilir.';
        }
        
        header('Location: /company/tickets');
        exit;
    }
    
    /**
     * Belirli bir seferin biletlerini gösterir
     */
    public function viewTripTickets($tripId) {
        $this->ensureCompanyAdmin();
        
        require_once __DIR__ . '/../models/Trip.php';
        require_once __DIR__ . '/../models/Ticket.php';
        
        $tripModel = new Trip();
        $ticketModel = new Ticket();
        $companyId = $_SESSION['company_id'] ?? null;
        
        if (!$companyId) {
            $_SESSION['flash_messages']['error'] = 'Firma bilgisi bulunamadı.';
            header('Location: /company/dashboard');
            exit;
        }
        
        // Seferi getir ve firma kontrolü yap
        $trip = $tripModel->find($tripId);
        
        if (!$trip || $trip['company_id'] !== $companyId) {
            $_SESSION['flash_messages']['error'] = 'Sefer bulunamadı veya bu sefere erişim yetkiniz yok.';
            header('Location: /company/trips');
            exit;
        }
        
        // Bu sefere ait biletleri getir
        $tickets = $ticketModel->getTicketsByTripId($tripId, $companyId);
        
        // İstatistikler
        $totalTickets = count($tickets);
        $activeTickets = count(array_filter($tickets, fn($t) => $t['status'] === 'active'));
        $canceledTickets = count(array_filter($tickets, fn($t) => $t['status'] === 'canceled'));
        $totalRevenue = array_sum(array_map(fn($t) => $t['status'] === 'active' ? $t['total_price'] : 0, $tickets));
        
        $pageTitle = 'Sefer Biletleri';
        $content = __DIR__ . '/../views/company/trip_tickets.php';
        require_once __DIR__ . '/../views/layouts/company.php';
    }
    
    /**
     * Profil sayfasını göster
     */
    public function showProfile() {
        Security::checkRole(ROLE_COMPANY_ADMIN);
        
        $pageTitle = "Profil Ayarları - Firma Admin Panel";
        $content = VIEWS_PATH . '/company/profile_content.php';
        
        require_once VIEWS_PATH . '/layouts/company.php';
    }
    
    /**
     * Profil bilgilerini güncelle
     */
    public function updateProfile() {
        Security::checkRole(ROLE_COMPANY_ADMIN);
        
        // CSRF kontrolü
        if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
            Security::setFlashMessage('error', 'Güvenlik doğrulaması başarısız oldu.');
            Security::redirect(BASE_PATH . '/company/profile');
            exit();
        }
        
        $full_name = Security::sanitizeInput($_POST['full_name'] ?? '');
        $email = Security::sanitizeInput($_POST['email'] ?? '');
        
        if (empty($full_name) || empty($email)) {
            Security::setFlashMessage('error', 'Tüm alanları doldurunuz.');
            Security::redirect(BASE_PATH . '/company/profile');
            exit();
        }
        
        if (!Security::validateEmail($email)) {
            Security::setFlashMessage('error', 'Geçersiz email adresi.');
            Security::redirect(BASE_PATH . '/company/profile');
            exit();
        }
        
        $userModel = new User();
        $userId = Security::getUserId();
        
        // Email değişmişse, başka kullanıcı tarafından kullanılıp kullanılmadığını kontrol et
        $currentUser = $userModel->findById($userId);
        if ($email !== $currentUser['email']) {
            $existingUser = $userModel->findByEmail($email);
            if ($existingUser && $existingUser['id'] !== $userId) {
                Security::setFlashMessage('error', 'Bu email adresi başka bir kullanıcı tarafından kullanılıyor.');
                Security::redirect(BASE_PATH . '/company/profile');
                exit();
            }
        }
        
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE User SET full_name = :full_name, email = :email WHERE id = :id");
        $success = $stmt->execute([
            ':full_name' => $full_name,
            ':email' => $email,
            ':id' => $userId
        ]);
        
        if ($success) {
            $_SESSION['user_name'] = $full_name;
            $_SESSION['user_email'] = $email;
            Security::setFlashMessage('success', 'Profil bilgileriniz başarıyla güncellendi.');
        } else {
            Security::setFlashMessage('error', 'Bir hata oluştu. Lütfen tekrar deneyin.');
        }
        
        Security::redirect(BASE_PATH . '/company/profile');
    }
    
    /**
     * Şifre değiştir
     */
    public function changePassword() {
        Security::checkRole(ROLE_COMPANY_ADMIN);
        
        // CSRF kontrolü
        if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
            Security::setFlashMessage('error', 'Güvenlik doğrulaması başarısız oldu.');
            Security::redirect(BASE_PATH . '/company/profile');
            exit();
        }
        
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            Security::setFlashMessage('error', 'Tüm alanları doldurunuz.');
            Security::redirect(BASE_PATH . '/company/profile');
            exit();
        }
        
        if ($new_password !== $confirm_password) {
            Security::setFlashMessage('error', 'Yeni şifreler eşleşmiyor.');
            Security::redirect(BASE_PATH . '/company/profile');
            exit();
        }
        
        if (!Security::validatePassword($new_password)) {
            Security::setFlashMessage('error', 'Şifre en az 8 karakter, 1 büyük harf, 1 küçük harf ve 1 rakam içermelidir.');
            Security::redirect(BASE_PATH . '/company/profile');
            exit();
        }
        
        $userModel = new User();
        $user = $userModel->findById(Security::getUserId());
        
        if (!Security::verifyPassword($current_password, $user['password'])) {
            Security::setFlashMessage('error', 'Mevcut şifreniz yanlış.');
            Security::redirect(BASE_PATH . '/company/profile');
            exit();
        }
        
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE User SET password = :password WHERE id = :id");
        $success = $stmt->execute([
            ':password' => Security::hashPassword($new_password),
            ':id' => Security::getUserId()
        ]);
        
        if ($success) {
            Security::setFlashMessage('success', 'Şifreniz başarıyla değiştirildi.');
        } else {
            Security::setFlashMessage('error', 'Bir hata oluştu. Lütfen tekrar deneyin.');
        }
        
        Security::redirect(BASE_PATH . '/company/profile');
    }
    
    /**
     * Firma ayarları sayfasını göster
     */
    public function showSettings() {
        Security::checkRole(ROLE_COMPANY_ADMIN);
        
        $pageTitle = "Firma Ayarları - Firma Admin Panel";
        $content = VIEWS_PATH . '/company/settings_content.php';
        
        require_once VIEWS_PATH . '/layouts/company.php';
    }
    
    /**
     * Firma bilgilerini güncelle (Sadece Ad ve Logo)
     */
    public function updateSettings() {
        Security::checkRole(ROLE_COMPANY_ADMIN);
        
        // CSRF kontrolü
        if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
            Security::setFlashMessage('error', 'Güvenlik doğrulaması başarısız oldu.');
            Security::redirect(BASE_PATH . '/company/settings');
            exit();
        }
        
        $name = Security::sanitizeInput($_POST['name'] ?? '');
        
        if (empty($name)) {
            Security::setFlashMessage('error', 'Firma adı zorunludur.');
            Security::redirect(BASE_PATH . '/company/settings');
            exit();
        }
        
        $companyId = Security::getCompanyId();
        $companyModel = new Company();
        
        // Güncelleme verileri
        $updateData = ['name' => $name];
        
        // Logo yükleme
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxSize = 2 * 1024 * 1024; // 2MB
            
            if (!in_array($_FILES['logo']['type'], $allowedTypes)) {
                Security::setFlashMessage('error', 'Geçersiz dosya formatı. Sadece JPG, PNG ve GIF dosyaları yüklenebilir.');
                Security::redirect(BASE_PATH . '/company/settings');
                exit();
            }
            
            if ($_FILES['logo']['size'] > $maxSize) {
                Security::setFlashMessage('error', 'Dosya boyutu çok büyük. Maksimum 2MB olmalıdır.');
                Security::redirect(BASE_PATH . '/company/settings');
                exit();
            }
            
            $uploadDir = __DIR__ . '/../../public/uploads/logos/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
            $logoFileName = Security::generateUUID() . '.' . $extension;
            
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadDir . $logoFileName)) {
                // Eski logoyu sil
                $oldCompany = $companyModel->find($companyId);
                if ($oldCompany && !empty($oldCompany['logo_path'])) {
                    $oldLogoPath = $uploadDir . $oldCompany['logo_path'];
                    if (file_exists($oldLogoPath)) {
                        unlink($oldLogoPath);
                    }
                }
                
                $updateData['logo_path'] = $logoFileName;
            } else {
                Security::setFlashMessage('error', 'Logo yüklenirken bir hata oluştu.');
                Security::redirect(BASE_PATH . '/company/settings');
                exit();
            }
        }
        
        // Firma bilgilerini güncelle
        $success = $companyModel->update($companyId, $updateData);
        
        if ($success) {
            Security::setFlashMessage('success', 'Firma bilgileri başarıyla güncellendi.');
        } else {
            Security::setFlashMessage('error', 'Bir hata oluştu. Lütfen tekrar deneyin.');
        }
        
        Security::redirect(BASE_PATH . '/company/settings');
    }
}
?>
