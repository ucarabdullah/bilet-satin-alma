<?php


// Hata raporlamasını etkinleştir
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Tüm config ve helper dosyalarını yükle
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/helpers/Security.php';
require_once __DIR__ . '/../src/helpers/Auth.php';
require_once __DIR__ . '/../src/helpers/UUIDHelper.php';
require_once __DIR__ . '/../src/helpers/DateHelper.php';
require_once __DIR__ . '/../src/helpers/Router.php';

// Model dosyalarını yükle
require_once __DIR__ . '/../src/models/Database.php';
require_once __DIR__ . '/../src/models/User.php';
require_once __DIR__ . '/../src/models/Company.php';
require_once __DIR__ . '/../src/models/Trip.php';
require_once __DIR__ . '/../src/models/Ticket.php';
require_once __DIR__ . '/../src/models/Coupon.php';
require_once __DIR__ . '/../src/models/BookedSeat.php';

// Güvenli session başlat
Security::startSecureSession();

// CSRF token oluştur
Security::generateCSRFToken();

// Router nesnesini oluştur
$router = new Router();

// Ana Sayfa Route'ları
$router->get('/', ['HomeController', 'index']);
$router->get('/home', ['HomeController', 'index']);

// Authentication Route'ları
$router->get('/login', ['AuthController', 'showLoginForm']);
$router->post('/login', ['AuthController', 'login']);
$router->get('/register', ['AuthController', 'showRegisterForm']);
$router->post('/register', ['AuthController', 'register']);
$router->get('/logout', ['AuthController', 'logout']);
$router->get('/forgot-password', ['AuthController', 'showForgotPasswordForm']);
$router->post('/forgot-password', ['AuthController', 'forgotPassword']);
$router->get('/reset-password/{token}', ['AuthController', 'showResetPasswordForm']);
$router->post('/reset-password', ['AuthController', 'resetPassword']);

// Kullanıcı Paneli Route'ları (user ve company_admin erişebilir)
$router->get('/user/dashboard', ['UserController', 'dashboard']);
$router->get('/user/tickets', ['UserController', 'ticketHistory']);
$router->get('/user/profile', ['UserController', 'profile']);
$router->post('/user/profile', ['UserController', 'profile']);

// Account alias route'ları (navbar için)
$router->get('/account/dashboard', ['UserController', 'dashboard']);
$router->get('/account/tickets', ['UserController', 'ticketHistory']);
$router->get('/account/profile', ['UserController', 'profile']);
$router->post('/account/profile', ['UserController', 'profile']);

// Bilet İşlemleri Route'ları
$router->get('/tickets/view/{id}', ['TicketController', 'view']);
$router->get('/tickets/download/{id}', ['TicketController', 'downloadPDF']);
$router->get('/tickets/cancel/{id}', ['TicketController', 'cancel']);

// Sefer Arama ve Rezervasyon Route'ları
$router->get('/trips/search', ['TripController', 'search']);
$router->post('/trips/search', ['TripController', 'search']);
$router->get('/trips/details/{id}', ['TripController', 'details']);
$router->get('/trips/book/{id}', ['TripController', 'showBookingForm']);
$router->post('/trips/book/{id}', ['TripController', 'book']);

// Kupon API Route'ları
$router->post('/api/coupons/validate', ['CouponController', 'validate']);
$router->get('/api/coupons/available', ['CouponController', 'available']);

// Firma Admin Route'ları
$router->get('/company/login', ['CompanyController', 'login']);
$router->post('/company/login', ['CompanyController', 'login']);
$router->get('/company/dashboard', ['CompanyController', 'dashboard']);
$router->get('/company/profile', ['CompanyController', 'showProfile']);
$router->post('/company/profile/update', ['CompanyController', 'updateProfile']);
$router->post('/company/profile/change-password', ['CompanyController', 'changePassword']);
$router->get('/company/settings', ['CompanyController', 'showSettings']);
$router->post('/company/settings/update', ['CompanyController', 'updateSettings']);
$router->get('/company/trips', ['CompanyController', 'trips']);
$router->get('/company/trips/new', ['CompanyController', 'createTrip']);
$router->post('/company/trips/new', ['CompanyController', 'createTrip']);
$router->get('/company/trips/edit/{id}', ['CompanyController', 'editTrip']);
$router->post('/company/trips/edit/{id}', ['CompanyController', 'editTrip']);
$router->post('/company/trips/delete/{id}', ['CompanyController', 'deleteTrip']);
$router->get('/company/trips/{id}/tickets', ['CompanyController', 'viewTripTickets']);
$router->get('/company/tickets', ['CompanyController', 'tickets']);
$router->post('/company/tickets/cancel/{id}', ['CompanyController', 'cancelTicket']);
$router->get('/company/coupons', ['CompanyController', 'coupons']);
$router->get('/company/coupons/new', ['CompanyController', 'createCoupon']);
$router->post('/company/coupons/new', ['CompanyController', 'createCoupon']);
$router->get('/company/coupons/edit/{id}', ['CompanyController', 'editCoupon']);
$router->post('/company/coupons/edit/{id}', ['CompanyController', 'editCoupon']);
$router->post('/company/coupons/delete/{id}', ['CompanyController', 'deleteCoupon']);

// Admin Route'ları
$router->get('/admin/login', ['AdminController', 'login']);
$router->post('/admin/login', ['AdminController', 'login']);
$router->get('/admin/dashboard', ['AdminController', 'dashboard']);
$router->get('/admin/profile', ['AdminController', 'showProfile']);
$router->post('/admin/profile/update', ['AdminController', 'updateProfile']);
$router->post('/admin/profile/change-password', ['AdminController', 'changePassword']);
$router->get('/admin/companies', ['AdminController', 'companies']);
$router->get('/admin/companies/edit/{id}', ['AdminController', 'editCompany']);
$router->post('/admin/companies/edit/{id}', ['AdminController', 'editCompany']);
$router->post('/admin/companies/delete/{id}', ['AdminController', 'deleteCompany']);
$router->get('/admin/companies/new', ['AdminController', 'createCompany']);
$router->post('/admin/companies/new', ['AdminController', 'createCompany']);
$router->get('/admin/users', ['AdminController', 'users']);
$router->get('/admin/users/new', ['AdminController', 'createUser']);
$router->post('/admin/users/new', ['AdminController', 'createUser']);
$router->get('/admin/users/edit/{id}', ['AdminController', 'editUser']);
$router->post('/admin/users/edit/{id}', ['AdminController', 'editUser']);
$router->post('/admin/users/delete/{id}', ['AdminController', 'deleteUser']);
$router->get('/admin/coupons', ['AdminController', 'coupons']);
$router->get('/admin/coupons/new', ['AdminController', 'createCouponAdmin']);
$router->post('/admin/coupons/new', ['AdminController', 'createCouponAdmin']);
$router->get('/admin/coupons/edit/{id}', ['AdminController', 'editCouponAdmin']);
$router->post('/admin/coupons/edit/{id}', ['AdminController', 'editCouponAdmin']);
$router->post('/admin/coupons/delete/{id}', ['AdminController', 'deleteCouponAdmin']);

// 404 Sayfası
$router->notFound(function() {
    require __DIR__ . '/../src/views/404.php';
});

// URL'yi çözümle
$router->resolve();
