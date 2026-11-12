-- BUYAPI Yama Paketi - Migration 001
-- Amaç: projects tablosuna is_new ve is_urban_renewal alanlarını eklemek.
-- Dayanak: buyapi_kod_sartnamesi.md Bölüm 3/A, 3/B, 4.
-- Not: Bu migration mevcut kayıtları bozmaz; yeni sütunlar DEFAULT 0 ile eklenir.

/* =========================
   UP MIGRATION
   ========================= */

START TRANSACTION;

ALTER TABLE `projects`
  ADD COLUMN `is_new` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Yeni Projeler sekmesinde gösterilsin' AFTER `status`,
  ADD COLUMN `is_urban_renewal` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Kentsel dönüşüm projesi' AFTER `is_new`;

ALTER TABLE `projects`
  ADD INDEX `idx_projects_is_new` (`is_new`),
  ADD INDEX `idx_projects_is_urban_renewal` (`is_urban_renewal`);

COMMIT;

/* =========================
   DOWN MIGRATION (GERİ AL)
   ========================= */

-- Geri almak için aşağıdaki blok ayrı çalıştırılmalıdır:
-- START TRANSACTION;
-- ALTER TABLE `projects`
--   DROP INDEX `idx_projects_is_new`,
--   DROP INDEX `idx_projects_is_urban_renewal`;
-- ALTER TABLE `projects`
--   DROP COLUMN `is_urban_renewal`,
--   DROP COLUMN `is_new`;
-- COMMIT;

/* =========================
   PANEL FORMU KISA NOTU
   =========================
1) Proje formuna iki checkbox ekleyin:
   - is_new            -> "Yeni Proje"
   - is_urban_renewal  -> "Kentsel Dönüşüm Projesi"
2) Kayıt/Güncelleme sorgularına iki alanı dahil edin.
3) Liste ekranında rozet/sütun olarak gösterin.
4) Geçiş dönemi için ön yüzde fallback koruyun:
   - isNew || status === 'yeni-proje'
   - isUrbanRenewal || type içinde 'kentsel'
*/
