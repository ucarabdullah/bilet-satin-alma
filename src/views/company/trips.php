<?php /** @var array $trips */ ?>
<div class="company-table-card">
  <div class="company-table-header">
    <h2><i class="bi bi-bus-front me-2"></i>Seferler</h2>
    <a href="/company/trips/new" class="btn btn-company-primary btn-sm">
      <i class="bi bi-plus-circle me-1"></i>Yeni Sefer
    </a>
  </div>
  <?php if (empty($trips)): ?>
    <div class="alert alert-info">
      <i class="bi bi-info-circle me-2"></i>Henüz sefer yok.
    </div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Güzergah</th>
            <th>Kalkış</th>
            <th>Varış</th>
            <th>Fiyat</th>
            <th>Kapasite</th>
            <th style="width:200px" class="text-end">İşlemler</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($trips as $t): ?>
          <tr>
            <td>
              <strong><?= htmlspecialchars($t['departure_city']) ?></strong>
              <i class="bi bi-arrow-right mx-2"></i>
              <strong><?= htmlspecialchars($t['destination_city']) ?></strong>
            </td>
            <td><small><?= date('d.m.Y H:i', strtotime($t['departure_time'])) ?></small></td>
            <td><small><?= date('d.m.Y H:i', strtotime($t['arrival_time'])) ?></small></td>
            <td><strong class="text-success"><?= number_format($t['price'], 0) ?> ₺</strong></td>
            <td>
              <span class="badge bg-primary"><?= (int)$t['capacity'] ?> kişi</span>
            </td>
            <td class="text-end">
              <a href="/company/trips/<?= urlencode($t['id']) ?>/tickets" class="btn btn-sm btn-outline-info me-1" title="Biletleri Görüntüle">
                <i class="bi bi-receipt"></i> Biletler
              </a>
              <a href="/company/trips/edit/<?= urlencode($t['id']) ?>" class="btn btn-sm btn-outline-primary me-1">
                <i class="bi bi-pencil"></i> Düzenle
              </a>
              <form method="post" action="/company/trips/delete/<?= urlencode($t['id']) ?>" class="d-inline" onsubmit="return confirm('Silmek istediğinize emin misiniz?');">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger">
                  <i class="bi bi-trash"></i> Sil
                </button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
