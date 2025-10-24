# BiBilet - Otobüs Bileti Satın Alma Platformu

Modern, güvenli ve kullanıcı dostu otobüs bileti rezervasyon sistemi. PHP 8.2, SQLite ve Bootstrap 5 ile geliştirilmiştir.

## 🚀 Özellikler

### Kullanıcı (Yolcu) Özellikleri
- ✅ Kullanıcı kayıt ve giriş sistemi
- ✅ Sefer arama ve filtreleme
- ✅ Koltuk seçimi ve rezervasyon
- ✅ Kupon kodu ile indirim uygulama
- ✅ Sanal bakiye ile bilet satın alma
- ✅ Bilet iptal ve otomatik para iadesi (seferden 1 saat öncesine kadar)
- ✅ PDF bilet indirme
- ✅ Bilet geçmişi görüntüleme

### Firma Admin Özellikleri
- ✅ Sefer yönetimi (CRUD)
- ✅ Satılan biletleri görüntüleme ve iptal etme
- ✅ Firmaya özel kupon oluşturma ve yönetme
- ✅ Dashboard ve istatistikler

### Sistem Admin Özellikleri
- ✅ Otobüs firması yönetimi (CRUD)
- ✅ Firma admin kullanıcıları oluşturma ve firmaya atama
- ✅ Genel indirim kuponları yönetimi
- ✅ Tüm kullanıcıları görüntüleme ve yönetme

## 🔒 Güvenlik Özellikleri

- ✅ **CSRF Koruması**: Tüm formlarda token doğrulama
- ✅ **XSS Koruması**: Kullanıcı girdilerinin temizlenmesi (htmlspecialchars)
- ✅ **SQL Injection Koruması**: Prepared statements kullanımı
- ✅ **Session Güvenliği**: Session hijacking koruması, timeout kontrolü
- ✅ **Brute Force Koruması**: Başarısız giriş denemesi limiti (5 deneme, 15 dakika bekleme)
- ✅ **Password Hashing**: Bcrypt ile güvenli şifre saklama (cost=12)
- ✅ **Role-Based Access Control (RBAC)**: Rol bazlı yetkilendirme sistemi
- ✅ **Session Fixation Koruması**: Her başarılı girişte session regeneration

## 📋 Gereksinimler

- PHP 8.2 veya üzeri
- SQLite 3
- Apache (mod_rewrite etkin) veya PHP dahili sunucu
- Docker (opsiyonel)

## 🐳 Docker ile Kurulum (Önerilen)

1. **Projeyi klonlayın:**
```bash
git clone https://github.com/ucarabdullah/bilet-satin-alma.git
cd bilet-satin-alma
```

2. **Docker container'ı başlatın:**
```bash
docker-compose up -d
```
   - İlk çalıştırmada image build edilecek (1-2 dakika sürebilir)
   - Veritabanı otomatik olarak oluşturulacak
   - Test verileri yüklenecek

3. **Uygulamayı açın:**
   - Tarayıcınızda `http://localhost:8080` adresine gidin
   - Test hesapları için `TEST_ACCOUNTS.md` dosyasına bakın

4. **Log'ları kontrol etmek için:**
```bash
docker-compose logs -f
```

5. **Container'ı durdurmak için:**
```bash
docker-compose down
```

6. **Tamamen silmek için (veritabanı dahil):**
```bash
docker-compose down -v
rm database.sqlite
```

## 💻 Manuel Kurulum

### 1. Projeyi İndirin
```bash
git clone https://github.com/ucarabdullah/bilet-satin-alma.git
cd bilet-satin-alma
```

### 2. Veritabanını Oluşturun
```bash
sqlite3 database.sqlite < database/schema.sql
sqlite3 database.sqlite < database/seed.sql
```

### 3. Sunucuyu Başlatın

**PHP Dahili Sunucu ile:**
```bash
php -S localhost:8000 -t public public/router.php
```

**Apache ile:**
- `public` klasörünü DocumentRoot olarak ayarlayın
- `.htaccess` dosyasının çalıştığından emin olun (`AllowOverride All`)
- `mod_rewrite` modülünün etkin olduğundan emin olun

### 4. Tarayıcıda Açın
```
http://localhost:8000
```

## 👥 Test Hesapları

Test hesapları ve şifreleri için `TEST_ACCOUNTS.md` dosyasına bakın.

**Hızlı Erişim:**
- **Admin Paneli**: `http://localhost:8000/admin/login`
- **Firma Paneli**: `http://localhost:8000/company/login`
- **Kullanıcı Girişi**: `http://localhost:8000/login`

## 📁 Proje Yapısı

```
BiBİlet/
├── config/              # Yapılandırma dosyaları
│   ├── config.php       # Genel yapılandırma
│   └── database.php     # Database sınıfı
├── database/            # Veritabanı şemaları ve seed dosyaları
│   ├── schema.sql       # Tablo yapısı
│   └── seed.sql         # Test verileri
├── public/              # Web root dizini
│   ├── .htaccess        # Apache routing kuralları
│   ├── index.php        # Ana giriş noktası
│   └── assets/          # CSS, JS, resim dosyaları
├── src/
│   ├── controllers/     # Controller sınıfları
│   ├── models/          # Model sınıfları
│   ├── views/           # View dosyaları
│   └── helpers/         # Yardımcı sınıflar (Security, Auth, Router, UUID)
├── Dockerfile           # Docker image yapılandırması
├── docker-compose.yml   # Docker Compose yapılandırması
└── README.md           # Bu dosya
```

## 🛠️ Teknolojiler

- **Backend**: PHP 8.2 (MVC Mimarisi)
- **Veritabanı**: SQLite 3
- **Frontend**: HTML5, CSS3, Bootstrap 5
- **Güvenlik**: CSRF Token, XSS Protection, Prepared Statements, Session Security
- **Mimari**: MVC (Model-View-Controller)

## 🔧 Önemli Notlar

- **JavaScript Kullanımı**: Proje backend logic'i için sadece PHP kullanır. JavaScript sadece UI animasyonları için kullanılmıştır (date picker, hover efektleri vb.).
- **No-JS Policy**: Koltuk seçimi, kupon kontrolü ve form validasyonları tamamen PHP ile sunucu tarafında yapılır.
- **Database**: SQLite kullanılır. Production ortamında PostgreSQL veya MySQL'e geçiş yapılabilir.
- **Transaction Safety**: Bilet iptal, sefer silme ve firma silme işlemleri transaction ile korunur.

## 📝 API Endpoint'leri

### Genel
- `GET /` - Ana sayfa (sefer arama)
- `GET /trips/search` - Sefer arama sonuçları
- `GET /trips/details/:id` - Sefer detayları

### Kullanıcı
- `POST /auth/register` - Kayıt ol
- `POST /auth/login` - Giriş yap
- `GET /user/dashboard` - Kullanıcı paneli
- `GET /user/tickets` - Biletlerim
- `POST /user/load-balance` - Bakiye yükle
- `POST /trips/book/:id` - Bilet satın al
- `POST /tickets/cancel/:id` - Bilet iptal

### Firma Admin
- `GET /company/dashboard` - Firma paneli
- `GET /company/trips` - Seferler
- `POST /company/trips/create` - Sefer ekle
- `POST /company/trips/edit/:id` - Sefer düzenle
- `POST /company/trips/delete/:id` - Sefer sil
- `GET /company/tickets` - Satılan biletler
- `GET /company/coupons` - Kuponlar

### Admin
- `GET /admin/dashboard` - Admin paneli
- `GET /admin/companies` - Firmalar
- `POST /admin/companies/create` - Firma ekle
- `GET /admin/users` - Kullanıcılar
- `GET /admin/coupons` - Kuponlar

## 🤝 Katkıda Bulunma

1. Fork edin
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Değişikliklerinizi commit edin (`git commit -m 'feat: Add amazing feature'`)
4. Branch'inizi push edin (`git push origin feature/amazing-feature`)
5. Pull Request açın

## 📄 Lisans

Bu proje MIT lisansı altında lisanslanmıştır.

## 📧 İletişim

Proje Sahibi - [@ucarabdullah](https://github.com/ucarabdullah)

Proje Linki: [https://github.com/ucarabdullah/bilet-satin-alma](https://github.com/ucarabdullah/bilet-satin-alma)

