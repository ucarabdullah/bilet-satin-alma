<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-icon primary">
                <i class="bi bi-building"></i>
            </div>
            <h3 class="stat-card-value"><?= $stats['companies'] ?? 0 ?></h3>
            <p class="stat-card-label">Toplam Firma</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-icon success">
                <i class="bi bi-people"></i>
            </div>
            <h3 class="stat-card-value"><?= $stats['users'] ?? 0 ?></h3>
            <p class="stat-card-label">Toplam Kullanıcı</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-icon warning">
                <i class="bi bi-bus-front"></i>
            </div>
            <h3 class="stat-card-value"><?= $stats['trips'] ?? 0 ?></h3>
            <p class="stat-card-label">Aktif Sefer</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-icon info">
                <i class="bi bi-ticket-perforated"></i>
            </div>
            <h3 class="stat-card-value"><?= $stats['coupons'] ?? 0 ?></h3>
            <p class="stat-card-label">Kupon</p>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-4">
    <div class="col-md-4">
        <div class="admin-table-card">
            <h5 class="mb-3"><i class="bi bi-lightning-charge me-2"></i>Hızlı İşlemler</h5>
            <div class="d-grid gap-2">
                <a href="/admin/companies/new" class="btn btn-admin-primary">
                    <i class="bi bi-plus-circle me-2"></i>Yeni Firma Ekle
                </a>
                <a href="/admin/users/new" class="btn btn-outline-primary">
                    <i class="bi bi-person-plus me-2"></i>Yeni Kullanıcı Ekle
                </a>
                <a href="/admin/coupons/new" class="btn btn-outline-primary">
                    <i class="bi bi-ticket-perforated me-2"></i>Yeni Kupon Oluştur
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="admin-table-card">
            <h5 class="mb-3"><i class="bi bi-activity me-2"></i>Son Aktiviteler</h5>
            <div class="list-group list-group-flush">
                <?php if (!empty($recentActivities ?? [])): ?>
                    <?php foreach ($recentActivities as $activity): ?>
                        <div class="list-group-item px-0">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <strong><?= htmlspecialchars($activity['title'] ?? '') ?></strong>
                                    <small class="text-muted d-block"><?= htmlspecialchars($activity['time'] ?? '') ?></small>
                                </div>
                                <span class="badge bg-primary"><?= htmlspecialchars($activity['type'] ?? '') ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="list-group-item px-0 text-muted text-center py-4">
                        <i class="bi bi-info-circle me-2"></i>Henüz aktivite yok
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
