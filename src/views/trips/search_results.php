<?php
/**
 * Sefer Arama Sonuçları
 * 
 * Variables:
 * - $trips: Bulunan seferler
 * - $departureCity: Kalkış şehri
 * - $destinationCity: Varış şehri
 * - $departureDate: Sefer tarihi
 * - $cities: Şehir listesi (yeniden arama için)
 */

// Debug: Değişkenleri kontrol et
if (!isset($trips)) {
    echo "<!-- HATA: \$trips değişkeni tanımlı değil! -->";
    $trips = [];
}
if (!isset($cities)) {
    echo "<!-- HATA: \$cities değişkeni tanımlı değil! -->";
    $cities = [];
}

// Debug bilgisi (HTML yorumu olarak)
echo "<!-- Debug: " . count($trips) . " sefer, " . count($cities) . " şehir -->";
?>

<div class="container my-4">
    <!-- Arama Formu (Sticky) -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="/trips/search" method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small mb-1">Nereden</label>
                    <select class="form-select form-select-sm" name="departure_city" required>
                        <option value="">Şehir Seçin</option>
                        <?php foreach ($cities as $city): ?>
                            <option value="<?= htmlspecialchars($city) ?>" 
                                    <?= ($departureCity === $city) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($city) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label small mb-1">Nereye</label>
                    <select class="form-select form-select-sm" name="destination_city" required>
                        <option value="">Şehir Seçin</option>
                        <?php foreach ($cities as $city): ?>
                            <option value="<?= htmlspecialchars($city) ?>" 
                                    <?= ($destinationCity === $city) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($city) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label small mb-1">Tarih</label>
                    <input type="date" 
                           class="form-control form-control-sm" 
                           name="departure_date" 
                           value="<?= htmlspecialchars($departureDate) ?>"
                           min="<?= date('Y-m-d') ?>" 
                           required>
                </div>
                
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-search me-1"></i>Ara
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Arama Bilgisi ve Tarih Navigasyonu -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <h4 class="mb-0">
                <i class="bi bi-bus-front text-primary me-2"></i>
                <?= htmlspecialchars($departureCity) ?> 
                <i class="bi bi-arrow-right mx-2"></i> 
                <?= htmlspecialchars($destinationCity) ?>
            </h4>
            <!-- Şehir Değiştirme Butonu -->
            <a href="/trips/search?departure_city=<?= urlencode($destinationCity) ?>&destination_city=<?= urlencode($departureCity) ?>&departure_date=<?= htmlspecialchars($departureDate) ?>" 
               class="btn btn-outline-primary btn-sm" 
               title="Nereden - Nereye değiştir">
                <i class="bi bi-arrow-left-right"></i>
            </a>
        </div>
        
        <!-- Tarih Navigasyonu -->
        <div class="btn-group" role="group">
            <?php
            $prevDate = date('Y-m-d', strtotime($departureDate . ' -1 day'));
            $nextDate = date('Y-m-d', strtotime($departureDate . ' +1 day'));
            $today = date('Y-m-d');
            ?>
            <a href="/trips/search?departure_city=<?= urlencode($departureCity) ?>&destination_city=<?= urlencode($destinationCity) ?>&departure_date=<?= $prevDate ?>" 
               class="btn btn-outline-primary btn-sm <?= ($prevDate < $today) ? 'disabled' : '' ?>">
                <i class="bi bi-chevron-left"></i> Önceki Gün
            </a>
            <span class="btn btn-primary btn-sm">
                <?= date('d.m.Y', strtotime($departureDate)) ?>
            </span>
            <a href="/trips/search?departure_city=<?= urlencode($departureCity) ?>&destination_city=<?= urlencode($destinationCity) ?>&departure_date=<?= $nextDate ?>" 
               class="btn btn-outline-primary btn-sm">
                Sonraki Gün <i class="bi bi-chevron-right"></i>
            </a>
        </div>
    </div>

    <?php if (empty($trips)): ?>
        <!-- Sonuç Bulunamadı -->
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-emoji-frown display-1 text-muted"></i>
                <h5 class="mt-3">Sefer Bulunamadı</h5>
                <p class="text-muted">
                    Seçtiğiniz güzergah ve tarih için uygun sefer bulunamamıştır.<br>
                    Lütfen farklı bir tarih veya güzergah deneyin.
                </p>
                <a href="/" class="btn btn-primary mt-3">
                    <i class="bi bi-arrow-left me-2"></i>Ana Sayfaya Dön
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Sonuç Sayısı -->
        <div class="alert alert-info alert-dismissible fade show">
            <i class="bi bi-info-circle me-2"></i>
            <strong><?= count($trips) ?></strong> sefer bulundu
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <!-- Sefer Listesi -->
        <div class="row g-3">
            <?php foreach ($trips as $trip): ?>
                <?php
                // Koltuk doluluk hesaplama
                $bookedSeats = $trip['booked_seats_count'] ?? 0;
                $availableSeats = $trip['available_seats'] ?? ($trip['capacity'] - $bookedSeats);
                $occupancyPercent = ($trip['capacity'] > 0) ? (($bookedSeats / $trip['capacity']) * 100) : 0;
                
                // Doluluk durumuna göre renk
                if ($occupancyPercent >= 90) {
                    $seatBadgeClass = 'bg-danger';
                    $seatText = 'Az koltuk';
                } elseif ($occupancyPercent >= 70) {
                    $seatBadgeClass = 'bg-warning text-dark';
                    $seatText = 'Dolmak üzere';
                } else {
                    $seatBadgeClass = 'bg-success';
                    $seatText = 'Uygun';
                }
                ?>
                
                <div class="col-12">
                    <div class="card border-0 shadow-sm hover-shadow transition">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <!-- Firma Bilgisi -->
                                <div class="col-md-2 text-center border-end">
                                    <h6 class="mb-1 text-primary"><?= htmlspecialchars($trip['company_name'] ?? 'Firma') ?></h6>
                                    <small class="text-muted">Otobüs</small>
                                </div>
                                
                                <!-- Saat Bilgileri -->
                                <div class="col-md-4 border-end">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="text-center">
                                            <h4 class="mb-0"><?= date('H:i', strtotime($trip['departure_time'])) ?></h4>
                                            <small class="text-muted"><?= htmlspecialchars($trip['departure_city']) ?></small>
                                        </div>
                                        
                                        <div class="text-center px-3">
                                            <i class="bi bi-arrow-right text-primary"></i>
                                            <br>
                                            <small class="text-muted">
                                                <?php
                                                $duration = (strtotime($trip['arrival_time']) - strtotime($trip['departure_time'])) / 3600;
                                                echo number_format($duration, 1) . ' saat';
                                                ?>
                                            </small>
                                        </div>
                                        
                                        <div class="text-center">
                                            <h4 class="mb-0"><?= date('H:i', strtotime($trip['arrival_time'])) ?></h4>
                                            <small class="text-muted"><?= htmlspecialchars($trip['destination_city']) ?></small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Koltuk Durumu -->
                                <div class="col-md-3 text-center border-end">
                                    <span class="badge <?= $seatBadgeClass ?> mb-2"><?= $seatText ?></span>
                                    <br>
                                    <small class="text-muted">
                                        <i class="bi bi-people me-1"></i>
                                        <?= $availableSeats ?> / <?= $trip['capacity'] ?> koltuk
                                    </small>
                                </div>
                                
                                <!-- Fiyat ve İşlemler -->
                                <div class="col-md-3 text-center">
                                    <h3 class="text-primary mb-2">
                                        <?= number_format($trip['price'], 2) ?> ₺
                                    </h3>
                                    <?php if ($availableSeats > 0): ?>
                                        <a href="/trips/details/<?= urlencode($trip['id']) ?>" 
                                           class="btn btn-primary btn-sm w-100">
                                            <i class="bi bi-ticket-detailed me-1"></i>Sefer Detayı
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm w-100" disabled>
                                            <i class="bi bi-x-circle me-1"></i>Doldu
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Bilgilendirme -->
        <div class="alert alert-light mt-4">
            <i class="bi bi-lightbulb text-warning me-2"></i>
            <strong>İpucu:</strong> Sefer detaylarına tıklayarak koltuk haritasını görüntüleyebilir ve bilet satın alabilirsiniz.
        </div>
    <?php endif; ?>
</div>

<style>
.hover-shadow {
    transition: all 0.3s ease;
}

.hover-shadow:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.transition {
    transition: all 0.3s ease;
}
</style>
