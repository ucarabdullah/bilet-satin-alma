FROM php:8.2-apache

# SQLite ve gerekli PHP eklentilerini yükle
RUN apt-get update && apt-get install -y \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Apache mod_rewrite'ı etkinleştir
RUN a2enmod rewrite

# Çalışma dizinini ayarla
WORKDIR /var/www/html

# Proje dosyalarını kopyala
COPY . /var/www/html/

# Veritabanını oluştur
RUN if [ -f database/schema.sql ]; then \
        sqlite3 /var/www/html/database.sqlite < database/schema.sql && \
        echo "Schema created"; \
    fi && \
    if [ -f database/seed.sql ]; then \
        sqlite3 /var/www/html/database.sqlite < database/seed.sql && \
        echo "Seed data loaded"; \
    fi && \
    chown www-data:www-data /var/www/html/database.sqlite && \
    chmod 664 /var/www/html/database.sqlite

# Public klasörünü DocumentRoot olarak ayarla
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Database klasörü için yazma izinleri
RUN mkdir -p /var/www/html/database && \
    chown -R www-data:www-data /var/www/html/database && \
    chmod -R 775 /var/www/html/database

# Log klasörü için yazma izinleri
RUN mkdir -p /var/www/html/logs && \
    chown -R www-data:www-data /var/www/html/logs && \
    chmod -R 775 /var/www/html/logs

# Upload klasörü için yazma izinleri
RUN mkdir -p /var/www/html/public/assets/uploads && \
    chown -R www-data:www-data /var/www/html/public/assets/uploads && \
    chmod -R 775 /var/www/html/public/assets/uploads

# Port 80'i aç
EXPOSE 80

# Apache'yi başlat
CMD ["apache2-foreground"]
