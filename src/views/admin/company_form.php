<?php
/**
 * Admin Company Form - Yeni/Düzenle
 * $company değişkeni düzenleme modunda dolu olacak
 */
$isEdit = isset($company) && !empty($company);
$formTitle = $isEdit ? 'Firma Düzenle' : 'Yeni Firma';
$formAction = $isEdit ? "/admin/companies/edit/{$company['id']}" : '/admin/companies/new';
?>

<div class="admin-table-card">
  <div class="admin-table-header">
    <h2><i class="bi bi-building me-2"></i><?= $formTitle ?></h2>
  </div>
  
  <form method="post" action="<?= $formAction ?>" class="p-4">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
    
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Firma Adı *</label>
        <input type="text" 
               name="name" 
               class="form-control" 
               value="<?= $isEdit ? htmlspecialchars($company['name']) : '' ?>"
               required>
      </div>
      
      <div class="col-md-6">
        <label class="form-label">Logo URL (opsiyonel)</label>
        <input type="url" 
               name="logo_path" 
               class="form-control" 
               value="<?= $isEdit ? htmlspecialchars($company['logo_path'] ?? '') : '' ?>"
               placeholder="https://example.com/logo.png">
        <small class="text-muted">Logo için tam URL girin</small>
      </div>
      
      <?php if ($isEdit): ?>
      <div class="col-12">
        <div class="alert alert-info">
          <i class="bi bi-info-circle me-2"></i>
          <strong>Firma ID:</strong> <?= htmlspecialchars($company['id']) ?>
        </div>
      </div>
      <?php endif; ?>
      
      <div class="col-12">
        <hr>
        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-2"></i><?= $isEdit ? 'Güncelle' : 'Kaydet' ?>
          </button>
          <a href="/admin/companies" class="btn btn-secondary">
            <i class="bi bi-x-circle me-2"></i>İptal
          </a>
        </div>
      </div>
    </div>
  </form>
</div>
