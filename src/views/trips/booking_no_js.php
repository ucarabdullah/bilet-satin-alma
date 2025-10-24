<?php
/**
 * Bilet Rezervasyon Sayfası - PHP Only (No JavaScript)
 * Orijinal tasarım korundu, koltuk seçimi checkbox ile otomatik submit
 */

// Koltuk durumunu hesapla
$totalSeats = $trip['capacity'] ?? 40;
$bookedSeatsArray = $bookedSeats ?? [];
$availableSeatsCount = $totalSeats - count($bookedSeatsArray);

// Form gönderildiyse seçilen koltukları al
$selectedSeatsArray = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['seats']) && is_array($_POST['seats'])) {
        $selectedSeatsArray = array_map('intval', $_POST['seats']);
    }
}

// Fiyat hesaplama
$seatCount = count($selectedSeatsArray);
$subtotal = $seatCount * $trip['price'];
$discount = 0;
$total = $subtotal;

// Kupon kontrolü - POST ile kupon kodu geldiyse kontrol et
$couponMessage = '';
$couponClass = '';
$appliedCoupon = null;
$couponCode = $_POST['coupon_code'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($couponCode) && $seatCount > 0) {
    require_once __DIR__ . '/../../models/Coupon.php';
    $couponModel = new Coupon();
    $couponValidation = $couponModel->validateCoupon(
        strtoupper(trim($couponCode)), 
        $trip['id'], 
        $_SESSION['user_id']
    );
    
    if ($couponValidation && $couponValidation['is_valid']) {
        $discount = $subtotal * ($couponValidation['discount_percent'] / 100);
        $total = $subtotal - $discount;
        $appliedCoupon = $couponValidation;
        $couponMessage = "%{$couponValidation['discount_percent']} indirim uygulandı!";
        $couponClass = 'alert-success';
    } else {
        $couponMessage = ($couponValidation['error_message'] ?? 'Kupon geçersiz');
        $couponClass = 'alert-danger';
    }
}
?>

<!-- Koltuk Seçimi İçin Stil -->
<style>
    .seat-map { max-width: 500px; margin: 0 auto; }
    .seat-checkbox { display: none; }
    .seat-label {
        display: inline-block; width: 45px; height: 50px;
        border: 2px solid #ddd; border-radius: 8px 8px 4px 4px;
        cursor: pointer; position: relative; transition: all 0.3s ease;
        background: linear-gradient(to bottom, #f8f9fa 0%, #e9ecef 100%);
    }
    .seat-label::before {
        content: attr(data-seat); position: absolute;
        top: 50%; left: 50%; transform: translate(-50%, -50%);
        font-size: 12px; font-weight: 600; color: #495057;
    }
    .seat-label:hover:not(.booked) {
        border-color: #0d6efd;
        background: linear-gradient(to bottom, #e7f1ff 0%, #cfe2ff 100%);
        transform: scale(1.05);
    }
    .seat-checkbox:checked + .seat-label {
        background: linear-gradient(to bottom, #0d6efd 0%, #0b5ed7 100%);
        border-color: #0a58ca;
    }
    .seat-checkbox:checked + .seat-label::before { color: white; }
    .seat-label.booked {
        background: linear-gradient(to bottom, #6c757d 0%, #5c636a 100%);
        border-color: #5c636a; cursor: not-allowed; opacity: 0.6;
    }
    .seat-label.booked::before { color: #f8f9fa; }
    .seat-label.guest-disabled {
        background: linear-gradient(to bottom, #ffc107 0%, #e0a800 100%);
        border-color: #d39e00; cursor: not-allowed; opacity: 0.7;
        pointer-events: none;
    }
    .seat-label.guest-disabled::before { color: #fff; }
    .seat-label.guest-disabled::after {
        content: "🔒"; position: absolute; top: 2px; right: 2px;
        font-size: 10px; opacity: 0.8;
    }
    .seat-row { display: flex; gap: 10px; justify-content: center; margin-bottom: 10px; }
    .seat-gap { width: 40px; }
    .booking-summary-sticky { position: sticky; top: 100px; }
    .legend-item { display: flex; align-items: center; gap: 8px; }
    .legend-box { width: 30px; height: 35px; border-radius: 6px 6px 3px 3px; border: 2px solid; }
</style>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3><i class="bi bi-ticket-perforated text-primary me-2"></i>Bilet Rezervasyonu</h3>
            <p class="text-muted mb-0">Lütfen koltuk seçimi yapınız</p>
        </div>
        <a href="/trips/details/<?= htmlspecialchars($trip['id']) ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Geri Dön
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <!-- Sefer Özeti -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="bi bi-bus-front me-2"></i>Sefer Bilgileri</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">Firma</small>
                            <strong><?= htmlspecialchars($trip['company_name'] ?? 'Firma') ?></strong>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">Kalkış Tarihi</small>
                            <strong><?= date('d.m.Y H:i', strtotime($trip['departure_time'])) ?></strong>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">Güzergah</small>
                            <strong><?= htmlspecialchars($trip['departure_city']) ?> → <?= htmlspecialchars($trip['destination_city']) ?></strong>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">Bilet Fiyatı</small>
                            <strong class="text-primary"><?= number_format($trip['price'], 2) ?> ₺</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Koltuk Haritası -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="bi bi-grid-3x3-gap me-2"></i>Koltuk Seçimi</h5>
                    
                    <!-- Legend -->
                    <div class="d-flex justify-content-center gap-3 mb-4 flex-wrap">
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="legend-item">
                            <div class="legend-box" style="background: linear-gradient(to bottom, #f8f9fa 0%, #e9ecef 100%); border-color: #ddd;"></div>
                            <small>Boş</small>
                        </div>
                        <div class="legend-item">
                            <div class="legend-box" style="background: linear-gradient(to bottom, #0d6efd 0%, #0b5ed7 100%); border-color: #0a58ca;"></div>
                            <small>Seçili</small>
                        </div>
                        <?php else: ?>
                        <div class="legend-item">
                            <div class="legend-box" style="background: linear-gradient(to bottom, #ffc107 0%, #e0a800 100%); border-color: #d39e00; position: relative;">
                                <span style="position: absolute; top: 2px; right: 2px; font-size: 10px;">🔒</span>
                            </div>
                            <small>Giriş Gerekli</small>
                        </div>
                        <?php endif; ?>
                        <div class="legend-item">
                            <div class="legend-box" style="background: linear-gradient(to bottom, #6c757d 0%, #5c636a 100%); border-color: #5c636a;"></div>
                            <small>Dolu</small>
                        </div>
                    </div>

                    <!-- Şoför İşareti -->
                    <div class="text-center mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background: #dc3545; border-radius: 10px; color: white;">
                            <i class="bi bi-person-fill" style="font-size: 30px;"></i>
                        </div>
                        <div><small class="text-muted">Şoför</small></div>
                    </div>

                    <!-- Otobüs Koltuk Düzeni -->
                    <form method="POST" action="/trips/book/<?= htmlspecialchars($trip['id']) ?>">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <input type="hidden" name="select_seats" value="1">
                        <?php if (!empty($_POST['coupon_code'])): ?>
                        <input type="hidden" name="coupon_code" value="<?= htmlspecialchars($_POST['coupon_code']) ?>">
                        <?php endif; ?>
                        
                        <div class="seat-map">
                            <?php
                            $totalCapacity = $trip['capacity'] ?? 40;
                            $cols = 4;
                            $rows = ceil($totalCapacity / $cols);
                            $isLoggedIn = isset($_SESSION['user_id']);
                            
                            for ($row = 1; $row <= $rows; $row++) {
                                echo '<div class="seat-row">';
                                for ($col = 1; $col <= 2; $col++) {
                                    $seatNumber = (($row - 1) * $cols) + $col;
                                    
                                    if ($seatNumber > $totalCapacity) {
                                        echo "<div style='width: 45px;'></div>";
                                        continue;
                                    }
                                    
                                    $isBooked = in_array($seatNumber, $bookedSeatsArray);
                                    $isSelected = in_array($seatNumber, $selectedSeatsArray);
                                    
                                    if ($isBooked) {
                                        echo "<label class='seat-label booked' data-seat='$seatNumber'></label>";
                                    } elseif (!$isLoggedIn) {
                                        echo "<label class='seat-label guest-disabled' data-seat='$seatNumber'></label>";
                                    } else {
                                        $checked = $isSelected ? 'checked' : '';
                                           echo "<input type='checkbox' class='seat-checkbox' id='seat-$seatNumber' name='seats[]' value='$seatNumber' $checked>";
                                        echo "<label class='seat-label' for='seat-$seatNumber' data-seat='$seatNumber'></label>";
                                    }
                                }
                                echo '<div class="seat-gap"></div>';
                                for ($col = 3; $col <= 4; $col++) {
                                    $seatNumber = (($row - 1) * $cols) + $col;
                                    
                                    if ($seatNumber > $totalCapacity) {
                                        echo "<div style='width: 45px;'></div>";
                                        continue;
                                    }
                                    
                                    $isBooked = in_array($seatNumber, $bookedSeatsArray);
                                    $isSelected = in_array($seatNumber, $selectedSeatsArray);
                                    
                                    if ($isBooked) {
                                        echo "<label class='seat-label booked' data-seat='$seatNumber'></label>";
                                    } elseif (!$isLoggedIn) {
                                        echo "<label class='seat-label guest-disabled' data-seat='$seatNumber'></label>";
                                    } else {
                                        $checked = $isSelected ? 'checked' : '';
                                           echo "<input type='checkbox' class='seat-checkbox' id='seat-$seatNumber' name='seats[]' value='$seatNumber' $checked>";
                                        echo "<label class='seat-label' for='seat-$seatNumber' data-seat='$seatNumber'></label>";
                                    }
                                }
                                echo '</div>';
                            }
                            ?>
                        </div>
                            <?php if ($isLoggedIn): ?>
                            <div class="text-center mt-3">
                                <button type="submit" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-check2-circle me-1"></i>Seçimi Tamamla
                                </button>
                            </div>
                            <?php endif; ?>
                    </form>

                    <div class="text-center mt-4">
                        <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Toplam <?= $availableSeatsCount ?> koltuk müsait</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="booking-summary-sticky">
                <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Rezervasyon Özeti -->
                <form method="POST" action="/trips/book/<?= htmlspecialchars($trip['id']) ?>">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                    <?php foreach ($selectedSeatsArray as $seat): ?>
                    <input type="hidden" name="seats[]" value="<?= $seat ?>">
                    <?php endforeach; ?>
                    
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-3"><i class="bi bi-receipt me-2"></i>Rezervasyon Özeti</h5>

                            <!-- Seçilen Koltuklar -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Seçilen Koltuklar</label>
                                <?php if ($seatCount > 0): ?>
                                <div class="alert alert-success">
                                    <strong>Koltuk No:</strong> <?= implode(', ', $selectedSeatsArray) ?>
                                </div>
                                <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>Henüz koltuk seçilmedi
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Kupon Kodu -->
                            <div class="mb-3">
                                <label for="couponCode" class="form-label">
                                    <i class="bi bi-tag me-1"></i>Kupon Kodu (Opsiyonel)
                                </label>
                                <input type="text" class="form-control mb-2" id="couponCode" name="coupon_code" 
                                       value="<?= htmlspecialchars($_POST['coupon_code'] ?? '') ?>"
                                       placeholder="İndirim kodunuzu girin">
                                <button type="submit" class="btn btn-outline-secondary btn-sm w-100" name="apply_coupon" value="1">
                                    <i class="bi bi-tag me-1"></i>Kupon Uygula
                                </button>
                                
                                <?php if ($couponMessage): ?>
                                <div class="alert <?= $couponClass ?> mt-2 mb-0">
                                    <i class="bi bi-<?= $couponClass === 'alert-success' ? 'check-circle' : 'x-circle' ?> me-2"></i><?= $couponMessage ?>
                                </div>
                                <?php endif; ?>
                            </div>

                            <hr>

                            <!-- Fiyat Hesaplama -->
                            <div class="d-flex justify-content-between mb-2">
                                <span>Bilet Fiyatı:</span>
                                <span><?= number_format($trip['price'], 2) ?> ₺</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Koltuk Sayısı:</span>
                                <span><?= $seatCount ?></span>
                            </div>
                            <?php if ($discount > 0): ?>
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span><i class="bi bi-tag-fill me-1"></i>İndirim:</span>
                                <span>-<?= number_format($discount, 2) ?> ₺</span>
                            </div>
                            <?php endif; ?>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Toplam Tutar:</strong>
                                <strong class="text-primary fs-5"><?= number_format($total, 2) ?> ₺</strong>
                            </div>

                            <!-- Sanal Bakiye Bilgisi -->
                            <div class="alert alert-warning mb-3">
                                <div class="d-flex justify-content-between">
                                    <small><i class="bi bi-wallet2 me-1"></i>Sanal Bakiyeniz:</small>
                                    <small><strong><?= number_format($_SESSION['user_balance'] ?? 0, 2) ?> ₺</strong></small>
                                </div>
                            </div>

                            <!-- Rezervasyon Butonu -->
                            <?php if ($seatCount == 0): ?>
                                <button type="button" class="btn btn-secondary btn-lg w-100" disabled>
                                    <i class="bi bi-info-circle me-2"></i>Lütfen koltuk seçiniz
                                </button>
                            <?php elseif ($total > ($_SESSION['user_balance'] ?? 0)): ?>
                                <button type="button" class="btn btn-danger btn-lg w-100" disabled>
                                    <i class="bi bi-x-circle me-2"></i>Yetersiz bakiye
                                </button>
                            <?php else: ?>
                                <button type="submit" name="confirm" value="1" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-check-circle me-2"></i>Rezervasyonu Tamamla
                                </button>
                            <?php endif; ?>

                            <div class="text-center mt-3">
                                <small class="text-muted"><i class="bi bi-shield-check me-1"></i>Güvenli ödeme</small>
                            </div>
                        </div>
                    </div>
                </form>
                <?php else: ?>
                <!-- Giriş Yapmamış Kullanıcılar -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-lock fs-1 text-warning mb-3"></i>
                        <h5>Giriş Yapmanız Gerekiyor</h5>
                        <p class="text-muted">Koltuk seçimi ve rezervasyon için lütfen giriş yapın.</p>
                        <a href="/login" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Giriş Yap
                        </a>
                    </div>
                </div>
                <?php endif; ?>

                <!-- İptal Koşulları -->
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-body">
                        <h6 class="card-title mb-2"><i class="bi bi-exclamation-triangle me-2"></i>İptal Koşulları</h6>
                        <small class="text-muted">
                            • Seyahatten 1 saat öncesine kadar ücretsiz iptal<br>
                            • 1 saatten az kalan rezervasyonlar iade edilmez<br>
                            • İptal işlemleri sanal bakiyenize yansıtılır
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
