<?php
require_once __DIR__ . '/_layout.php';
require_login();

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
<?php admin_footer();
