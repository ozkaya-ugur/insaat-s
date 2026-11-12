-- Mobil proje detay akordiyonu: "Proje Künyesi" başlığı (TR/EN/AR). İdempotent.
INSERT INTO translations (lang, t_key, t_value) VALUES ('tr', 'detail.spec_title', 'PROJE KÜNYESİ') ON DUPLICATE KEY UPDATE t_value=VALUES(t_value);
INSERT INTO translations (lang, t_key, t_value) VALUES ('en', 'detail.spec_title', 'PROJECT FACTS') ON DUPLICATE KEY UPDATE t_value=VALUES(t_value);
INSERT INTO translations (lang, t_key, t_value) VALUES ('ar', 'detail.spec_title', 'بطاقة المشروع') ON DUPLICATE KEY UPDATE t_value=VALUES(t_value);
