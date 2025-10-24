<?php /** @var array $users */ ?>
<div class="admin-table-card">
  <div class="admin-table-header">
    <h2><i class="bi bi-people me-2"></i>Kullanıcılar</h2>
    <a href="/admin/users/new" class="btn btn-admin-primary btn-sm">
      <i class="bi bi-person-plus me-1"></i>Yeni Kullanıcı
    </a>
  </div>
  
  <?php if (empty($users)): ?>
    <div class="alert alert-info">
      <i class="bi bi-info-circle me-2"></i>Henüz kullanıcı yok.
    </div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Ad Soyad</th>
            <th>E-posta</th>
            <th>Rol</th>
            <th>Firma</th>
            <th>Bakiye</th>
            <th style="width:200px" class="text-end">İşlemler</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $u): ?>
          <tr>
            <td><strong><?= htmlspecialchars($u['full_name']) ?></strong></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td>
              <?php 
                $badgeClass = $u['role'] === 'admin' ? 'bg-danger' : ($u['role'] === 'company' ? 'bg-warning' : 'bg-secondary');
              ?>
              <span class="badge <?= $badgeClass ?> text-uppercase"><?= htmlspecialchars($u['role']) ?></span>
            </td>
            <td><?= htmlspecialchars($u['company_name'] ?? '-') ?></td>
            <td><strong><?= isset($u['balance']) ? number_format($u['balance'], 0) : 0 ?> ₺</strong></td>
            <td class="text-end">
              <a href="/admin/users/edit/<?= urlencode($u['id']) ?>" class="btn btn-sm btn-outline-primary me-1">
                <i class="bi bi-pencil"></i> Düzenle
              </a>
              <form method="post" action="/admin/users/delete/<?= urlencode($u['id']) ?>" class="d-inline" onsubmit="return confirm('Kullanıcıyı silmek istediğinize emin misiniz?');">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger" <?= ($u['role'] === 'admin') ? 'disabled' : '' ?>>
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
