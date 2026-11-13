-- 021: "Satışta" artık bir durum değil, ayrı bir bayrak.
-- Devam eden + satışta ve tamamlanan + satışta projeler ayrıştırılabilsin diye
-- status ENUM'undan 'satista' çıkarılır, yerine projects.is_on_sale kolonu gelir.
-- Idempotent: tekrar çalıştırılabilir.

SET @has_col := (SELECT COUNT(*) FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'projects' AND COLUMN_NAME = 'is_on_sale');
SET @sql := IF(@has_col = 0,
  'ALTER TABLE `projects` ADD COLUMN `is_on_sale` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_urban_renewal`',
  'DO 0');
PREPARE st FROM @sql; EXECUTE st; DEALLOCATE PREPARE st;

UPDATE `projects` SET `is_on_sale` = 1, `status` = 'devam-eden' WHERE `status` = 'satista';

SET @enum := (SELECT COLUMN_TYPE FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'projects' AND COLUMN_NAME = 'status');
SET @sql := IF(@enum LIKE '%satista%',
  "ALTER TABLE `projects` MODIFY COLUMN `status` ENUM('devam-eden','tamamlanan','yeni-proje') NOT NULL DEFAULT 'devam-eden'",
  'DO 0');
PREPARE st FROM @sql; EXECUTE st; DEALLOCATE PREPARE st;
