-- 015: Projelere ilçe alanı ve projeye özel özellik (feature) listesi
ALTER TABLE projects
  ADD COLUMN district VARCHAR(80) NULL AFTER city_key,
  ADD COLUMN features LONGTEXT NULL AFTER district;

ALTER TABLE projects ADD INDEX idx_projects_district (district);
