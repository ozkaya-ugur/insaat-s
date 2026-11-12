<?php
require_once __DIR__ . '/_layout.php';
require_perm('index', 'view');
admin_header('Genel Bakış', 'index');

$c = fn($sql) => (int)q($sql)->fetchColumn();
$stats = [
    ['Toplam Proje', $c('SELECT COUNT(*) FROM projects')],
    ['Devam Eden', $c("SELECT COUNT(*) FROM projects WHERE status='devam-eden'")],
    ['Tamamlanan', $c("SELECT COUNT(*) FROM projects WHERE status='tamamlanan'")],
    ['Okunmamış Mesaj', $c('SELECT COUNT(*) FROM messages WHERE is_read=0')],
];
$last = q('SELECT * FROM messages ORDER BY id DESC LIMIT 6')->fetchAll();
?>
<div class="grid g4" style="margin-bottom:20px">
<?php foreach ($stats as [$l, $v]): ?><div class="stat"><b><?= $v ?></b><span><?= e($l) ?></span></div><?php endforeach; ?>
</div>

<div class="card">
  <div class="row between"><h2>Son Mesajlar</h2><a class="btn ghost sm" href="messages.php">Tümü</a></div>
  <?php if (!$last): ?><p class="muted">Henüz mesaj yok.</p><?php else: ?>
  <table class="resp"><thead><tr><th>Ad</th><th>Telefon</th><th>Proje</th><th>Tarih</th></tr></thead><tbody>
  <?php foreach ($last as $m): ?>
    <tr class="<?= $m['is_read'] ? '' : 'msg-unread' ?>">
      <td><a href="messages.php?view=<?= (int)$m['id'] ?>"><?= e($m['full_name']) ?></a></td>
      <td><?= e($m['phone']) ?></td><td><?= e($m['project']) ?></td>
      <td class="muted"><?= e(date('d.m.Y H:i', strtotime($m['created_at']))) ?></td>
    </tr>
  <?php endforeach; ?></tbody></table><?php endif; ?>
</div>

<div class="card">
  <h2>Hızlı İşlemler</h2>
  <div class="row">
    <a class="btn" href="project_edit.php"><i class="fa-solid fa-plus"></i> Yeni Proje</a>
    <a class="btn ghost" href="sliders.php">Slider Düzenle</a>
    <a class="btn ghost" href="translations.php">Çevirileri Düzenle</a>
  </div>
</div>
<?php admin_footer();
