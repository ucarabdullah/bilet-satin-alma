<?php
/**
 * TicketController - Bilet işlemleri için kontrol sınıfı
 */

class TicketController {
    public function view($id) {
        // Debug
        error_log("TicketController::view() called with id: " . $id);
        error_log("Session user_id: " . ($_SESSION['user_id'] ?? 'NOT SET'));
        error_log("Session user_role: " . ($_SESSION['user_role'] ?? 'NOT SET'));
        error_log("Session company_id: " . ($_SESSION['company_id'] ?? 'NOT SET'));
        
        if (!isset($_SESSION['user_id'])) {header('Location: /login');
            exit;
        }
        
        // User ve company/company_admin erişebilir
        $allowedRoles = ['user', 'company_admin', 'company'];
        if (!in_array($_SESSION['user_role'] ?? '', $allowedRoles)) {
            error_log("Role not allowed: " . ($_SESSION['user_role'] ?? 'NONE'));
            $_SESSION['flash_messages']['error'] = 'Bu sayfaya erişim yetkiniz yok.';
            if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
                header('Location: /admin/dashboard');
            } else {
                header('Location: /login');
            }
            exit;
        }
        
        require_once __DIR__ . '/../models/Ticket.php';
        require_once __DIR__ . '/../models/Trip.php';
        $ticketModel = new Ticket();
        $tripModel = new Trip();
        $ticket = $ticketModel->find($id);
        
        if (!$ticket) {
            $_SESSION['flash_messages']['error'] = 'Bilet bulunamadı.';
            header('Location: /');
            exit;
        }
        
        // Yetki kontrolü: user kendi biletini, company/company_admin kendi firmasının biletlerini görebilir
        $canView = false;
        if ($_SESSION['user_role'] === 'user' && $ticket['user_id'] === $_SESSION['user_id']) {
            $canView = true;
        } elseif (in_array($_SESSION['user_role'], ['company_admin', 'company'])) {
            // Company ID kontrolü
            $sessionCompanyId = $_SESSION['company_id'] ?? null;
            
            if (!$sessionCompanyId) {
                $_SESSION['flash_messages']['error'] = 'Firma bilgisi bulunamadı. Lütfen tekrar giriş yapın.';
                header('Location: /company/login');
                exit;
            }
            
            $trip = $tripModel->find($ticket['trip_id']);
            if ($trip && $trip['company_id'] === $sessionCompanyId) {
                $canView = true;
            }
        }
        
        if (!$canView) {
            $_SESSION['flash_messages']['error'] = 'Bu bilete erişim yetkiniz yok.';
            if (in_array($_SESSION['user_role'], ['company_admin', 'company'])) {
                header('Location: /company/tickets');
            } else {
                header('Location: /user/tickets');
            }
            exit;
        }
        
        $pageTitle = 'Bilet Detayı - BiBilet';
        $content = __DIR__ . '/../views/tickets/view.php';
        require_once __DIR__ . '/../views/layouts/app.php';
    }
    
    public function cancel($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        if (($_SESSION['user_role'] ?? '') !== 'user') {
            $_SESSION['flash_messages']['error'] = 'Bu sayfaya erişim yetkiniz yok.';
            if (($_SESSION['user_role'] ?? '') === 'admin') {
                header('Location: /admin/dashboard');
            } else {
                header('Location: /company/dashboard');
            }
            exit;
        }
        
        require_once __DIR__ . '/../models/Ticket.php';
        $ticketModel = new Ticket();
        $ticket = $ticketModel->find($id);
        
        if (!$ticket) {
            $_SESSION['flash_messages']['error'] = 'Bilet bulunamadı.';
            header('Location: /user/tickets');
            exit;
        }
        
        if ($ticket['user_id'] !== $_SESSION['user_id']) {
            $_SESSION['flash_messages']['error'] = 'Bu bileti iptal etme yetkiniz yok.';
            header('Location: /user/tickets');
            exit;
        }
        
        if ($ticketModel->cancelTicket($id, $_SESSION['user_id'])) {
            $_SESSION['flash_messages']['success'] = 'Biletiniz başarıyla iptal edildi. Para iadeniz hesabınıza yatırıldı.';
        } else {
            $_SESSION['flash_messages']['error'] = 'Bilet iptal edilemedi. Seyahatten 1 saatten az kaldıysa iptal işlemi yapılamaz.';
        }
        
        header('Location: /user/tickets');
        exit;
    }
    
    public function downloadPDF($id) {
        // Debug
        error_log("TicketController::downloadPDF() called with id: " . $id);
        error_log("Session user_id: " . ($_SESSION['user_id'] ?? 'NOT SET'));
        error_log("Session user_role: " . ($_SESSION['user_role'] ?? 'NOT SET'));
        error_log("Session company_id: " . ($_SESSION['company_id'] ?? 'NOT SET'));
        
        if (!isset($_SESSION['user_id'])) {header('Location: /login');
            exit;
        }
        
        $userRole = $_SESSION['user_role'] ?? '';
        if (!in_array($userRole, ['user', 'company_admin', 'company'])) {header('Location: /login');
            exit;
        }
        
        require_once __DIR__ . '/../models/Ticket.php';
        require_once __DIR__ . '/../models/User.php';
        require_once __DIR__ . '/../models/Trip.php';
        
        $ticketModel = new Ticket();
        $ticket = $ticketModel->find($id);
        
        if (!$ticket) {
            header('Location: /user/tickets');
            exit;
        }
        
        // User can only download their own tickets
        if ($userRole === 'user' && $ticket['user_id'] !== $_SESSION['user_id']) {
            header('Location: /user/tickets');
            exit;
        }
        
        // Company admin can only download tickets from their company's trips
        if (in_array($userRole, ['company_admin', 'company'])) {
            $sessionCompanyId = $_SESSION['company_id'] ?? null;
            
            if (!$sessionCompanyId) {
                $_SESSION['flash_messages']['error'] = 'Firma bilgisi bulunamadı. Lütfen tekrar giriş yapın.';
                header('Location: /company/login');
                exit;
            }
            
            $tripModel = new Trip();
            $trip = $tripModel->find($ticket['trip_id']);
            
            if (!$trip || $trip['company_id'] !== $sessionCompanyId) {
                $_SESSION['flash_messages']['error'] = 'Bu bileti indirme yetkiniz yok.';
                header('Location: /company/tickets');
                exit;
            }
        }
        // Compute safe display fields
        $userModel = new User();
        $user = $userModel->find($ticket['user_id']);
        $passengerName = isset($user['full_name']) && $user['full_name'] !== '' ? $user['full_name'] : '—';

        $depTs = !empty($ticket['departure_time']) ? strtotime($ticket['departure_time']) : null;
        $depDate = $depTs ? date('d.m.Y', $depTs) : '—';
        $depTime = $depTs ? date('H:i', $depTs) : '—';

        $seatNumbers = (!empty($ticket['seats']) && is_array($ticket['seats'])) ? implode(', ', $ticket['seats']) : '—';

        $departureCity = $ticket['departure_city'] ?? '—';
        $destinationCity = $ticket['destination_city'] ?? '—';
        $companyName = $ticket['company_name'] ?? '—';
        $ticketId = $ticket['id'] ?? '';
        $totalPrice = isset($ticket['total_price']) && is_numeric($ticket['total_price']) ? (float)$ticket['total_price'] : 0.0;
        ?><!DOCTYPE html><html><head><meta charset="UTF-8"><title>BiBilet</title><style>@media print{body{margin:0;}.no-print{display:none;}}*{margin:0;padding:0;box-sizing:border-box;}body{font-family:Arial,sans-serif;padding:40px;background:#f5f5f5;}.ticket-container{max-width:800px;margin:0 auto;background:white;border:3px solid #1F2E64;border-radius:15px;padding:30px;}.header{text-align:center;border-bottom:2px dashed #3DE0C4;padding-bottom:20px;margin-bottom:30px;}.logo{font-size:32px;font-weight:bold;color:#1F2E64;margin-bottom:10px;}.info-row{display:flex;padding:12px;border-bottom:1px solid #eee;}.info-label{font-weight:bold;color:#1F2E64;width:30%;}.info-value{color:#333;flex:1;}.seats{background:#f8f9fb;padding:15px;border-radius:8px;margin:20px 0;}.seat-numbers{font-size:18px;color:#3DE0C4;font-weight:bold;}.price-total{font-size:24px;font-weight:bold;color:#1F2E64;text-align:right;margin:20px 0;}.footer{text-align:center;margin-top:30px;padding-top:20px;border-top:2px dashed #3DE0C4;color:#666;font-size:12px;}.print-btn{background:#1F2E64;color:white;border:none;padding:15px 30px;border-radius:8px;font-size:16px;cursor:pointer;margin:20px auto;display:block;}.print-btn:hover{background:#151f47;}</style></head><body><button onclick="window.print()" class="print-btn no-print">🖨️ Yazdır / PDF Kaydet</button><div class="ticket-container"><div class="header"><div class="logo">🎫 BiBilet</div><div style="color:#666;font-size:14px;">Otobüs Bileti</div></div><div class="ticket-info"><div class="info-row"><div class="info-label">Bilet No:</div><div class="info-value"><?= htmlspecialchars($ticketId) ?></div></div><div class="info-row"><div class="info-label">Yolcu:</div><div class="info-value"><?= htmlspecialchars($passengerName) ?></div></div><div class="info-row"><div class="info-label">Nereden:</div><div class="info-value"><?= htmlspecialchars($departureCity) ?></div></div><div class="info-row"><div class="info-label">Nereye:</div><div class="info-value"><?= htmlspecialchars($destinationCity) ?></div></div><div class="info-row"><div class="info-label">Tarih:</div><div class="info-value"><?= htmlspecialchars($depDate) ?></div></div><div class="info-row"><div class="info-label">Saat:</div><div class="info-value"><?= htmlspecialchars($depTime) ?></div></div><div class="info-row"><div class="info-label">Firma:</div><div class="info-value"><?= htmlspecialchars($companyName) ?></div></div></div><div class="seats"><div style="font-weight:bold;color:#1F2E64;margin-bottom:10px;">Koltuk Numaraları:</div><div class="seat-numbers"><?= htmlspecialchars($seatNumbers) ?></div></div><div class="price-total">Toplam: <?= number_format($totalPrice, 2, ',', '.') ?> TL</div><div class="footer"><p><strong>BiBilet</strong></p><p>info@bibilet.com | 0850 123 45 67</p><p style="margin-top:10px;">İyi yolculuklar!</p></div></div></body></html><?php
        exit;
    }
}
?>
