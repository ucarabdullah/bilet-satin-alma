-- Test Verileri (BiBilet Platformu)
-- Tüm şifreler: password123
-- ID'ler benzersiz string olarak verilmiştir

-- 1. Admin Hesabı
INSERT INTO User (id, full_name, email, password, role, balance) VALUES
('admin-uuid-001', 'Sistem Yöneticisi', 'admin@bibilet.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYyILuxm7EW', 'admin', 0);

-- 2. Otobüs Firmaları
INSERT INTO Bus_Company (id, name, logo_path) VALUES
('company-uuid-001', 'Metro Turizm', NULL),
('company-uuid-002', 'Pamukkale Turizm', NULL),
('company-uuid-003', 'Ulusoy Seyahat', NULL);

-- 3. Firma Admin Kullanıcıları
INSERT INTO User (id, full_name, email, password, role, company_id, balance) VALUES
('user-uuid-001', 'Metro Admin', 'metro@bibilet.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYyILuxm7EW', 'company', 'company-uuid-001', 0),
('user-uuid-002', 'Pamukkale Admin', 'pamukkale@bibilet.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYyILuxm7EW', 'company', 'company-uuid-002', 0),
('user-uuid-003', 'Ulusoy Admin', 'ulusoy@bibilet.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYyILuxm7EW', 'company', 'company-uuid-003', 0);

-- 4. Normal Kullanıcılar (Yolcular)
INSERT INTO User (id, full_name, email, password, role, balance) VALUES
('user-uuid-101', 'Ahmet Yılmaz', 'ahmet@example.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYyILuxm7EW', 'user', 1000),
('user-uuid-102', 'Ayşe Demir', 'ayse@example.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYyILuxm7EW', 'user', 1500),
('user-uuid-103', 'Mehmet Kaya', 'mehmet@example.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYyILuxm7EW', 'user', 2000);

-- 5. Seferler (Metro Turizm)
INSERT INTO Trips (id, company_id, departure_city, destination_city, departure_time, arrival_time, price, capacity) VALUES
('trip-uuid-001', 'company-uuid-001', 'İstanbul', 'Ankara', 
    datetime('now', '+1 day', 'start of day', '+9 hours'), 
    datetime('now', '+1 day', 'start of day', '+15 hours'), 200, 45),
('trip-uuid-002', 'company-uuid-001', 'Ankara', 'İzmir', 
    datetime('now', '+1 day', 'start of day', '+14 hours'), 
    datetime('now', '+1 day', 'start of day', '+22 hours'), 250, 45),
('trip-uuid-003', 'company-uuid-001', 'İzmir', 'Antalya', 
    datetime('now', '+2 days', 'start of day', '+20 hours'), 
    datetime('now', '+3 days', 'start of day', '+4 hours'), 180, 45);

-- 6. Seferler (Pamukkale Turizm)
INSERT INTO Trips (id, company_id, departure_city, destination_city, departure_time, arrival_time, price, capacity) VALUES
('trip-uuid-004', 'company-uuid-002', 'İstanbul', 'İzmir', 
    datetime('now', '+1 day', 'start of day', '+10 hours'), 
    datetime('now', '+1 day', 'start of day', '+18 hours'), 220, 45),
('trip-uuid-005', 'company-uuid-002', 'Ankara', 'Antalya', 
    datetime('now', '+2 days', 'start of day', '+16 hours'), 
    datetime('now', '+3 days', 'start of day', '+2 hours'), 280, 45),
('trip-uuid-006', 'company-uuid-002', 'İzmir', 'Bursa', 
    datetime('now', '+1 day', 'start of day', '+8 hours'), 
    datetime('now', '+1 day', 'start of day', '+13 hours'), 150, 45);

-- 7. Seferler (Ulusoy Seyahat)
INSERT INTO Trips (id, company_id, departure_city, destination_city, departure_time, arrival_time, price, capacity) VALUES
('trip-uuid-007', 'company-uuid-003', 'İstanbul', 'Antalya', 
    datetime('now', '+1 day', 'start of day', '+22 hours'), 
    datetime('now', '+2 days', 'start of day', '+8 hours'), 300, 45),
('trip-uuid-008', 'company-uuid-003', 'İzmir', 'Ankara', 
    datetime('now', '+2 days', 'start of day', '+8 hours'), 
    datetime('now', '+2 days', 'start of day', '+16 hours'), 240, 45),
('trip-uuid-009', 'company-uuid-003', 'Bursa', 'Antalya', 
    datetime('now', '+3 days', 'start of day', '+19 hours'), 
    datetime('now', '+4 days', 'start of day', '+5 hours'), 260, 45);

-- 8. Genel Kuponlar (Admin - company_id NULL)
INSERT INTO Coupons (id, code, discount, usage_limit, expire_date, company_id) VALUES
('coupon-uuid-001', 'SUMMER2024', 0.20, 100, datetime('now', '+60 days'), NULL),
('coupon-uuid-002', 'WELCOME50', 0.50, 50, datetime('now', '+30 days'), NULL);

-- 9. Firma Kuponları
INSERT INTO Coupons (id, code, discount, usage_limit, expire_date, company_id) VALUES
('coupon-uuid-003', 'METRO30', 0.30, 30, datetime('now', '+45 days'), 'company-uuid-001'),
('coupon-uuid-004', 'PAMUKKALE25', 0.25, 40, datetime('now', '+45 days'), 'company-uuid-002'),
('coupon-uuid-005', 'ULUSOY15', 0.15, 50, datetime('now', '+45 days'), 'company-uuid-003');
