<?php
// İlk yöneticiyi oluşturur. Yönetici varsa kendini kilitler.
require_once __DIR__ . '/../includes/core.php';
start_session();

try {
    if ((int)q('SELECT COUNT(*) FROM admins')->fetchColumn() > 0) { http_response_code(403); exit('Kurulum zaten tamamlanmış. Bu dosyayı sunucudan silebilirsiniz.'); }
} catch (Throwable $ex) {
    exit('Veritabanına bağlanılamadı veya tablolar yok. Önce database.sql dosyasını içe aktarın ve config.php ayarlarını yapın.');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $u = trim($_POST['username'] ?? '');
    $p = (string)($_POST['password'] ?? '');
    $n = trim($_POST['full_name'] ?? '');
    if (!preg_match('/^[A-Za-z0-9_.-]{3,40}$/', $u)) $error = 'Kullanıcı adı 3-40 karakter; harf, rakam, . _ - kullanılabilir.';
    elseif (strlen($p) < 10) $error = 'Şifre en az 10 karakter olmalı.';
    elseif ($p !== ($_POST['password2'] ?? '')) $error = 'Şifreler eşleşmiyor.';
    else {
        q('INSERT INTO admins (username,password_hash,full_name) VALUES (?,?,?)', [$u, password_hash($p, PASSWORD_DEFAULT), $n]);
        flash('Yönetici oluşturuldu. Giriş yapabilirsiniz.');
        redirect('login.php');
    }
}
?><!DOCTYPE html>
<html lang="tr"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Kurulum | BUYAPI Panel</title>
<link rel="stylesheet" href="assets/admin.css?v=202611130635">
</head><body>
<div class="login-wrap"><form class="login-box" method="post" autocomplete="off">
  <h1>İLK KURULUM</h1><p class="sub">İlk yönetici hesabını oluşturun</p>
  <?php if ($error): ?><div class="alert err"><?= e($error) ?></div><?php endif; ?>
  <?= csrf_field() ?>
  <div class="field"><label>Ad Soyad</label><input type="text" name="full_name"></div>
  <div class="field"><label>Kullanıcı Adı</label><input type="text" name="username" required></div>
  <div class="field"><label>Şifre (min. 10 karakter)</label><input type="password" name="password" required></div>
  <div class="field"><label>Şifre (tekrar)</label><input type="password" name="password2" required></div>
  <button class="btn" style="width:100%;justify-content:center">Hesabı Oluştur</button>
</form></div>
</body></html>
