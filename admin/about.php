<?php
require_once __DIR__ . '/_layout.php';
require_login();

$activeLangs = get_languages();

// ─── POST HANDLER ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $section = $_POST['section'] ?? '';

    // ── 1. Banner & Ana İçerik ──────────────────────────────────────────────
    if ($section === 'content') {
        $langKeys = [
            'corp_banner_tag', 'corp_banner_title',
            'corp_section_tag', 'corp_title', 'corp_lead', 'corp_body',
        ];
        foreach ($langKeys as $k) {
            // default (TR / no suffix)
            if (isset($_POST[$k])) {
                q('INSERT INTO site_settings (s_key,s_value) VALUES (?,?) ON DUPLICATE KEY UPDATE s_value=VALUES(s_value)',
                    [$k, trim($_POST[$k])]);
            }
            foreach ($activeLangs as $al) {
                $lk = "{$k}_{$al['code']}";
                if (isset($_POST[$lk])) {
                    q('INSERT INTO site_settings (s_key,s_value) VALUES (?,?) ON DUPLICATE KEY UPDATE s_value=VALUES(s_value)',
                        [$lk, trim($_POST[$lk])]);
                }
            }
        }

        // Banner görseli yükleme
        if (!empty($_FILES['corp_banner_img']['name']) && $_FILES['corp_banner_img']['error'] === UPLOAD_ERR_OK) {
            try {
                $up = upload_file('corp_banner_img', ['jpg','jpeg','png','webp'], 'about');
                if ($up) {
                    q('INSERT INTO site_settings (s_key,s_value) VALUES (?,?) ON DUPLICATE KEY UPDATE s_value=VALUES(s_value)',
                        ['corp_banner_img', $up]);
                }
            } catch (Throwable $ex) {
                flash('Görsel yüklenemedi: ' . $ex->getMessage(), 'err');
                redirect('about.php#content');
            }
        }

        flash('Hakkımızda içeriği kaydedildi.');
        redirect('about.php#content');
    }

    // ── 2. İstatistik Sayaçları ─────────────────────────────────────────────
    if ($section === 'stats') {
        $statKeys = [
            'corp_stat_1_num', 'corp_stat_1_sym', 'corp_stat_1_lbl',
            'corp_stat_2_num', 'corp_stat_2_sym', 'corp_stat_2_lbl',
            'corp_stat_3_num', 'corp_stat_3_sym', 'corp_stat_3_lbl',
        ];
        foreach ($statKeys as $k) {
            if (isset($_POST[$k])) {
                q('INSERT INTO site_settings (s_key,s_value) VALUES (?,?) ON DUPLICATE KEY UPDATE s_value=VALUES(s_value)',
                    [$k, trim($_POST[$k])]);
            }
            foreach ($activeLangs as $al) {
                $lk = "{$k}_{$al['code']}";
                if (isset($_POST[$lk])) {
                    q('INSERT INTO site_settings (s_key,s_value) VALUES (?,?) ON DUPLICATE KEY UPDATE s_value=VALUES(s_value)',
                        [$lk, trim($_POST[$lk])]);
                }
            }
        }
        flash('İstatistik sayaçları kaydedildi.');
        redirect('about.php#stats');
    }

    // ── 3. Ofisler & Harita ─────────────────────────────────────────────────
    if ($section === 'offices') {
        $offKeys = [
            'office_sakarya_badge', 'office_sakarya_city', 'office_sakarya_title',
            'office_sakarya_address', 'office_sakarya_map_link', 'office_sakarya_embed',
            'office_istanbul_badge', 'office_istanbul_city', 'office_istanbul_title',
            'office_istanbul_address', 'office_istanbul_map_link', 'office_istanbul_embed',
        ];
        foreach ($offKeys as $k) {
            if (isset($_POST[$k])) {
                q('INSERT INTO site_settings (s_key,s_value) VALUES (?,?) ON DUPLICATE KEY UPDATE s_value=VALUES(s_value)',
                    [$k, trim($_POST[$k])]);
            }
            foreach ($activeLangs as $al) {
                $lk = "{$k}_{$al['code']}";
                if (isset($_POST[$lk])) {
                    q('INSERT INTO site_settings (s_key,s_value) VALUES (?,?) ON DUPLICATE KEY UPDATE s_value=VALUES(s_value)',
                        [$lk, trim($_POST[$lk])]);
                }
            }
        }
        flash('Ofis bilgileri kaydedildi.');
        redirect('about.php#offices');
    }

    flash('Bilinmeyen işlem.', 'err');
    redirect('about.php');
}

// ─── RENDER ───────────────────────────────────────────────────────────────────
admin_header('Hakkımızda & Kurumsal', 'about');
$bannerImg = setting('corp_banner_img');
?>
<style>
.section-card { border:1px solid var(--line); border-radius:10px; padding:22px 24px; margin-bottom:28px; background:var(--card); }
.section-card h2 { font-size:1.1rem; display:flex; align-items:center; gap:9px; margin:0 0 18px; }
.section-card h2 i { color:var(--gold); }
.preview-img { width:100%; max-height:160px; object-fit:cover; border-radius:6px; margin-bottom:10px; }
.stat-box { background:rgba(255,255,255,0.03); border:1px solid var(--line); border-radius:8px; padding:14px 16px; }
.stat-box strong { color:var(--gold); font-size:.85rem; display:block; margin-bottom:8px; }
.office-box { background:rgba(255,255,255,0.03); border:1px solid var(--line); border-radius:8px; padding:16px 18px; }
.office-box strong { color:var(--gold); display:block; margin-bottom:12px; }
.save-row { display:flex; justify-content:flex-end; padding-top:14px; border-top:1px solid var(--line); margin-top:14px; }
.tab-hint { font-size:.78rem; color:var(--muted); margin:0 0 12px; }
</style>

<!-- ═══════════════════════════════════════════════════════════════════════════
     SECTION 1 — BANNER & ANA İÇERİK
     ══════════════════════════════════════════════════════════════════════════ -->
<div id="content" class="section-card">
  <h2><i class="fa-solid fa-landmark"></i> Banner & Ana İçerik <span style="margin-left:auto;font-size:.8rem;color:var(--muted);font-weight:400">/kurumsal.html</span></h2>
  <form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <input type="hidden" name="section" value="content">

    <!-- Banner Görseli -->
    <div class="field" style="margin-bottom:16px">
      <label><i class="fa-solid fa-image" style="color:var(--gold)"></i> Banner Arka Plan Görseli</label>
      <?php if ($bannerImg): ?>
        <img src="../<?= e($bannerImg) ?>" class="preview-img" alt="Banner">
      <?php endif; ?>
      <input type="file" name="corp_banner_img" accept="image/*">
      <small class="tab-hint">Yeni görsel yüklersen mevcut değişir. Boş bırakırsan korunur.</small>
    </div>

    <?php echo lang_tabs('ab_content', function($langCode, $isDefault) {
        $s = $isDefault ? '' : "_{$langCode}";
    ?>
      <p class="tab-hint">Dil: <strong><?= strtoupper($langCode) ?></strong> — boş bırakılan alanlar TR içeriğini kullanır.</p>

      <div class="grid g2" style="margin-bottom:12px">
        <div class="field">
          <label>Banner Üst Etiket (<?= strtoupper($langCode) ?>)</label>
          <input type="text" name="corp_banner_tag<?= $s ?>" value="<?= e(setting("corp_banner_tag{$s}", 'BUYAPI MİMARİSİ')) ?>">
        </div>
        <div class="field">
          <label>Banner Ana Başlık (<?= strtoupper($langCode) ?>)</label>
          <input type="text" name="corp_banner_title<?= $s ?>" value="<?= e(setting("corp_banner_title{$s}", 'Kurumsal Felsefemiz')) ?>">
        </div>
      </div>

      <div class="field" style="margin-bottom:12px">
        <label>Bölüm Üst Etiketi (<?= strtoupper($langCode) ?>)</label>
        <input type="text" name="corp_section_tag<?= $s ?>" value="<?= e(setting("corp_section_tag{$s}", 'GELECEĞİ İNŞA EDİYORUZ')) ?>">
      </div>

      <div class="field" style="margin-bottom:12px">
        <label>Ana Başlık (<?= strtoupper($langCode) ?>)</label>
        <input type="text" name="corp_title<?= $s ?>" value="<?= e(setting("corp_title{$s}", 'Sıradışı ve prestijli projeleri hayata geçiriyoruz.')) ?>">
      </div>

      <div class="field" style="margin-bottom:12px">
        <label>Öne Çıkan Spot Metin — Lead (<?= strtoupper($langCode) ?>)</label>
        <textarea name="corp_lead<?= $s ?>" style="min-height:65px"><?= e(setting("corp_lead{$s}")) ?></textarea>
      </div>

      <div class="field" style="margin-bottom:4px">
        <label>Detaylı Paragraf — Body (<?= strtoupper($langCode) ?>)</label>
        <textarea name="corp_body<?= $s ?>" style="min-height:100px"><?= e(setting("corp_body{$s}")) ?></textarea>
      </div>
    <?php }); ?>

    <div class="save-row">
      <button class="btn"><i class="fa-solid fa-floppy-disk"></i> İçeriği Kaydet</button>
    </div>
  </form>
</div>

<!-- ═══════════════════════════════════════════════════════════════════════════
     SECTION 2 — İSTATİSTİK SAYAÇLARI
     ══════════════════════════════════════════════════════════════════════════ -->
<div id="stats" class="section-card">
  <h2><i class="fa-solid fa-chart-line"></i> İstatistik Sayaçları</h2>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="section" value="stats">

    <p class="tab-hint">Sayı ve sembol dile bağlı değildir. Etiket (lbl) her dil için ayrı girilebilir.</p>

    <?php echo lang_tabs('ab_stats', function($langCode, $isDefault) {
        $s = $isDefault ? '' : "_{$langCode}";
    ?>
      <div class="grid g3" style="gap:14px">
        <?php foreach ([1,2,3] as $i): ?>
        <div class="stat-box">
          <strong><?= $i ?>. İstatistik</strong>
          <?php if ($isDefault): ?>
          <div class="row" style="gap:6px;margin-bottom:8px">
            <div class="field" style="flex:1">
              <label>Sayı</label>
              <input type="text" name="corp_stat_<?= $i ?>_num" placeholder="35"
                     value="<?= e(setting("corp_stat_{$i}_num", ['35','40','100'][$i-1])) ?>">
            </div>
            <div class="field" style="width:60px">
              <label>Sembol</label>
              <input type="text" name="corp_stat_<?= $i ?>_sym" placeholder="+"
                     value="<?= e(setting("corp_stat_{$i}_sym", ['+','+','%'][$i-1])) ?>">
            </div>
          </div>
          <?php endif; ?>
          <div class="field">
            <label>Etiket (<?= strtoupper($langCode) ?>)</label>
            <input type="text" name="corp_stat_<?= $i ?>_lbl<?= $s ?>" placeholder="Etiket"
                   value="<?= e(setting("corp_stat_{$i}_lbl{$s}", ['YILLIK MİMARİ MİRAS','TAMAMLANAN İMZA ESER','TEKNİK MÜKEMMELLİK'][$i-1])) ?>">
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    <?php }); ?>

    <div class="save-row">
      <button class="btn"><i class="fa-solid fa-floppy-disk"></i> Sayaçları Kaydet</button>
    </div>
  </form>
</div>

<!-- ═══════════════════════════════════════════════════════════════════════════
     SECTION 3 — OFİSLER & HARİTA
     ══════════════════════════════════════════════════════════════════════════ -->
<div id="offices" class="section-card">
  <h2><i class="fa-solid fa-map-location-dot"></i> Ofisler & Harita Bilgileri</h2>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="section" value="offices">

    <div class="grid g2" style="gap:20px">

      <!-- Sakarya Ofisi -->
      <div class="office-box">
        <strong><i class="fa-solid fa-location-dot"></i> Sakarya Genel Merkez</strong>

        <?php echo lang_tabs('ab_sak', function($langCode, $isDefault) {
            $s = $isDefault ? '' : "_{$langCode}";
        ?>
          <?php if ($isDefault): ?>
          <div class="field" style="margin-bottom:8px">
            <label>Rozet Metni (tüm diller)</label>
            <input type="text" name="office_sakarya_badge" value="<?= e(setting('office_sakarya_badge','SAKARYA GENEL MERKEZ')) ?>">
          </div>
          <?php endif; ?>
          <div class="field" style="margin-bottom:8px">
            <label>Şehir (<?= strtoupper($langCode) ?>)</label>
            <input type="text" name="office_sakarya_city<?= $s ?>" value="<?= e(setting("office_sakarya_city{$s}",'Sakarya')) ?>">
          </div>
          <div class="field" style="margin-bottom:8px">
            <label>Ofis Başlığı (<?= strtoupper($langCode) ?>)</label>
            <input type="text" name="office_sakarya_title<?= $s ?>" value="<?= e(setting("office_sakarya_title{$s}",'Serdivan Genel Merkez')) ?>">
          </div>
          <div class="field" style="margin-bottom:8px">
            <label>Adres (<?= strtoupper($langCode) ?>)</label>
            <textarea name="office_sakarya_address<?= $s ?>" style="min-height:50px"><?= e(setting("office_sakarya_address{$s}",'Arabacıalanı Mh. Çark Cd. Benli Sk. No: 1/B Serdivan / SAKARYA')) ?></textarea>
          </div>
          <?php if ($isDefault): ?>
          <div class="field" style="margin-bottom:8px">
            <label>Haritada Gör Linki</label>
            <input type="url" name="office_sakarya_map_link" value="<?= e(setting('office_sakarya_map_link')) ?>">
          </div>
          <div class="field">
            <label>Google Maps Embed (iframe src URL)</label>
            <textarea name="office_sakarya_embed" style="min-height:55px"><?= e(setting('office_sakarya_embed')) ?></textarea>
            <small class="tab-hint">Google Maps → Paylaş → Haritayı yerleştir → iframe src kısmını buraya yapıştır.</small>
          </div>
          <?php endif; ?>
        <?php }); ?>
      </div>

      <!-- İstanbul Ofisi -->
      <div class="office-box">
        <strong><i class="fa-solid fa-location-dot"></i> İstanbul Bölge Ofisi</strong>

        <?php echo lang_tabs('ab_ist', function($langCode, $isDefault) {
            $s = $isDefault ? '' : "_{$langCode}";
        ?>
          <?php if ($isDefault): ?>
          <div class="field" style="margin-bottom:8px">
            <label>Rozet Metni (tüm diller)</label>
            <input type="text" name="office_istanbul_badge" value="<?= e(setting('office_istanbul_badge','İSTANBUL BÖLGE OFİSİ')) ?>">
          </div>
          <?php endif; ?>
          <div class="field" style="margin-bottom:8px">
            <label>Şehir (<?= strtoupper($langCode) ?>)</label>
            <input type="text" name="office_istanbul_city<?= $s ?>" value="<?= e(setting("office_istanbul_city{$s}",'İstanbul')) ?>">
          </div>
          <div class="field" style="margin-bottom:8px">
            <label>Ofis Başlığı (<?= strtoupper($langCode) ?>)</label>
            <input type="text" name="office_istanbul_title<?= $s ?>" value="<?= e(setting("office_istanbul_title{$s}",'Kadıköy Bölge Ofisi')) ?>">
          </div>
          <div class="field" style="margin-bottom:8px">
            <label>Adres (<?= strtoupper($langCode) ?>)</label>
            <textarea name="office_istanbul_address<?= $s ?>" style="min-height:50px"><?= e(setting("office_istanbul_address{$s}",'Caddebostan Mh. Cemil Topuzlu Cd. 58 /1/4 Kadıköy / İSTANBUL')) ?></textarea>
          </div>
          <?php if ($isDefault): ?>
          <div class="field" style="margin-bottom:8px">
            <label>Haritada Gör Linki</label>
            <input type="url" name="office_istanbul_map_link" value="<?= e(setting('office_istanbul_map_link')) ?>">
          </div>
          <div class="field">
            <label>Google Maps Embed (iframe src URL)</label>
            <textarea name="office_istanbul_embed" style="min-height:55px"><?= e(setting('office_istanbul_embed')) ?></textarea>
          </div>
          <?php endif; ?>
        <?php }); ?>
      </div>

    </div><!-- /grid -->

    <div class="save-row">
      <button class="btn"><i class="fa-solid fa-floppy-disk"></i> Ofisleri Kaydet</button>
    </div>
  </form>
</div>

<!-- Hızlı önizleme linki -->
<div style="text-align:right;margin-top:-16px;margin-bottom:32px">
  <a href="../kurumsal.html" target="_blank" class="btn" style="background:transparent;border:1px solid var(--gold);color:var(--gold)">
    <i class="fa-solid fa-eye"></i> Sayfayı Önizle
  </a>
</div>

<?php admin_footer(); ?>
