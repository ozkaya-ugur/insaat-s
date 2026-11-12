<?php
require_once __DIR__ . '/_layout.php';
require_perm('sliders', 'view');
admin_readonly(!admin_can('sliders', 'edit'));
$IMG = ['jpg','jpeg','png','webp','mp4','webm'];
$isVideo = fn($p) => (bool)preg_match('/\.(mp4|webm)$/i', (string)$p);

// Yüklü medya kütüphanesi (JSON)
if (isset($_GET['media'])) {
    header('Content-Type: application/json; charset=utf-8');
    $type = in_array($_GET['type'] ?? 'all', ['all', 'image', 'video'], true) ? $_GET['type'] : 'all';
    $all = media_library(mb_substr(trim((string)($_GET['q'] ?? '')), 0, 80), $type);
    $per = 48; $page = max(1, (int)($_GET['p'] ?? 1));
    $prefix = fn($p) => strpos($p, 'uploads/') === 0 ? '../' . $p : rtrim(SITE_URL, '/') . '/' . $p;
    $items = array_map(fn($i) => $i + ['url' => $prefix($i['path'])], array_slice($all, ($page - 1) * $per, $per));
    echo json_encode(['total' => count($all), 'page' => $page, 'pages' => max(1, (int)ceil(count($all) / $per)), 'items' => $items], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (!admin_can('sliders', 'edit')) admin_deny();
    $act = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    try {
        if ($act === 'delete' && $id) {
            $s = q('SELECT image_url FROM sliders WHERE id=?', [$id])->fetch();
            if ($s && strpos($s['image_url'], 'uploads/sliders/') === 0 && !upload_in_use($s['image_url'], $id)) delete_upload($s['image_url']);
            q('DELETE FROM sliders WHERE id=?', [$id]);
            flash('Slayt silindi.');
        } elseif ($act === 'save') {
            $f = fn($k) => trim((string)($_POST[$k] ?? ''));
            if ($f('title_tr') === '') throw new RuntimeException('Türkçe başlık zorunludur.');
            $old = $id ? q('SELECT image_url FROM sliders WHERE id=?', [$id])->fetch() : null;
            $img = $old['image_url'] ?? '';
            $oldImg = $img;
            if ($new = upload_file('image', $IMG, 'sliders')) { $img = $new; }
            elseif ($f('image_path') !== '') {
                $picked = ltrim($f('image_path'), '/');
                if (!media_path_valid($picked, $IMG)) throw new RuntimeException('Seçilen dosya bulunamadı veya izin verilmeyen bir yol.');
                $img = $picked;
            }
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
            // Eski dosya yalnızca slider klasöründeyse ve başka yerde kullanılmıyorsa silinir
            if ($oldImg && $oldImg !== $img && strpos($oldImg, 'uploads/sliders/') === 0 && !upload_in_use($oldImg, $id)) delete_upload($oldImg);
            flash('Slayt kaydedildi.');
        }
    } catch (Throwable $ex) { flash($ex->getMessage(), 'err'); }
    redirect('sliders.php');
}

admin_header('Ana Sayfa Slider', 'sliders');
$edit = isset($_GET['edit']) ? q('SELECT * FROM sliders WHERE id=?', [(int)$_GET['edit']])->fetch() : null;
$show = $edit || isset($_GET['new']);
$rows = q('SELECT * FROM sliders ORDER BY sort_order, id')->fetchAll();
$allProjects = q('SELECT id, slug, title_tr, is_active FROM projects ORDER BY is_active DESC, title_tr')->fetchAll();
$curLink = (string)($edit['link_url'] ?? 'projeler.html');
$curProjectLink = '';
foreach ($allProjects as $p) {
    if ($curLink === 'proje-detay.html?slug=' . $p['slug'] || $curLink === 'proje-detay.html?id=' . $p['slug']) { $curProjectLink = 'proje-detay.html?slug=' . $p['slug']; break; }
}
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
      <div class="field"><label>veya yüklü dosyalardan seç</label>
        <div class="row" style="gap:8px;flex-wrap:nowrap">
          <input type="text" name="image_path" id="imagePath" placeholder="images/banner-1.webp veya uploads/sliders/video.mp4" style="flex:1">
          <button type="button" class="btn ghost" id="openMediaLib" style="white-space:nowrap"><i class="fa-solid fa-photo-film"></i> Kütüphaneden Seç</button>
        </div>
        <div id="pickedPreview" style="margin-top:8px;display:none"></div>
        <div class="hint">Sunucuya daha önce yüklenmiş görsel ve videolardan seçebilirsiniz. Dosya yüklerseniz seçim yerine yüklenen dosya kullanılır.</div>
      </div>
    </div>
    <div>
      <div class="field"><label>Buton Bağlantısı</label><input type="text" name="link_url" id="linkUrl" value="<?= $s('link_url', 'projeler.html') ?>"><div class="hint">Boş bırakılırsa projeler.html. Örn: proje-detay.html?slug=intense</div></div>
      <div class="field"><label>veya Projeden Seç</label>
        <select id="projectPick">
          <option value="">— Proje seçin —</option>
          <?php foreach ($allProjects as $p): ?>
            <option value="proje-detay.html?slug=<?= e($p['slug']) ?>" <?= $curProjectLink === 'proje-detay.html?slug=' . $p['slug'] ? 'selected' : '' ?>><?= e($p['title_tr']) ?><?= $p['is_active'] ? '' : ' (pasif)' ?></option>
          <?php endforeach; ?>
        </select>
        <div class="hint"><?= count($allProjects) ?> proje listeleniyor. Seçim yapınca bağlantı alanı otomatik dolar.</div>
      </div>
      <div class="field"><label>Sıra</label><input type="number" name="sort_order" value="<?= $s('sort_order', 0) ?>"></div>
      <label class="chk"><input type="checkbox" name="is_active" value="1" <?= ($edit['is_active'] ?? 1) ? 'checked' : '' ?>><span>Yayında</span></label>
    </div>
  </div>
  <div class="row"><button class="btn" name="action" value="save">Kaydet</button><a class="btn ghost" href="sliders.php">Vazgeç</a></div>
</form>
<style>
#mediaLib{position:fixed;inset:0;background:rgba(0,0,0,.72);z-index:1000;display:none;align-items:center;justify-content:center;padding:20px}
#mediaLib.on{display:flex}
#mediaLib .ml-box{background:var(--panel,#16181d);border:1px solid var(--line,#2a2d34);border-radius:10px;width:min(1100px,100%);max-height:90vh;display:flex;flex-direction:column;overflow:hidden}
#mediaLib .ml-head,#mediaLib .ml-foot{padding:14px 16px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;border-bottom:1px solid var(--line,#2a2d34)}
#mediaLib .ml-foot{border-bottom:0;border-top:1px solid var(--line,#2a2d34);justify-content:space-between}
#mediaLib .ml-head input{flex:1;min-width:180px}
#mediaLib .ml-head select{width:auto}
#mlGrid{padding:14px;overflow:auto;display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));grid-auto-rows:max-content;align-content:start;gap:12px;flex:1;min-height:0}
#mlGrid .ml-item{position:relative;background:var(--panel2,#1d2026);border:2px solid transparent;border-radius:8px;padding:0;cursor:pointer;text-align:left;color:var(--text,#eee);overflow:hidden;font:inherit}
#mlGrid .ml-item:hover{border-color:var(--gold,#c5a059)}
#mlGrid .ml-item img,#mlGrid .ml-item video{width:100%;height:100px;object-fit:cover;display:block;background:#000}
#mlGrid .ml-name{display:block;font-size:.75rem;padding:6px 8px 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
#mlGrid .ml-size{display:block;font-size:.7rem;padding:0 8px 7px;color:var(--dim,#999)}
#mlGrid .ml-badge{position:absolute;top:6px;left:6px;background:var(--gold,#c5a059);color:#000;font-size:.65rem;font-weight:700;padding:2px 6px;border-radius:4px}
</style>
<div id="mediaLib" role="dialog" aria-modal="true" aria-label="Medya kütüphanesi">
  <div class="ml-box">
    <div class="ml-head">
      <strong style="margin-right:6px"><i class="fa-solid fa-photo-film" style="color:var(--gold)"></i> Yüklü Dosyalar</strong>
      <input type="text" id="mlSearch" placeholder="Dosya adı veya klasörde ara (örn: intense, sliders)">
      <select id="mlType"><option value="image">Görseller</option><option value="video">Videolar</option><option value="all">Tümü</option></select>
      <button type="button" class="btn ghost sm" data-ml-close>Kapat</button>
    </div>
    <div id="mlGrid"></div>
    <div class="ml-foot">
      <span class="muted" id="mlInfo"></span>
      <div class="row" style="gap:8px"><button type="button" class="btn ghost sm" id="mlPrev">‹ Önceki</button><button type="button" class="btn ghost sm" id="mlNext">Sonraki ›</button></div>
    </div>
  </div>
</div>
<script>
document.querySelectorAll('.ltabs').forEach(t=>t.querySelectorAll('button').forEach(b=>b.onclick=()=>{
  t.querySelectorAll('button').forEach(x=>x.classList.toggle('on',x===b));
  t.parentElement.querySelectorAll('.lpane').forEach(p=>p.classList.toggle('on',p.dataset.pane===b.dataset.l));
}));
(function(){
  const modal = document.getElementById('mediaLib');
  const grid = document.getElementById('mlGrid');
  const info = document.getElementById('mlInfo');
  const search = document.getElementById('mlSearch');
  const type = document.getElementById('mlType');
  const prevBtn = document.getElementById('mlPrev');
  const nextBtn = document.getElementById('mlNext');
  const input = document.getElementById('imagePath');
  const preview = document.getElementById('pickedPreview');
  if (!modal || !input) return;
  let page = 1, pages = 1, timer = null;
  const esc = s => String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const kb = n => n > 1048576 ? (n / 1048576).toFixed(1) + ' MB' : Math.max(1, Math.round(n / 1024)) + ' KB';
  const showPreview = (path, url, isVid) => {
    preview.style.display = 'block';
    preview.innerHTML = (isVid ? `<video src="${esc(url)}" muted playsinline style="width:200px;height:110px;object-fit:cover;border-radius:6px"></video>`
      : `<img src="${esc(url)}" alt="" style="width:200px;height:110px;object-fit:cover;border-radius:6px">`) + `<div class="hint">Seçildi: ${esc(path)}</div>`;
  };
  async function load() {
    grid.innerHTML = '<div class="muted" style="padding:20px">Yükleniyor...</div>';
    try {
      const r = await fetch(`sliders.php?media=1&p=${page}&type=${encodeURIComponent(type.value)}&q=${encodeURIComponent(search.value.trim())}`, {credentials: 'same-origin'});
      const d = await r.json();
      pages = d.pages; page = d.page;
      info.textContent = `${d.total} dosya · Sayfa ${d.page}/${d.pages}`;
      prevBtn.disabled = page <= 1; nextBtn.disabled = page >= pages;
      grid.innerHTML = d.items.length ? d.items.map(i => `
        <button type="button" class="ml-item" data-path="${esc(i.path)}" data-url="${esc(i.url)}" data-video="${i.type === 'video' ? 1 : 0}" title="${esc(i.path)}">
          ${i.type === 'video' ? `<video src="${esc(i.url)}" muted preload="metadata"></video><span class="ml-badge">Video</span>` : `<img src="${esc(i.url)}" loading="lazy" alt="">`}
          <span class="ml-name">${esc(i.path.split('/').pop())}</span><span class="ml-size">${kb(i.size)}</span>
        </button>`).join('') : '<div class="muted" style="padding:20px">Dosya bulunamadı.</div>';
    } catch (e) { grid.innerHTML = '<div class="muted" style="padding:20px">Kütüphane yüklenemedi.</div>'; }
  }
  document.getElementById('openMediaLib').addEventListener('click', () => { modal.classList.add('on'); load(); });
  modal.addEventListener('click', e => { if (e.target === modal || e.target.closest('[data-ml-close]')) modal.classList.remove('on'); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') modal.classList.remove('on'); });
  search.addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(() => { page = 1; load(); }, 300); });
  type.addEventListener('change', () => { page = 1; load(); });
  prevBtn.addEventListener('click', () => { if (page > 1) { page--; load(); } });
  nextBtn.addEventListener('click', () => { if (page < pages) { page++; load(); } });
  grid.addEventListener('click', e => {
    const it = e.target.closest('.ml-item'); if (!it) return;
    input.value = it.dataset.path;
    const fileInput = document.querySelector('input[name="image"]'); if (fileInput) fileInput.value = '';
    showPreview(it.dataset.path, it.dataset.url, it.dataset.video === '1');
    modal.classList.remove('on');
  });
})();
(function(){
  const pick = document.getElementById('projectPick');
  const link = document.getElementById('linkUrl');
  if (!pick || !link) return;
  pick.addEventListener('change', () => {
    if (!pick.value) return;
    link.value = pick.value;
    link.focus();
  });
})();
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
