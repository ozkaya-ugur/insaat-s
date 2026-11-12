<?php
require_once __DIR__ . '/_layout.php';
require_login();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$p = $id ? q('SELECT * FROM projects WHERE id=?', [$id])->fetch() : null;
if ($id && !$p) { flash('Proje bulunamadı.', 'err'); redirect('projects.php'); }

$IMG = ['jpg','jpeg','png','webp','gif'];
$KINDS = [
    'hero'   => 'Arka Plan Slider Görselleri (Hero)',
    'render' => 'Proje Görselleri (Render)',
    'plan'   => 'Kat Planları',
    'site'   => 'Şantiye Görselleri',
    'video'  => 'Tanıtım Videoları',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $act = $_POST['action'] ?? 'save';
    try {
        // ---- Medya yükle ----
        if ($act === 'media_add' && $p) {
            $kind = $_POST['kind'] ?? '';
            if (!isset($KINDS[$kind])) throw new RuntimeException('Geçersiz tür.');
            $ext = $kind === 'video' ? ['mp4','webm'] : $IMG;
            $cap = trim($_POST['caption'] ?? '');
            $max = (int)q('SELECT COALESCE(MAX(sort_order),0) FROM project_media WHERE project_id=? AND kind=?', [$id, $kind])->fetchColumn();
            $n = upload_multi('files', $ext, 'projects/' . $id, function ($path, $orig) use ($id, $kind, $cap, &$max) {
                q('INSERT INTO project_media (project_id,kind,file_path,caption,sort_order) VALUES (?,?,?,?,?)',
                    [$id, $kind, $path, $cap !== '' ? $cap : pathinfo($orig, PATHINFO_FILENAME), ++$max]);
            });
            flash($n ? "$n dosya yüklendi." : 'Dosya seçilmedi.', $n ? 'ok' : 'err');
            redirect("project_edit.php?id=$id#media-$kind");
        }
        // ---- Medya sil ----
        if ($act === 'media_del' && $p) {
            $m = q('SELECT * FROM project_media WHERE id=? AND project_id=?', [(int)$_POST['mid'], $id])->fetch();
            if ($m) { delete_upload($m['file_path']); q('DELETE FROM project_media WHERE id=?', [$m['id']]); flash('Dosya silindi.'); }
            redirect("project_edit.php?id=$id#media");
        }

        // ---- Kaydet ----
        $f = fn($k, $d = '') => trim((string)($_POST[$k] ?? $d));
        $title = $f('title_tr');
        if ($title === '') throw new RuntimeException('Türkçe başlık zorunludur.');

        $slug = slugify($f('slug') ?: $title);
        $dup = q('SELECT id FROM projects WHERE slug=? AND id<>?', [$slug, $id])->fetch();
        if ($dup) $slug .= '-' . substr(bin2hex(random_bytes(2)), 0, 4);

        $cityKey = $f('city_key', 'tr-sk');
        $types = array_values(array_intersect(array_keys(PROJECT_TYPES), (array)($_POST['type'] ?? [])));
        $status = array_key_exists($f('status'), PROJECT_STATUS) ? $f('status') : 'devam-eden';

        $cover = $p['cover_image'] ?? '';
        if ($new = upload_file('cover', $IMG, 'covers')) { if ($cover) delete_upload($cover); $cover = $new; }
        elseif ($f('cover_path') !== '') $cover = $f('cover_path');
        if (!empty($_POST['cover_remove'])) { delete_upload($cover); $cover = ''; }

        $catalog = $p['catalog_file'] ?? '';
        if ($new = upload_file('catalog', ['pdf'], 'catalogs')) { if ($catalog) delete_upload($catalog); $catalog = $new; }
        if (!empty($_POST['catalog_remove'])) { delete_upload($catalog); $catalog = ''; }

        $googleMapCode = $f('google_map_code');
        $streetViewCode = $f('street_view_code');

        $data = [
            $slug, $title, $f('title_en'), $f('title_ar'), $f('title_de'),
            $f('subtitle_tr'), $f('subtitle_en'), $f('subtitle_ar'), $f('subtitle_de'),
            $f('description_tr'), $f('description_en'), $f('description_ar'), $f('description_de'),
            $f('cat'), $f('loc'), $cityKey, implode(',', $types), $status,
            $cover, $f('area'), $f('concept'), $f('delivery'), $catalog,
            $streetViewCode, $googleMapCode,
            !empty($_POST['is_featured']) ? 1 : 0, !empty($_POST['is_active']) ? 1 : 0, (int)$f('sort_order', '0'),
            $f('seo_title'), $f('seo_desc'), $f('seo_keywords'),
        ];
        $cols = 'slug,title_tr,title_en,title_ar,title_de,subtitle_tr,subtitle_en,subtitle_ar,subtitle_de,description_tr,description_en,description_ar,description_de,cat,loc,city_key,type,status,cover_image,area,concept,delivery,catalog_file,street_view_code,google_map_code,is_featured,is_active,sort_order,seo_title,seo_desc,seo_keywords';
        if ($p) {
            $set = implode(',', array_map(fn($c) => "$c=?", explode(',', $cols)));
            q("UPDATE projects SET $set WHERE id=?", [...$data, $id]);
            flash('Proje güncellendi.');
        } else {
            $ph = implode(',', array_fill(0, count($data), '?'));
            q("INSERT INTO projects ($cols) VALUES ($ph)", $data);
            $id = (int)db()->lastInsertId();
            flash('Proje oluşturuldu. Şimdi arka plan, galeri ve harita içeriklerini ekleyebilirsiniz.');
        }
        redirect("project_edit.php?id=$id");
    } catch (Throwable $ex) {
        flash($ex->getMessage(), 'err');
        redirect('project_edit.php' . ($id ? "?id=$id" : ''));
    }
}

admin_header($p ? 'Projeyi Düzenle: ' . $p['title_tr'] : 'Yeni Proje', 'projects');
$v = fn($k, $d = '') => e($p[$k] ?? $d);
$curTypes = $p ? explode(',', $p['type']) : ['konut'];
$prev = fn($path) => strpos($path, 'uploads/') === 0 ? '../' . $path : rtrim(SITE_URL, '/') . '/' . $path;
?>
<form method="post" enctype="multipart/form-data" class="grid" style="gap:0" id="projectMainForm">
  <?= csrf_field() ?><input type="hidden" name="id" value="<?= $id ?>">

  <!-- İçerik Sekmeleri -->
  <div class="card">
    <h2><i class="fa-solid fa-pen-nib" style="color:var(--gold)"></i> Çok Dilli İçerik</h2>
    <?= lang_tabs('pt') ?>
    <?php foreach (get_languages() as $idx => $langObj): $l = $langObj['code']; ?>
    <div class="lpane <?= $idx === 0 ? 'on' : '' ?> <?= ($langObj['direction'] ?? 'ltr') === 'rtl' ? 'rtl' : '' ?>" data-pane="<?= e($l) ?>">
      <div class="field"><label>Başlık (<?= e($langObj['name']) ?>) <?= $l === 'tr' ? '*' : '' ?></label><input type="text" name="title_<?= e($l) ?>" value="<?= $v("title_$l") ?>" <?= $l === 'tr' ? 'required' : '' ?>></div>
      <div class="field"><label>Alt Başlık (<?= e($langObj['name']) ?>)</label><input type="text" name="subtitle_<?= e($l) ?>" value="<?= $v("subtitle_$l") ?>"></div>
      <div class="field"><label>Açıklama (<?= e($langObj['name']) ?>)</label><textarea name="description_<?= e($l) ?>"><?= $v("description_$l") ?></textarea></div>
    </div>
    <?php endforeach; ?>
    <p class="hint">Boş bırakılan yabancı dillerde otomatik olarak Türkçe içerik gösterilir.</p>
  </div>

  <!-- Temel Bilgiler -->
  <div class="card">
    <h2><i class="fa-solid fa-circle-info" style="color:var(--gold)"></i> Proje Bilgileri</h2>
    <div class="grid g3">
      <div class="field"><label>URL Kısa Adı (slug)</label><input type="text" name="slug" value="<?= $v('slug') ?>" placeholder="otomatik">
        <div class="hint">proje-detay.html?id=<b>slug</b></div></div>
      <div class="field"><label>Şehir Adı (cat)</label><input type="text" name="cat" value="<?= $v('cat', 'Sakarya') ?>"></div>
      <div class="field"><label>Lokasyon Etiketi (loc)</label><input type="text" name="loc" value="<?= $v('loc', 'Serdivan / Sakarya') ?>"></div>
      <div class="field"><label>Harita İl Kodu</label>
        <select name="city_key">
          <?php foreach (CITY_KEYS as $k => $n): if ($n === '—') continue; ?>
            <option value="<?= e($k) ?>" <?= ($p['city_key'] ?? 'tr-sk') === $k ? 'selected' : '' ?>><?= e($n) ?> (<?= e($k) ?>)</option>
          <?php endforeach; ?>
        </select></div>
      <div class="field"><label>Durum</label>
        <select name="status"><?php foreach (PROJECT_STATUS as $k => $n): ?>
          <option value="<?= e($k) ?>" <?= ($p['status'] ?? 'devam-eden') === $k ? 'selected' : '' ?>><?= e($n) ?></option><?php endforeach; ?></select></div>
      <div class="field"><label>Sıralama (küçük önce)</label><input type="number" name="sort_order" value="<?= $v('sort_order', 0) ?>"></div>
      <div class="field"><label>Proje Alanı</label><input type="text" name="area" value="<?= $v('area') ?>" placeholder="18.500 m²"></div>
      <div class="field"><label>Mimari Konsept</label><input type="text" name="concept" value="<?= $v('concept') ?>"></div>
      <div class="field"><label>Teslim</label><input type="text" name="delivery" value="<?= $v('delivery') ?>" placeholder="2026"></div>
    </div>
    <label>Yapı Türü (birden fazla seçilebilir)</label>
    <div class="row" style="margin-bottom:10px">
      <?php foreach (PROJECT_TYPES as $k => $n): ?>
        <label class="chk"><input type="checkbox" name="type[]" value="<?= e($k) ?>" <?= in_array($k, $curTypes, true) ? 'checked' : '' ?>><span><?= e($n) ?></span></label>
      <?php endforeach; ?>
    </div>
    <div class="row">
      <label class="chk"><input type="checkbox" name="is_active" value="1" <?= ($p['is_active'] ?? 1) ? 'checked' : '' ?>><span>Sitede yayınla</span></label>
      <label class="chk"><input type="checkbox" name="is_featured" value="1" <?= ($p['is_featured'] ?? 0) ? 'checked' : '' ?>><span>Ana sayfa vitrininde göster</span></label>
    </div>
  </div>

  <!-- Kapak Görseli & Katalog (Sürükle - Bırak Destekli) -->
  <div class="card">
    <h2><i class="fa-solid fa-file-arrow-up" style="color:var(--gold)"></i> Kapak Görseli & E-Katalog</h2>
    <div class="grid g2">
      <div>
        <label>Kapak Görseli</label>
        <?php if (!empty($p['cover_image'])): ?>
          <div style="margin-bottom:10px">
            <img class="thumb" style="width:220px;height:120px" src="<?= e($prev($p['cover_image'])) ?>" alt="">
            <label class="chk" style="margin-top:8px"><input type="checkbox" name="cover_remove" value="1"><span>Mevcut kapağı kaldır</span></label>
          </div>
        <?php endif; ?>
        <div class="dropzone" id="dz-cover">
          <i class="fa-solid fa-image"></i>
          <p>Kapak görselini sürükleyin veya <span>seçin</span></p>
          <small class="file-list-preview"></small>
          <input type="file" name="cover" accept="image/*">
        </div>
        <div class="field" style="margin-top:10px">
          <label>veya harici dosya yolu</label>
          <input type="text" name="cover_path" placeholder="images/banner-1.png" value="">
        </div>
      </div>
      <div>
        <label>E-Katalog (PDF)</label>
        <?php if (!empty($p['catalog_file'])): ?>
          <div style="margin-bottom:10px">
            <p><i class="fa-solid fa-file-pdf" style="color:#e5484d;font-size:1.2rem"></i> <a href="../<?= e($p['catalog_file']) ?>" target="_blank" style="color:var(--gold);font-weight:600"><?= e(basename($p['catalog_file'])) ?></a></p>
            <label class="chk" style="margin-top:8px"><input type="checkbox" name="catalog_remove" value="1"><span>Kataloğu kaldır</span></label>
          </div>
        <?php endif; ?>
        <div class="dropzone" id="dz-catalog">
          <i class="fa-solid fa-file-pdf"></i>
          <p>PDF Kataloğu sürükleyin veya <span>seçin</span></p>
          <small class="file-list-preview"></small>
          <input type="file" name="catalog" accept="application/pdf">
        </div>
      </div>
    </div>
  </div>

  <!-- Google Harita & Street View (Canlı Önizlemeli) -->
  <div class="card">
    <div class="row between" style="margin-bottom:12px">
      <h2 style="margin:0"><i class="fa-solid fa-map-location-dot" style="color:var(--gold)"></i> Konum & Street View (Canlı Önizlemeli)</h2>
      <span class="muted" style="font-size:0.85rem"><i class="fa-solid fa-eye"></i> Değişiklikler anında aşağıdaki önizleme pencerelerine yansır.</span>
    </div>
    
    <div class="grid g2">
      <!-- Google Harita -->
      <div>
        <div class="field">
          <label><i class="fa-solid fa-location-dot" style="color:var(--gold)"></i> Google Harita Konumu (Embed / iframe Kodu)</label>
          <textarea name="google_map_code" id="google_map_code" style="min-height:90px" placeholder='<iframe src="https://www.google.com/maps/embed?..." width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>'><?= e($p['google_map_code'] ?? '') ?></textarea>
          <div class="hint">Google Maps'ten "Harita paylaşın veya yerleştirin" > "Harita yerleştirme" kodunu yapıştırın.</div>
        </div>
        <label>Harita Canlı Önizleme</label>
        <div class="map-preview-box" id="map-preview">
          <div class="map-preview-placeholder"><i class="fa-solid fa-map"></i><span>Harita kodu girilmediğinde önizleme burada görünür.</span></div>
        </div>
      </div>

      <!-- Google Street View -->
      <div>
        <div class="field">
          <label><i class="fa-solid fa-street-view" style="color:var(--gold)"></i> Street View 360° Cadde Görünümü (Embed / iframe Kodu)</label>
          <textarea name="street_view_code" id="street_view_code" style="min-height:90px" placeholder='<iframe src="https://www.google.com/maps/embed?pb=!4v...6m8!1m7!1s..." width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>'><?= e($p['street_view_code'] ?? '') ?></textarea>
          <div class="hint">Google Maps Street View modundayken "Görüntüyü paylaşın veya yerleştirin" kodunu yapıştırın.</div>
        </div>
        <label>Street View Canlı Önizleme</label>
        <div class="map-preview-box" id="streetview-preview">
          <div class="map-preview-placeholder"><i class="fa-solid fa-street-view"></i><span>Street View kodu girilmediğinde önizleme burada görünür.</span></div>
        </div>
      </div>
    </div>
  </div>

  <!-- SEO -->
  <div class="card">
    <h2><i class="fa-solid fa-magnifying-glass-chart" style="color:var(--gold)"></i> Proje Sayfası SEO</h2>
    <div class="field"><label>SEO Başlığı</label><input type="text" name="seo_title" value="<?= $v('seo_title') ?>" placeholder="Boş bırakılırsa proje başlığı kullanılır"></div>
    <div class="field"><label>Meta Açıklama</label><input type="text" name="seo_desc" value="<?= $v('seo_desc') ?>" placeholder="Boş bırakılırsa proje alt başlığı kullanılır"></div>
    <div class="field"><label>Anahtar Kelimeler</label><input type="text" name="seo_keywords" value="<?= $v('seo_keywords') ?>"></div>
  </div>

  <div class="row">
    <button class="btn" name="action" value="save"><i class="fa-solid fa-floppy-disk"></i> Değişiklikleri Kaydet</button>
    <a class="btn ghost" href="projects.php">Listeye Dön</a>
  </div>
</form>

<?php if ($p): ?>
<!-- Medya Yükleme Bölümleri (Arka Plan Görselleri, Render, Plan, Şantiye, Video) Sürükle-Bırak Destekli -->
<div id="media" style="margin-top:30px">
  <div class="row between" style="margin-bottom:14px">
    <h2 style="margin:0;font-size:18px"><i class="fa-solid fa-photo-film" style="color:var(--gold)"></i> Proje Medya & Arka Plan Yönetimi</h2>
    <span class="muted" style="font-size:0.85rem">Dosyaları doğrudan sürükleyip bırakarak topluca yükleyebilirsiniz.</span>
  </div>

  <?php foreach ($KINDS as $kind => $label):
      $items = q('SELECT * FROM project_media WHERE project_id=? AND kind=? ORDER BY sort_order,id', [$id, $kind])->fetchAll();
      $isHero = ($kind === 'hero');
  ?>
    <div class="card" id="media-<?= e($kind) ?>" style="<?= $isHero ? 'border-color:rgba(197,160,89,0.4);background:rgba(18,21,28,0.95)' : '' ?>">
      <div class="row between" style="margin-bottom:12px">
        <h2 style="margin:0">
          <?php if ($isHero): ?><i class="fa-solid fa-panorama" style="color:var(--gold)"></i><?php endif; ?>
          <?= e($label) ?> (<?= count($items) ?>)
        </h2>
        <?php if ($isHero): ?>
          <span class="pill" style="background:rgba(197,160,89,0.2);color:var(--gold)">Proje Detay Hero Arka Plan Slider</span>
        <?php endif; ?>
      </div>

      <?php if ($items): ?>
        <div class="media-grid" style="margin-bottom:16px">
          <?php foreach ($items as $m): $src = '../' . $m['file_path']; ?>
            <div class="media-item">
              <?php if ($kind === 'video'): ?>
                <video src="<?= e($src) ?>" muted preload="metadata"></video>
              <?php else: ?>
                <img src="<?= e($src) ?>" alt="" style="<?= $isHero ? 'height:110px' : '' ?>">
              <?php endif; ?>
              <div class="cap"><?= e($m['caption']) ?></div>
              <form method="post" data-confirm="Bu dosya silinsin mi?">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= $id ?>">
                <input type="hidden" name="mid" value="<?= (int)$m['id'] ?>">
                <button class="x" name="action" value="media_del" title="Sil">×</button>
              </form>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- Sürükle - Bırak Formu -->
      <form method="post" enctype="multipart/form-data" class="grid g2" style="align-items:center;background:rgba(255,255,255,0.02);padding:14px;border-radius:8px;border:1px solid var(--line)">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= $id ?>">
        <input type="hidden" name="kind" value="<?= e($kind) ?>">
        
        <div class="dropzone" id="dz-<?= e($kind) ?>" style="padding:16px">
          <i class="fa-solid fa-cloud-arrow-up"></i>
          <p><?= e($label) ?> için dosyaları sürükleyin veya <span>seçin</span></p>
          <small class="file-list-preview"></small>
          <input type="file" name="files[]" multiple accept="<?= $kind === 'video' ? 'video/mp4,video/webm' : 'image/*' ?>">
        </div>

        <div>
          <div class="field" style="margin-bottom:10px">
            <label>Açıklama / Başlık (İsteğe bağlı)</label>
            <input type="text" name="caption" placeholder="<?= $kind === 'video' ? 'Video başlığı (ör. Lansman Filmi)' : ($isHero ? 'Arka plan adı (ör. Giriş Cephesi)' : 'Görsel açıklaması') ?>">
          </div>
          <button class="btn" name="action" value="media_add" style="width:100%;justify-content:center">
            <i class="fa-solid fa-upload"></i> Seçilen Dosyaları Yükle
          </button>
        </div>
      </form>
    </div>
  <?php endforeach; ?>
</div>
<?php else: ?>
<div class="card" style="margin-top:20px;text-align:center;padding:36px">
  <i class="fa-solid fa-images" style="font-size:38px;color:rgba(197,160,89,0.5);margin-bottom:12px;display:block"></i>
  <p class="muted">Arka plan görselleri (Hero), galeri, plan, şantiye ve video yükleme alanları projeyi ilk kez kaydettikten sonra açılır.</p>
</div>
<?php endif; ?>

<script>
// Harita & Street View Canlı Önizleme Motoru
function updateEmbedPreview(textareaId, previewContainerId) {
    const txt = document.getElementById(textareaId);
    const box = document.getElementById(previewContainerId);
    if (!txt || !box) return;

    function render() {
        const val = txt.value.trim();
        if (!val) {
            box.innerHTML = '<div class="map-preview-placeholder"><i class="fa-solid fa-map-location-dot"></i><span>Kod girildiğinde canlı önizleme burada görünecektir.</span></div>';
            return;
        }

        // Eğer doğrudan iframe yapıştırılmışsa
        if (val.toLowerCase().includes('<iframe')) {
            box.innerHTML = val;
            const ifr = box.querySelector('iframe');
            if (ifr) {
                ifr.style.width = '100%';
                ifr.style.height = '100%';
                ifr.style.border = '0';
            }
        } else {
            // URL girilmişse
            box.innerHTML = `<iframe src="${val}" style="width:100%;height:100%;border:0" allowfullscreen loading="lazy"></iframe>`;
        }
    }

    txt.addEventListener('input', render);
    txt.addEventListener('paste', () => setTimeout(render, 50));
    render(); // Sayfa yüklendiğinde ilk çalıştır
}

updateEmbedPreview('google_map_code', 'map-preview');
updateEmbedPreview('street_view_code', 'streetview-preview');

// Sürükle - Bırak (Dropzone) Dinamik Motoru
document.querySelectorAll('.dropzone').forEach(dz => {
    const input = dz.querySelector('input[type="file"]');
    const preview = dz.querySelector('.file-list-preview');

    function updateLabel(files) {
        if (!files || files.length === 0) {
            preview.textContent = '';
            return;
        }
        if (files.length === 1) {
            preview.textContent = '✓ Seçildi: ' + files[0].name + ' (' + (files[0].size / 1024 / 1024).toFixed(2) + ' MB)';
        } else {
            preview.textContent = '✓ ' + files.length + ' dosya seçildi: ' + Array.from(files).map(f => f.name).slice(0, 3).join(', ') + (files.length > 3 ? '...' : '');
        }
    }

    dz.addEventListener('dragover', (e) => {
        e.preventDefault();
        e.stopPropagation();
        dz.classList.add('dragover');
    });

    dz.addEventListener('dragleave', (e) => {
        e.preventDefault();
        e.stopPropagation();
        dz.classList.remove('dragover');
    });

    dz.addEventListener('drop', (e) => {
        e.preventDefault();
        e.stopPropagation();
        dz.classList.remove('dragover');
        if (e.dataTransfer && e.dataTransfer.files.length > 0) {
            input.files = e.dataTransfer.files;
            updateLabel(input.files);
        }
    });

    input.addEventListener('change', () => {
        updateLabel(input.files);
    });
});
</script>
<?php admin_footer();
