<?php
/**
 * Firma Admin - Sefer Biletleri Sayfası
 * Belirli bir sefere ait tüm biletleri görüntüler
 */
?>

<div class="container-fluid py-4">
    <!-- Sefer Bilgileri -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <a href="/company/trips" class="btn btn-outline-secondary btn-sm mb-2">
                        <i class="bi bi-arrow-left"></i> Seferlere Dön
                    </a>
                    <h2><i class="bi bi-bus-front me-2"></i>Sefer Biletleri</h2>
                </div>
            </div>
            
            <!-- Sefer Detayları Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="mb-3">
                                <strong><?= htmlspecialchars($trip['departure_city']) ?></strong>
                                <i class="bi bi-arrow-right mx-2 text-primary"></i>
                                <strong><?= htmlspecialchars($trip['destination_city']) ?></strong>
                            </h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <i class="bi bi-calendar3 me-2 text-muted"></i>
                                        <strong>Kalkış:</strong> <?= date('d.m.Y H:i', strtotime($trip['departure_time'])) ?>
                                    </p>
                                    <p class="mb-2">
                                        <i class="bi bi-calendar-check me-2 text-muted"></i>
                                        <strong>Varış:</strong> <?= date('d.m.Y H:i', strtotime($trip['arrival_time'])) ?>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <i class="bi bi-cash me-2 text-muted"></i>
                                        <strong>Bilet Fiyatı:</strong> <?= number_format($trip['price'], 2) ?> ₺
                                    </p>
                                    <p class="mb-2">
                                        <i class="bi bi-people me-2 text-muted"></i>
                                        <strong>Kapasite:</strong> <?= $trip['capacity'] ?> koltuk
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="p-3 bg-primary text-white rounded">
                                        <h3 class="mb-0"><?= $totalTickets ?></h3>
                                        <small>Toplam Bilet</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-3 bg-success text-white rounded">
                                        <h3 class="mb-0"><?= $activeTickets ?></h3>
                                        <small>Aktif</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-3 bg-danger text-white rounded">
                                        <h3 class="mb-0"><?= $canceledTickets ?></h3>
                                        <small>İptal</small>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 p-3 bg-light rounded text-center">
                                <h4 class="mb-0 text-success"><?= number_format($totalRevenue, 2) ?> ₺</h4>
                                <small class="text-muted">Toplam Gelir</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Biletler Listesi -->
    <?php if (empty($tickets)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Bu sefere ait henüz bilet satılmamış.
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-receipt me-2"></i>
                    Satılan Biletler (<?= $totalTickets ?>)
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Bilet No</th>
                                <th>Müşteri Bilgileri</th>
                                <th>Koltuklar</th>
                                <th>Alım Tarihi</th>
                                <th>Tutar</th>
                                <th>Durum</th>
                                <th class="text-end">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tickets as $ticket): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">
                                            #<?= substr($ticket['id'], 0, 8) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong class="d-block">
                                                <i class="bi bi-person-fill me-1 text-primary"></i>
                                                <?= htmlspecialchars($ticket['customer_name']) ?>
                                            </strong>
                                            <small class="text-muted">
                                                <i class="bi bi-envelope me-1"></i>
                                                <?= htmlspecialchars($ticket['customer_email']) ?>
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($ticket['seats'])): ?>
                                            <div class="d-flex flex-wrap gap-1">
                                                <?php foreach ($ticket['seats'] as $seat): ?>
                                                    <span class="badge bg-info"><?= $seat ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small>
                                            <?= date('d.m.Y H:i', strtotime($ticket['created_at'])) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <strong class="text-success">
                                            <?= number_format($ticket['total_price'], 2) ?> ₺
                                        </strong>
                                    </td>
                                    <td>
                                        <?php if ($ticket['status'] === 'active'): ?>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Aktif
                                            </span>
                                        <?php elseif ($ticket['status'] === 'canceled'): ?>
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle me-1"></i>İptal
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">
                                                <?= htmlspecialchars($ticket['status']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <!-- PDF İndirme -->
                                            <a href="<?= BASE_PATH ?>/tickets/download/<?= $ticket['id'] ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="PDF İndir"
                                               target="_blank">
                                                <i class="bi bi-file-pdf"></i>
                                            </a>
                                            
                                            <!-- Bilet Detay -->
                                            <a href="<?= BASE_PATH ?>/tickets/view/<?= $ticket['id'] ?>" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="Detay Görüntüle"
                                               target="_blank">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            
                                            <!-- İptal Butonu (sadece aktif biletler için) -->
                                            <?php if ($ticket['status'] === 'active'): ?>
                                                <?php
                                                // Kalkış saatine 1 saatten fazla var mı kontrol et
                                                $departureTime = strtotime($trip['departure_time']);
                                                $currentTime = time();
                                                $timeDiff = $departureTime - $currentTime;
                                                $canCancel = $timeDiff > 3600; // 1 saat = 3600 saniye
                                                ?>
                                                
                                                <?php if ($canCancel): ?>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            onclick="confirmCancel('<?= $ticket['id'] ?>', '<?= htmlspecialchars($ticket['customer_name'], ENT_QUOTES) ?>')"
                                                            title="Bileti İptal Et">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-secondary" 
                                                            disabled
                                                            title="İptal süresi geçti (1 saat kuralı)">
                                                        <i class="bi bi-ban"></i>
                                                    </button>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- İptal Onay Formu (Gizli) -->
<form id="cancelForm" method="POST" action="" style="display: none;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
</form>

<script>
function confirmCancel(ticketId, customerName) {
    if (confirm(`${customerName} müşterisine ait bileti iptal etmek istediğinizden emin misiniz?\n\nBilet ücreti müşterinin hesabına iade edilecektir.`)) {
        const form = document.getElementById('cancelForm');
        form.action = '<?= BASE_PATH ?>/company/tickets/cancel/' + ticketId;
        form.submit();
    }
}
</script>

<style>
.table th {
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
    white-space: nowrap;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.btn-group .btn {
    padding: 0.25rem 0.5rem;
}

.badge {
    font-weight: 500;
}
</style>
