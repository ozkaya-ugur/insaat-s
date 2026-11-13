<?php
require_once __DIR__ . '/../includes/core.php';
start_session();

if (admin_user()) redirect(admin_home_url());

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
            $_SESSION['admin'] = ['id' => (int)$row['id'], 'username' => $row['username'], 'name' => $row['full_name'], 'role' => $row['role'] ?: 'staff', 'permissions' => $row['permissions'] ?? null, 'must_change_password' => (int)($row['must_change_password'] ?? 0)];
            $_SESSION['fails'] = 0;
            throttle_clear($throttleKey);
            q('UPDATE admins SET last_login=NOW() WHERE id=?', [$row['id']]);
            redirect(admin_home_url($_SESSION['admin']));
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
<link rel="stylesheet" href="assets/admin.css?v=202611130635">
</head><body>
<div class="login-page">
  <aside class="login-brand">
    <div class="lb-top">
      <img src="../images/l-white.svg" alt="BUYAPI" class="lb-logo">
    </div>
    <div class="lb-mid">
      <span class="lb-eyebrow">Yönetim Paneli</span>
      <h2 class="lb-title">Her projede aynı özen,<br><em>aynı imza.</em></h2>
      <p class="lb-text">Projeler, galeriler, sayfa içerikleri ve mesajlar tek panelden yönetilir. Bu alan yalnızca yetkili kullanıcılar içindir.</p>
      <div class="lb-stats">
        <div class="lb-stat"><b>44</b><span>Proje</span></div>
        <div class="lb-stat"><b>35+</b><span>Yıl</span></div>
        <div class="lb-stat"><b>3</b><span>Dil</span></div>
      </div>
    </div>
    <div class="lb-bottom">
      <div class="lb-credit"><span>Tasarım ve kodlama:</span> <a href="https://www.sosyobyte.com" target="_blank" rel="noopener">Sosyobyte</a></div>
    </div>
  </aside>
  <main class="login-panel">
    <form class="login-card" method="post" autocomplete="off">
      <h1>Panele giriş</h1>
      <p class="sub">Devam etmek için hesabınızla oturum açın.</p>
      <?php if ($error): ?><div class="alert err"><?= e($error) ?></div><?php endif; ?>
      <?= csrf_field() ?>
      <div class="field"><label for="username">Kullanıcı Adı</label><input type="text" id="username" name="username" required autofocus autocomplete="username"></div>
      <div class="field"><label for="password">Şifre</label><input type="password" id="password" name="password" required autocomplete="current-password"></div>
      <button class="btn" type="submit">Giriş Yap</button>
      <p class="lc-foot"><a href="../index.html">Siteye dön</a> &nbsp;·&nbsp; BUYAPI Yönetim Paneli</p>
    </form>
  </main>
</div>
</body></html>
