-- 012: Personel hesapları ve sayfa/bölüm bazlı yetkilendirme
-- admins tablosuna rol, yetki (JSON), aktiflik ve şifre değiştirme zorunluluğu eklenir.

ALTER TABLE admins
  ADD COLUMN role VARCHAR(20) NOT NULL DEFAULT 'staff' AFTER full_name,
  ADD COLUMN permissions TEXT NULL AFTER role,
  ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER permissions,
  ADD COLUMN must_change_password TINYINT(1) NOT NULL DEFAULT 0 AFTER is_active,
  ADD COLUMN updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

-- Mevcut en eski yönetici sahip (owner) olur; sahip tüm yetkilere otomatik sahiptir.
UPDATE admins SET role = 'owner' WHERE id = (SELECT mn FROM (SELECT MIN(id) AS mn FROM admins) AS t);

CREATE INDEX idx_admins_role ON admins (role);
