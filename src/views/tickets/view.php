<?php
/**
 * Bilet Detay Görünümü
 * 
 * Variables:
 * - $ticket: Bilet bilgileri (trip_id, user_id, status, total_price, seats, departure_city, destination_city, departure_time, arrival_time, company_name)
 */

// Bilet durumu badge rengi
$statusColors = [
    'active' => 'success',
    'canceled' => 'danger',
    'expired' => 'secondary'
];

$statusTexts = [
    'active' => 'Aktif',
    'canceled' => 'İptal Edildi',
    'expired' => 'Süresi Doldu'
];

$statusColor = $statusColors[$ticket['status']] ?? 'secondary';
$statusText = $statusTexts[$ticket['status']] ?? 'Bilinmiyor';

// Koltuk numaralarını virgülle ayır
$seatNumbers = implode(', ', $ticket['seats'] ?? []);

// Seyahat süresini hesapla
$departureTime = new DateTime($ticket['departure_time']);
$arrivalTime = new DateTime($ticket['arrival_time']);
$duration = $departureTime->diff($arrivalTime);
$durationText = $duration->h . ' saat ' . $duration->i . ' dakika';

// İptal edilebilir mi kontrol et (seyahatten 24 saat öncesine kadar)
$now = new DateTime();
$hoursUntilDeparture = ($departureTime->getTimestamp() - $now->getTimestamp()) / 3600;
$canCancel = ($ticket['status'] === 'active') && ($hoursUntilDeparture >= 1);
?>

<div class="container my-5">
    <!-- Başlık -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>
            <i class="bi bi-ticket-perforated-fill text-primary me-2"></i>
            Bilet Detayı
        </h3>
        <a href="/user/tickets" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Biletlerime Dön
        </a>
    </div>

    <div class="row g-4">
        <!-- Sol Kolon: Bilet Bilgileri -->
        <div class="col-lg-8">
            <!-- Bilet Kartı -->
            <div class="card border-0 shadow-lg">
                <!-- Bilet Başlığı -->
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">
                                <i class="bi bi-building me-2"></i>
                                <?= htmlspecialchars($ticket['company_name'] ?? 'Otobüs Firması') ?>
                            </h5>
                            <small>Bilet No: <?= htmlspecialchars($ticket['id']) ?></small>
                        </div>
                        <span class="badge bg-<?= $statusColor ?> fs-6 px-3 py-2">
                            <?= $statusText ?>
                        </span>
                    </div>
                </div>

                <!-- Bilet İçeriği -->
                <div class="card-body p-4">
                    <!-- Güzergah Bilgisi -->
                    <div class="row text-center mb-4 py-3 bg-light rounded">
                        <div class="col-5">
                            <h4 class="text-primary mb-1"><?= htmlspecialchars($ticket['departure_city']) ?></h4>
                            <p class="text-muted mb-1">Kalkış</p>
                            <p class="mb-0 fw-bold"><?= date('H:i', strtotime($ticket['departure_time'])) ?></p>
                            <small class="text-muted"><?= date('d.m.Y', strtotime($ticket['departure_time'])) ?></small>
                        </div>
                        <div class="col-2 d-flex align-items-center justify-content-center">
                            <div class="text-center">
                                <i class="bi bi-arrow-right text-primary" style="font-size: 2rem;"></i>
                                <div><small class="text-muted"><?= $durationText ?></small></div>
                            </div>
                        </div>
                        <div class="col-5">
                            <h4 class="text-primary mb-1"><?= htmlspecialchars($ticket['destination_city']) ?></h4>
                            <p class="text-muted mb-1">Varış</p>
                            <p class="mb-0 fw-bold"><?= date('H:i', strtotime($ticket['arrival_time'])) ?></p>
                            <small class="text-muted"><?= date('d.m.Y', strtotime($ticket['arrival_time'])) ?></small>
                        </div>
                    </div>

                    <hr>

                    <!-- Bilet Detayları -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block mb-1">
                                    <i class="bi bi-calendar-event me-1"></i>
                                    Tarih
                                </small>
                                <strong><?= date('d F Y, l', strtotime($ticket['departure_time'])) ?></strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block mb-1">
                                    <i class="bi bi-clock me-1"></i>
                                    Kalkış Saati
                                </small>
                                <strong><?= date('H:i', strtotime($ticket['departure_time'])) ?></strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block mb-1">
                                    <i class="bi bi-people me-1"></i>
                                    Koltuk Numaraları
                                </small>
                                <strong><?= htmlspecialchars($seatNumbers) ?></strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block mb-1">
                                    <i class="bi bi-cash me-1"></i>
                                    Toplam Ücret
                                </small>
                                <strong class="text-primary fs-5"><?= number_format($ticket['total_price'], 2) ?> ₺</strong>
                            </div>
                        </div>
                    </div>

                    <!-- QR Kod Alanı (Placeholder) -->
                    <div class="text-center py-4 border rounded">
                        <div class="mb-3">
                            <i class="bi bi-qr-code" style="font-size: 120px; color: #ddd;"></i>
                        </div>
                        <p class="text-muted mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            Bu QR kodu otobüse binerken gösteriniz
                        </p>
                    </div>
                </div>

                <!-- Bilet Footer -->
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="bi bi-calendar-plus me-1"></i>
                            Oluşturulma: <?= date('d.m.Y H:i', strtotime($ticket['created_at'])) ?>
                        </small>
                        <?php if ($ticket['status'] === 'canceled'): ?>
                            <small class="text-danger">
                                <i class="bi bi-x-circle me-1"></i>
                                İptal Tarihi: <?= date('d.m.Y H:i', strtotime($ticket['updated_at'] ?? $ticket['created_at'])) ?>
                            </small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sağ Kolon: İşlemler ve Bilgiler -->
        <div class="col-lg-4">
            <!-- İşlem Butonları -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="card-title mb-3">
                        <i class="bi bi-gear me-2"></i>
                        İşlemler
                    </h6>
                    
                    <?php if ($ticket['status'] === 'active'): ?>
                        <!-- PDF İndir Butonu -->
                        <a href="/tickets/download/<?= htmlspecialchars($ticket['id']) ?>" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-file-earmark-pdf me-2"></i>
                            PDF İndir
                        </a>

                        <!-- İptal Butonu -->
                        <?php if ($canCancel): ?>
                            <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                <i class="bi bi-x-circle me-2"></i>
                                Bileti İptal Et
                            </button>
                            <small class="text-muted d-block mt-2 text-center">
                                <i class="bi bi-info-circle me-1"></i>
                                <?= max(0, (int)round($hoursUntilDeparture)) ?> saat kaldı (iptal edilebilir)
                            </small>
                        <?php else: ?>
                            <button type="button" class="btn btn-secondary w-100" disabled>
                                <i class="bi bi-x-circle me-2"></i>
                                İptal Edilemez
                            </button>
                            <small class="text-muted d-block mt-2 text-center">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Seyahatten 1 saatten az kaldı
                            </small>
                        <?php endif; ?>
                    <?php elseif ($ticket['status'] === 'canceled'): ?>
                        <div class="alert alert-danger mb-0">
                            <i class="bi bi-x-circle me-2"></i>
                            Bu bilet iptal edilmiştir
                        </div>
                    <?php elseif ($ticket['status'] === 'expired'): ?>
                        <div class="alert alert-secondary mb-0">
                            <i class="bi bi-clock-history me-2"></i>
                            Bu biletin süresi dolmuştur
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Önemli Bilgiler -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        Önemli Bilgiler
                    </h6>
                    
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <small>Seyahatten 30 dakika önce terminalde olun</small>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <small>Kimlik kontrolü yapılacaktır</small>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <small>QR kodu ve bilet numarasını gösteriniz</small>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <small>24 saat öncesine kadar ücretsiz iptal</small>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Yardım -->
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="card-title mb-2">
                        <i class="bi bi-headset me-2"></i>
                        Yardım mı lazım?
                    </h6>
                    <small class="text-muted">
                        Sorularınız için 7/24 müşteri hizmetlerimizi arayabilirsiniz.
                    </small>
                    <a href="tel:0850123456" class="btn btn-outline-primary btn-sm w-100 mt-2">
                        <i class="bi bi-telephone me-2"></i>
                        0850 123 45 67
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- İptal Onay Modal -->
<?php if ($ticket['status'] === 'active' && $canCancel): ?>
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="cancelModalLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Bilet İptal Onayı
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">
                    <strong><?= htmlspecialchars($ticket['departure_city']) ?> → <?= htmlspecialchars($ticket['destination_city']) ?></strong> 
                    seferine ait biletinizi iptal etmek istediğinize emin misiniz?
                </p>
                
                <div class="alert alert-warning">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong><?= number_format($ticket['total_price'], 2) ?> ₺</strong> tutarındaki ücret sanal hesabınıza iade edilecektir.
                </div>

                <ul class="mb-0">
                    <li>Koltuk No: <strong><?= htmlspecialchars($seatNumbers) ?></strong></li>
                    <li>Tarih: <strong><?= date('d.m.Y H:i', strtotime($ticket['departure_time'])) ?></strong></li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x me-2"></i>Vazgeç
                </button>
                <a href="/tickets/cancel/<?= htmlspecialchars($ticket['id']) ?>" class="btn btn-danger">
                    <i class="bi bi-check-circle me-2"></i>Evet, İptal Et
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
