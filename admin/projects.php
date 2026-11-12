<?php
require_once __DIR__ . '/_layout.php';
require_perm('projects', 'view');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);
    $act = $_POST['action'] ?? '';

    if ($act === 'reorder') {
        if (!admin_can('projects', 'edit')) admin_deny();
        header('Content-Type: application/json; charset=utf-8');
        $raw = trim((string)($_POST['order'] ?? ''));
        $ids = array_values(array_filter(array_map('intval', explode(',', $raw)), fn($v) => $v > 0));
        if (!$ids) {
            echo json_encode(['ok' => false, 'message' => 'Geçerli sıralama listesi gönderilmedi.'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        $all = array_map('intval', array_column(q('SELECT id FROM projects ORDER BY sort_order ASC, id DESC')->fetchAll(), 'id'));
        if (count($ids) !== count($all) || array_diff($ids, $all) || array_diff($all, $ids)) {
            echo json_encode(['ok' => false, 'message' => 'Sıralama verisi eksik veya geçersiz.'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        try {
            db()->beginTransaction();
            $st = db()->prepare('UPDATE projects SET sort_order=? WHERE id=?');
            $i = 1;
            foreach ($ids as $pid) {
                $st->execute([$i++, $pid]);
            }
            db()->commit();
            echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE);
        } catch (Throwable $e) {
            if (db()->inTransaction()) db()->rollBack();
            echo json_encode(['ok' => false, 'message' => 'Sıralama kaydedilemedi.'], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    if ($act === 'delete' && $id) {
        if (!admin_can('projects', 'delete')) admin_deny();
        $p = q('SELECT cover_image, catalog_file FROM projects WHERE id=?', [$id])->fetch();
        foreach (q('SELECT file_path FROM project_media WHERE project_id=?', [$id])->fetchAll() as $m) delete_upload($m['file_path']);
        if ($p) { delete_upload($p['cover_image']); delete_upload($p['catalog_file']); }
        q('DELETE FROM projects WHERE id=?', [$id]);
        flash('Proje silindi.');
    } elseif ($act === 'toggle' && $id) {
        if (!admin_can('projects', 'edit')) admin_deny();
        q('UPDATE projects SET is_active = 1 - is_active WHERE id=?', [$id]);
        flash('Yayın durumu değiştirildi.');
    } elseif ($act === 'feature' && $id) {
        if (!admin_can('projects', 'edit')) admin_deny();
        q('UPDATE projects SET is_featured = 1 - is_featured WHERE id=?', [$id]);
        flash('Ana sayfa vitrini güncellendi.');
    } elseif ($act === 'new' && $id) {
        if (!admin_can('projects', 'edit')) admin_deny();
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
    <?php if (admin_can('projects', 'create')): ?><a class="btn" href="project_edit.php"><i class="fa-solid fa-plus"></i> Yeni Proje</a><?php endif; ?>
  </div>
  <div class="table-wrap">
    <table class="resp projects-table" id="projectsTable">
      <thead><tr><th style="width:36px"></th><th></th><th>Proje</th><th>Lokasyon</th><th>Durum</th><th>Sıra</th><th>Yayın</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($rows as $idx => $r): ?>
        <tr data-id="<?= (int)$r['id'] ?>" draggable="true">
          <td>
            <button type="button" class="drag-handle" aria-label="Sırayı değiştir" title="Sürükle bırak ile sırala"><i class="fa-solid fa-grip-vertical"></i></button>
          </td>
          <td><?php if ($r['cover_image']): ?><img class="thumb" src="<?= e(strpos($r['cover_image'], 'uploads/') === 0 ? '../' . $r['cover_image'] : rtrim(SITE_URL, '/') . '/' . $r['cover_image']) ?>" alt=""><?php endif; ?></td>
          <td><strong><?= e($r['title_tr']) ?></strong><br><span class="muted"><?= e($r['slug']) ?></span></td>
          <td><?= e($r['loc']) ?></td>
          <td>
            <span class="badge b-<?= e($r['status']) ?>"><?= e(PROJECT_STATUS[$r['status']] ?? $r['status']) ?></span>
            <?php if (!empty($r['is_new'])): ?><span class="badge b-devam-eden" title="Yeni Proje">Yeni</span><?php endif; ?>
            <?php if (!empty($r['is_urban_renewal'])): ?><span class="badge b-tamamlanan" title="Kentsel Dönüşüm">Kentsel</span><?php endif; ?>
          </td>
          <td class="row-order"><?= $idx + 1 ?></td>
          <td>
            <form method="post" style="display:inline"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <button name="action" value="toggle" class="badge <?= $r['is_active'] ? 'b-tamamlanan' : 'b-off' ?>" style="cursor:pointer;background:none"><?= $r['is_active'] ? 'Yayında' : 'Gizli' ?></button>
              <button name="action" value="new" class="badge <?= !empty($r['is_new']) ? 'b-devam-eden' : 'b-off' ?>" style="cursor:pointer;background:none" title="Yeni Proje olarak işaretle"><?= !empty($r['is_new']) ? 'Yeni' : 'Yeni değil' ?></button>
              <button name="action" value="feature" class="badge <?= $r['is_featured'] ? 'b-devam-eden' : 'b-off' ?>" style="cursor:pointer;background:none" title="Ana sayfa vitrini"><?= $r['is_featured'] ? '★ Vitrin' : '☆ Vitrin' ?></button>
            </form>
          </td>
          <td>
            <div class="row table-actions" style="justify-content:flex-end">
              <?php if (admin_can('projects', 'edit')): ?><a class="btn ghost sm" href="project_edit.php?id=<?= (int)$r['id'] ?>">Düzenle</a><?php endif; ?>
              <?php if (admin_can('projects', 'delete')): ?><form method="post" data-confirm="Bu proje ve tüm görselleri kalıcı olarak silinsin mi?"><?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button name="action" value="delete" class="btn danger sm">Sil</button>
              </form><?php endif; ?>
            </div>
          </td>
        </tr>
      <?php endforeach; if (!$rows): ?><tr><td colspan="8" class="muted">Henüz proje yok.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<script>
(function(){
  const table = document.getElementById('projectsTable');
  if (!table) return;
  const tbody = table.querySelector('tbody');
  const rows = () => Array.from(tbody.querySelectorAll('tr[data-id]'));
  const token = table.closest('.card')?.querySelector('input[name="csrf"]')?.value || '';
  let dragRow = null;
  let dragMoved = false;

  function updateRowNumbers(){ rows().forEach((tr, i) => { const c = tr.querySelector('.row-order'); if (c) c.textContent = String(i+1); }); }

  function showWarn(msg){ window.alert(msg || 'Sıralama kaydedilemedi.'); }

  async function saveOrder(){
    const order = rows().map(tr => tr.dataset.id).join(',');
    const fd = new FormData();
    fd.append('csrf', token);
    fd.append('action', 'reorder');
    fd.append('order', order);
    try {
      const r = await fetch('projects.php', { method:'POST', body:fd, credentials:'same-origin' });
      const d = await r.json();
      if (!d || !d.ok) throw new Error((d && d.message) || 'Sıralama kaydedilemedi.');
    } catch (e) {
      showWarn(e.message || 'Sıralama kaydedilemedi.');
      window.location.reload();
    }
  }

  tbody.addEventListener('dragstart', (e) => {
    const tr = e.target.closest('tr[data-id]');
    if (!tr || !e.target.closest('.drag-handle')) { e.preventDefault(); return; }
    dragRow = tr;
    dragMoved = false;
    tr.classList.add('dragging');
    if (e.dataTransfer) {
      e.dataTransfer.effectAllowed = 'move';
      e.dataTransfer.setData('text/plain', tr.dataset.id || '');
    }
  });

  tbody.addEventListener('dragover', (e) => {
    if (!dragRow) return;
    e.preventDefault();
    const tr = e.target.closest('tr[data-id]');
    if (!tr || tr === dragRow) return;
    const rect = tr.getBoundingClientRect();
    const before = e.clientY < rect.top + rect.height / 2;
    if (before) tbody.insertBefore(dragRow, tr);
    else tbody.insertBefore(dragRow, tr.nextSibling);
    dragMoved = true;
  });

  tbody.addEventListener('dragend', async () => {
    if (!dragRow) return;
    dragRow.classList.remove('dragging');
    dragRow = null;
    if (!dragMoved) return;
    updateRowNumbers();
    await saveOrder();
  });

  // Klavye ile sıralama
  tbody.querySelectorAll('.drag-handle').forEach((btn) => {
    btn.setAttribute('tabindex', '0');
    btn.addEventListener('keydown', async (e) => {
      const tr = btn.closest('tr[data-id]');
      if (!tr) return;
      if (e.key !== 'ArrowUp' && e.key !== 'ArrowDown') return;
      e.preventDefault();
      const sib = e.key === 'ArrowUp' ? tr.previousElementSibling : tr.nextElementSibling;
      if (!sib || !sib.matches('tr[data-id]')) return;
      if (e.key === 'ArrowUp') tbody.insertBefore(tr, sib);
      else tbody.insertBefore(sib, tr);
      updateRowNumbers();
      await saveOrder();
      btn.focus();
    });
  });
})();
</script>
<?php admin_footer();
