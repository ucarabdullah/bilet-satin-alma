<?php
/**
 * Sefer Detay Sayfası
 * 
 * Variables:
 * - $trip: Sefer bilgileri
 */

// Koltuk durumunu hesapla
$bookedSeats = $trip['booked_seats_count'] ?? 0;
$availableSeats = $trip['capacity'] - $bookedSeats;
$occupancyPercent = ($trip['capacity'] > 0) ? (($bookedSeats / $trip['capacity']) * 100) : 0;

// Süre hesaplama
$duration = (strtotime($trip['arrival_time']) - strtotime($trip['departure_time'])) / 3600;
?>

<div class="container my-4">
    <!-- Başlık -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>
            <i class="bi bi-bus-front text-primary me-2"></i>
            Sefer Detayı
        </h3>
        <a href="javascript:history.back()" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Geri Dön
        </a>
    </div>

    <div class="row g-4">
        <!-- Sol Kolon: Sefer Bilgileri -->
        <div class="col-lg-7">
            <!-- Firma ve Güzergah Kartı -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="text-primary mb-0">
                            <i class="bi bi-building me-2"></i>
                            <?= htmlspecialchars($trip['company_name'] ?? 'Firma') ?>
                        </h4>
                        <span class="badge bg-primary px-3 py-2">
                            <?= number_format($trip['price'], 2) ?> ₺
                        </span>
                    </div>

                    <hr>

                    <!-- Güzergah Bilgisi -->
                    <div class="row text-center mb-3">
                        <div class="col-4">
                            <h2 class="text-primary mb-1"><?= date('H:i', strtotime($trip['departure_time'])) ?></h2>
                            <h5><?= htmlspecialchars($trip['departure_city']) ?></h5>
                            <small class="text-muted"><?= date('d.m.Y', strtotime($trip['departure_time'])) ?></small>
                        </div>

                        <div class="col-4 my-auto">
                            <i class="bi bi-arrow-right text-primary display-6"></i>
                            <br>
                            <span class="badge bg-secondary mt-2">
                                <?= number_format($duration, 1) ?> saat
                            </span>
                        </div>

                        <div class="col-4">
                            <h2 class="text-primary mb-1"><?= date('H:i', strtotime($trip['arrival_time'])) ?></h2>
                            <h5><?= htmlspecialchars($trip['destination_city']) ?></h5>
                            <small class="text-muted"><?= date('d.m.Y', strtotime($trip['arrival_time'])) ?></small>
                        </div>
                    </div>

                    <!-- Koltuk Bilgisi -->
                    <div class="alert alert-<?= $availableSeats > 0 ? 'success' : 'danger' ?> mb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-people me-2"></i>
                                <strong>Koltuk Durumu:</strong> 
                                <?= $availableSeats ?> / <?= $trip['capacity'] ?> koltuk müsait
                            </div>
                            <div class="progress" style="width: 200px; height: 20px;">
                                <div class="progress-bar <?= $occupancyPercent >= 90 ? 'bg-danger' : ($occupancyPercent >= 70 ? 'bg-warning' : 'bg-success') ?>" 
                                     style="width: <?= $occupancyPercent ?>%">
                                    <?= number_format($occupancyPercent, 0) ?>%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Özellikler Kartı -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-star me-2"></i>Sefer Özellikleri</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-wifi text-primary fs-4 me-3"></i>
                                <div>
                                    <strong>Ücretsiz WiFi</strong>
                                    <br><small class="text-muted">Yolculuk boyunca internet</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-cup-hot text-primary fs-4 me-3"></i>
                                <div>
                                    <strong>İkram</strong>
                                    <br><small class="text-muted">Çay, kahve, su</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-display text-primary fs-4 me-3"></i>
                                <div>
                                    <strong>Eğlence Sistemi</strong>
                                    <br><small class="text-muted">Kişisel ekranlar</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-plug text-primary fs-4 me-3"></i>
                                <div>
                                    <strong>USB Şarj</strong>
                                    <br><small class="text-muted">Her koltukta mevcut</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sağ Kolon: Rezervasyon -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-ticket-detailed me-2"></i>Bilet Rezervasyonu</h5>
                </div>
                <div class="card-body">
                    <?php if ($availableSeats > 0): ?>
                        <!-- Fiyat Bilgisi -->
                        <div class="text-center mb-4">
                            <p class="text-muted mb-2">Bilet Fiyatı</p>
                            <h1 class="display-4 text-primary mb-0">
                                <?= number_format($trip['price'], 2) ?> ₺
                            </h1>
                            <small class="text-muted">Kişi başı</small>
                        </div>

                        <hr>

                        <!-- Bilgi Kutusu -->
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Önemli:</strong> Koltuk seçimini bir sonraki adımda yapacaksınız.
                        </div>

                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <!-- Ziyaretçi için Uyarı -->
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Bilet satın almak için giriş yapmalısınız.
                            </div>
                        <?php endif; ?>

                        <!-- Bilet Al Butonu - Admin ve Company Admin göremez -->
                        <?php if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin', 'company_admin'])): ?>
                            <a href="/trips/book/<?= urlencode($trip['id']) ?>" class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="bi bi-cart-plus me-2"></i>
                                Bilet Al
                            </a>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                Admin ve Firma Yöneticileri bilet satın alamaz.
                            </div>
                        <?php endif; ?>

                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <!-- Hızlı Giriş Butonları -->
                            <a href="/login?redirect=/trips/book/<?= urlencode($trip['id']) ?>" 
                               class="btn btn-outline-primary w-100 mb-2">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Giriş Yap
                            </a>
                            <a href="/register" class="btn btn-outline-secondary w-100 mb-3">
                                <i class="bi bi-person-plus me-2"></i>
                                Kayıt Ol
                            </a>
                        <?php endif; ?>

                        <!-- Özellikler Listesi -->
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Anında onay
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Mobil bilet
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Ücretsiz iptal (1 saat öncesine kadar)
                            </li>
                            <li class="mb-0">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Güvenli ödeme
                            </li>
                        </ul>
                    <?php else: ?>
                        <!-- Koltuk Yok -->
                        <div class="text-center py-4">
                            <i class="bi bi-emoji-frown display-1 text-muted"></i>
                            <h5 class="mt-3">Üzgünüz</h5>
                            <p class="text-muted">Bu sefer için müsait koltuk kalmamıştır.</p>
                            <a href="javascript:history.back()" class="btn btn-secondary">
                                Başka Sefer Ara
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- İptal Koşulları -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>İptal ve İade Koşulları</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6><i class="bi bi-clock-history text-success me-2"></i>İptal Süresi</h6>
                    <p class="text-muted">
                        Kalkış saatinden en az 1 saat önce iptal işlemi yapabilirsiniz. 
                        Son 1 saat içinde iptal işlemi yapılamaz.
                    </p>
                </div>
                <div class="col-md-6">
                    <h6><i class="bi bi-cash-coin text-success me-2"></i>İade İşlemi</h6>
                    <p class="text-muted">
                        İptal edilen biletlerin ücreti anında hesabınıza iade edilir. 
                        İade işlemi için ekstra ücret alınmaz.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.sticky-top {
    position: -webkit-sticky;
    position: sticky;
}

.card:hover {
    transform: translateY(-2px);
    transition: all 0.3s ease;
}
</style>
