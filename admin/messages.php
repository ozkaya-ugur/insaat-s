<?php
require_once __DIR__ . '/_layout.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);
    $act = $_POST['action'] ?? '';
    if ($act === 'delete' && $id) { q('DELETE FROM messages WHERE id=?', [$id]); flash('Mesaj silindi.'); redirect('messages.php'); }
    if ($act === 'unread' && $id) { q('UPDATE messages SET is_read=0 WHERE id=?', [$id]); redirect('messages.php'); }
    if ($act === 'all_read') { q('UPDATE messages SET is_read=1'); flash('Tümü okundu işaretlendi.'); redirect('messages.php'); }
}

$filter = trim($_GET['filter'] ?? '');

if (isset($_GET['export'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="mesajlar-' . date('Ymd') . '.csv"');
    $o = fopen('php://output', 'w'); fwrite($o, "\xEF\xBB\xBF");
    fputcsv($o, ['ID','Ad','Telefon','E-posta','Proje/Konu','Mesaj','Kaynak','Tarih']);
    $sql = 'SELECT * FROM messages ' . ($filter ? 'WHERE source = ? ' : '') . 'ORDER BY id DESC';
    $params = $filter ? [$filter] : [];
    foreach (q($sql, $params)->fetchAll() as $m) {
        $clean = fn($v) => preg_match('/^[=+\-@]/', (string)$v) ? "'" . $v : $v;
        fputcsv($o, [$m['id'], $clean($m['full_name']), $clean($m['phone']), $clean($m['email']), $clean($m['project']), $clean($m['message']), $m['source'], $m['created_at']]);
    }
    exit;
}

admin_header('Mesajlar & Başvurular', 'messages');

if (isset($_GET['view'])) {
    $m = q('SELECT * FROM messages WHERE id=?', [(int)$_GET['view']])->fetch();
    if ($m) {
        q('UPDATE messages SET is_read=1 WHERE id=?', [$m['id']]);
        $isKd = ($m['source'] === 'kentsel_donusum');
        ?>
        <div class="card">
          <div class="row between">
            <h2 style="margin:0">
              <?= e($m['full_name']) ?>
              <?php if ($isKd): ?>
                <span class="pill" style="background:rgba(197,160,89,0.25);color:var(--gold);margin-left:10px">Kentsel Dönüşüm Başvurusu</span>
              <?php endif; ?>
            </h2>
            <a class="btn ghost sm" href="messages.php<?= $filter ? '?filter=' . urlencode($filter) : '' ?>">← Listeye dön</a>
          </div>
          <p class="muted" style="margin:10px 0 16px"><?= e(date('d.m.Y H:i', strtotime($m['created_at']))) ?> · Kaynak: <strong><?= e($m['source']) ?></strong> · IP: <?= e($m['ip']) ?></p>
          <div class="grid g3" style="margin-bottom:16px">
            <div><label>Telefon</label><?= $m['phone'] ? '<a style="color:var(--gold);font-weight:600" href="tel:' . e(preg_replace('/[^0-9+]/', '', $m['phone'])) . '">' . e($m['phone']) . '</a>' : '—' ?></div>
            <div><label>E-posta</label><?= $m['email'] ? '<a style="color:var(--gold);font-weight:600" href="mailto:' . e($m['email']) . '">' . e($m['email']) . '</a>' : '—' ?></div>
            <div><label>Konu / Proje</label><?= e($m['project'] ?: '—') ?></div>
          </div>
          <label><?= $isKd ? 'Başvuru ve Bina Detayları' : 'Mesaj İçeriği' ?></label>
          <div class="msg-body" style="<?= $isKd ? 'border-color:rgba(197,160,89,0.3);background:rgba(197,160,89,0.03)' : '' ?>"><?= e($m['message']) ?></div>
          <div class="row" style="margin-top:16px">
            <form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$m['id'] ?>"><button class="btn ghost" name="action" value="unread">Okunmadı yap</button></form>
            <form method="post" data-confirm="Mesaj silinsin mi?"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$m['id'] ?>"><button class="btn danger" name="action" value="delete">Sil</button></form>
          </div>
        </div>
        <?php admin_footer(); exit;
    }
}

$page = max(1, (int)($_GET['p'] ?? 1)); $per = 25;
$whereSql = $filter ? 'WHERE source = ? ' : '';
$whereParams = $filter ? [$filter] : [];

$total = (int)q("SELECT COUNT(*) FROM messages $whereSql", $whereParams)->fetchColumn();
$countKd = (int)q("SELECT COUNT(*) FROM messages WHERE source = 'kentsel_donusum'")->fetchColumn();
$countContact = (int)q("SELECT COUNT(*) FROM messages WHERE source <> 'kentsel_donusum'")->fetchColumn();

$rows = q("SELECT * FROM messages $whereSql ORDER BY id DESC LIMIT $per OFFSET " . (($page - 1) * $per), $whereParams)->fetchAll();
?>
<div class="card">
  <!-- Filtre Butonları -->
  <div class="row between" style="margin-bottom:16px">
    <div class="row" style="gap:8px">
      <a class="btn sm <?= $filter === '' ? '' : 'ghost' ?>" href="messages.php">Tümü (<?= $countKd + $countContact ?>)</a>
      <a class="btn sm <?= $filter === 'kentsel_donusum' ? '' : 'ghost' ?>" href="messages.php?filter=kentsel_donusum">
        <i class="fa-solid fa-recycle" style="color:var(--gold)"></i> Kentsel Dönüşüm Başvuruları (<?= $countKd ?>)
      </a>
      <a class="btn sm <?= $filter === 'iletisim' ? '' : 'ghost' ?>" href="messages.php?filter=iletisim">İletişim Formu (<?= $countContact ?>)</a>
    </div>

    <div class="row">
      <a class="btn ghost sm" href="messages.php?export=1<?= $filter ? '&filter=' . urlencode($filter) : '' ?>"><i class="fa-solid fa-file-csv"></i> CSV İndir</a>
      <form method="post"><?= csrf_field() ?><button class="btn ghost sm" name="action" value="all_read">Tümünü okundu yap</button></form>
    </div>
  </div>

  <table class="resp">
    <thead>
      <tr>
        <th>Ad Soyad</th>
        <th>Tür</th>
        <th>Telefon</th>
        <th>E-posta</th>
        <th>Konu / Proje</th>
        <th>Tarih</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($rows as $m): $isKd = ($m['source'] === 'kentsel_donusum'); ?>
      <tr class="<?= $m['is_read'] ? '' : 'msg-unread' ?>">
        <td>
          <a href="messages.php?view=<?= (int)$m['id'] ?><?= $filter ? '&filter=' . urlencode($filter) : '' ?>" style="color:var(--text);font-weight:<?= $m['is_read'] ? '500' : '700' ?>">
            <?= e($m['full_name']) ?>
          </a>
        </td>
        <td>
          <?php if ($isKd): ?>
            <span class="pill" style="background:rgba(197,160,89,0.25);color:var(--gold)">Kentsel Dönüşüm</span>
          <?php else: ?>
            <span class="pill" style="background:rgba(255,255,255,0.06);color:var(--dim)">İletişim</span>
          <?php endif; ?>
        </td>
        <td><?= e($m['phone']) ?></td>
        <td><?= e($m['email']) ?></td>
        <td><?= e(mb_strimwidth($m['project'] ?: '—', 0, 30, '...')) ?></td>
        <td class="muted"><?= e(date('d.m.Y H:i', strtotime($m['created_at']))) ?></td>
        <td style="text-align:right">
          <a class="btn ghost sm" href="messages.php?view=<?= (int)$m['id'] ?><?= $filter ? '&filter=' . urlencode($filter) : '' ?>">Aç</a>
        </td>
      </tr>
    <?php endforeach; if (!$rows): ?>
      <tr><td colspan="7" class="muted" style="text-align:center;padding:30px">Kayıtlı mesaj veya başvuru bulunmamaktadır.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>

  <?php $pages = (int)ceil($total / $per); if ($pages > 1): ?>
    <div class="row" style="margin-top:14px"><?php for ($i = 1; $i <= $pages; $i++): ?>
      <a class="btn sm <?= $i === $page ? '' : 'ghost' ?>" href="messages.php?p=<?= $i ?><?= $filter ? '&filter=' . urlencode($filter) : '' ?>"><?= $i ?></a><?php endfor; ?></div>
  <?php endif; ?>
</div>
<?php admin_footer();
