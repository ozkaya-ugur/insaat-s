<?php
require_once __DIR__ . '/_layout.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);
    $act = $_POST['action'] ?? '';
    if ($act === 'delete' && $id) {
        $p = q('SELECT cover_image, catalog_file FROM projects WHERE id=?', [$id])->fetch();
        foreach (q('SELECT file_path FROM project_media WHERE project_id=?', [$id])->fetchAll() as $m) delete_upload($m['file_path']);
        if ($p) { delete_upload($p['cover_image']); delete_upload($p['catalog_file']); }
        q('DELETE FROM projects WHERE id=?', [$id]);
        flash('Proje silindi.');
    } elseif ($act === 'toggle' && $id) {
        q('UPDATE projects SET is_active = 1 - is_active WHERE id=?', [$id]);
        flash('Yayın durumu değiştirildi.');
    } elseif ($act === 'feature' && $id) {
        q('UPDATE projects SET is_featured = 1 - is_featured WHERE id=?', [$id]);
        flash('Ana sayfa vitrini güncellendi.');
    } elseif ($act === 'new' && $id) {
        q('UPDATE projects SET is_new = 1 - is_new WHERE id=?', [$id]);
        flash('Yeni proje durumu değiştirildi.');
    }
    redirect('projects.php');
}

admin_header('Projeler', 'projects');
$rows = q('SELECT * FROM projects ORDER BY sort_order ASC, id DESC')->fetchAll();
?>
<div class="card">
  <div class="row between" style="margin-bottom:14px">
    <h2 style="margin:0">Tüm Projeler (<?= count($rows) ?>)</h2>
    <a class="btn" href="project_edit.php"><i class="fa-solid fa-plus"></i> Yeni Proje</a>
  </div>
  <table class="resp">
    <thead><tr><th></th><th>Proje</th><th>Lokasyon</th><th>Durum</th><th>Sıra</th><th>Yayın</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><?php if ($r['cover_image']): ?><img class="thumb" src="<?= e(strpos($r['cover_image'], 'uploads/') === 0 ? '../' . $r['cover_image'] : rtrim(SITE_URL, '/') . '/' . $r['cover_image']) ?>" alt=""><?php endif; ?></td>
        <td><strong><?= e($r['title_tr']) ?></strong><br><span class="muted"><?= e($r['slug']) ?></span></td>
        <td><?= e($r['loc']) ?></td>
        <td>
          <span class="badge b-<?= e($r['status']) ?>"><?= e(PROJECT_STATUS[$r['status']] ?? $r['status']) ?></span>
          <?php if (!empty($r['is_new'])): ?><span class="badge b-devam-eden" title="Yeni Proje">Yeni</span><?php endif; ?>
          <?php if (!empty($r['is_urban_renewal'])): ?><span class="badge b-tamamlanan" title="Kentsel Dönüşüm">Kentsel</span><?php endif; ?>
        </td>
        <td><?= (int)$r['sort_order'] ?></td>
        <td>
          <form method="post" style="display:inline"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
            <button name="action" value="toggle" class="badge <?= $r['is_active'] ? 'b-tamamlanan' : 'b-off' ?>" style="cursor:pointer;background:none"><?= $r['is_active'] ? 'Yayında' : 'Gizli' ?></button>
            <button name="action" value="new" class="badge <?= !empty($r['is_new']) ? 'b-devam-eden' : 'b-off' ?>" style="cursor:pointer;background:none" title="Yeni Proje olarak işaretle"><?= !empty($r['is_new']) ? 'Yeni' : 'Yeni değil' ?></button>
            <button name="action" value="feature" class="badge <?= $r['is_featured'] ? 'b-devam-eden' : 'b-off' ?>" style="cursor:pointer;background:none" title="Ana sayfa vitrini"><?= $r['is_featured'] ? '★ Vitrin' : '☆ Vitrin' ?></button>
          </form>
        </td>
        <td class="row" style="justify-content:flex-end">
          <a class="btn ghost sm" href="project_edit.php?id=<?= (int)$r['id'] ?>">Düzenle</a>
          <form method="post" data-confirm="Bu proje ve tüm görselleri kalıcı olarak silinsin mi?"><?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
            <button name="action" value="delete" class="btn danger sm">Sil</button>
          </form>
        </td>
      </tr>
    <?php endforeach; if (!$rows): ?><tr><td colspan="7" class="muted">Henüz proje yok.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php admin_footer();
