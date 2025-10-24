<?php /** @var array $companies */ ?>
<div class="admin-table-card">
  <div class="admin-table-header">
    <h2><i class="bi bi-building me-2"></i>Firmalar</h2>
    <a href="/admin/companies/new" class="btn btn-admin-primary btn-sm">
      <i class="bi bi-plus-circle me-1"></i>Yeni Firma
    </a>
  </div>
  
  <?php if (empty($companies)): ?>
    <div class="alert alert-info">
      <i class="bi bi-info-circle me-2"></i>Henüz firma yok.
    </div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Ad</th>
            <th>Logo</th>
            <th>ID</th>
            <th style="width:200px" class="text-end">İşlemler</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($companies as $co): ?>
          <tr>
            <td>
              <strong><?= htmlspecialchars($co['name']) ?></strong>
            </td>
            <td>
              <?php if ($co['logo_path'] ?? null): ?>
                <span class="badge bg-success">Logo mevcut</span>
              <?php else: ?>
                <span class="text-muted">-</span>
              <?php endif; ?>
            </td>
            <td><small class="text-muted"><?= htmlspecialchars($co['id']) ?></small></td>
            <td class="text-end">
              <a href="/admin/companies/edit/<?= urlencode($co['id']) ?>" class="btn btn-sm btn-outline-primary me-1">
                <i class="bi bi-pencil"></i> Düzenle
              </a>
              <form method="post" action="/admin/companies/delete/<?= urlencode($co['id']) ?>" class="d-inline" onsubmit="return confirm('Firmayı silmek istediğinize emin misiniz?');">
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
