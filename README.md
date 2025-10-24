# BiBilet 🚌

Online otobüs bileti satın alma platformu.

## Kurulum

### Gereksinimler
- Docker
- Docker Compose

### Adımlar

1. **Projeyi klonlayın:**
```bash
git clone https://github.com/ucarabdullah/bilet-satin-alma.git
cd bilet-satin-alma
```

2. **Docker ile başlatın:**
```bash
docker-compose up -d
```

3. **Uygulamayı açın:**
Tarayıcınızda aşağıdaki adrese gidin:
```
http://localhost:8080
```

## 🔑 Admin Giriş Bilgileri

| Bilgi | Değer |
|-------|-------|
| **URL** | http://localhost:8080/admin/login |
| **Email** | admin@bibilet.com |
| **Şifre** | password123 |

## 🚌 Firma Giriş Bilgileri
| **URL** | http://localhost:8080/copmany/login |
| Firma | Email | Şifre |
|-------|-------|-------|
| Metro Turizm | metro@bibilet.com | password123 |
| Pamukkale | pamukkale@bibilet.com | password123 |
| Ulusoy | ulusoy@bibilet.com | password123 |

## 🛠️ Teknolojiler

- PHP 8.2
- SQLite
- Bootstrap 5
- Docker & Apache
