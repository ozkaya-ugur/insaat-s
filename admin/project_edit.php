<?php
require_once __DIR__ . '/_layout.php';
require_perm('projects', 'view');

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$p = $id ? q('SELECT * FROM projects WHERE id=?', [$id])->fetch() : null;
if ($id && !$p) { flash('Proje bulunamadı.', 'err'); redirect('projects.php'); }
if (!admin_can('projects', $p ? 'edit' : 'create')) admin_deny();

// İl değişince ilçe listesi (JSON)
if (isset($_GET['districts'])) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(districts_of((string)($_GET['il'] ?? '')), JSON_UNESCAPED_UNICODE);
    exit;
}

$IMG = ['jpg','jpeg','png','webp','gif'];
$KINDS = [
    'hero'   => 'Arka Plan Slider Görselleri (Hero)',
    'gallery'=> 'Proje Görselleri (Galeri)',
    'plan'   => 'Kat Planları',
    'site'   => 'Şantiye Görselleri',
    'video'  => 'Tanıtım Videoları',
];

// Yüklü medya kütüphanesi (JSON) — sliders.php deseninin proje formuna uyarlanmış hali
if (isset($_GET['media'])) {
    header('Content-Type: application/json; charset=utf-8');
    $type = in_array($_GET['type'] ?? 'all', ['all', 'image', 'video'], true) ? $_GET['type'] : 'all';
    $all = media_library(mb_substr(trim((string)($_GET['q'] ?? '')), 0, 80), $type);
    $per = 48;
    $page = max(1, (int)($_GET['p'] ?? 1));
    $prefix = fn($p) => strpos($p, 'uploads/') === 0 ? '../' . $p : rtrim(SITE_URL, '/') . '/' . $p;
    $items = array_map(fn($i) => $i + ['url' => $prefix($i['path'])], array_slice($all, ($page - 1) * $per, $per));
    echo json_encode([
        'total' => count($all),
        'page' => $page,
        'pages' => max(1, (int)ceil(count($all) / $per)),
        'items' => $items,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

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
        // ---- Medya kütüphanesinden tek görsel ekle ----
        if ($act === 'media_add_path' && $p) {
            $kind = $_POST['kind'] ?? '';
            if (!isset($KINDS[$kind]) || $kind === 'video') throw new RuntimeException('Bu bölüm için yalnızca görsel seçilebilir.');
            $picked = ltrim(trim((string)($_POST['media_path'] ?? '')), '/');
            if (!media_path_valid($picked, $IMG)) throw new RuntimeException('Seçilen görsel bulunamadı veya izin verilmeyen bir yol.');
            $cap = trim((string)($_POST['caption'] ?? ''));
            $max = (int)q('SELECT COALESCE(MAX(sort_order),0) FROM project_media WHERE project_id=? AND kind=?', [$id, $kind])->fetchColumn();
            q('INSERT INTO project_media (project_id,kind,file_path,caption,sort_order) VALUES (?,?,?,?,?)', [
                $id,
                $kind,
                $picked,
                $cap !== '' ? $cap : pathinfo($picked, PATHINFO_FILENAME),
                $max + 1,
            ]);
            flash('Görsel medya kütüphanesinden eklendi.');
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
        $district = $f('district');
        if ($district !== '' && !in_array($district, districts_of($cityKey), true)) $district = '';
        $features = [];
        $fIcons = (array)($_POST['feat_icon'] ?? []);
        $fTitles = (array)($_POST['feat_title_tr'] ?? []);
        foreach ($fTitles as $i => $t) {
            $t = trim((string)$t);
            if ($t === '') continue;
            $features[] = [
                'icon'     => preg_replace('/[^a-z0-9-]/', '', strtolower((string)($fIcons[$i] ?? 'fa-circle-check'))) ?: 'fa-circle-check',
                'title_tr' => $t,
                'title_en' => trim((string)($_POST['feat_title_en'][$i] ?? '')),
                'title_ar' => trim((string)($_POST['feat_title_ar'][$i] ?? '')),
                'sub_tr'   => trim((string)($_POST['feat_sub_tr'][$i] ?? '')),
                'sub_en'   => trim((string)($_POST['feat_sub_en'][$i] ?? '')),
                'sub_ar'   => trim((string)($_POST['feat_sub_ar'][$i] ?? '')),
            ];
            if (count($features) >= 12) break;
        }
        $featuresJson = $features ? json_encode($features, JSON_UNESCAPED_UNICODE) : null;
        $types = array_values(array_intersect(array_keys(PROJECT_TYPES), (array)($_POST['type'] ?? [])));
        $status = array_key_exists($f('status'), PROJECT_STATUS) ? $f('status') : 'devam-eden';

        $cover = $p['cover_image'] ?? '';
        if ($new = upload_file('cover', $IMG, 'covers')) {
            if ($cover) delete_upload($cover);
            $cover = $new;
        } elseif ($f('cover_path') !== '') {
            $pickedCover = ltrim($f('cover_path'), '/');
            if (!media_path_valid($pickedCover, $IMG)) throw new RuntimeException('Kapak görseli yolu geçersiz veya izin verilmeyen bir konuma işaret ediyor.');
            $cover = $pickedCover;
        }
        if (!empty($_POST['cover_remove'])) { delete_upload($cover); $cover = ''; }

        $catalog = $p['catalog_file'] ?? '';
        if ($new = upload_file('catalog', ['pdf'], 'catalogs')) { if ($catalog) delete_upload($catalog); $catalog = $new; }
        if (!empty($_POST['catalog_remove'])) { delete_upload($catalog); $catalog = ''; }

        $googleMapCode = $f('google_map_code');
        $streetViewCode = $f('street_view_code');

        $data = [
            $slug, $title, $f('title_en'), $f('title_ar'), $f('title_de'),
            $f('subtitle_tr'), $f('slogan_tr'), $f('subtitle_en'), $f('slogan_en'), $f('subtitle_ar'), $f('slogan_ar'), $f('subtitle_de'),
            $f('description_tr'), $f('description_en'), $f('description_ar'), $f('description_de'),
            $f('cat'), $f('loc'), $cityKey, $district, $featuresJson, implode(',', $types), $status,
            !empty($_POST['is_new']) ? 1 : 0, !empty($_POST['is_urban_renewal']) ? 1 : 0,
            $cover, $f('area'), $f('concept'), $f('delivery'), $catalog,
            $streetViewCode, $googleMapCode,
            !empty($_POST['is_featured']) ? 1 : 0, !empty($_POST['is_active']) ? 1 : 0, (int)$f('sort_order', '0'),
            $f('seo_title'), $f('seo_desc'), $f('seo_keywords'),
        ];
        $cols = 'slug,title_tr,title_en,title_ar,title_de,subtitle_tr,slogan_tr,subtitle_en,slogan_en,subtitle_ar,slogan_ar,subtitle_de,description_tr,description_en,description_ar,description_de,cat,loc,city_key,district,features,type,status,is_new,is_urban_renewal,cover_image,area,concept,delivery,catalog_file,street_view_code,google_map_code,is_featured,is_active,sort_order,seo_title,seo_desc,seo_keywords';
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
      <div class="field"><label>Slogan (<?= e($langObj['name']) ?>)</label><input type="text" name="slogan_<?= e($l) ?>" value="<?= $v("slogan_$l") ?>" placeholder="Konumdan sonra proje vurgusu"></div>
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
          <?php foreach (CITY_KEYS as $k => $n): ?>
            <option value="<?= e($k) ?>" <?= ($p['city_key'] ?? 'tr-sk') === $k ? 'selected' : '' ?>><?= e($n) ?> (<?= e($k) ?>)</option>
          <?php endforeach; ?>
        </select></div>
      <div class="field"><label>İlçe</label>
        <select name="district" id="districtSelect" data-city="<?= e($p['city_key'] ?? 'tr-sk') ?>">
          <option value="">Seçilmedi</option>
          <?php foreach (districts_of($p['city_key'] ?? 'tr-sk') as $d): ?>
            <option value="<?= e($d) ?>" <?= ($p['district'] ?? '') === $d ? 'selected' : '' ?>><?= e($d) ?></option>
          <?php endforeach; ?>
        </select>
        <div class="hint">İl değişince liste yenilenir.</div></div>
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
    <div class="row" style="margin-top:10px">
      <label class="chk"><input type="checkbox" name="is_new" value="1" <?= ($p['is_new'] ?? 0) ? 'checked' : '' ?>><span>Yeni Proje</span></label>
      <label class="chk"><input type="checkbox" name="is_urban_renewal" value="1" <?= ($p['is_urban_renewal'] ?? 0) ? 'checked' : '' ?>><span>Kentsel Dönüşüm Projesi</span></label>
    </div>
    <p class="hint">"Yeni Proje" projeler sayfasındaki <b>Yeni Projeler</b> filtresinde, "Kentsel Dönüşüm Projesi" ise Kentsel Dönüşüm sayfasında listelenir.</p>
  </div>

  <!-- Projeye Özel Özellikler -->
  <div class="card">
    <div class="row between" style="margin-bottom:6px;flex-wrap:wrap;gap:10px">
      <h2 style="margin:0"><i class="fa-solid fa-list-check" style="color:var(--gold)"></i> Projeye Özel Özellikler</h2>
      <button type="button" class="btn ghost sm" id="featAdd"><i class="fa-solid fa-plus"></i> Özellik Ekle</button>
    </div>
    <p class="hint" style="margin:0 0 12px">Proje detay sayfasındaki özellik kutuları buradan yönetilir. Boş bırakılırsa sitede varsayılan dört kutu gösterilir. En fazla 12 özellik eklenebilir.</p>
    <div id="featList" class="feat-list">
      <?php
      $featRows = [];
      if (!empty($p['features'])) {
          $decoded = json_decode((string)$p['features'], true);
          if (is_array($decoded)) $featRows = $decoded;
      }
      foreach ($featRows as $fr): ?>
        <div class="feat-row">
          <div class="feat-icon-cell">
            <span class="feat-preview"><i class="fa-solid <?= e($fr['icon'] ?? 'fa-circle-check') ?>"></i></span>
            <select name="feat_icon[]" class="feat-icon-select">
              <?php foreach (PROJECT_FEATURE_ICONS as $ik => $il): ?>
                <option value="<?= e($ik) ?>" <?= ($fr['icon'] ?? '') === $ik ? 'selected' : '' ?>><?= e($il) ?> — <?= e($ik) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="feat-fields">
            <input type="text" name="feat_title_tr[]" value="<?= e($fr['title_tr'] ?? '') ?>" placeholder="Başlık (TR) *">
            <input type="text" name="feat_sub_tr[]" value="<?= e($fr['sub_tr'] ?? '') ?>" placeholder="Alt açıklama (TR)">
            <input type="text" name="feat_title_en[]" value="<?= e($fr['title_en'] ?? '') ?>" placeholder="Title (EN)">
            <input type="text" name="feat_sub_en[]" value="<?= e($fr['sub_en'] ?? '') ?>" placeholder="Subtitle (EN)">
            <input type="text" name="feat_title_ar[]" value="<?= e($fr['title_ar'] ?? '') ?>" placeholder="العنوان (AR)">
            <input type="text" name="feat_sub_ar[]" value="<?= e($fr['sub_ar'] ?? '') ?>" placeholder="الوصف (AR)">
          </div>
          <button type="button" class="btn danger sm feat-del" title="Özelliği kaldır"><i class="fa-solid fa-trash"></i></button>
        </div>
      <?php endforeach; ?>
    </div>
    <p class="hint" id="featEmpty" style="margin:8px 0 0;<?= $featRows ? 'display:none' : '' ?>">Henüz özellik eklenmedi.</p>
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
          <label>veya medya kütüphanesinden seçin</label>
          <div class="row" style="gap:8px;flex-wrap:nowrap">
            <input type="text" name="cover_path" id="coverPathInput" placeholder="images/banner-1.webp veya uploads/covers/ornek.webp" value="<?= $v('cover_image') ?>" style="flex:1">
            <button type="button" class="btn ghost" id="openCoverMediaLib" style="white-space:nowrap"><i class="fa-solid fa-photo-film"></i> Kütüphaneden Seç</button>
          </div>
          <div id="coverPathPreview" style="margin-top:8px;display:none"></div>
          <div class="hint">Sadece uploads/ ve images/ altındaki güvenli görsel yolları kabul edilir.</div>
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
      $items = $kind === 'gallery'
        ? q("SELECT * FROM project_media WHERE project_id=? AND kind IN ('gallery','render') ORDER BY sort_order,id", [$id])->fetchAll()
        : q('SELECT * FROM project_media WHERE project_id=? AND kind=? ORDER BY sort_order,id', [$id, $kind])->fetchAll();
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
          <?php if ($kind === 'gallery'): ?>
          <div class="field" style="margin-bottom:10px">
            <label>veya kütüphaneden tek görsel ekle</label>
            <div class="row" style="gap:8px;flex-wrap:nowrap">
              <input type="text" name="media_path" class="galleryMediaPath" placeholder="uploads/projects/...webp" style="flex:1" readonly>
              <button type="button" class="btn ghost openGalleryMediaLib" style="white-space:nowrap"><i class="fa-solid fa-photo-film"></i> Kütüphaneden Seç</button>
            </div>
            <div class="galleryMediaPreview" style="margin-top:8px;display:none"></div>
            <button class="btn ghost" name="action" value="media_add_path" style="width:100%;justify-content:center;margin-top:8px">
              <i class="fa-solid fa-circle-plus"></i> Galeriye Ekle
            </button>
          </div>
          <?php endif; ?>
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

<style>
#projectMediaLib{position:fixed;inset:0;background:rgba(0,0,0,.72);z-index:1100;display:none;align-items:center;justify-content:center;padding:20px}
#projectMediaLib.on{display:flex}
#projectMediaLib .ml-box{background:var(--panel,#16181d);border:1px solid var(--line,#2a2d34);border-radius:10px;width:min(1100px,100%);max-height:90vh;display:flex;flex-direction:column;overflow:hidden}
#projectMediaLib .ml-head,#projectMediaLib .ml-foot{padding:14px 16px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;border-bottom:1px solid var(--line,#2a2d34)}
#projectMediaLib .ml-foot{border-bottom:0;border-top:1px solid var(--line,#2a2d34);justify-content:space-between}
#projectMediaLib .ml-head input{flex:1;min-width:180px}
#projectMediaLib .ml-head select{width:auto}
#pmlGrid{padding:14px;overflow:auto;display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));grid-auto-rows:max-content;align-content:start;gap:12px;flex:1;min-height:0}
#pmlGrid .ml-item{position:relative;background:var(--panel2,#1d2026);border:2px solid transparent;border-radius:8px;padding:0;cursor:pointer;text-align:left;color:var(--text,#eee);overflow:hidden;font:inherit}
#pmlGrid .ml-item:hover{border-color:var(--gold,#c5a059)}
#pmlGrid .ml-item img,#pmlGrid .ml-item video{width:100%;height:100px;object-fit:cover;display:block;background:#000}
#pmlGrid .ml-name{display:block;font-size:.75rem;padding:6px 8px 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
#pmlGrid .ml-size{display:block;font-size:.7rem;padding:0 8px 7px;color:var(--dim,#999)}
#pmlGrid .ml-badge{position:absolute;top:6px;left:6px;background:var(--gold,#c5a059);color:#000;font-size:.65rem;font-weight:700;padding:2px 6px;border-radius:4px}
@media(max-width:768px){
  #projectMediaLib{padding:10px}
  #projectMediaLib .ml-head,#projectMediaLib .ml-foot{padding:10px}
}
</style>
<div id="projectMediaLib" role="dialog" aria-modal="true" aria-label="Medya kütüphanesi">
  <div class="ml-box">
    <div class="ml-head">
      <strong style="margin-right:6px"><i class="fa-solid fa-photo-film" style="color:var(--gold)"></i> Yüklü Dosyalar</strong>
      <input type="text" id="pmlSearch" placeholder="Dosya adı veya klasörde ara">
      <select id="pmlType"><option value="image">Görseller</option><option value="video">Videolar</option><option value="all">Tümü</option></select>
      <button type="button" class="btn ghost sm" data-pml-close>Kapat</button>
    </div>
    <div id="pmlGrid"></div>
    <div class="ml-foot">
      <span class="muted" id="pmlInfo"></span>
      <div class="row" style="gap:8px"><button type="button" class="btn ghost sm" id="pmlPrev">‹ Önceki</button><button type="button" class="btn ghost sm" id="pmlNext">Sonraki ›</button></div>
    </div>
  </div>
</div>

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

function initProjectMediaLibrary() {
    const modal = document.getElementById('projectMediaLib');
    const grid = document.getElementById('pmlGrid');
    const info = document.getElementById('pmlInfo');
    const search = document.getElementById('pmlSearch');
    const type = document.getElementById('pmlType');
    const prevBtn = document.getElementById('pmlPrev');
    const nextBtn = document.getElementById('pmlNext');
    if (!modal || !grid || !info || !search || !type || !prevBtn || !nextBtn) return;

    let page = 1, pages = 1, timer = null;
    let targetInput = null, targetPreview = null, targetType = 'image';

    const esc = (str) => String(str).replace(/[&<>"']/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    const kb = (n) => n > 1048576 ? (n/1048576).toFixed(1)+' MB' : Math.max(1, Math.round(n/1024))+' KB';

    function showPreview(path, url, isVideo) {
        if (!targetPreview) return;
        targetPreview.style.display = 'block';
        targetPreview.innerHTML = (isVideo
            ? `<video src="${esc(url)}" muted playsinline style="width:220px;height:120px;object-fit:cover;border-radius:6px"></video>`
            : `<img src="${esc(url)}" alt="" style="width:220px;height:120px;object-fit:cover;border-radius:6px">`)
            + `<div class="hint">Seçildi: ${esc(path)}</div>`;
    }

    async function loadMedia() {
        grid.innerHTML = '<div class="muted" style="padding:20px">Yükleniyor...</div>';
        try {
            const url = `project_edit.php?media=1&p=${page}&type=${encodeURIComponent(type.value)}&q=${encodeURIComponent(search.value.trim())}`;
            const r = await fetch(url, {credentials:'same-origin'});
            const d = await r.json();
            pages = d.pages || 1; page = d.page || 1;
            info.textContent = `${d.total || 0} dosya · Sayfa ${page}/${pages}`;
            prevBtn.disabled = page <= 1;
            nextBtn.disabled = page >= pages;
            const wantedType = targetType;
            const items = (d.items || []).filter((i) => wantedType === 'all' ? true : i.type === wantedType);
            grid.innerHTML = items.length ? items.map((i) => `
                <button type="button" class="ml-item" data-path="${esc(i.path)}" data-url="${esc(i.url)}" data-video="${i.type === 'video' ? 1 : 0}" title="${esc(i.path)}">
                    ${i.type === 'video' ? `<video src="${esc(i.url)}" muted preload="metadata"></video><span class="ml-badge">Video</span>` : `<img src="${esc(i.url)}" loading="lazy" alt="">`}
                    <span class="ml-name">${esc(i.path.split('/').pop())}</span>
                    <span class="ml-size">${kb(i.size || 0)}</span>
                </button>
            `).join('') : '<div class="muted" style="padding:20px">Dosya bulunamadı.</div>';
        } catch (e) {
            grid.innerHTML = '<div class="muted" style="padding:20px">Kütüphane yüklenemedi.</div>';
        }
    }

    function openFor(input, preview, mType='image') {
        targetInput = input;
        targetPreview = preview;
        targetType = mType;
        type.value = mType === 'video' ? 'video' : 'image';
        page = 1;
        modal.classList.add('on');
        loadMedia();
    }

    document.querySelectorAll('#openCoverMediaLib').forEach((btn) => {
        btn.addEventListener('click', () => openFor(document.getElementById('coverPathInput'), document.getElementById('coverPathPreview'), 'image'));
    });

    document.querySelectorAll('.openGalleryMediaLib').forEach((btn) => {
        btn.addEventListener('click', () => {
            const card = btn.closest('.field');
            const input = card ? card.querySelector('.galleryMediaPath') : null;
            const preview = card ? card.querySelector('.galleryMediaPreview') : null;
            openFor(input, preview, 'image');
        });
    });

    modal.addEventListener('click', (e) => {
        if (e.target === modal || e.target.closest('[data-pml-close]')) modal.classList.remove('on');
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.classList.contains('on')) modal.classList.remove('on');
    });

    search.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => { page = 1; loadMedia(); }, 250);
    });
    type.addEventListener('change', () => { page = 1; loadMedia(); });
    prevBtn.addEventListener('click', () => { if (page > 1) { page--; loadMedia(); } });
    nextBtn.addEventListener('click', () => { if (page < pages) { page++; loadMedia(); } });

    grid.addEventListener('click', (e) => {
        const it = e.target.closest('.ml-item');
        if (!it || !targetInput) return;
        targetInput.value = it.dataset.path;
        showPreview(it.dataset.path, it.dataset.url, it.dataset.video === '1');
        modal.classList.remove('on');
    });

    // Var olan kapak yolunun önizlemesi
    const coverPath = document.getElementById('coverPathInput');
    const coverPreview = document.getElementById('coverPathPreview');
    if (coverPath && coverPreview && coverPath.value.trim()) {
        const path = coverPath.value.trim();
        const url = path.startsWith('uploads/') ? '../' + path : path;
        showPreview(path, url, false);
    }
}

initProjectMediaLibrary();

// ---- İlçe listesi: il değişince sunucudan tazele ----
(function () {
  const city = document.querySelector('select[name="city_key"]');
  const dist = document.getElementById('districtSelect');
  if (!city || !dist) return;
  city.addEventListener('change', async () => {
    const keep = dist.value;
    dist.innerHTML = '<option value="">Seçilmedi</option>';
    try {
      const r = await fetch('project_edit.php?districts=1&il=' + encodeURIComponent(city.value), { credentials: 'same-origin' });
      const list = await r.json();
      list.forEach(d => {
        const o = document.createElement('option');
        o.value = d; o.textContent = d;
        if (d === keep) o.selected = true;
        dist.appendChild(o);
      });
    } catch (_) {}
  });
})();

// ---- Projeye özel özellikler ----
(function () {
  const list = document.getElementById('featList');
  const addBtn = document.getElementById('featAdd');
  const empty = document.getElementById('featEmpty');
  if (!list || !addBtn) return;

  const icons = <?= json_encode(PROJECT_FEATURE_ICONS, JSON_UNESCAPED_UNICODE) ?>;

  function refreshEmpty() {
    if (empty) empty.style.display = list.querySelector('.feat-row') ? 'none' : '';
  }

  function bindRow(row) {
    const sel = row.querySelector('.feat-icon-select');
    const prev = row.querySelector('.feat-preview i');
    if (sel && prev) sel.addEventListener('change', () => { prev.className = 'fa-solid ' + sel.value; });
    const del = row.querySelector('.feat-del');
    if (del) del.addEventListener('click', () => { row.remove(); refreshEmpty(); });
  }

  list.querySelectorAll('.feat-row').forEach(bindRow);

  addBtn.addEventListener('click', () => {
    if (list.querySelectorAll('.feat-row').length >= 12) { alert('En fazla 12 özellik eklenebilir.'); return; }
    const row = document.createElement('div');
    row.className = 'feat-row';
    const opts = Object.entries(icons).map(([k, v]) => `<option value="${k}">${v} — ${k}</option>`).join('');
    row.innerHTML = `
      <div class="feat-icon-cell">
        <span class="feat-preview"><i class="fa-solid fa-circle-check"></i></span>
        <select name="feat_icon[]" class="feat-icon-select">${opts}</select>
      </div>
      <div class="feat-fields">
        <input type="text" name="feat_title_tr[]" placeholder="Başlık (TR) *">
        <input type="text" name="feat_sub_tr[]" placeholder="Alt açıklama (TR)">
        <input type="text" name="feat_title_en[]" placeholder="Title (EN)">
        <input type="text" name="feat_sub_en[]" placeholder="Subtitle (EN)">
        <input type="text" name="feat_title_ar[]" placeholder="العنوان (AR)">
        <input type="text" name="feat_sub_ar[]" placeholder="الوصف (AR)">
      </div>
      <button type="button" class="btn danger sm feat-del" title="Özelliği kaldır"><i class="fa-solid fa-trash"></i></button>`;
    list.appendChild(row);
    bindRow(row);
    refreshEmpty();
    row.querySelector('input[name="feat_title_tr[]"]').focus();
  });
})();

</script>
<?php admin_footer();
