-- 014: Proje detay talep formuna e-posta alanı çevirileri
INSERT INTO translations (lang, t_key, t_value) VALUES
  ('tr', 'detail.inquiry_email_label', 'E-POSTA ADRESİNİZ *'),
  ('en', 'detail.inquiry_email_label', 'YOUR E-MAIL ADDRESS *'),
  ('ar', 'detail.inquiry_email_label', 'بريدك الإلكتروني *'),
  ('tr', 'detail.ph_email', 'ornek@eposta.com'),
  ('en', 'detail.ph_email', 'name@example.com'),
  ('ar', 'detail.ph_email', 'name@example.com')
ON DUPLICATE KEY UPDATE t_value = VALUES(t_value);
