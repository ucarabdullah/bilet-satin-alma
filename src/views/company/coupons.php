<?php /** @var array $coupons */ ?>
<div class="company-table-card">
  <div class="company-table-header">
    <h2><i class="bi bi-ticket-perforated me-2"></i>Kuponlar</h2>
    <a href="/company/coupons/new" class="btn btn-company-primary btn-sm">
      <i class="bi bi-plus-circle me-1"></i>Yeni Kupon
    </a>
  </div>
  <?php if (empty($coupons)): ?>
    <div class="alert alert-info">
      <i class="bi bi-info-circle me-2"></i>Henüz kupon yok.
    </div>
  <?php else: ?>
  <div class="table-responsive">
    <table class="table table-hover">
      <thead>
        <tr>
          <th>Kod</th>
          <th>İndirim</th>
          <th>Limit</th>
          <th>Son Tarih</th>
          <th>Oluşturan</th>
          <th style="width:200px" class="text-end">İşlemler</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($coupons as $c): ?>
        <?php $isAdminCoupon = ($c['company_id'] === null); ?>
        <tr>
          <td>
            <code class="bg-light px-2 py-1 rounded"><?= htmlspecialchars($c['code']) ?></code>
          </td>
          <td><strong class="text-success"><?= htmlspecialchars($c['discount']) ?>%</strong></td>
          <td><span class="badge bg-secondary"><?= (int)$c['usage_limit'] ?> kullanım</span></td>
          <td><small><?= htmlspecialchars($c['expire_date']) ?></small></td>
          <td>
            <?php if ($isAdminCoupon): ?>
              <span class="badge bg-warning text-dark"><i class="bi bi-shield-check"></i> Admin</span>
            <?php else: ?>
              <span class="badge bg-info"><i class="bi bi-building"></i> Firma</span>
            <?php endif; ?>
          </td>
          <td class="text-end">
            <?php if ($isAdminCoupon): ?>
              <!-- Admin kuponları düzenlenemez/silinemez -->
              <button class="btn btn-sm btn-outline-secondary me-1" disabled title="Admin kuponları düzenlenemez">
                <i class="bi bi-pencil"></i> Düzenle
              </button>
              <button class="btn btn-sm btn-outline-secondary" disabled title="Admin kuponları silinemez">
                <i class="bi bi-trash"></i> Sil
              </button>
            <?php else: ?>
              <!-- Firma kuponları düzenlenebilir/silinebilir -->
              <a href="/company/coupons/edit/<?= urlencode($c['id']) ?>" class="btn btn-sm btn-outline-primary me-1">
                <i class="bi bi-pencil"></i> Düzenle
              </a>
              <form method="post" action="/company/coupons/delete/<?= urlencode($c['id']) ?>" class="d-inline" onsubmit="return confirm('Kuponu silmek istediğinize emin misiniz?');">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger">
                  <i class="bi bi-trash"></i> Sil
                </button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
