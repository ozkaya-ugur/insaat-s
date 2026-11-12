<?php
require_once __DIR__ . '/../includes/core.php';

function admin_header(string $title, string $active = ''): array {
    $u = require_login();
    $unread = 0;
    try { $unread = (int)q('SELECT COUNT(*) FROM messages WHERE is_read=0')->fetchColumn(); } catch (Throwable $e) {}
    $menu = [
        'index'        => ['Genel Bakış',  'fa-gauge-high'],
        'projects'     => ['Projeler',     'fa-building'],
        'urban_renewal'=> ['Kentsel Dönüşüm', 'fa-recycle'],
        'about'        => ['Hakkımızda / Kurumsal', 'fa-landmark'],
        'pages'        => ['Sayfalar',     'fa-file-lines'],
        'sliders'      => ['Ana Sayfa Slider', 'fa-images'],
        'messages'     => ['Mesajlar',     'fa-envelope'],
        'languages'    => ['Diller',       'fa-globe'],
        'translations' => ['Çeviriler',    'fa-language'],
        'seo'          => ['SEO',          'fa-magnifying-glass-chart'],
        'settings'     => ['Site Ayarları','fa-gear'],
        'account'      => ['Hesabım',      'fa-user-shield'],
    ];
    $f = flash();
    ?><!DOCTYPE html>
<html lang="tr"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title><?= e($title) ?> | BUYAPI Panel</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="assets/admin.css">
</head><body>
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
    <div><i class="fa-solid fa-circle-user"></i> <?= e($u['username']) ?></div>
    <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Çıkış</a>
  </div>
</aside>
<main class="main">
  <header class="top">
    <button class="burger" onclick="document.getElementById('side').classList.toggle('open')"><i class="fa-solid fa-bars"></i></button>
    <h1><?= e($title) ?></h1>
  </header>
  <?php if ($f): ?><div class="alert <?= $f[1] === 'err' ? 'err' : 'ok' ?>"><?= e($f[0]) ?></div><?php endif; ?>
<?php
    return $u;
}

function admin_footer(): void { ?>
</main>
<script>
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

function lang_tabs(string $id): string {
    $langs = get_languages();
    $html = '<div class="ltabs" data-for="' . e($id) . '">';
    foreach ($langs as $idx => $l) {
        $on = ($idx === 0) ? ' class="on"' : '';
        $html .= '<button type="button"' . $on . ' data-l="' . e($l['code']) . '">' . e($l['flag'] . ' ' . $l['name']) . '</button>';
    }
    $html .= '</div>';
    return $html;
}
