# Test Hesapları# BiBilet Projesi - Test Hesapları



Bu dosya, BiBilet platformundaki test hesaplarını içerir. Development ve test amaçlı kullanılabilir.## 🔐 Giriş Bilgileri



## 📝 Önemli NotlarTüm hesapların şifresi: **Admin123**



- **Tüm şifreler:** `password123`### 👨‍💼 Admin Hesabı

- Test verileri `database/seed.sql` dosyasında bulunur- **Email:** admin@bibilet.com

- Production ortamında bu hesapları **mutlaka silin veya şifrelerini değiştirin**- **Şifre:** Admin123

- **Rol:** Admin

---- **Yetkiler:** Tüm sistem yönetimi



## 👤 Kullanıcı Hesapları (Yolcu)### 🏢 Firma Admin Hesapları



### Normal Kullanıcı 1#### Metro Turizm

- **Email:** `user1@example.com`- **Email:** metro@bibilet.com

- **Şifre:** `password123`- **Şifre:** Admin123

- **Ad Soyad:** Ali Yılmaz- **Rol:** Company Admin

- **Bakiye:** 1000 TL- **Firma:** Metro Turizm

- **Kullanım:** Bilet satın alma, iptal etme testleri için

#### Pamukkale Turizm

### Normal Kullanıcı 2- **Email:** pamukkale@bibilet.com

- **Email:** `user2@example.com`- **Şifre:** Admin123

- **Şifre:** `password123`- **Rol:** Company Admin

- **Ad Soyad:** Ayşe Demir- **Firma:** Pamukkale Turizm

- **Bakiye:** 500 TL

- **Kullanım:** Kupon uygulama testleri için#### Kamil Koç

- **Email:** kamilkoc@bibilet.com

### Normal Kullanıcı 3- **Şifre:** Admin123

- **Email:** `user3@example.com`- **Rol:** Company Admin

- **Şifre:** `password123`- **Firma:** Kamil Koç

- **Ad Soyad:** Mehmet Kaya

- **Bakiye:** 750 TL### 👤 Normal Kullanıcı Hesapları

- **Kullanım:** Koltuk seçimi testleri için

#### Kullanıcı 1

---- **Email:** ayse@example.com

- **Şifre:** Admin123

## 🏢 Firma Admin Hesapları- **Bakiye:** 1500 TL



### Metro Turizm Admin#### Kullanıcı 2

- **Email:** `metro.admin@example.com`- **Email:** fatma@example.com

- **Şifre:** `password123`- **Şifre:** Admin123

- **Ad Soyad:** Metro Admin- **Bakiye:** 2000 TL

- **Firma:** Metro Turizm

- **Kullanım:** Sefer yönetimi, bilet görüntüleme testleri#### Kullanıcı 3

- **Panel:** http://localhost:8000/company/login- **Email:** zeynep@example.com

- **Şifre:** Admin123

### Pamukkale Admin- **Bakiye:** 1000 TL

- **Email:** `pamukkale.admin@example.com`

- **Şifre:** `password123`---

- **Ad Soyad:** Pamukkale Admin

- **Firma:** Pamukkale Turizm## 🎟️ Örnek Kupon Kodları

- **Kullanım:** Firma kupon oluşturma testleri

- **Panel:** http://localhost:8000/company/login| Kod | İndirim | Geçerlilik | Limit |

|-----|---------|------------|-------|

### Ulusoy Admin| WELCOME2025 | %10 | 31.12.2025 | 100 |

- **Email:** `ulusoy.admin@example.com`| EARLYBIRD | %15 | 30.11.2025 | 50 |

- **Şifre:** `password123`| METRO20 | %20 | 31.10.2025 | 30 |

- **Ad Soyad:** Ulusoy Admin| PAMUKKALE15 | %15 | 31.10.2025 | 40 |

- **Firma:** Ulusoy Turizm| KAMILKOC25 | %25 | 31.10.2025 | 20 |

- **Kullanım:** Sefer CRUD işlemleri testleri

- **Panel:** http://localhost:8000/company/login---



---## 🚌 Örnek Seferler



## 🔐 Sistem Admin HesabıVeritabanında 9 adet örnek sefer bulunmaktadır:

- İstanbul → Ankara (3 firma)

### Admin- İstanbul → İzmir

- **Email:** `admin@example.com`- İstanbul → Trabzon

- **Şifre:** `password123`- İzmir → Ankara

- **Ad Soyad:** System Admin- Ankara → Antalya

- **Kullanım:** - Ankara → İzmir

  - Firma yönetimi (ekleme, düzenleme, silme)- Bursa → Antalya

  - Firma admin kullanıcıları oluşturma

  - Genel kupon yönetimi---

  - Tüm kullanıcıları görüntüleme

- **Panel:** http://localhost:8000/admin/login## 📊 Veritabanı İstatistikleri



---- **Toplam Firma:** 3

- **Toplam Kullanıcı:** 7 (1 Admin + 3 Firma Admin + 3 User)

## 🎟️ Test Kuponları- **Toplam Sefer:** 9

- **Toplam Kupon:** 5

### Genel Kupon (Tüm Firmalar)- **Toplam Bilet:** 3 (test için)

- **Kod:** `SUMMER2024`

- **İndirim:** %20---

- **Kullanım Limiti:** 100

- **Son Kullanma:** 2025-12-31## 🚀 Projeyi Başlatma



### Metro Turizm Kuponu1. XAMPP veya benzeri bir PHP sunucusu çalıştırın

- **Kod:** `METRO50`2. Tarayıcınızda `http://localhost/BiBilet/public/` adresine gidin

- **İndirim:** %503. Yukarıdaki hesaplardan biriyle giriş yapın

- **Kullanım Limiti:** 50

- **Firma:** Sadece Metro Turizm---



### Pamukkale Kuponu## ⚙️ Geliştirme Notları

- **Kod:** `PAMUKKALE30`

- **İndirim:** %30- Tüm şifreler Bcrypt ile hash'lenmiştir

- **Kullanım Limiti:** 30- Foreign key constraint'leri aktiftir

- **Firma:** Sadece Pamukkale Turizm- Session güvenliği yapılandırılmıştır

- CSRF koruması hazırdır

---- Input validation fonksiyonları hazırdır



## 🚌 Örnek Seferler---



Test veritabanında aşağıdaki seferler mevcuttur:**Tarih:** 17 Ekim 2025  

**Durum:** Altyapı tamamlandı, frontend geliştirmeye hazır

### Metro Turizm Seferleri
1. **İstanbul → Ankara**
   - Kalkış: Her gün 09:00
   - Fiyat: 200 TL
   - Kapasite: 45 koltuk

2. **Ankara → İzmir**
   - Kalkış: Her gün 14:00
   - Fiyat: 250 TL
   - Kapasite: 45 koltuk

3. **İzmir → Antalya**
   - Kalkış: Her gün 20:00
   - Fiyat: 180 TL
   - Kapasite: 45 koltuk

### Pamukkale Turizm Seferleri
1. **İstanbul → İzmir**
   - Kalkış: Her gün 10:00
   - Fiyat: 220 TL

2. **Ankara → Antalya**
   - Kalkış: Her gün 16:00
   - Fiyat: 280 TL

### Ulusoy Turizm Seferleri
1. **İstanbul → Antalya**
   - Kalkış: Her gün 22:00
   - Fiyat: 300 TL

2. **İzmir → Ankara**
   - Kalkış: Her gün 08:00
   - Fiyat: 240 TL

---

## 🧪 Test Senaryoları

### Senaryo 1: Bilet Satın Alma
1. `user1@example.com` ile giriş yapın
2. İstanbul → Ankara seferini arayın
3. Koltuk seçin (örn: 1, 2, 3)
4. `SUMMER2024` kuponunu uygulayın
5. Ödeme yapın
6. PDF bilet indirin

### Senaryo 2: Bilet İptal
1. `user1@example.com` ile giriş yapın
2. "Biletlerim" sayfasına gidin
3. Bir bileti iptal edin
4. Para iadesinin yapıldığını kontrol edin

### Senaryo 3: Firma Admin - Sefer Ekleme
1. `metro.admin@example.com` ile giriş yapın
2. "Seferler" → "Yeni Sefer" ekleyin
3. Sefer detaylarını girin
4. Kaydedin

### Senaryo 4: Sistem Admin - Firma Ekleme
1. `admin@example.com` ile giriş yapın
2. "Firmalar" → "Yeni Firma" ekleyin
3. Firma bilgilerini girin
4. Firma admin kullanıcısı oluşturun

---

## ⚠️ Güvenlik Uyarısı

**Production ortamında:**
- Tüm test hesaplarını silin
- Varsayılan admin şifresini değiştirin
- Güçlü şifreler kullanın
- Test kuponlarını silin veya pasif hale getirin

---

## 📞 Sorun Bildirimi

Eğer test hesaplarıyla ilgili bir sorun yaşarsanız:
1. `database/seed.sql` dosyasını kontrol edin
2. Veritabanını yeniden oluşturun:
   ```bash
   rm database.sqlite
   sqlite3 database.sqlite < database/schema.sql
   sqlite3 database.sqlite < database/seed.sql
   ```
