<?php
class AdminController {
    private function ensureAdmin() {
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== ROLE_ADMIN) {
            header('Location: /admin/login'); exit;
        }
    }

    /**
     * Admin login page
     */
    public function login() {
        // If already logged in as admin, redirect to dashboard
        if (isset($_SESSION['user_id']) && ($_SESSION['user_role'] ?? '') === ROLE_ADMIN) {
            header('Location: /admin/dashboard'); exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
                $_SESSION['flash_messages']['error'] = 'CSRF doğrulaması başarısız.';
                header('Location: /admin/login'); exit;
            }
            
            require_once __DIR__ . '/../models/User.php';
            $userModel = new User();
            
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            
            $user = $userModel->login($email, $password);
            
            if ($user && $user['role'] === ROLE_ADMIN) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['flash_messages']['success'] = 'Hoş geldiniz, ' . $user['full_name'];
                header('Location: /admin/dashboard'); exit;
            } else {
                $_SESSION['flash_messages']['error'] = 'Geçersiz admin bilgileri veya yetkiniz yok.';
                header('Location: /admin/login'); exit;
            }
        }
        
        // Show admin login view (without layout)
        require_once __DIR__ . '/../views/admin/login.php';
    }

    public function dashboard() {
        $this->ensureAdmin();
        
        // Gather statistics
        require_once __DIR__ . '/../models/Company.php';
        require_once __DIR__ . '/../models/User.php';
        require_once __DIR__ . '/../models/Coupon.php';
        
        $companyModel = new Company();
        $userModel = new User();
        $couponModel = new Coupon();
        
        $stats = [
            'companies' => count($companyModel->getAll()),
            'users' => $userModel->count(),
            'trips' => 0, 
            'coupons' => count($couponModel->getAll())
        ];
        
        $recentActivities = [];
        
        $pageTitle = 'Dashboard';
        $content = __DIR__ . '/../views/admin/dashboard.php';
        require_once __DIR__ . '/../views/layouts/admin.php';
    }

    public function companies() {
        $this->ensureAdmin();
        require_once __DIR__ . '/../models/Company.php';
        $companyModel = new Company();
        $companies = $companyModel->getAll();
        $pageTitle = 'Firmalar';
        $content = __DIR__ . '/../views/admin/companies.php';
        require_once __DIR__ . '/../views/layouts/admin.php';
    }

    // (createCompany moved below with extended admin scope CRUD)
    public function coupons() {
        $this->ensureAdmin();
        require_once __DIR__ . '/../models/Coupon.php';
        $couponModel = new Coupon();
        $coupons = $couponModel->getAll();
        $pageTitle = 'Kuponlar';
        $content = __DIR__ . '/../views/admin/coupons.php';
        require_once __DIR__ . '/../views/layouts/admin.php';
    }

    // Companies CRUD (Admin scope)
    public function createCompany() {
        $this->ensureAdmin();
        require_once __DIR__ . '/../models/Company.php';
        $companyModel = new Company();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
                $_SESSION['flash_messages']['error'] = 'CSRF doğrulaması başarısız.'; header('Location: /admin/companies'); exit;
            }
            $name = trim($_POST['name'] ?? '');
            $logo = trim($_POST['logo_path'] ?? '');
            if (!$name) { $_SESSION['flash_messages']['error'] = 'Firma adı zorunludur.'; header('Location: /admin/companies/new'); exit; }
            if (!$companyModel->isNameUnique($name)) { $_SESSION['flash_messages']['error'] = 'Bu firma adı zaten kayıtlı.'; header('Location: /admin/companies/new'); exit; }
            $id = $companyModel->create(['name' => $name, 'logo_path' => $logo ?: null]);
            $_SESSION['flash_messages'][$id ? 'success' : 'error'] = $id ? 'Firma oluşturuldu.' : 'Firma oluşturulamadı.';
            header('Location: /admin/companies'); exit;
        }
        $pageTitle = 'Yeni Firma';
        $content = __DIR__ . '/../views/admin/company_form.php';
        require_once __DIR__ . '/../views/layouts/admin.php';
    }

    public function editCompany($id) {
        $this->ensureAdmin();
        require_once __DIR__ . '/../models/Company.php';
        $companyModel = new Company();
        $company = $companyModel->find($id);
        if (!$company) { $_SESSION['flash_messages']['error'] = 'Firma bulunamadı.'; header('Location: /admin/companies'); exit; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
                $_SESSION['flash_messages']['error'] = 'CSRF doğrulaması başarısız.'; header('Location: /admin/companies'); exit;
            }
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'logo_path' => trim($_POST['logo_path'] ?? '') ?: null,
            ];
            $ok = $companyModel->update($id, $data);
            $_SESSION['flash_messages'][$ok ? 'success' : 'error'] = $ok ? 'Firma güncellendi.' : 'Güncelleme başarısız.';
            header('Location: /admin/companies'); exit;
        }
        $pageTitle = 'Firma Düzenle';
        $content = __DIR__ . '/../views/admin/company_form.php';
        require_once __DIR__ . '/../views/layouts/admin.php';
    }

    public function deleteCompany($id) {
        $this->ensureAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
                $_SESSION['flash_messages']['error'] = 'CSRF doğrulaması başarısız.'; 
                header('Location: /admin/companies'); 
                exit;
            }
        }
        require_once __DIR__ . '/../models/Company.php';
        $companyModel = new Company();
        
        try {
            $ok = $companyModel->delete($id);
            
            if ($ok) {
                $_SESSION['flash_messages']['success'] = 'Firma, seferleri ve biletleri başarıyla silindi. Aktif biletler için para iadesi yapıldı.';
            } else {
                $_SESSION['flash_messages']['error'] = 'Firma silinirken bir hata oluştu. Lütfen tekrar deneyin.';
            }
        } catch (Exception $e) {
            $_SESSION['flash_messages']['error'] = 'Firma silme hatası: ' . htmlspecialchars($e->getMessage()) . ' (Satır: ' . $e->getLine() . ')';
            error_log("AdminController firma silme hatası: " . $e->getMessage() . " - " . $e->getTraceAsString());
        }
        
        header('Location: /admin/companies'); 
        exit;
    }

    // Users CRUD (Admin scope)
    public function users() {
        $this->ensureAdmin();
        require_once __DIR__ . '/../models/User.php';
        require_once __DIR__ . '/../models/Company.php';
        $userModel = new User();
        $companyModel = new Company();
        $users = $userModel->getAllWithCompany(100, 0);
        $companies = $companyModel->getAll();
        $pageTitle = 'Kullanıcılar';
        $content = __DIR__ . '/../views/admin/users.php';
        require_once __DIR__ . '/../views/layouts/admin.php';
    }

    public function createUser() {
        $this->ensureAdmin();
        require_once __DIR__ . '/../models/User.php';
        require_once __DIR__ . '/../models/Company.php';
        $userModel = new User();
        $companyModel = new Company();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
                $_SESSION['flash_messages']['error'] = 'CSRF doğrulaması başarısız.'; 
                header('Location: /admin/users'); 
                exit;
            }
            
            // PHP-only preview: If preview button pressed, re-render form with posted values (no validation, no save)
            if (isset($_POST['preview'])) {
                // Preserve entered fields to re-render the form
                $formData = [
                    'full_name' => trim($_POST['full_name'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'role' => $_POST['role'] ?? 'user',
                    'company_id' => $_POST['company_id'] ?? ''
                ];
                $companies = $companyModel->getAll();
                $pageTitle = 'Yeni Kullanıcı';
                $content = __DIR__ . '/../views/admin/user_form.php';
                require_once __DIR__ . '/../views/layouts/admin.php';
                return;
            }
            
            $full = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'user';
            $companyId = $_POST['company_id'] ?? null;
            
            // Validasyon
            if (!$full || !$email || !$password) { 
                $_SESSION['flash_messages']['error'] = 'Zorunlu alanlar eksik.'; 
                header('Location: /admin/users/new'); 
                exit; 
            }
            
            // Email validasyonu
            if (!Security::validateEmail($email)) {
                $_SESSION['flash_messages']['error'] = 'Geçersiz email adresi.';
                header('Location: /admin/users/new');
                exit;
            }
            
            // Şifre güvenlik kontrolü
            if (!Security::validatePassword($password)) {
                $_SESSION['flash_messages']['error'] = 'Şifre en az 8 karakter olmalı, en az 1 büyük harf, 1 küçük harf ve 1 rakam içermelidir.';
                header('Location: /admin/users/new');
                exit;
            }
            
            // Company admin (company) rolü için firma seçimi zorunlu
            if ($role === 'company' && empty($companyId)) {
                $_SESSION['flash_messages']['error'] = 'Company Admin için firma seçimi zorunludur.';
                header('Location: /admin/users/new');
                exit;
            }
            
            // Diğer roller için company_id null olmalı
            if ($role !== 'company') { 
                $companyId = null; 
            }
            
            $id = $userModel->create($full, $email, $password, $role, $companyId);
            $_SESSION['flash_messages'][$id ? 'success' : 'error'] = $id ? 'Kullanıcı oluşturuldu.' : 'Kullanıcı oluşturulamadı.';
            header('Location: /admin/users'); 
            exit;
        }
        
        $companies = $companyModel->getAll();
        $pageTitle = 'Yeni Kullanıcı';
        $content = __DIR__ . '/../views/admin/user_form.php';
        require_once __DIR__ . '/../views/layouts/admin.php';
    }

    public function editUser($id) {
        $this->ensureAdmin();
        require_once __DIR__ . '/../models/User.php';
        require_once __DIR__ . '/../models/Company.php';
        $userModel = new User();
        $companyModel = new Company();
        $user = $userModel->find($id);
        if (!$user) { $_SESSION['flash_messages']['error'] = 'Kullanıcı bulunamadı.'; header('Location: /admin/users'); exit; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
                $_SESSION['flash_messages']['error'] = 'CSRF doğrulaması başarısız.'; header('Location: /admin/users'); exit;
            }
            $data = [
                'full_name' => trim($_POST['full_name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'role' => $_POST['role'] ?? $user['role'],
                'company_id' => $_POST['role'] === 'company' ? ($_POST['company_id'] ?: null) : null,
            ];
            // ŞİFRE GÜNCELLEMESİ KALDIRILDI - Admin şifre değiştiremez, sadece oluştururken belirleyebilir
            $ok = $userModel->update($id, $data);
            $_SESSION['flash_messages'][$ok ? 'success' : 'error'] = $ok ? 'Kullanıcı güncellendi.' : 'Güncelleme başarısız.';
            header('Location: /admin/users'); exit;
        }
        $companies = $companyModel->getAll();
        $pageTitle = 'Kullanıcı Düzenle';
        $content = __DIR__ . '/../views/admin/user_form.php';
        require_once __DIR__ . '/../views/layouts/admin.php';
    }

    public function deleteUser($id) {
        $this->ensureAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
                $_SESSION['flash_messages']['error'] = 'CSRF doğrulaması başarısız.'; header('Location: /admin/users'); exit;
            }
        }
        require_once __DIR__ . '/../models/User.php';
        $userModel = new User();
        $ok = $userModel->delete($id);
        $_SESSION['flash_messages'][$ok ? 'success' : 'error'] = $ok ? 'Kullanıcı silindi.' : 'Silme başarısız.';
        header('Location: /admin/users'); exit;
    }

    // Coupons CRUD (Admin scope)
    public function createCouponAdmin() {
        $this->ensureAdmin();
        require_once __DIR__ . '/../models/Coupon.php';
        require_once __DIR__ . '/../models/Company.php';
        $couponModel = new Coupon();
        $companyModel = new Company();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
                $_SESSION['flash_messages']['error'] = 'CSRF doğrulaması başarısız.'; header('Location: /admin/coupons'); exit;
            }
            $data = [
                'code' => trim($_POST['code'] ?? ''),
                'discount' => (float)($_POST['discount'] ?? 0),
                'usage_limit' => (int)($_POST['usage_limit'] ?? 1),
                'expire_date' => trim($_POST['expire_date'] ?? ''),
                'company_id' => null, // Admin kuponları HER ZAMAN genel kupon (company_id = NULL)
            ];
            if (!$data['code'] || $data['discount'] <= 0 || $data['usage_limit'] <= 0 || !$data['expire_date']) {
                $_SESSION['flash_messages']['error'] = 'Zorunlu alanlar eksik.'; header('Location: /admin/coupons/new'); exit;
            }
            $id = $couponModel->create($data);
            $_SESSION['flash_messages'][$id ? 'success' : 'error'] = $id ? 'Kupon oluşturuldu.' : 'Kupon oluşturulamadı.';
            header('Location: /admin/coupons'); exit;
        }
        $companies = $companyModel->getAll();
        $pageTitle = 'Yeni Kupon';
        $content = __DIR__ . '/../views/admin/coupon_form.php';
        require_once __DIR__ . '/../views/layouts/admin.php';
    }

    public function editCouponAdmin($id) {
        $this->ensureAdmin();
        require_once __DIR__ . '/../models/Coupon.php';
        require_once __DIR__ . '/../models/Company.php';
        $couponModel = new Coupon();
        $companyModel = new Company();
        $coupon = $couponModel->find($id);
        if (!$coupon) { $_SESSION['flash_messages']['error'] = 'Kupon bulunamadı.'; header('Location: /admin/coupons'); exit; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
                $_SESSION['flash_messages']['error'] = 'CSRF doğrulaması başarısız.'; header('Location: /admin/coupons'); exit;
            }
            $data = [
                'code' => trim($_POST['code'] ?? ''),
                'discount' => (float)($_POST['discount'] ?? 0),
                'usage_limit' => (int)($_POST['usage_limit'] ?? 1),
                'expire_date' => trim($_POST['expire_date'] ?? ''),
                'company_id' => null, // Admin kuponları HER ZAMAN genel kupon
            ];
            $ok = $couponModel->update($id, $data);
            $_SESSION['flash_messages'][$ok ? 'success' : 'error'] = $ok ? 'Kupon güncellendi.' : 'Güncelleme başarısız.';
            header('Location: /admin/coupons'); exit;
        }
        $companies = $companyModel->getAll();
        $pageTitle = 'Kupon Düzenle';
        $content = __DIR__ . '/../views/admin/coupon_form.php';
        require_once __DIR__ . '/../views/layouts/admin.php';
    }

    public function deleteCouponAdmin($id) {
        $this->ensureAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
                $_SESSION['flash_messages']['error'] = 'CSRF doğrulaması başarısız.'; header('Location: /admin/coupons'); exit;
            }
        }
        require_once __DIR__ . '/../models/Coupon.php';
        $couponModel = new Coupon();
        $ok = $couponModel->delete($id);
        $_SESSION['flash_messages'][$ok ? 'success' : 'error'] = $ok ? 'Kupon silindi.' : 'Silme başarısız.';
        header('Location: /admin/coupons'); exit;
    }
    
    /**
     * Admin profil sayfası
     */
    public function showProfile() {
        Security::checkRole(ROLE_ADMIN);
        $pageTitle = 'Profilim';
        $content = __DIR__ . '/../views/admin/profile.php';
        require_once __DIR__ . '/../views/layouts/admin.php';
    }
    
    /**
     * Admin profil güncelleme
     */
    public function updateProfile() {
        Security::checkRole(ROLE_ADMIN);
        
        if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
            Security::setFlashMessage('error', 'Güvenlik doğrulaması başarısız oldu.');
            Security::redirect(BASE_PATH . '/admin/profile');
            exit();
        }
        
        $full_name = Security::sanitizeInput($_POST['full_name'] ?? '');
        $email = Security::sanitizeInput($_POST['email'] ?? '');
        
        if (empty($full_name) || empty($email)) {
            Security::setFlashMessage('error', 'Tüm alanları doldurunuz.');
            Security::redirect(BASE_PATH . '/admin/profile');
            exit();
        }
        
        if (!Security::validateEmail($email)) {
            Security::setFlashMessage('error', 'Geçersiz email adresi.');
            Security::redirect(BASE_PATH . '/admin/profile');
            exit();
        }
        
        $userModel = new User();
        $userId = Security::getUserId();
        
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
        
        Security::redirect(BASE_PATH . '/admin/profile');
    }
    
    /**
     * Admin şifre değiştir
     */
    public function changePassword() {
        Security::checkRole(ROLE_ADMIN);
        
        if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
            Security::setFlashMessage('error', 'Güvenlik doğrulaması başarısız oldu.');
            Security::redirect(BASE_PATH . '/admin/profile');
            exit();
        }
        
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            Security::setFlashMessage('error', 'Tüm alanları doldurunuz.');
            Security::redirect(BASE_PATH . '/admin/profile');
            exit();
        }
        
        if ($new_password !== $confirm_password) {
            Security::setFlashMessage('error', 'Yeni şifreler eşleşmiyor.');
            Security::redirect(BASE_PATH . '/admin/profile');
            exit();
        }
        
        if (!Security::validatePassword($new_password)) {
            Security::setFlashMessage('error', 'Şifre en az 8 karakter, 1 büyük harf, 1 küçük harf ve 1 rakam içermelidir.');
            Security::redirect(BASE_PATH . '/admin/profile');
            exit();
        }
        
        $userModel = new User();
        $user = $userModel->findById(Security::getUserId());
        
        if (!Security::verifyPassword($current_password, $user['password'])) {
            Security::setFlashMessage('error', 'Mevcut şifreniz yanlış.');
            Security::redirect(BASE_PATH . '/admin/profile');
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
        
        Security::redirect(BASE_PATH . '/admin/profile');
    }

}
?>
