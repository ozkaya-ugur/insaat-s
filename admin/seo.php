<?php
require_once __DIR__ . '/_layout.php';
require_perm('seo', 'view');
admin_readonly(!admin_can('seo', 'edit'));

$activeLangs = get_languages();
$LANGS = [];
foreach ($activeLangs as $al) {
    $LANGS[$al['code']] = [
        'name' => $al['name'],
        'flag' => $al['flag'],
        'dir'  => $al['direction'],
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (!admin_can('seo', 'edit')) admin_deny();
    $act = $_POST['action'] ?? '';
    if ($act === 'favicon_upload') {
        try {
            $f = $_FILES['favicon'] ?? null;
            if (!$f || $f['error'] === UPLOAD_ERR_NO_FILE) throw new RuntimeException('Lütfen bir favicon dosyası seçin.');
            if ($f['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Yükleme hatası (kod ' . $f['error'] . ').');
            if ($f['size'] > 2 * 1024 * 1024) throw new RuntimeException('Favicon dosyası en fazla 2 MB olabilir.');
            $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'ico'], true)) throw new RuntimeException('Sadece PNG, JPG, WEBP veya ICO yükleyebilirsiniz.');
            if ($ext !== 'ico' && @getimagesize($f['tmp_name']) === false) throw new RuntimeException('Geçerli bir görsel değil.');
            if ($ext === 'ico' && substr((string)file_get_contents($f['tmp_name'], false, null, 0, 4), 0, 4) !== "\x00\x00\x01\x00") throw new RuntimeException('Geçerli bir ICO dosyası değil.');
            $r = favicon_generate($f['tmp_name'], $ext);
            flash('Favicon güncellendi.' . ($r['root_updated'] ? '' : ' (Kök dizin dosyaları yazılamadı; site yeni favicon\'u ayar üzerinden kullanacak.)'));
        } catch (Throwable $ex) { flash($ex->getMessage(), 'err'); }
        redirect('seo.php');
    }
    if ($act === 'favicon_reset') {
        favicon_reset();
        flash('Varsayılan favicon geri yüklendi.');
        redirect('seo.php');
    }
    foreach (array_keys($LANGS) as $l) {
        q('INSERT INTO seo_settings (lang,title,description,keywords) VALUES (?,?,?,?)
           ON DUPLICATE KEY UPDATE title=VALUES(title), description=VALUES(description), keywords=VALUES(keywords)', [
            $l, mb_substr(trim($_POST["title_$l"] ?? ''), 0, 200),
            mb_substr(trim($_POST["desc_$l"] ?? ''), 0, 300), mb_substr(trim($_POST["kw_$l"] ?? ''), 0, 300),
        ]);
    }
    flash('SEO ayarları kaydedildi.');
    redirect('seo.php');
}

admin_header('SEO Ayarları', 'seo');
$seo = [];
foreach (q('SELECT * FROM seo_settings')->fetchAll() as $r) $seo[$r['lang']] = $r;
$trSeo = $seo['tr'] ?? ['title' => '', 'description' => '', 'keywords' => ''];
?>
<form method="post" class="card"><?= csrf_field() ?>
  <div class="row between" style="margin-bottom:8px">
    <h2 style="margin:0">Site Geneli Meta & SEO Ayarları</h2>
    <span class="muted" style="font-size:0.85rem"><i class="fa-solid fa-circle-info" style="color:var(--gold)"></i> Diğer diller boş bırakılırsa otomatik Türkçe baz alınır.</span>
  </div>

  <?= lang_tabs('seo') ?>

  <?php $first = true; foreach ($LANGS as $l => $meta): $s = $seo[$l] ?? []; ?>
  <div class="lpane <?= $first ? 'on' : '' ?> <?= $meta['dir'] === 'rtl' ? 'rtl' : '' ?>" data-pane="<?= $l ?>">
    <div style="margin-bottom:12px;padding:8px 12px;background:rgba(255,255,255,0.03);border-radius:4px;font-size:0.85rem">
      <strong><?= e($meta['flag'] . ' ' . $meta['name']) ?> (<?= strtoupper(e($l)) ?>)</strong>
      <?php if ($l !== 'tr' && empty($s['title']) && !empty($trSeo['title'])): ?>
        <span style="color:#d4af37;margin-left:10px"><i class="fa-solid fa-arrow-turn-down"></i> Şu an Türkçe SEO bilgisi devrede: "<?= e(mb_strimwidth($trSeo['title'], 0, 40, '...')) ?>"</span>
      <?php endif; ?>
    </div>

    <div class="field">
      <label>Sayfa Başlığı (title) <?= $l === 'tr' ? '<span style="color:var(--gold)">* (Varsayılan Referans)</span>' : '' ?></label>
      <input type="text" name="title_<?= $l ?>" value="<?= e($s['title'] ?? '') ?>" maxlength="200" placeholder="<?= $l !== 'tr' && !empty($trSeo['title']) ? 'Boş bırakılırsa: ' . e($trSeo['title']) : 'Ör: BUYAPI | Mimari & Mühendislik' ?>">
      <div class="hint">Arama motorlarında görünen ana başlık (İdeal: 50–60 karakter)</div>
    </div>

    <div class="field">
      <label>Meta Açıklama (description)</label>
      <textarea name="desc_<?= $l ?>" maxlength="300" placeholder="<?= $l !== 'tr' && !empty($trSeo['description']) ? 'Boş bırakılırsa: ' . e($trSeo['description']) : 'Ör: Prestijli konut, ticari ve endüstriyel projeler...' ?>"><?= e($s['description'] ?? '') ?></textarea>
      <div class="hint">Arama sonuçlarında sitenin altında çıkan özet (İdeal: 140–160 karakter)</div>
    </div>

    <div class="field">
      <label>Anahtar Kelimeler (keywords)</label>
      <input type="text" name="kw_<?= $l ?>" value="<?= e($s['keywords'] ?? '') ?>" placeholder="<?= $l !== 'tr' && !empty($trSeo['keywords']) ? 'Boş bırakılırsa: ' . e($trSeo['keywords']) : 'inşaat, mimari, sakarya, kentsel dönüşüm' ?>">
      <div class="hint">Virgülle ayırarak giriniz.</div>
    </div>
  </div>
  <?php $first = false; endforeach; ?>

  <div class="row" style="margin-top:16px">
    <button class="btn"><i class="fa-solid fa-floppy-disk"></i> Tüm Diller İçin Kaydet</button>
  </div>
  <p class="hint" style="margin-top:12px">Proje sayfalarına özel SEO alanları, her projenin kendi düzenleme ekranındadır.</p>
</form>

<?php
$favV = setting('favicon_version', '');
$favQ = $favV !== '' ? '?v=' . rawurlencode($favV) : '';
$fav32 = '../' . (setting('favicon_32', '') ?: 'favicon-32x32.png') . $favQ;
$favApple = '../' . (setting('favicon_apple', '') ?: 'apple-touch-icon.png') . $favQ;
$favCustom = setting('favicon_ico', '') !== '';
?>
<form method="post" enctype="multipart/form-data" class="card" id="faviconCard"><?= csrf_field() ?>
  <h2 style="margin-top:0"><i class="fa-solid fa-star" style="color:var(--gold)"></i> Favicon</h2>
  <div class="row" style="gap:22px;align-items:center;margin-bottom:14px">
    <div style="text-align:center"><img id="favPrev32" src="<?= e($fav32) ?>" alt="favicon 32" style="width:32px;height:32px;image-rendering:auto;background:#fff;border-radius:4px;padding:2px"><div class="hint">32×32</div></div>
    <div style="text-align:center"><img id="favPrevApple" src="<?= e($favApple) ?>" alt="apple touch icon" style="width:72px;height:72px;border-radius:14px;background:#fff"><div class="hint">Mobil (180×180)</div></div>
    <div class="muted" style="font-size:.9rem">Durum: <strong><?= $favCustom ? 'Özel favicon kullanılıyor' : 'Varsayılan favicon' ?></strong><?php if ($favV): ?><br>Son güncelleme: <?= e(date('d.m.Y H:i', strtotime($favV))) ?><?php endif; ?></div>
  </div>
  <div class="field">
    <label>Yeni Favicon Yükle</label>
    <input type="file" name="favicon" id="faviconInput" accept="image/png,image/jpeg,image/webp,image/x-icon,.ico">
    <div class="hint">PNG, JPG, WEBP veya ICO (en fazla 2 MB). Kare ve en az 180×180 px önerilir; kare değilse ortadan kırpılır. 16×16, 32×32, 180×180 ve favicon.ico otomatik üretilir.</div>
  </div>
  <div class="row">
    <button class="btn" name="action" value="favicon_upload"><i class="fa-solid fa-upload"></i> Favicon'u Kaydet</button>
    <?php if ($favCustom): ?><button class="btn ghost" name="action" value="favicon_reset" formnovalidate onclick="return confirm('Varsayılan favicon geri yüklensin mi?')">Varsayılana Dön</button><?php endif; ?>
  </div>
</form>
<script>
document.getElementById('faviconInput')?.addEventListener('change', e => {
  const f = e.target.files && e.target.files[0]; if (!f) return;
  const u = URL.createObjectURL(f);
  ['favPrev32', 'favPrevApple'].forEach(id => { const el = document.getElementById(id); if (el) el.src = u; });
});
</script>
<?php admin_footer();
