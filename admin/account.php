<?php
require_once __DIR__ . '/_layout.php';
$me = require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $act = $_POST['action'] ?? '';
    try {
        if ($act === 'password') {
            $row = q('SELECT * FROM admins WHERE id=?', [$me['id']])->fetch();
            if (!password_verify((string)$_POST['current'], $row['password_hash'])) throw new RuntimeException('Mevcut şifre hatalı.');
            $new = (string)$_POST['new'];
            if (strlen($new) < 10) throw new RuntimeException('Yeni şifre en az 10 karakter olmalı.');
            if ($new !== $_POST['new2']) throw new RuntimeException('Yeni şifreler eşleşmiyor.');
            q('UPDATE admins SET password_hash=? WHERE id=?', [password_hash($new, PASSWORD_DEFAULT), $me['id']]);
            session_regenerate_id(true);
            flash('Şifre değiştirildi.');
        } elseif ($act === 'add_user') {
            $u = trim($_POST['username'] ?? ''); $p = (string)($_POST['password'] ?? '');
            if (!preg_match('/^[A-Za-z0-9_.-]{3,40}$/', $u)) throw new RuntimeException('Geçersiz kullanıcı adı.');
            if (strlen($p) < 10) throw new RuntimeException('Şifre en az 10 karakter olmalı.');
            if (q('SELECT id FROM admins WHERE username=?', [$u])->fetch()) throw new RuntimeException('Bu kullanıcı adı kullanımda.');
            q('INSERT INTO admins (username,password_hash,full_name) VALUES (?,?,?)', [$u, password_hash($p, PASSWORD_DEFAULT), trim($_POST['full_name'] ?? '')]);
            flash('Yönetici eklendi.');
        } elseif ($act === 'del_user') {
            $id = (int)$_POST['id'];
            if ($id === (int)$me['id']) throw new RuntimeException('Kendi hesabınızı silemezsiniz.');
            q('DELETE FROM admins WHERE id=?', [$id]);
            flash('Yönetici silindi.');
        }
    } catch (Throwable $ex) { flash($ex->getMessage(), 'err'); }
    redirect('account.php');
}

admin_header('Hesabım', 'account');
$users = q('SELECT id,username,full_name,last_login FROM admins ORDER BY id')->fetchAll();
?>
<div class="grid g2">
<form method="post" class="card"><?= csrf_field() ?>
  <h2>Şifre Değiştir</h2>
  <div class="field"><label>Mevcut Şifre</label><input type="password" name="current" required></div>
  <div class="field"><label>Yeni Şifre (min. 10)</label><input type="password" name="new" required></div>
  <div class="field"><label>Yeni Şifre (tekrar)</label><input type="password" name="new2" required></div>
  <button class="btn" name="action" value="password">Şifreyi Güncelle</button>
</form>

<form method="post" class="card"><?= csrf_field() ?>
  <h2>Yeni Yönetici Ekle</h2>
  <div class="field"><label>Ad Soyad</label><input type="text" name="full_name"></div>
  <div class="field"><label>Kullanıcı Adı</label><input type="text" name="username" required></div>
  <div class="field"><label>Şifre (min. 10)</label><input type="password" name="password" required></div>
  <button class="btn ghost" name="action" value="add_user">Yönetici Ekle</button>
</form>
</div>

<div class="card"><h2>Yöneticiler</h2>
  <table class="resp"><thead><tr><th>Kullanıcı</th><th>Ad</th><th>Son Giriş</th><th></th></tr></thead><tbody>
  <?php foreach ($users as $u): ?>
    <tr><td><?= e($u['username']) ?></td><td><?= e($u['full_name']) ?></td>
    <td class="muted"><?= $u['last_login'] ? e(date('d.m.Y H:i', strtotime($u['last_login']))) : '—' ?></td>
    <td style="text-align:right"><?php if ((int)$u['id'] !== (int)$me['id']): ?>
      <form method="post" data-confirm="Yönetici silinsin mi?"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$u['id'] ?>"><button class="btn danger sm" name="action" value="del_user">Sil</button></form>
    <?php else: ?><span class="muted">siz</span><?php endif; ?></td></tr>
  <?php endforeach; ?></tbody></table>
</div>
<?php admin_footer();
