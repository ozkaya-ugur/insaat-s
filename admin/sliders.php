<?php
require_once __DIR__ . '/_layout.php';
require_login();
$IMG = ['jpg','jpeg','png','webp','mp4','webm'];
$isVideo = fn($p) => (bool)preg_match('/\.(mp4|webm)$/i', (string)$p);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $act = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    try {
        if ($act === 'delete' && $id) {
            $s = q('SELECT image_url FROM sliders WHERE id=?', [$id])->fetch();
            if ($s) delete_upload($s['image_url']);
            q('DELETE FROM sliders WHERE id=?', [$id]);
            flash('Slayt silindi.');
        } elseif ($act === 'save') {
            $f = fn($k) => trim((string)($_POST[$k] ?? ''));
            if ($f('title_tr') === '') throw new RuntimeException('Türkçe başlık zorunludur.');
            $old = $id ? q('SELECT image_url FROM sliders WHERE id=?', [$id])->fetch() : null;
            $img = $old['image_url'] ?? '';
            if ($new = upload_file('image', $IMG, 'sliders')) { if ($img) delete_upload($img); $img = $new; }
            elseif ($f('image_path') !== '') $img = $f('image_path');
            if ($img === '') throw new RuntimeException('Görsel/video yükleyin veya dosya yolu girin.');
            $mediaType = $isVideo($img) ? 'video' : 'image';
            $link = $f('link_url') ?: 'projeler.html';
            if (preg_match('#^\s*(javascript|data):#i', $link)) throw new RuntimeException('Geçersiz bağlantı.');
            $vals = [$f('title_tr'), $f('title_en'), $f('title_ar'), $f('subtitle_tr'), $f('subtitle_en'), $f('subtitle_ar'),
                     $img, $mediaType, $link, (int)$f('sort_order'), !empty($_POST['is_active']) ? 1 : 0];
            if ($id) {
                q('UPDATE sliders SET title_tr=?,title_en=?,title_ar=?,subtitle_tr=?,subtitle_en=?,subtitle_ar=?,image_url=?,media_type=?,link_url=?,sort_order=?,is_active=? WHERE id=?', [...$vals, $id]);
            } else {
                q('INSERT INTO sliders (title_tr,title_en,title_ar,subtitle_tr,subtitle_en,subtitle_ar,image_url,media_type,link_url,sort_order,is_active) VALUES (?,?,?,?,?,?,?,?,?,?,?)', $vals);
            }
            flash('Slayt kaydedildi.');
        }
    } catch (Throwable $ex) { flash($ex->getMessage(), 'err'); }
    redirect('sliders.php');
}

admin_header('Ana Sayfa Slider', 'sliders');
$edit = isset($_GET['edit']) ? q('SELECT * FROM sliders WHERE id=?', [(int)$_GET['edit']])->fetch() : null;
$show = $edit || isset($_GET['new']);
$rows = q('SELECT * FROM sliders ORDER BY sort_order, id')->fetchAll();
$prev = fn($p) => strpos($p, 'uploads/') === 0 ? '../' . $p : rtrim(SITE_URL, '/') . '/' . $p;
$s = fn($k, $d = '') => e($edit[$k] ?? $d);
?>
<?php if ($show): ?>
<form class="card" method="post" enctype="multipart/form-data">
  <h2><?= $edit ? 'Slaytı Düzenle' : 'Yeni Slayt' ?></h2>
  <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
  <?= lang_tabs('sl') ?>
  <?php foreach (['tr', 'en', 'ar'] as $l): ?>
  <div class="lpane <?= $l === 'tr' ? 'on' : '' ?> <?= $l === 'ar' ? 'rtl' : '' ?>" data-pane="<?= $l ?>">
    <div class="field"><label>Başlık</label><input type="text" name="title_<?= $l ?>" value="<?= $s("title_$l") ?>"></div>
    <div class="field"><label>Alt Metin</label><input type="text" name="subtitle_<?= $l ?>" value="<?= $s("subtitle_$l") ?>"></div>
  </div>
  <?php endforeach; ?>
  <div class="grid g2">
    <div>
      <?php if ($edit): ?><?php if ($isVideo($edit['image_url'])): ?><video class="thumb" style="width:200px;height:110px;object-fit:cover" src="<?= e($prev($edit['image_url'])) ?>" muted playsinline controls></video><?php else: ?><img class="thumb" style="width:200px;height:110px" src="<?= e($prev($edit['image_url'])) ?>" alt=""><?php endif; ?><?php endif; ?>
      <div class="field"><label>Görsel veya Video Yükle</label><input type="file" name="image" accept="image/jpeg,image/png,image/webp,video/mp4,video/webm"><div class="hint">JPG, PNG, WEBP veya MP4/WEBM video (en fazla <?= MAX_UPLOAD_MB ?> MB). Video slaytlar sessiz ve döngüde otomatik oynar.</div></div>
      <div class="field"><label>veya dosya yolu</label><input type="text" name="image_path" placeholder="images/banner-1.webp veya uploads/sliders/video.mp4"></div>
    </div>
    <div>
      <div class="field"><label>Buton Bağlantısı</label><input type="text" name="link_url" value="<?= $s('link_url', 'projeler.html') ?>"><div class="hint">Boş bırakılırsa projeler.html. Örn: proje-detay.html?id=intense</div></div>
      <div class="field"><label>Sıra</label><input type="number" name="sort_order" value="<?= $s('sort_order', 0) ?>"></div>
      <label class="chk"><input type="checkbox" name="is_active" value="1" <?= ($edit['is_active'] ?? 1) ? 'checked' : '' ?>><span>Yayında</span></label>
    </div>
  </div>
  <div class="row"><button class="btn" name="action" value="save">Kaydet</button><a class="btn ghost" href="sliders.php">Vazgeç</a></div>
</form>
<script>
document.querySelectorAll('.ltabs').forEach(t=>t.querySelectorAll('button').forEach(b=>b.onclick=()=>{
  t.querySelectorAll('button').forEach(x=>x.classList.toggle('on',x===b));
  t.parentElement.querySelectorAll('.lpane').forEach(p=>p.classList.toggle('on',p.dataset.pane===b.dataset.l));
}));
</script>
<?php endif; ?>

<div class="card">
  <div class="row between" style="margin-bottom:14px"><h2 style="margin:0">Slaytlar</h2><a class="btn" href="sliders.php?new=1"><i class="fa-solid fa-plus"></i> Yeni Slayt</a></div>
  <table class="resp"><thead><tr><th></th><th>Başlık</th><th>Bağlantı</th><th>Sıra</th><th>Durum</th><th></th></tr></thead><tbody>
  <?php foreach ($rows as $r): ?>
    <tr>
      <td><?php if ($isVideo($r['image_url'])): ?><video class="thumb" src="<?= e($prev($r['image_url'])) ?>" muted playsinline></video><br><span class="badge">Video</span><?php else: ?><img class="thumb" src="<?= e($prev($r['image_url'])) ?>" alt=""><?php endif; ?></td>
      <td><strong><?= e($r['title_tr']) ?></strong><br><span class="muted"><?= e($r['subtitle_tr']) ?></span></td>
      <td class="muted"><?= e($r['link_url']) ?></td><td><?= (int)$r['sort_order'] ?></td>
      <td><span class="badge <?= $r['is_active'] ? 'b-tamamlanan' : 'b-off' ?>"><?= $r['is_active'] ? 'Yayında' : 'Gizli' ?></span></td>
      <td class="row" style="justify-content:flex-end">
        <a class="btn ghost sm" href="sliders.php?edit=<?= (int)$r['id'] ?>">Düzenle</a>
        <form method="post" data-confirm="Slayt silinsin mi?"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn danger sm" name="action" value="delete">Sil</button></form>
      </td>
    </tr>
  <?php endforeach; if (!$rows): ?><tr><td colspan="6" class="muted">Slayt yok.</td></tr><?php endif; ?>
  </tbody></table>
</div>
<?php admin_footer();
