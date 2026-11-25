-- 022: "Kiralık" bayrağı. Satışta'dan bağımsız, kırmızı rozetle gösterilir.
-- Idempotent: tekrar çalıştırılabilir.
SET @has_col := (SELECT COUNT(*) FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'projects' AND COLUMN_NAME = 'is_for_rent');
SET @sql := IF(@has_col = 0,
  'ALTER TABLE `projects` ADD COLUMN `is_for_rent` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_on_sale`',
  'DO 0');
PREPARE st FROM @sql; EXECUTE st; DEALLOCATE PREPARE st;
