<div class="container py-4">
  <?php 
    // Güvenli varsayılanlar
    $user = (isset($user) && is_array($user)) ? $user : [];
    $formData = (isset($formData) && is_array($formData)) ? $formData : [];
    
    $isEdit = isset($user['id']);
    $action = $isEdit ? "/admin/users/edit/{$user['id']}" : "/admin/users/new";
    $formGet = function($k) use ($formData, $user) { return htmlspecialchars($formData[$k] ?? ($user[$k] ?? '')); };
    $roleVal = $formData['role'] ?? ($user['role'] ?? 'user');
    $selectedCompany = $formData['company_id'] ?? ($user['company_id'] ?? '');
  ?>
  <h1 class="h4 mb-3"><?= $isEdit ? 'Kullanıcı Düzenle' : 'Yeni Kullanıcı' ?></h1>

  <form method="post" action="<?= $action ?>" class="row g-3">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

    <div class="col-md-6">
      <label class="form-label">Ad Soyad</label>
      <input type="text" name="full_name" class="form-control" required value="<?= $formGet('full_name') ?>">
    </div>

    <div class="col-md-6">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" required value="<?= $formGet('email') ?>">
    </div>

    <?php if (!$isEdit): ?>
    <div class="col-md-6">
      <label class="form-label">Şifre</label>
      <input type="password" name="password" class="form-control" required>
      <small class="text-muted">En az 8 karakter, 1 büyük harf, 1 küçük harf, 1 rakam</small>
    </div>
    <?php else: ?>
    <div class="col-md-6">
      <div class="alert alert-info mb-0">
        <i class="bi bi-info-circle me-2"></i>Şifre değiştirmek için kullanıcı kendi profilinden değiştirebilir.
      </div>
    </div>
    <?php endif; ?>

    <div class="col-md-6">
      <label class="form-label">Rol</label>
      <select name="role" class="form-select" required>
        <option value="user" <?= $roleVal==='user'?'selected':'' ?>>User</option>
        <option value="company" <?= $roleVal==='company'?'selected':'' ?>>Company Admin</option>
        <option value="admin" <?= $roleVal==='admin'?'selected':'' ?>>Admin</option>
      </select>
    </div>

    <div class="col-md-6">
      <label class="form-label">Firma</label>
      <select name="company_id" class="form-select">
        <option value="">Firma Seçiniz</option>
        <?php foreach (($companies ?? []) as $co): ?>
          <?php $sel = ($selectedCompany === $co['id']) ? 'selected' : ''; ?>
          <option value="<?= htmlspecialchars($co['id']) ?>" <?= $sel ?>><?= htmlspecialchars($co['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <small class="text-muted">Not: Admin veya User rolü için firma seçimi kaydedilmez. Sadece Company Admin için zorunludur.</small>
    </div>

    <div class="col-12 d-flex gap-2">
      <button type="submit" class="btn btn-primary">Kaydet</button>
      <a href="/admin/users" class="btn btn-secondary">İptal</a>
    </div>
  </form>
</div>
