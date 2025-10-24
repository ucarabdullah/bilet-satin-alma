FROM php:8.2-apache

# SQLite yükle
RUN apt-get update && \
    apt-get install -y sqlite3 libsqlite3-dev && \
    docker-php-ext-install pdo pdo_sqlite && \
    a2enmod rewrite && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Çalışma dizini
WORKDIR /var/www/html

# Proje dosyaları
COPY . .

# Apache DocumentRoot ve .htaccess ayarı
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf && \
    sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Yazma izinleri
RUN chown -R www-data:www-data /var/www/html/database /var/www/html/logs /var/www/html/public/assets/uploads && \
    chmod -R 775 /var/www/html/database /var/www/html/logs /var/www/html/public/assets/uploads

EXPOSE 80
CMD ["apache2-foreground"]
