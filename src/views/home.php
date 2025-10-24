<!-- src/views/home.php -->

<!-- Hero Section with Search Form -->
<div class="gradient-primary text-white py-5" id="search-form">
    <div class="container">
        <!-- Logo and Title Row -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h1 class="display-4 fw-bold mb-2">BiBilet</h1>
                <p class="lead mb-0">Türkiye'nin her yerine tek tıkla bilet alın</p>
            </div>
        </div>
        
        <!-- Search Form -->
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-lg" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <form action="/trips/search" method="GET">
                            <div class="row g-3 align-items-end">
                                <!-- Nereden -->
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-geo-alt-fill text-primary me-1"></i>
                                        Nereden
                                    </label>
                                    <input list="departure_cities" type="text" class="form-control form-control-lg modern-input" id="departure_city" name="departure_city" placeholder="🔍 Kalkış şehri yazın..." required autocomplete="off">
                                    <datalist id="departure_cities">
                                        <?php foreach ($tripModel->getUniqueCities() as $city): ?>
                                            <option value="<?= htmlspecialchars($city) ?>">
                                        <?php endforeach; ?>
                                    </datalist>
                                </div>
                                
                                <!-- Swap Button -->
                                <div class="col-md-2 text-center">
                                    <button type="button" class="btn btn-outline-primary btn-lg rounded-circle swap-cities" style="width: 50px; height: 50px;" title="Şehirleri değiştir">
                                        <i class="bi bi-arrow-left-right"></i>
                                    </button>
                                </div>
                                
                                <!-- Nereye -->
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-geo-fill text-primary me-1"></i>
                                        Nereye
                                    </label>
                                    <input list="destination_cities" type="text" class="form-control form-control-lg modern-input" id="destination_city" name="destination_city" placeholder="🔍 Varış şehri yazın..." required autocomplete="off">
                                    <datalist id="destination_cities">
                                        <?php foreach ($tripModel->getUniqueCities() as $city): ?>
                                            <option value="<?= htmlspecialchars($city) ?>">
                                        <?php endforeach; ?>
                                    </datalist>
                                </div>
                                
                                <!-- Tarih ve Hızlı Seçim -->
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-calendar3 text-primary me-1"></i>
                                        Tarih
                                    </label>
                                    <div class="input-group">
                                        <input type="date" class="form-control form-control-lg" id="departure_date" name="departure_date" 
                                               min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>" required>
                                        <button type="button" class="btn btn-outline-secondary quick-date" data-days="0" title="Bugün">
                                            Bugün
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary quick-date" data-days="1" title="Yarın">
                                            Yarın
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Search Button -->
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="bi bi-search me-2"></i>Bilet Ara
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Links -->
        <div class="row mt-4">
            <div class="col-12 text-center">
                <p class="text-white-50 mb-2">Hesabınız yok mu?</p>
                <a href="/register" class="btn btn-outline-light">
                    <i class="bi bi-person-plus me-2"></i>Üye Ol
                </a>
            </div>
        </div>
    </div>
</div>

<!-- City Swap and Quick Date Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const swapBtn = document.querySelector('.swap-cities');
    const departureSelect = document.getElementById('departure_city');
    const destinationSelect = document.getElementById('destination_city');
    const dateInput = document.getElementById('departure_date');
    const searchForm = document.querySelector('form[action="/trips/search"]');
    const quickDateBtns = document.querySelectorAll('.quick-date');
    
    // Hızlı tarih seçimi
    quickDateBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const days = parseInt(this.getAttribute('data-days'));
            const today = new Date();
            today.setDate(today.getDate() + days);
            const dateStr = today.toISOString().split('T')[0];
            dateInput.value = dateStr;
            
            // Visual feedback
            quickDateBtns.forEach(b => b.classList.remove('active', 'btn-primary'));
            quickDateBtns.forEach(b => b.classList.add('btn-outline-secondary'));
            this.classList.remove('btn-outline-secondary');
            this.classList.add('btn-primary', 'active');
        });
    });
    
    // Aynı şehir seçimi kontrolü
    function validateCitySelection() {
        const departureCity = departureSelect.value;
        const destinationCity = destinationSelect.value;
        
        if (departureCity && destinationCity && departureCity === destinationCity) {
            showCityError();
            return false;
        } else {
            removeCityError();
            return true;
        }
    }
    
    function showCityError() {
        removeCityError();
        const errorDiv = document.createElement('div');
        errorDiv.className = 'city-error';
        errorDiv.style.display = 'block';
        errorDiv.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i>Kalkış ve varış şehri aynı olamaz!';
        destinationSelect.parentElement.appendChild(errorDiv);
        departureSelect.classList.add('is-invalid');
        destinationSelect.classList.add('is-invalid');
    }
    
    function removeCityError() {
        const errorDivs = document.querySelectorAll('.city-error');
        errorDivs.forEach(div => div.remove());
        departureSelect.classList.remove('is-invalid');
        destinationSelect.classList.remove('is-invalid');
        if (departureSelect.value && destinationSelect.value) {
            departureSelect.classList.add('is-valid');
            destinationSelect.classList.add('is-valid');
        }
    }
    
    if (departureSelect && destinationSelect) {
        departureSelect.addEventListener('change', validateCitySelection);
        destinationSelect.addEventListener('change', validateCitySelection);
    }
    
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            if (!validateCitySelection()) {
                e.preventDefault();
                destinationSelect.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return false;
            }
        });
    }
    
    if (swapBtn) {
        swapBtn.addEventListener('click', function() {
            const tempValue = departureSelect.value;
            departureSelect.value = destinationSelect.value;
            destinationSelect.value = tempValue;
            validateCitySelection();
            this.style.transform = 'rotate(180deg)';
            setTimeout(() => {
                this.style.transform = 'rotate(0deg)';
            }, 300);
        });
    }
});
</script>

<div class="container my-5">
    <!-- Özellikler -->
    <div class="row my-5 py-4">
        <div class="col-12 text-center mb-4">
            <h2 class="fw-bold">
                <span class="gradient-text">Neden BiBilet?</span>
            </h2>
            <p class="text-muted">Yolculuğunuzu kolaylaştıran özellikler</p>
        </div>
        <div class="col-md-4 mb-4">
            <div class="text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-lightning-charge-fill" style="font-size: 3rem; color: var(--brand-secondary);"></i>
                </div>
                <h5 class="fw-bold">Hızlı Rezervasyon</h5>
                <p class="text-muted">Saniyeler içinde biletinizi alın</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-shield-check" style="font-size: 3rem; color: var(--brand-secondary);"></i>
                </div>
                <h5 class="fw-bold">Güvenli Ödeme</h5>
                <p class="text-muted">256-bit SSL ile korumalı</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-headset" style="font-size: 3rem; color: var(--brand-secondary);"></i>
                </div>
                <h5 class="fw-bold">7/24 Destek</h5>
                <p class="text-muted">Her zaman yanınızdayız</p>
            </div>
        </div>
    </div>
    
    <!-- Popüler Seferler -->
    <div class="row mt-5">
        <div class="col-12 mb-4">
            <h2 class="fw-bold">
                <i class="bi bi-fire text-primary me-2"></i>
                Popüler Seferler
            </h2>
            <p class="text-muted">En çok tercih edilen rotalar</p>
        </div>
            
            <?php if (empty($popularTrips)): ?>
                <div class="alert alert-info">
                    Henüz popüler sefer bulunmamaktadır. Daha sonra tekrar kontrol edin.
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($popularTrips as $trip): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-white border-0">
                                    <div class="d-flex align-items-center">
                                        <?php if ($trip['company_logo']): ?>
                                            <img src="<?= htmlspecialchars($trip['company_logo']) ?>" alt="<?= htmlspecialchars($trip['company_name']) ?>" height="40" class="me-3">
                                        <?php else: ?>
                                            <div class="bg-light rounded p-2 me-3">
                                                <i class="bi bi-bus-front fs-4"></i>
                                            </div>
                                        <?php endif; ?>
                                        <span class="fw-bold"><?= htmlspecialchars($trip['company_name']) ?></span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h5 class="card-title mb-1"><?= htmlspecialchars($trip['departure_city']) ?></h5>
                                            <p class="text-muted small mb-0"><?= date('H:i', strtotime($trip['departure_time'])) ?></p>
                                        </div>
                                        
                                        <div class="text-center text-primary">
                                            <i class="bi bi-arrow-right"></i>
                                            <p class="text-muted small mb-0"><?= round((strtotime($trip['arrival_time']) - strtotime($trip['departure_time'])) / 3600, 1) ?> saat</p>
                                        </div>
                                        
                                        <div class="text-end">
                                            <h5 class="card-title mb-1"><?= htmlspecialchars($trip['destination_city']) ?></h5>
                                            <p class="text-muted small mb-0"><?= date('H:i', strtotime($trip['arrival_time'])) ?></p>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div>
                                            <span class="badge bg-success">
                                                <?= date('d.m.Y', strtotime($trip['departure_time'])) ?>
                                            </span>
                                        </div>
                                        
                                        <div>
                                            <h5 class="card-title text-end text-primary mb-0">
                                                <?= number_format($trip['price'], 2) ?> TL
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="/trips/details/<?= $trip['id'] ?>" class="btn btn-outline-primary d-block">
                                        Detayları Görüntüle
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Özellikler Bölümü -->
    <div class="row mt-5 py-5 bg-light rounded">
        <div class="col-12 text-center mb-4">
            <h2>Neden BiBilet?</h2>
            <p class="lead text-muted">Size en iyi bilet satın alma deneyimini sunuyoruz</p>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm text-center p-3">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-search text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="card-title">Kolay Arama</h5>
                    <p class="card-text">Seyahat etmek istediğiniz güzergahı seçin, size uygun biletleri anında bulun.</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm text-center p-3">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-credit-card text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="card-title">Güvenli Ödeme</h5>
                    <p class="card-text">Güvenli altyapımız ile biletinizi hızlı ve güvenli bir şekilde satın alın.</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm text-center p-3">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-person-check text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="card-title">7/24 Destek</h5>
                    <p class="card-text">Herhangi bir sorunla karşılaştığınızda müşteri hizmetlerimiz size yardımcı olmak için hazır.</p>
                </div>
            </div>
        </div>
    </div>
</div>