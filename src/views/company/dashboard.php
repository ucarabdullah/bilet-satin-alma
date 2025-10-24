<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-icon blue">
                <i class="bi bi-bus-front"></i>
            </div>
            <h3 class="stat-card-value"><?= $stats['trips'] ?? 0 ?></h3>
            <p class="stat-card-label">Toplam Sefer</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-icon green">
                <i class="bi bi-ticket-perforated"></i>
            </div>
            <h3 class="stat-card-value"><?= $stats['bookings'] ?? 0 ?></h3>
            <p class="stat-card-label">Rezervasyon</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-icon orange">
                <i class="bi bi-cash-stack"></i>
            </div>
            <h3 class="stat-card-value"><?= number_format($stats['revenue'] ?? 0, 0) ?> ₺</h3>
            <p class="stat-card-label">Toplam Gelir</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-icon purple">
                <i class="bi bi-tag"></i>
            </div>
            <h3 class="stat-card-value"><?= $stats['coupons'] ?? 0 ?></h3>
            <p class="stat-card-label">Aktif Kupon</p>
        </div>
    </div>
</div>

<!-- Quick Actions & Recent Trips -->
<div class="row g-4">
    <div class="col-md-4">
        <div class="company-table-card">
            <h5 class="mb-3"><i class="bi bi-lightning-charge me-2"></i>Hızlı İşlemler</h5>
            <div class="d-grid gap-2">
                <a href="/company/trips/new" class="btn btn-company-primary">
                    <i class="bi bi-plus-circle me-2"></i>Yeni Sefer Ekle
                </a>
                <a href="/company/coupons/new" class="btn btn-outline-primary">
                    <i class="bi bi-ticket-perforated me-2"></i>Yeni Kupon Oluştur
                </a>
                <a href="/company/trips" class="btn btn-outline-secondary">
                    <i class="bi bi-list-ul me-2"></i>Tüm Seferleri Görüntüle
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="company-table-card">
            <h5 class="mb-3"><i class="bi bi-clock-history me-2"></i>Son Seferler</h5>
            <?php if (!empty($recentTrips ?? [])): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Güzergah</th>
                                <th>Tarih</th>
                                <th>Fiyat</th>
                                <th>Kapasite</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentTrips as $trip): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($trip['departure_city']) ?></strong>
                                        <i class="bi bi-arrow-right mx-1"></i>
                                        <strong><?= htmlspecialchars($trip['destination_city']) ?></strong>
                                    </td>
                                    <td>
                                        <small><?= date('d.m.Y H:i', strtotime($trip['departure_time'])) ?></small>
                                    </td>
                                    <td><strong><?= number_format($trip['price'], 0) ?> ₺</strong></td>
                                    <td><?= (int)$trip['capacity'] ?> kişi</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center text-muted py-4">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    <p>Henüz sefer eklenmemiş</p>
                    <a href="/company/trips/new" class="btn btn-sm btn-company-primary">İlk Seferi Ekle</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
