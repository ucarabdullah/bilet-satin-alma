<?php $isEdit = isset($coupon) && is_array($coupon); $action = $isEdit ? "/company/coupons/edit/{$coupon['id']}" : "/company/coupons/new"; ?>
<div class="container py-4">
  <h1 class="h4 mb-3"><?= $isEdit ? 'Kupon Düzenle' : 'Yeni Kupon' ?></h1>
  <form method="post" action="<?= $action ?>" class="row g-3">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
    <div class="col-md-6">
      <label class="form-label">Kupon Kodu</label>
      <input type="text" name="code" class="form-control" required placeholder="ORNEK10" value="<?= htmlspecialchars($coupon['code'] ?? '') ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label">İndirim (%)</label>
      <input type="number" name="discount" class="form-control" min="1" max="100" step="1" required value="<?= htmlspecialchars($coupon['discount'] ?? '') ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label">Kullanım Limiti</label>
      <input type="number" name="usage_limit" class="form-control" min="1" step="1" required value="<?= htmlspecialchars($coupon['usage_limit'] ?? '') ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label">Son Kullanma Tarihi</label>
      <input type="date" name="expire_date" class="form-control" required value="<?= isset($coupon['expire_date']) ? date('Y-m-d', strtotime($coupon['expire_date'])) : '' ?>">
    </div>
    <div class="col-12 d-flex gap-2">
      <button type="submit" class="btn btn-primary">Kaydet</button>
      <a href="/company/coupons" class="btn btn-secondary">İptal</a>
    </div>
  </form>
</div>
