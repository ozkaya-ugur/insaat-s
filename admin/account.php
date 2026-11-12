<?php
require_once __DIR__ . '/_layout.php';
$me = require_perm('account', 'view');
$isOwner = admin_is_owner($me);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $act = $_POST['action'] ?? '';
    try {
        if ($act === 'password') {
            if (!admin_can('account', 'password', $me)) admin_deny();
            $row = q('SELECT * FROM admins WHERE id=?', [$me['id']])->fetch();
            if (!password_verify((string)$_POST['current'], $row['password_hash'])) throw new RuntimeException('Mevcut şifre hatalı.');
            $new = (string)$_POST['new'];
            if (strlen($new) < 8) throw new RuntimeException('Yeni şifre en az 8 karakter olmalı.');
            if ($new !== $_POST['new2']) throw new RuntimeException('Yeni şifreler eşleşmiyor.');
            q('UPDATE admins SET password_hash=?, must_change_password=0 WHERE id=?', [password_hash($new, PASSWORD_DEFAULT), $me['id']]);
            session_regenerate_id(true);
            flash('Şifre değiştirildi.');
        } elseif ($act === 'profile') {
            $name = trim((string)($_POST['full_name'] ?? ''));
            q('UPDATE admins SET full_name=? WHERE id=?', [$name, $me['id']]);
            flash('Profil bilgisi güncellendi.');
        }
    } catch (Throwable $ex) { flash($ex->getMessage(), 'err'); }
    redirect('account.php');
}

admin_header('Hesabım', 'account');
$row = q('SELECT * FROM admins WHERE id=?', [$me['id']])->fetch();
$tree = admin_perm_tree();
$myPerms = admin_perms($me);
?>
<div class="grid g2">
  <div class="card">
    <h2><i class="fa-solid fa-id-badge" style="color:var(--gold)"></i> Hesap Bilgileri</h2>
    <p class="muted" style="margin-top:-6px">
      Rol: <b style="color:var(--gold)"><?= $isOwner ? 'Sahip (tüm yetkiler)' : 'Personel' ?></b><br>
      Kullanıcı adı: <b><?= e($row['username']) ?></b><br>
      Son giriş: <?= $row['last_login'] ? e(date('d.m.Y H:i', strtotime($row['last_login']))) : '—' ?>
    </p>
    <form method="post"><?= csrf_field() ?>
      <input type="hidden" name="action" value="profile">
      <div class="field"><label>Ad Soyad</label><input type="text" name="full_name" value="<?= e($row['full_name']) ?>"></div>
      <button class="btn ghost sm">Adı Güncelle</button>
    </form>
  </div>

  <?php if (admin_can('account', 'password', $me)): ?>
  <form method="post" class="card"><?= csrf_field() ?>
    <h2><i class="fa-solid fa-key" style="color:var(--gold)"></i> Şifre Değiştir</h2>
    <div class="field"><label>Mevcut Şifre</label><input type="password" name="current" required></div>
    <div class="field"><label>Yeni Şifre (min. 8)</label><input type="password" name="new" required></div>
    <div class="field"><label>Yeni Şifre (tekrar)</label><input type="password" name="new2" required></div>
    <button class="btn" name="action" value="password">Şifreyi Güncelle</button>
  </form>
  <?php endif; ?>
</div>

<div class="card" style="margin-top:24px">
  <div class="row between" style="margin-bottom:10px">
    <h2><i class="fa-solid fa-shield-halved" style="color:var(--gold)"></i> Yetkilerim</h2>
    <?php if (admin_can_any('staff', $me)): ?><a class="btn ghost sm" href="staff.php"><i class="fa-solid fa-users-gear"></i> Personel & Yetkiler</a><?php endif; ?>
  </div>
  <?php if ($isOwner): ?>
    <p class="muted">Sahip rolü tüm sayfa ve bölümlere erişir. Personel hesaplarını ve yetkilerini <a href="staff.php" style="color:var(--gold)">Personel &amp; Yetkiler</a> sayfasından yönetebilirsiniz.</p>
  <?php else: ?>
    <table class="resp"><thead><tr><th>Sayfa</th><th>Erişebildiğiniz bölümler</th></tr></thead><tbody>
    <?php foreach ($tree as $page => $meta): $sel = $myPerms[$page] ?? []; if (!$sel) continue; ?>
      <tr><td><b><?= e($meta['label']) ?></b></td>
      <td><?php $lbl = []; foreach ($meta['sections'] as $sec => $l) if (in_array($sec, $sel, true)) $lbl[] = $l; echo e(implode(', ', $lbl)); ?></td></tr>
    <?php endforeach; if (!array_filter($myPerms)): ?>
      <tr><td colspan="2" class="muted" style="text-align:center;padding:18px">Henüz yetki verilmemiş. Yöneticinize başvurun.</td></tr>
    <?php endif; ?>
    </tbody></table>
  <?php endif; ?>
</div>
<?php admin_footer(); ?>
