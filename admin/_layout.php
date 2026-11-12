<?php
require_once __DIR__ . '/../includes/core.php';

function admin_header(string $title, string $active = ''): array {
    $u = require_login();
    // Emniyet kemeri: sayfa kendi kontrolünü yapmayı unutsa bile yetkisiz erişim engellenir.
    if ($active !== '' && !admin_can_any($active, $u)) admin_deny();
    $unread = 0;
    if (admin_can('messages', 'view', $u)) {
        try { $unread = (int)q('SELECT COUNT(*) FROM messages WHERE is_read=0')->fetchColumn(); } catch (Throwable $e) {}
    }
    $menu = [];
    foreach (admin_perm_tree() as $k => $meta) {
        if (!admin_can_any($k, $u)) continue;
        $menu[$k] = [$meta['label'], $meta['icon']];
    }
    $f = flash();
    ?><!DOCTYPE html>
<html lang="tr"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title><?= e($title) ?> | BUYAPI Panel</title>
<?= favicon_links_html('../') ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="assets/admin.css?v=<?= @filemtime(__DIR__ . '/assets/admin.css') ?: 1 ?>">
</head><body class="<?= admin_readonly() ? 'ro' : '' ?>">
<aside class="side" id="side">
  <div class="brand">BUYAPI <small>YÖNETİM</small></div>
  <nav>
  <?php foreach ($menu as $k => [$label, $ic]): ?>
    <a href="<?= $k ?>.php" class="<?= $active === $k ? 'on' : '' ?>">
      <i class="fa-solid <?= $ic ?>"></i><span><?= e($label) ?></span>
      <?php if ($k === 'messages' && $unread): ?><b class="pill"><?= $unread ?></b><?php endif; ?>
    </a>
  <?php endforeach; ?>
  </nav>
  <div class="side-foot">
    <div><i class="fa-solid fa-circle-user"></i> <?= e($u['username']) ?>
      <span class="pill" style="background:rgba(212,175,55,0.16);color:var(--gold);margin-left:6px"><?= admin_is_owner($u) ? 'Sahip' : 'Personel' ?></span>
    </div>
    <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Çıkış</a>
  </div>
</aside>
<main class="main">
  <header class="top">
    <button class="burger" onclick="document.getElementById('side').classList.toggle('open')"><i class="fa-solid fa-bars"></i></button>
    <h1><?= e($title) ?></h1>
  </header>
  <?php if (!empty($u['must_change_password']) && admin_can('account', 'password', $u)): ?>
    <div class="alert err">Güvenliğiniz için şifrenizi değiştirmeniz gerekiyor. <a href="account.php" style="color:inherit;text-decoration:underline">Hesabım</a> sayfasından yeni şifre belirleyin.</div>
  <?php endif; ?>
  <?php if (admin_readonly()): ?><div class="alert">Salt görüntüleme yetkiniz var. Bu sayfada değişiklik kaydedemezsiniz.</div><?php endif; ?>
  <?php if ($f): ?><div class="alert <?= $f[1] === 'err' ? 'err' : 'ok' ?>"><?= e($f[0]) ?></div><?php endif; ?>
<?php
    return $u;
}

function admin_footer(): void { ?>
</main>
<script src="assets/dropzone.js?v=<?= @filemtime(__DIR__ . '/assets/dropzone.js') ?: 1 ?>"></script>
<script>
const lockForm = (f) => f.querySelectorAll('input,select,textarea,button').forEach(el => { if (!el.closest('[data-keep]')) el.disabled = true; });
if (document.body.classList.contains('ro')) document.querySelectorAll('main form').forEach(lockForm);
document.querySelectorAll('main form[data-ro]').forEach(lockForm);
document.querySelectorAll('form[data-confirm]').forEach(f=>f.addEventListener('submit',e=>{
  if(!confirm(f.dataset.confirm)) e.preventDefault();
}));
// Global sekme değiştirici
document.addEventListener('click', (e) => {
  const btn = e.target.closest('.ltabs button');
  if (btn) {
    const tabsContainer = btn.closest('.ltabs');
    const parentCard = tabsContainer.closest('.card') || tabsContainer.parentElement;
    const lang = btn.dataset.l;
    tabsContainer.querySelectorAll('button').forEach(b => b.classList.toggle('on', b === btn));
    parentCard.querySelectorAll('.lpane').forEach(p => p.classList.toggle('on', p.dataset.pane === lang));
  }
});
</script>
</body></html>
<?php }

function lang_tabs(string $id, ?callable $render = null): string {
    $langs = get_languages();
    $html = '<div class="ltabs" data-for="' . e($id) . '">';
    foreach ($langs as $idx => $l) {
        $on = ($idx === 0) ? ' class="on"' : '';
        $html .= '<button type="button"' . $on . ' data-l="' . e($l['code']) . '">' . e($l['flag'] . ' ' . $l['name']) . '</button>';
    }
    $html .= '</div>';

    // İsteğe bağlı içerik üretici: her dil için bir .lpane üretir (ilk dil açık gelir)
    if ($render !== null) {
        foreach ($langs as $idx => $l) {
            $on = ($idx === 0) ? ' on' : '';
            ob_start();
            $render($l['code'], $idx === 0);
            $pane = ob_get_clean();
            $html .= '<div class="lpane' . $on . '" data-pane="' . e($l['code']) . '">' . $pane . '</div>';
        }
    }

    return $html;
}
