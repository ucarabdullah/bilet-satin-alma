<!-- src/views/user/dashboard.php -->
<div class="container my-4">
    <h1 class="mb-4">Hoş Geldin, <?= htmlspecialchars($user['full_name']) ?></h1>
    
    <div class="row">
        <!-- Kullanıcı Bilgileri -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Kullanıcı Bilgileri</h5>
                </div>
                <div class="card-body">
                    <p><strong>Ad Soyad:</strong> <?= htmlspecialchars($user['full_name']) ?></p>
                    <p><strong>E-posta:</strong> <?= htmlspecialchars($user['email']) ?></p>
                    <p><strong>Hesap Bakiyesi:</strong> <?= number_format($user['balance'], 2) ?> TL</p>
                    <a href="/user/profile" class="btn btn-outline-primary btn-sm">Profili Düzenle</a>
                </div>
            </div>
        </div>
        
        <!-- Hızlı İşlemler -->
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Hızlı İşlemler</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 col-md-4 mb-3 text-center">
                            <a href="/#search-form" class="text-decoration-none">
                                <div class="bg-light p-3 rounded">
                                    <i class="bi bi-search fs-2"></i>
                                    <p class="mb-0 mt-2">Bilet Ara</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-4 mb-3 text-center">
                            <a href="/user/tickets" class="text-decoration-none">
                                <div class="bg-light p-3 rounded">
                                    <i class="bi bi-ticket-perforated fs-2"></i>
                                    <p class="mb-0 mt-2">Biletlerim</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-4 mb-3 text-center">
                            <a href="/user/profile" class="text-decoration-none">
                                <div class="bg-light p-3 rounded">
                                    <i class="bi bi-person fs-2"></i>
                                    <p class="mb-0 mt-2">Profilim</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Yaklaşan Seyahatler -->
    <div class="row mt-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Yaklaşan Seyahatleriniz</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($upcomingTrips)): ?>
                        <div class="alert alert-info">
                            Yaklaşan seyahatiniz bulunmamaktadır. 
                            <a href="/#search-form" class="alert-link">Bilet aramak için tıklayın</a>.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Firma</th>
                                        <th>Nereden - Nereye</th>
                                        <th>Tarih</th>
                                        <th>Saat</th>
                                        <th>İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($upcomingTrips as $trip): ?>
                                        <tr>
                                            <td>
                                                <?php if ($trip['company_logo']): ?>
                                                    <img src="<?= htmlspecialchars($trip['company_logo']) ?>" alt="<?= htmlspecialchars($trip['company_name']) ?>" height="30">
                                                <?php else: ?>
                                                    <?= htmlspecialchars($trip['company_name']) ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($trip['departure_city']) ?> - 
                                                <?= htmlspecialchars($trip['destination_city']) ?>
                                            </td>
                                            <td>
                                                <?= date('d.m.Y', strtotime($trip['departure_time'])) ?>
                                            </td>
                                            <td>
                                                <?= date('H:i', strtotime($trip['departure_time'])) ?>
                                            </td>
                                            <td>
                                                <a href="/tickets/view/<?= $trip['ticket_id'] ?>" class="btn btn-sm btn-outline-primary">
                                                    Bileti Görüntüle
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Geçmiş Seyahatler -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Geçmiş Seyahatleriniz</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($pastTrips)): ?>
                        <div class="alert alert-secondary">
                            Geçmiş seyahatiniz bulunmamaktadır.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Firma</th>
                                        <th>Nereden - Nereye</th>
                                        <th>Tarih</th>
                                        <th>Saat</th>
                                        <th>İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pastTrips as $trip): ?>
                                        <tr class="text-muted">
                                            <td>
                                                <?php if ($trip['company_logo']): ?>
                                                    <img src="<?= htmlspecialchars($trip['company_logo']) ?>" alt="<?= htmlspecialchars($trip['company_name']) ?>" height="30" style="opacity: 0.7;">
                                                <?php else: ?>
                                                    <?= htmlspecialchars($trip['company_name']) ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($trip['departure_city']) ?> - 
                                                <?= htmlspecialchars($trip['destination_city']) ?>
                                            </td>
                                            <td>
                                                <?= date('d.m.Y', strtotime($trip['departure_time'])) ?>
                                            </td>
                                            <td>
                                                <?= date('H:i', strtotime($trip['departure_time'])) ?>
                                            </td>
                                            <td>
                                                <a href="/tickets/view/<?= $trip['ticket_id'] ?>" class="btn btn-sm btn-outline-secondary">
                                                    Bileti Görüntüle
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Aktif Biletler -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Aktif Biletleriniz</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($activeTickets)): ?>
                        <div class="alert alert-info">
                            Aktif biletiniz bulunmamaktadır.
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($activeTickets as $ticket): ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 border-primary">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>Bilet #<?= substr($ticket['id'], 0, 8) ?></span>
                                                <span class="badge bg-success">Aktif</span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title">
                                                <?= htmlspecialchars($ticket['departure_city']) ?> - 
                                                <?= htmlspecialchars($ticket['destination_city']) ?>
                                            </h5>
                                            
                                            <p class="card-text">
                                                <strong>Firma:</strong> <?= htmlspecialchars($ticket['company_name']) ?><br>
                                                <strong>Tarih:</strong> <?= date('d.m.Y', strtotime($ticket['departure_time'])) ?><br>
                                                <strong>Kalkış Saati:</strong> <?= date('H:i', strtotime($ticket['departure_time'])) ?><br>
                                                <strong>Varış Saati:</strong> <?= date('H:i', strtotime($ticket['arrival_time'])) ?><br>
                                                <strong>Koltuk:</strong> 
                                                <?php if (!empty($ticket['seats'])): ?>
                                                    <?= implode(', ', $ticket['seats']) ?>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        <div class="card-footer bg-white">
                                            <div class="d-flex justify-content-between">
                                                <a href="/tickets/view/<?= $ticket['id'] ?>" class="btn btn-sm btn-primary">
                                                    Detaylar
                                                </a>
                                                <a href="/tickets/cancel/<?= $ticket['id'] ?>" class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('Bileti iptal etmek istediğinize emin misiniz?');">
                                                    İptal Et
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>