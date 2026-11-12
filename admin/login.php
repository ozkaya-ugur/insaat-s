<?php
require_once __DIR__ . '/../includes/core.php';
start_session();

if (admin_user()) redirect('index.php');

// Hiç yönetici yoksa kuruluma yönlendir
try {
    if ((int)q('SELECT COUNT(*) FROM admins')->fetchColumn() === 0) redirect('setup.php');
} catch (Throwable $ex) {
    exit('Veritabanına bağlanılamadı. config.php ve database.sql dosyalarını kontrol edin.');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    // Kaba kuvvet koruması: oturum başına 5 hatalı denemeden sonra 60 sn bekleme
    $_SESSION['fails'] = $_SESSION['fails'] ?? 0;
    $_SESSION['lock_until'] = $_SESSION['lock_until'] ?? 0;

    // IP bazlı koruma (çerez silinse de devre dışı kalmaz): 15 dakikada 10 hatalı deneme
    $throttleKey = 'login:' . client_ip();
    if (throttle_count($throttleKey, 900) >= 10) {
        $error = 'Çok fazla hatalı giriş denemesi. Lütfen 15 dakika sonra tekrar deneyin.';
    } elseif (time() < $_SESSION['lock_until']) {
        $error = 'Çok fazla hatalı deneme. Lütfen ' . ($_SESSION['lock_until'] - time()) . ' saniye bekleyin.';
    } else {
        $u = trim($_POST['username'] ?? '');
        $p = (string)($_POST['password'] ?? '');
        $row = q('SELECT * FROM admins WHERE username=?', [$u])->fetch();
        if ($row && password_verify($p, $row['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin'] = ['id' => (int)$row['id'], 'username' => $row['username'], 'name' => $row['full_name']];
            $_SESSION['fails'] = 0;
            throttle_clear($throttleKey);
            q('UPDATE admins SET last_login=NOW() WHERE id=?', [$row['id']]);
            redirect('index.php');
        }
        $_SESSION['fails']++;
        throttle_hit($throttleKey);
        if ($_SESSION['fails'] >= 5) { $_SESSION['lock_until'] = time() + 60; $_SESSION['fails'] = 0; }
        $error = 'Kullanıcı adı veya şifre hatalı.';
        usleep(400000);
    }
}
?><!DOCTYPE html>
<html lang="tr"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Giriş | BUYAPI Panel</title>
<link rel="stylesheet" href="assets/admin.css">
</head><body>
<div class="login-wrap"><form class="login-box" method="post" autocomplete="off">
  <h1>BUYAPI</h1><p class="sub">YÖNETİM PANELİ</p>
  <?php if ($error): ?><div class="alert err"><?= e($error) ?></div><?php endif; ?>
  <?= csrf_field() ?>
  <div class="field"><label>Kullanıcı Adı</label><input type="text" name="username" required autofocus></div>
  <div class="field"><label>Şifre</label><input type="password" name="password" required></div>
  <button class="btn" style="width:100%;justify-content:center">Giriş Yap</button>
</form></div>
</body></html>
