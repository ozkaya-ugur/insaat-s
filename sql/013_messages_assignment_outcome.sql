-- 013: Gelen taleplerin personel ataması ve sonuç takibi
-- Okuyan personel otomatik atanır; sonuç olumlu/olumsuz tutulur.

ALTER TABLE messages
  ADD COLUMN assigned_admin_id INT NULL AFTER is_read,
  ADD COLUMN assigned_at DATETIME NULL AFTER assigned_admin_id,
  ADD COLUMN result_status VARCHAR(20) NOT NULL DEFAULT 'beklemede' AFTER assigned_at,
  ADD COLUMN result_note TEXT NULL AFTER result_status,
  ADD COLUMN result_admin_id INT NULL AFTER result_note,
  ADD COLUMN result_at DATETIME NULL AFTER result_admin_id;

CREATE INDEX idx_messages_assigned_admin ON messages (assigned_admin_id);
CREATE INDEX idx_messages_result_status ON messages (result_status);
