<div class="container py-4">
  <?php $isEdit = isset($coupon); $action = $isEdit ? "/admin/coupons/edit/{$coupon['id']}" : "/admin/coupons/new"; ?>
  <h1 class="h4 mb-3"><?= $isEdit ? 'Kupon Düzenle' : 'Yeni Kupon' ?></h1>
  <form method="post" action="<?= $action ?>" class="row g-3">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
    <div class="col-md-4">
      <label class="form-label">Kupon Kodu</label>
      <input type="text" name="code" class="form-control" required value="<?= htmlspecialchars($coupon['code'] ?? '') ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label">İndirim (%)</label>
      <input type="number" min="1" max="100" step="1" name="discount" class="form-control" required value="<?= htmlspecialchars($coupon['discount'] ?? '') ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label">Kullanım Limiti</label>
      <input type="number" min="1" step="1" name="usage_limit" class="form-control" required value="<?= htmlspecialchars($coupon['usage_limit'] ?? '') ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label">Son Kullanma Tarihi</label>
      <input type="date" name="expire_date" class="form-control" required value="<?= isset($coupon['expire_date']) ? date('Y-m-d', strtotime($coupon['expire_date'])) : '' ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label">Kupon Türü</label>
      <div class="alert alert-info mb-0">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Genel Kupon:</strong> Tüm firmalar için geçerlidir. Firma yöneticileri bu kuponu silemez veya düzenleyemez.
      </div>
    </div>
    <div class="col-12 d-flex gap-2">
      <button type="submit" class="btn btn-primary">Kaydet</button>
      <a href="/admin/coupons" class="btn btn-secondary">İptal</a>
    </div>
  </form>
</div>
