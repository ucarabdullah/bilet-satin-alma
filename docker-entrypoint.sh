#!/bin/bash

# Renk kodları
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}BiBilet - Database Setup${NC}"
echo -e "${GREEN}========================================${NC}"

# Veritabanı dosyası kontrolü
if [ ! -f "database.sqlite" ]; then
    echo -e "${YELLOW}Veritabanı bulunamadı. Oluşturuluyor...${NC}"
    
    # Schema'yı import et
    if [ -f "database/schema.sql" ]; then
        sqlite3 database.sqlite < database/schema.sql
        echo -e "${GREEN}✓ Schema oluşturuldu${NC}"
    else
        echo -e "${YELLOW}⚠ database/schema.sql bulunamadı!${NC}"
    fi
    
    # Seed data'yı import et
    if [ -f "database/seed.sql" ]; then
        sqlite3 database.sqlite < database/seed.sql
        echo -e "${GREEN}✓ Test verileri yüklendi${NC}"
    else
        echo -e "${YELLOW}⚠ database/seed.sql bulunamadı!${NC}"
    fi
    
    # İzinleri ayarla
    chown www-data:www-data database.sqlite
    chmod 664 database.sqlite
    
    echo -e "${GREEN}✓ Veritabanı başarıyla oluşturuldu!${NC}"
else
    echo -e "${GREEN}✓ Veritabanı zaten mevcut${NC}"
fi

echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}Apache başlatılıyor...${NC}"
echo -e "${GREEN}========================================${NC}"

# Apache'yi başlat
exec apache2-foreground
