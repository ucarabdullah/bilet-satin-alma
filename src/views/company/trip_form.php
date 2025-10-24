<?php
// Trip create/edit form. Expects optional $trip array for edit.
$isEdit = isset($trip) && is_array($trip);
$action = $isEdit ? "/company/trips/edit/{$trip['id']}" : "/company/trips/new";

// 81 Türkiye ili
$cities = ['Adana', 'Adıyaman', 'Afyonkarahisar', 'Ağrı', 'Aksaray', 'Amasya', 'Ankara', 'Antalya', 'Ardahan', 'Artvin', 'Aydın', 'Balıkesir', 'Bartın', 'Batman', 'Bayburt', 'Bilecik', 'Bingöl', 'Bitlis', 'Bolu', 'Burdur', 'Bursa', 'Çanakkale', 'Çankırı', 'Çorum', 'Denizli', 'Diyarbakır', 'Düzce', 'Edirne', 'Elazığ', 'Erzincan', 'Erzurum', 'Eskişehir', 'Gaziantep', 'Giresun', 'Gümüşhane', 'Hakkari', 'Hatay', 'Iğdır', 'Isparta', 'İstanbul', 'İzmir', 'Kahramanmaraş', 'Karabük', 'Karaman', 'Kars', 'Kastamonu', 'Kayseri', 'Kilis', 'Kırıkkale', 'Kırklareli', 'Kırşehir', 'Kocaeli', 'Konya', 'Kütahya', 'Malatya', 'Manisa', 'Mardin', 'Mersin', 'Muğla', 'Muş', 'Nevşehir', 'Niğde', 'Ordu', 'Osmaniye', 'Rize', 'Sakarya', 'Samsun', 'Şanlıurfa', 'Siirt', 'Sinop', 'Sivas', 'Şırnak', 'Tekirdağ', 'Tokat', 'Trabzon', 'Tunceli', 'Uşak', 'Van', 'Yalova', 'Yozgat', 'Zonguldak'];
?>
<div class="container py-4">
  <h1 class="h4 mb-3"><?= $isEdit ? 'Sefer Düzenle' : 'Yeni Sefer' ?></h1>
  <form method="post" action="<?= $action ?>" class="row g-3">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
    <div class="col-md-6">
      <label class="form-label">Kalkış Şehri</label>
      <input list="departure_cities" type="text" name="departure_city" class="form-control" placeholder="Şehir seçin veya yazın" required value="<?= htmlspecialchars($trip['departure_city'] ?? '') ?>">
      <datalist id="departure_cities">
        <?php foreach ($cities as $city): ?>
          <option value="<?= htmlspecialchars($city) ?>">
        <?php endforeach; ?>
      </datalist>
    </div>
    <div class="col-md-6">
      <label class="form-label">Varış Şehri</label>
      <input list="destination_cities" type="text" name="destination_city" class="form-control" placeholder="Şehir seçin veya yazın" required value="<?= htmlspecialchars($trip['destination_city'] ?? '') ?>">
      <datalist id="destination_cities">
        <?php foreach ($cities as $city): ?>
          <option value="<?= htmlspecialchars($city) ?>">
        <?php endforeach; ?>
      </datalist>
    </div>
    <div class="col-md-6">
      <label class="form-label">Kalkış Tarihi</label>
      <input type="date" name="departure_date" class="form-control" required value="<?= isset($trip['departure_time']) ? date('Y-m-d', strtotime($trip['departure_time'])) : '' ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label">Kalkış Saati - Saat (24 saat)</label>
      <select name="departure_hour" class="form-control" required>
        <option value="">Saat</option>
        <?php 
        $depHour = isset($trip['departure_time']) ? (int)date('H', strtotime($trip['departure_time'])) : '';
        for($h = 0; $h <= 23; $h++): 
          $hh = str_pad($h, 2, '0', STR_PAD_LEFT);
          $selected = ($depHour === $h) ? 'selected' : '';
        ?>
          <option value="<?= $hh ?>" <?= $selected ?>><?= $hh ?></option>
        <?php endfor; ?>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">Kalkış Saati - Dakika</label>
      <select name="departure_minute" class="form-control" required>
        <option value="">Dakika</option>
        <?php 
        $depMin = isset($trip['departure_time']) ? (int)date('i', strtotime($trip['departure_time'])) : '';
        for($m = 0; $m <= 59; $m++): 
          $mm = str_pad($m, 2, '0', STR_PAD_LEFT);
          $selected = ($depMin === $m) ? 'selected' : '';
        ?>
          <option value="<?= $mm ?>" <?= $selected ?>><?= $mm ?></option>
        <?php endfor; ?>
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label">Varış Tarihi</label>
      <input type="date" name="arrival_date" class="form-control" required value="<?= isset($trip['arrival_time']) ? date('Y-m-d', strtotime($trip['arrival_time'])) : '' ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label">Varış Saati - Saat (24 saat)</label>
      <select name="arrival_hour" class="form-control" required>
        <option value="">Saat</option>
        <?php 
        $arrHour = isset($trip['arrival_time']) ? (int)date('H', strtotime($trip['arrival_time'])) : '';
        for($h = 0; $h <= 23; $h++): 
          $hh = str_pad($h, 2, '0', STR_PAD_LEFT);
          $selected = ($arrHour === $h) ? 'selected' : '';
        ?>
          <option value="<?= $hh ?>" <?= $selected ?>><?= $hh ?></option>
        <?php endfor; ?>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">Varış Saati - Dakika</label>
      <select name="arrival_minute" class="form-control" required>
        <option value="">Dakika</option>
        <?php 
        $arrMin = isset($trip['arrival_time']) ? (int)date('i', strtotime($trip['arrival_time'])) : '';
        for($m = 0; $m <= 59; $m++): 
          $mm = str_pad($m, 2, '0', STR_PAD_LEFT);
          $selected = ($arrMin === $m) ? 'selected' : '';
        ?>
          <option value="<?= $mm ?>" <?= $selected ?>><?= $mm ?></option>
        <?php endfor; ?>
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label">Fiyat (₺)</label>
      <input type="number" min="1" step="1" name="price" class="form-control" required value="<?= htmlspecialchars($trip['price'] ?? '') ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label">Kapasite</label>
      <input type="number" min="1" step="1" name="capacity" class="form-control" required value="<?= htmlspecialchars($trip['capacity'] ?? '') ?>">
    </div>
    <div class="col-12 d-flex gap-2">
      <button type="submit" class="btn btn-primary">Kaydet</button>
      <a href="/company/trips" class="btn btn-secondary">İptal</a>
    </div>
  </form>
  <p class="text-muted small mt-3">Not: Fiyat ve kapasite tamsayıdır. Tarih-saat yerel saat dilimine göre kaydedilir.</p>
  <!-- İstemci tarafı JS kaldırıldı: Zaman doğrulaması sunucu tarafında yapılır. -->
</div>
