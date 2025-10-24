<!-- src/views/user/profile.php -->
<div class="container my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/user/dashboard">Ana Sayfa</a></li>
            <li class="breadcrumb-item active" aria-current="page">Profilim</li>
        </ol>
    </nav>
    
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h1 class="h5 mb-0">Profil Bilgileri</h1>
                </div>
                <div class="card-body">
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Başarılı!</strong> Profil bilgileriniz başarıyla güncellendi.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Hata!</strong> Profil güncellenirken aşağıdaki hatalar oluştu:
                            <ul class="mb-0 mt-2">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form action="/user/profile" method="POST" class="needs-validation" novalidate>
                        <!-- CSRF Token -->
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="full_name" class="form-label">Ad Soyad</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" 
                                       value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" required>
                                <div class="invalid-feedback">
                                    Lütfen adınızı ve soyadınızı girin.
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="email" class="form-label">E-posta Adresi</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                                <div class="invalid-feedback">
                                    Lütfen geçerli bir e-posta adresi girin.
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        <h5>Şifre Değiştirme</h5>
                        <p class="text-muted small">Şifrenizi değiştirmek istemiyorsanız bu alanları boş bırakabilirsiniz.</p>
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="current_password" class="form-label">Mevcut Şifre</label>
                                <input type="password" class="form-control" id="current_password" name="current_password">
                                <div class="invalid-feedback">
                                    Lütfen mevcut şifrenizi girin.
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="new_password" class="form-label">Yeni Şifre</label>
                                <input type="password" class="form-control" id="new_password" name="new_password">
                                <div class="form-text">
                                    Şifreniz en az 8 karakter olmalıdır.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="confirm_password" class="form-label">Yeni Şifre (Tekrar)</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                <div class="invalid-feedback">
                                    Şifreler eşleşmiyor.
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="row">
                            <div class="col-12 d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="/user/dashboard" class="btn btn-outline-secondary me-md-2">İptal</a>
                                <button type="submit" class="btn btn-primary">Profili Güncelle</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    <h2 class="h5 mb-0">Hesap Bilgileriniz</h2>
                </div>
                <div class="card-body">
                    <p><strong>Kullanıcı Rolü:</strong> <?= htmlspecialchars(ucfirst($user['role'])) ?></p>
                    <p><strong>Mevcut Bakiye:</strong> <?= number_format($user['balance'], 2) ?> TL</p>
                    <p><strong>Kayıt Tarihi:</strong> <?= date('d.m.Y', strtotime($user['created_at'])) ?></p>
                    
                    <?php if ($user['company_id']): ?>
                    <div class="alert alert-info">
                        Bu hesap bir otobüs firmasına bağlıdır. Firma yönetim paneline erişmek için 
                        <a href="/company/dashboard" class="alert-link">tıklayınız</a>.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- İptal Edilen Biletler -->
<div class="container my-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h2 class="h5 mb-0"><i class="bi bi-x-circle me-2"></i>İptal Edilen Son Biletler</h2>
                </div>
                <div class="card-body">
                    <?php if (empty($canceledTickets ?? [])): ?>
                        <div class="alert alert-light border">Henüz iptal edilmiş biletiniz bulunmuyor.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Firma</th>
                                        <th>Güzergah</th>
                                        <th>Tarih</th>
                                        <th>Saat</th>
                                        <th>Durum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (($canceledTickets ?? []) as $ct): ?>
                                        <tr class="text-muted">
                                            <td><?= htmlspecialchars($ct['company_name'] ?? 'Silinen Sefer') ?></td>
                                            <td>
                                                <?php if (!empty($ct['departure_city']) && !empty($ct['destination_city'])): ?>
                                                    <?= htmlspecialchars($ct['departure_city']) ?> → <?= htmlspecialchars($ct['destination_city']) ?>
                                                <?php else: ?>
                                                    Silinen Sefer
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($ct['departure_time'])): ?>
                                                    <?= date('d.m.Y', strtotime($ct['departure_time'])) ?>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($ct['departure_time'])): ?>
                                                    <?= date('H:i', strtotime($ct['departure_time'])) ?>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td><span class="badge bg-secondary">İptal</span></td>
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
</div>