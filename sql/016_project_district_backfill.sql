-- 016: Boş ilçe alanlarını lokasyon metninin ilk parçasından doldur ("Serdivan / Sakarya" -> "Serdivan")
-- Yalnız boş kayıtlara dokunur; tekrar çalıştırmak güvenlidir.
UPDATE projects
   SET district = TRIM(SUBSTRING_INDEX(loc, '/', 1))
 WHERE (district IS NULL OR district = '')
   AND loc LIKE '%/%';
