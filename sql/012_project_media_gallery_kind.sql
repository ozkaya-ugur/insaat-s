-- project_media kind alanına gallery değeri ekle (render ile geriye dönük uyum korunur)
ALTER TABLE project_media
  MODIFY COLUMN kind ENUM('hero','gallery','render','plan','site','video') NOT NULL;
