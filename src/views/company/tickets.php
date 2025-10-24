<?php
/**
 * Firma Admin - Biletler Sayfası
 * Firma seferlerindeki tüm biletleri görüntüler
 */
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-ticket-alt me-2"></i>Firma Biletleri</h2>
                    <p class="text-muted mb-0">Firmanızın seferlerindeki tüm biletleri görüntüleyin ve yönetin</p>
                </div>
                <div>
                    <span class="badge bg-primary fs-6">Toplam: <?= $totalTickets ?> bilet</span>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($tickets)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Henüz firmanıza ait bilet bulunmamaktadır.
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Bilet No</th>
                                <th>Müşteri</th>
                                <th>Sefer</th>
                                <th>Tarih</th>
                                <th>Koltuklar</th>
                                <th>Tutar</th>
                                <th>Durum</th>
                                <th class="text-end">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tickets as $ticket): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">#<?= substr($ticket['id'], 0, 8) ?></span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?= htmlspecialchars($ticket['customer_name']) ?></strong><br>
                                            <small class="text-muted"><?= htmlspecialchars($ticket['customer_email']) ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?= htmlspecialchars($ticket['departure_city']) ?></strong>
                                            <i class="fas fa-arrow-right mx-1 text-primary"></i>
                                            <strong><?= htmlspecialchars($ticket['destination_city']) ?></strong>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <?= date('d.m.Y', strtotime($ticket['departure_time'])) ?><br>
                                            <small class="text-muted"><?= date('H:i', strtotime($ticket['departure_time'])) ?></small>
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
                                        <strong class="text-success"><?= number_format($ticket['total_price'], 2) ?> ₺</strong>
                                    </td>
                                    <td>
                                        <?php if ($ticket['status'] === 'active'): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php elseif ($ticket['status'] === 'canceled'): ?>
                                            <span class="badge bg-danger">İptal</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?= htmlspecialchars($ticket['status']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <!-- PDF İndirme -->
                                            <a href="<?= BASE_PATH ?>/tickets/download/<?= $ticket['id'] ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="PDF İndir"
                                               target="_blank">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                            
                                            <!-- Bilet Detay -->
                                            <a href="<?= BASE_PATH ?>/tickets/view/<?= $ticket['id'] ?>" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="Detay Görüntüle"
                                               target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <!-- İptal Butonu (sadece aktif biletler için) -->
                                            <?php if ($ticket['status'] === 'active'): ?>
                                                <?php
                                                // Kalkış saatine 1 saatten fazla var mı kontrol et
                                                $departureTime = strtotime($ticket['departure_time']);
                                                $currentTime = time();
                                                $timeDiff = $departureTime - $currentTime;
                                                $canCancel = $timeDiff > 3600; // 1 saat = 3600 saniye
                                                ?>
                                                
                                                <?php if ($canCancel): ?>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            onclick="confirmCancel('<?= $ticket['id'] ?>', '<?= htmlspecialchars($ticket['customer_name'], ENT_QUOTES) ?>')"
                                                            title="Bileti İptal Et">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-secondary" 
                                                            disabled
                                                            title="İptal süresi geçti (1 saat kuralı)">
                                                        <i class="fas fa-ban"></i>
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

        <!-- Sayfalama -->
        <?php if ($totalPages > 1): ?>
            <nav class="mt-4" aria-label="Sayfa navigasyonu">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page - 1 ?>">Önceki</a>
                        </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page + 1 ?>">Sonraki</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
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
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.btn-group .btn {
    padding: 0.25rem 0.5rem;
}
</style>
