# BiBilet 🚌

Online otobüs bileti satın alma platformu. Kullanıcıların otobüs bileti satın alabileceği, firmaların seferlerini yönetebildiği modern bir web uygulaması.

## 🛠️ Teknolojiler

- Backend: PHP 8.2 (MVC)
- Database: SQLite
- Frontend: Bootstrap 5
- Container: Docker + Apache

## ✨ Özellikler

- Online bilet satın alma ve rezervasyon
- Koltuk seçimi ve bilet iptali
- Firma ve sefer yönetimi
- Çoklu firma desteği
- İndirim kuponu sistemi
- PDF bilet çıktısı

## 🚀 Docker Kurulum

1. Projeyi klonlayın:
```bash
git clone https://github.com/ucarabdullah/bilet-satin-alma.git
cd bilet-satin-alma
```

2. Docker container'ı başlatın:
```bash
docker-compose up -d
```

Uygulama http://localhost:8080 adresinde çalışacaktır.

## 🔑 Giriş Bilgileri

Tüm hesapların şifresi: `password123`

### Admin Paneli
- URL: http://localhost:8080/admin
- Email: admin@bibilet.com

### Firma Paneli
- URL: http://localhost:8080/company
- Hesaplar:
  - metro@bibilet.com (Metro Turizm)
  - pamukkale@bibilet.com (Pamukkale)
  - ulusoy@bibilet.com (Ulusoy)

## 📧 İletişim

[@ucarabdullah](https://github.com/ucarabdullah)



## 🔑 Giriş Bilgileri- Docker Compose



Tüm şifreler: **password123**## 📋 Gereksinimler## 📋 Gereksinimler



### Admin Paneli## 🚀 Hızlı Başlangıç

👉 http://localhost:8080/admin/login

- admin@bibilet.com



### Firma Paneli### 1. Projeyi İndir

👉 http://localhost:8080/company/login

- metro@bibilet.com (Metro Turizm)```bash- Docker- Docker

- pamukkale@bibilet.com (Pamukkale)

- ulusoy@bibilet.com (Ulusoy)git clone https://github.com/ucarabdullah/bilet-satin-alma.git



## 📦 Teknolojilercd bilet-satin-alma- Docker Compose- Docker Compose



- PHP 8.2 + SQLite```

- Docker + Apache

- Bootstrap 5



## 📄 Lisans### 2. Docker Çalıştır



MIT © [@ucarabdullah](https://github.com/ucarabdullah)```bash## 🚀 Hızlı Başlangıç## 🚀 Hızlı Başlangıç


docker-compose up -d

```



### 3. Tarayıcıda Aç### 1. Projeyi İndir### 1. Projeyi İndir

```

http://localhost:8080```bash```bash

```

git clone https://github.com/ucarabdullah/bilet-satin-alma.gitgit clone https://github.com/ucarabdullah/bilet-satin-alma.git

## 🔑 Yönetici Girişleri

cd bilet-satin-almacd bilet-satin-alma

Tüm hesapların şifresi: **password123**

``````

### Admin Paneli

**URL:** http://localhost:8080/admin/login



| Email | Şifre | Açıklama |### 2. Docker Çalıştır### 2. Docker Çalıştır

|-------|-------|----------|

| admin@bibilet.com | password123 | Sistem yöneticisi |```bash```bash



### Firma Paneli  docker-compose up -ddocker-compose up -d

**URL:** http://localhost:8080/company/login

``````

| Firma | Email | Şifre |

|-------|-------|-------|

| Metro Turizm | metro@bibilet.com | password123 |

| Pamukkale Turizm | pamukkale@bibilet.com | password123 |### 3. Tarayıcıda Aç### 3. Tarayıcıda Aç

| Ulusoy Seyahat | ulusoy@bibilet.com | password123 |

``````

## ✨ Özellikler

http://localhost:8080http://localhost:8080

### Kullanıcı (Yolcu) Özellikleri

- ✅ Kullanıcı kayıt ve giriş sistemi``````

- ✅ Sefer arama ve filtreleme

- ✅ Koltuk seçimi ve rezervasyon

- ✅ Kupon kodu ile indirim uygulama

- ✅ Sanal bakiye ile bilet satın alma## 🔑 Yönetici Girişleri## 🔑 Yönetici Girişleri

- ✅ Bilet iptal ve otomatik para iadesi

- ✅ PDF bilet indirme



### Firma Admin ÖzellikleriTüm hesapların şifresi: **password123**Tüm hesapların şifresi: **password123**

- ✅ Sefer yönetimi (CRUD)

- ✅ Satılan biletleri görüntüleme

- ✅ Firmaya özel kupon oluşturma

- ✅ Dashboard ve istatistikler### Admin Paneli### Admin Paneli



### Sistem Admin Özellikleri**URL:** http://localhost:8080/admin/login**URL:** http://localhost:8080/admin/login

- ✅ Otobüs firması yönetimi

- ✅ Firma admin atama

- ✅ Kupon yönetimi

- ✅ Kullanıcı yönetimi| Email | Şifre | Açıklama || Email | Şifre | Açıklama |



## 🔒 Güvenlik|-------|-------|----------||-------|-------|----------|



- ✅ CSRF Koruması| admin@bibilet.com | password123 | Sistem yöneticisi || admin@bibilet.com | password123 | Sistem yöneticisi |

- ✅ XSS Koruması 

- ✅ SQL Injection Koruması

- ✅ Session Güvenliği

- ✅ Brute Force Koruması### Firma Paneli  ### Firma Paneli  

- ✅ Password Hashing (Bcrypt)

**URL:** http://localhost:8080/company/login**URL:** http://localhost:8080/company/login

## 🐳 Docker Komutları



```bash

# Container'ı başlat| Firma | Email | Şifre || Firma | Email | Şifre |

docker-compose up -d

|-------|-------|-------||-------|-------|-------|

# Container'ı durdur

docker-compose down| Metro Turizm | metro@bibilet.com | password123 || Metro Turizm | metro@bibilet.com | password123 |



# Log'ları görüntüle| Pamukkale Turizm | pamukkale@bibilet.com | password123 || Pamukkale Turizm | pamukkale@bibilet.com | password123 |

docker-compose logs -f

| Ulusoy Seyahat | ulusoy@bibilet.com | password123 || Ulusoy Seyahat | ulusoy@bibilet.com | password123 |

# Yeniden build et

docker-compose build --no-cache

```

## ✨ Özellikler## ✨ Özellikler

## 📁 Proje Yapısı



```

BiBİlet/### Kullanıcı (Yolcu) Özellikleri### Kullanıcı (Yolcu) Özellikleri

├── config/              # Yapılandırma

├── database/           - ✅ Kullanıcı kayıt ve giriş sistemi- ✅ Kullanıcı kayıt ve giriş sistemi

│   ├── schema.sql      # DB şeması

│   └── seed.sql        # Test verileri- ✅ Sefer arama ve filtreleme- ✅ Sefer arama ve filtreleme

├── public/             

│   ├── .htaccess      - ✅ Koltuk seçimi ve rezervasyon- ✅ Koltuk seçimi ve rezervasyon

│   └── assets/         # CSS, JS, uploads

├── src/- ✅ Kupon kodu ile indirim uygulama- ✅ Kupon kodu ile indirim uygulama

│   ├── controllers/   

│   ├── models/        - ✅ Sanal bakiye ile bilet satın alma- ✅ Sanal bakiye ile bilet satın alma

│   ├── views/         

│   └── helpers/        - ✅ Bilet iptal ve otomatik para iadesi- ✅ Bilet iptal ve otomatik para iadesi

├── Dockerfile          

└── docker-compose.yml  - ✅ PDF bilet indirme- ✅ PDF bilet indirme

```



## 🛠️ Teknolojiler

### Firma Admin Özellikleri### Firma Admin Özellikleri

- **Backend**: PHP 8.2 (MVC)

- **Database**: SQLite 3- ✅ Sefer yönetimi (CRUD)- ✅ Sefer yönetimi (CRUD)

- **Frontend**: Bootstrap 5

- **Container**: Docker + Apache- ✅ Satılan biletleri görüntüleme- ✅ Satılan biletleri görüntüleme



## 📄 Lisans- ✅ Firmaya özel kupon oluşturma- ✅ Firmaya özel kupon oluşturma



MIT lisansı altında lisanslanmıştır.- ✅ Dashboard ve istatistikler- ✅ Dashboard ve istatistikler



## 📧 İletişim



[@ucarabdullah](https://github.com/ucarabdullah)### Sistem Admin Özellikleri### Sistem Admin Özellikleri


- ✅ Otobüs firması yönetimi- ✅ Otobüs firması yönetimi

- ✅ Firma admin atama- ✅ Firma admin atama

- ✅ Kupon yönetimi- ✅ Kupon yönetimi

- ✅ Kullanıcı yönetimi- ✅ Kullanıcı yönetimi



## 🔒 Güvenlik## 🔒 Güvenlik



- ✅ CSRF Koruması- ✅ CSRF Koruması

- ✅ XSS Koruması - ✅ XSS Koruması 

- ✅ SQL Injection Koruması- ✅ SQL Injection Koruması

- ✅ Session Güvenliği- ✅ Session Güvenliği

- ✅ Brute Force Koruması- ✅ Brute Force Koruması

- ✅ Password Hashing (Bcrypt)- ✅ Password Hashing (Bcrypt)



## 🐳 Docker Komutları## � Hızlı Başlangıç (Önerilen)



```bash### PHP Dahili Sunucu ile (En Kolay)

# Container'ı başlat

docker-compose up -d1. **Projeyi klonlayın:**

```bash

# Container'ı durdurgit clone https://github.com/ucarabdullah/bilet-satin-alma.git

docker-compose downcd bilet-satin-alma

```

# Log'ları görüntüle

docker-compose logs -f2. **Sunucuyu başlatın:**

```bash

# Yeniden build etphp -S localhost:8000 -t public public/router.php

docker-compose build --no-cache```

```

3. **Tarayıcıda açın:**

## 📁 Proje Yapısı   - Ana Sayfa: `http://localhost:8000`

   - Admin Paneli: `http://localhost:8000/admin/login`

```   - Firma Paneli: `http://localhost:8000/company/login`

BiBİlet/

├── config/              # Yapılandırma4. **Test hesapları:**

├── database/              - `TEST_ACCOUNTS.md` dosyasına bakın

│   ├── schema.sql      # DB şeması   - Tüm şifreler: `password123`

│   └── seed.sql        # Test verileri

├── public/             > **Not:** Veritabanı (`database.sqlite`) projeye dahildir ve test verileri yüklenmiş haldedir.

│   ├── .htaccess      

│   └── assets/         # CSS, JS, uploads---

├── src/

│   ├── controllers/   ##  Docker ile Kurulum (Alternatif)

│   ├── models/        

│   ├── views/         > **Uyarı:** Docker kurulumu şu anda `.htaccess` routing sorunu yaşamaktadır. PHP dahili sunucu kullanmanız önerilir.

│   └── helpers/        

├── Dockerfile          1. **Projeyi klonlayın:**

└── docker-compose.yml  ```bash

```git clone https://github.com/ucarabdullah/bilet-satin-alma.git

cd bilet-satin-alma

## 📄 Lisans```



MIT lisansı altında lisanslanmıştır.2. **Docker container'ı başlatın:**

```bash

## 📧 İletişimdocker-compose up -d

```

[@ucarabdullah](https://github.com/ucarabdullah)   - İlk çalıştırmada image build edilecek (1-2 dakika sürebilir)

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
