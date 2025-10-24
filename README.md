# BiBilet - Otobüs Bileti Satın Alma Platformu 🚌

Docker üzerinde çalışan modern otobüs bileti rezervasyon sistemi.

## 📋 Gereksinimler

- Docker
- Docker Compose

## 🚀 Hızlı Başlangıç

### 1. Projeyi İndir
```bash
git clone https://github.com/ucarabdullah/bilet-satin-alma.git
cd bilet-satin-alma
```

### 2. Docker Çalıştır
```bash
docker-compose up -d
```

### 3. Tarayıcıda Aç
```
http://localhost:8080
```

### 4. Giriş Yap
- **Admin**: admin@bibilet.com / password123
- Diğer hesaplar: `TEST_ACCOUNTS.md`

## ✨ Özellikler

### Kullanıcı (Yolcu) Özellikleri
- ✅ Kullanıcı kayıt ve giriş sistemi
- ✅ Sefer arama ve filtreleme
- ✅ Koltuk seçimi ve rezervasyon
- ✅ Kupon kodu ile indirim uygulama
- ✅ Sanal bakiye ile bilet satın alma
- ✅ Bilet iptal ve otomatik para iadesi
- ✅ PDF bilet indirme

### Firma Admin Özellikleri
- ✅ Sefer yönetimi (CRUD)
- ✅ Satılan biletleri görüntüleme
- ✅ Firmaya özel kupon oluşturma
- ✅ Dashboard ve istatistikler

### Sistem Admin Özellikleri
- ✅ Otobüs firması yönetimi
- ✅ Firma admin atama
- ✅ Kupon yönetimi
- ✅ Kullanıcı yönetimi

## 🔒 Güvenlik

- ✅ CSRF Koruması
- ✅ XSS Koruması 
- ✅ SQL Injection Koruması
- ✅ Session Güvenliği
- ✅ Brute Force Koruması
- ✅ Password Hashing (Bcrypt)

## � Hızlı Başlangıç (Önerilen)

### PHP Dahili Sunucu ile (En Kolay)

1. **Projeyi klonlayın:**
```bash
git clone https://github.com/ucarabdullah/bilet-satin-alma.git
cd bilet-satin-alma
```

2. **Sunucuyu başlatın:**
```bash
php -S localhost:8000 -t public public/router.php
```

3. **Tarayıcıda açın:**
   - Ana Sayfa: `http://localhost:8000`
   - Admin Paneli: `http://localhost:8000/admin/login`
   - Firma Paneli: `http://localhost:8000/company/login`

4. **Test hesapları:**
   - `TEST_ACCOUNTS.md` dosyasına bakın
   - Tüm şifreler: `password123`

> **Not:** Veritabanı (`database.sqlite`) projeye dahildir ve test verileri yüklenmiş haldedir.

---

##  Docker ile Kurulum (Alternatif)

> **Uyarı:** Docker kurulumu şu anda `.htaccess` routing sorunu yaşamaktadır. PHP dahili sunucu kullanmanız önerilir.

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

## 💻 Manuel Kurulum (Apache)

### Apache Web Sunucu ile

> **Not:** Veritabanı zaten proje ile birlikte gelir. Yeniden oluşturmanıza gerek yok.
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

## � Test Hesapları

Tüm hesapların şifresi: `password123`

### Admin
- 👨‍💼 admin@bibilet.com

### Firma Adminleri
- 🚌 Metro: metro@bibilet.com
- 🚌 Pamukkale: pamukkale@bibilet.com
- 🚌 Ulusoy: ulusoy@bibilet.com

### Test Kullanıcıları
- 👤 ahmet@example.com (1000 TL bakiye)
- 👤 ayse@example.com (1500 TL bakiye)
- 👤 mehmet@example.com (2000 TL bakiye)

### Test Kuponları
- 🎫 SUMMER2024: %20 indirim
- 🎫 WELCOME50: %50 indirim
- 🎫 METRO30: %30 Metro indirimi
- 🎫 PAMUKKALE25: %25 Pamukkale indirimi
- 🎫 ULUSOY15: %15 Ulusoy indirimi

## � Docker Komutları

```bash
# Container'ı başlat
docker-compose up -d

# Container'ı durdur
docker-compose down

# Log'ları görüntüle
docker-compose logs -f

# Yeniden build et
docker-compose build --no-cache
```

## � Proje Yapısı

```
BiBİlet/
├── config/              # Yapılandırma
├── database/           
│   ├── schema.sql      # DB şeması
│   └── seed.sql        # Test verileri
├── public/             
│   ├── .htaccess      
│   └── assets/         # CSS, JS, uploads
├── src/
│   ├── controllers/   
│   ├── models/        
│   ├── views/         
│   └── helpers/        
├── Dockerfile          
└── docker-compose.yml  
```

## 📄 Lisans

MIT lisansı altında lisanslanmıştır.

## 📧 İletişim

[@ucarabdullah](https://github.com/ucarabdullah)

