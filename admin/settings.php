<?php
require_once __DIR__ . '/_layout.php';
require_login();

$activeLangs = get_languages();

$FIELDS_CONTACT = [
    'phone'        => ['Telefon', 'text'],
    'whatsapp'     => ['WhatsApp Numarası (uluslararası, + olmadan)', 'text'],
    'email'        => ['Genel E-posta', 'email'],
    'instagram'    => ['Instagram Adresi', 'url'],
    'facebook'     => ['Facebook Adresi', 'url'],
    'notify_email' => ['Yeni Mesaj Bildirimi Gönderilecek E-posta', 'email'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    // 1. İletişim & Sosyal Medya
    foreach ($FIELDS_CONTACT as $k => [$l, $t]) {
        $val = trim((string)($_POST[$k] ?? ''));
        if ($t === 'url' && $val !== '' && !preg_match('#^https?://#i', $val)) { flash("$l: http(s):// ile başlamalı.", 'err'); redirect('settings.php'); }
        if ($t === 'email' && $val !== '' && !filter_var($val, FILTER_VALIDATE_EMAIL)) { flash("$l: geçersiz e-posta.", 'err'); redirect('settings.php'); }
        q('INSERT INTO site_settings (s_key,s_value) VALUES (?,?) ON DUPLICATE KEY UPDATE s_value=VALUES(s_value)', [$k, $val]);
    }

    // 2. Kurumsal Sayfa Metinleri & İstatistikleri (Dillere Göre)
    $corpKeys = [
        'corp_banner_tag', 'corp_banner_title',
        'corp_section_tag', 'corp_title', 'corp_lead', 'corp_body',
        'corp_stat_1_num', 'corp_stat_1_sym', 'corp_stat_1_lbl',
        'corp_stat_2_num', 'corp_stat_2_sym', 'corp_stat_2_lbl',
        'corp_stat_3_num', 'corp_stat_3_sym', 'corp_stat_3_lbl',
    ];

    foreach ($corpKeys as $ck) {
        if (isset($_POST[$ck])) {
            q('INSERT INTO site_settings (s_key,s_value) VALUES (?,?) ON DUPLICATE KEY UPDATE s_value=VALUES(s_value)', [$ck, trim($_POST[$ck])]);
        }
        foreach ($activeLangs as $al) {
            $langKey = "{$ck}_{$al['code']}";
            if (isset($_POST[$langKey])) {
                q('INSERT INTO site_settings (s_key,s_value) VALUES (?,?) ON DUPLICATE KEY UPDATE s_value=VALUES(s_value)', [$langKey, trim($_POST[$langKey])]);
            }
        }
    }

    // 3. Ana Sayfa İçerikleri (Hero slaytları + öne çıkan bölümü)
    $homeKeys = ['home_featured_tag', 'home_featured_title', 'home_cta_text'];
    for ($i = 1; $i <= 4; $i++) {
        $homeKeys[] = "home_slide_{$i}_tag";
        $homeKeys[] = "home_slide_{$i}_title";
        $homeKeys[] = "home_slide_{$i}_desc";
        $homeKeys[] = "home_slide_{$i}_image"; // dil bağımsız
    }

    foreach ($homeKeys as $hk) {
        if (!isset($_POST[$hk])) continue;
        q('INSERT INTO site_settings (s_key,s_value) VALUES (?,?) ON DUPLICATE KEY UPDATE s_value=VALUES(s_value)', [$hk, trim($_POST[$hk])]);
        if (str_ends_with($hk, '_image')) continue; // görseller dile göre çoğaltılmaz
        foreach ($activeLangs as $al) {
            $langKey = "{$hk}_{$al['code']}";
            if (isset($_POST[$langKey])) {
                q('INSERT INTO site_settings (s_key,s_value) VALUES (?,?) ON DUPLICATE KEY UPDATE s_value=VALUES(s_value)', [$langKey, trim($_POST[$langKey])]);
            }
        }
    }

    // 3b. İletişim Sayfası & Footer Metinleri (çok dilli)
    $textKeys = [
        'contact_banner_tag', 'contact_page_title', 'contact_form_title', 'contact_form_desc',
        'footer_section_tag', 'footer_cta_title', 'footer_cta_desc', 'footer_cta_btn_text',
        'footer_brand_desc', 'footer_copy_text', 'footer_tagline',
    ];
    foreach ($textKeys as $tk) {
        if (isset($_POST[$tk])) {
            q('INSERT INTO site_settings (s_key,s_value) VALUES (?,?) ON DUPLICATE KEY UPDATE s_value=VALUES(s_value)', [$tk, trim($_POST[$tk])]);
        }
        foreach ($activeLangs as $al) {
            $langKey = "{$tk}_{$al['code']}";
            if (isset($_POST[$langKey])) {
                q('INSERT INTO site_settings (s_key,s_value) VALUES (?,?) ON DUPLICATE KEY UPDATE s_value=VALUES(s_value)', [$langKey, trim($_POST[$langKey])]);
            }
        }
    }
    if (isset($_POST['footer_cta_btn_link'])) {
        q('INSERT INTO site_settings (s_key,s_value) VALUES (?,?) ON DUPLICATE KEY UPDATE s_value=VALUES(s_value)', ['footer_cta_btn_link', trim($_POST['footer_cta_btn_link'])]);
    }

    // 4. Ofis Bilgileri & Haritalar
    $officeKeys = [
        'office_sakarya_badge', 'office_sakarya_city', 'office_sakarya_title', 'office_sakarya_address', 'office_sakarya_map_link', 'office_sakarya_embed',
        'office_istanbul_badge', 'office_istanbul_city', 'office_istanbul_title', 'office_istanbul_address', 'office_istanbul_map_link', 'office_istanbul_embed'
    ];

    foreach ($officeKeys as $ok) {
        if (isset($_POST[$ok])) {
            q('INSERT INTO site_settings (s_key,s_value) VALUES (?,?) ON DUPLICATE KEY UPDATE s_value=VALUES(s_value)', [$ok, trim($_POST[$ok])]);
        }
        foreach ($activeLangs as $al) {
            $langKey = "{$ok}_{$al['code']}";
            if (isset($_POST[$langKey])) {
                q('INSERT INTO site_settings (s_key,s_value) VALUES (?,?) ON DUPLICATE KEY UPDATE s_value=VALUES(s_value)', [$langKey, trim($_POST[$langKey])]);
            }
        }
    }

    flash('Tüm site ve sayfa ayarları başarıyla kaydedildi.');
    redirect('settings.php');
}

admin_header('Site & Sayfa Ayarları', 'settings');
?>

<form method="post" style="display:flex;flex-direction:column;gap:24px;"><?= csrf_field() ?>

  <!-- 1. GENEL İLETİŞİM & SOSYAL MEDYA -->
  <div class="card">
    <h2><i class="fa-solid fa-headset" style="color:var(--gold)"></i> İletişim & Sosyal Medya</h2>
    <div class="grid g2">
      <?php foreach ($FIELDS_CONTACT as $k => [$l, $t]): ?>
        <div class="field">
          <label><?= e($l) ?></label>
          <input type="<?= $t === 'url' ? 'url' : ($t === 'email' ? 'email' : 'text') ?>" name="<?= e($k) ?>" value="<?= e(setting($k)) ?>">
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- 2. KURUMSAL SAYFASI YÖNETİMİ (ÇOK DİLLİ) -->
  <div class="card">
    <div class="row between" style="margin-bottom:14px">
      <h2><i class="fa-solid fa-landmark" style="color:var(--gold)"></i> Kurumsal Sayfası İçerik & İstatistik Yönetimi</h2>
      <span class="muted">/kurumsal.html sayfası metinleri ve istatistikleri</span>
    </div>

    <?php echo lang_tabs('corp_page_tabs', function($langCode, $isDefault) { ?>
      <?php $suffix = $isDefault ? '' : "_{$langCode}"; ?>
      <div class="grid g2" style="margin-bottom:14px">
        <div class="field">
          <label>Banner Üst Etiketi (<?= strtoupper($langCode) ?>)</label>
          <input type="text" name="corp_banner_tag<?= $suffix ?>" value="<?= e(setting("corp_banner_tag{$suffix}", 'BUYAPI MİMARİSİ')) ?>">
        </div>
        <div class="field">
          <label>Banner Başlığı (<?= strtoupper($langCode) ?>)</label>
          <input type="text" name="corp_banner_title<?= $suffix ?>" value="<?= e(setting("corp_banner_title{$suffix}", 'Kurumsal Felsefemiz')) ?>">
        </div>
      </div>

      <div class="field" style="margin-bottom:14px">
        <label>Bölüm Üst Tag (<?= strtoupper($langCode) ?>)</label>
        <input type="text" name="corp_section_tag<?= $suffix ?>" value="<?= e(setting("corp_section_tag{$suffix}", 'GELECEĞİ İNŞA EDİYORUZ')) ?>">
      </div>

      <div class="field" style="margin-bottom:14px">
        <label>Ana Vurgulu Başlık (<?= strtoupper($langCode) ?>)</label>
        <input type="text" name="corp_title<?= $suffix ?>" value="<?= e(setting("corp_title{$suffix}", 'Sıradışı ve prestijli projeleri hayata geçiriyoruz.')) ?>">
      </div>

      <div class="field" style="margin-bottom:14px">
        <label>Öne Çıkan Spot Metin (Lead Text) (<?= strtoupper($langCode) ?>)</label>
        <textarea name="corp_lead<?= $suffix ?>" style="min-height:60px"><?= e(setting("corp_lead{$suffix}")) ?></textarea>
      </div>

      <div class="field" style="margin-bottom:14px">
        <label>Detaylı Paragraf Metni (<?= strtoupper($langCode) ?>)</label>
        <textarea name="corp_body<?= $suffix ?>" style="min-height:90px"><?= e(setting("corp_body{$suffix}")) ?></textarea>
      </div>

      <h3 style="font-size:1rem;color:var(--gold);margin:16px 0 10px"><i class="fa-solid fa-chart-line"></i> Kurumsal İstatistik Sayaçları (<?= strtoupper($langCode) ?>)</h3>
      <div class="grid g3" style="gap:14px">
        <div style="background:rgba(255,255,255,0.02);padding:12px;border-radius:6px;border:1px solid var(--line)">
          <strong>1. İstatistik</strong>
          <?php if ($isDefault): ?>
          <div class="row" style="gap:6px;margin:8px 0">
            <input type="text" name="corp_stat_1_num" placeholder="35" value="<?= e(setting('corp_stat_1_num', '35')) ?>" style="width:70px">
            <input type="text" name="corp_stat_1_sym" placeholder="+" value="<?= e(setting('corp_stat_1_sym', '+')) ?>" style="width:50px">
          </div>
          <?php endif; ?>
          <input type="text" name="corp_stat_1_lbl<?= $suffix ?>" placeholder="Etiket" value="<?= e(setting("corp_stat_1_lbl{$suffix}", 'YILLIK MİMARİ MİRAS')) ?>">
        </div>

        <div style="background:rgba(255,255,255,0.02);padding:12px;border-radius:6px;border:1px solid var(--line)">
          <strong>2. İstatistik</strong>
          <?php if ($isDefault): ?>
          <div class="row" style="gap:6px;margin:8px 0">
            <input type="text" name="corp_stat_2_num" placeholder="40" value="<?= e(setting('corp_stat_2_num', '40')) ?>" style="width:70px">
            <input type="text" name="corp_stat_2_sym" placeholder="+" value="<?= e(setting('corp_stat_2_sym', '+')) ?>" style="width:50px">
          </div>
          <?php endif; ?>
          <input type="text" name="corp_stat_2_lbl<?= $suffix ?>" placeholder="Etiket" value="<?= e(setting("corp_stat_2_lbl{$suffix}", 'TAMAMLANAN İMZA ESER')) ?>">
        </div>

        <div style="background:rgba(255,255,255,0.02);padding:12px;border-radius:6px;border:1px solid var(--line)">
          <strong>3. İstatistik</strong>
          <?php if ($isDefault): ?>
          <div class="row" style="gap:6px;margin:8px 0">
            <input type="text" name="corp_stat_3_num" placeholder="100" value="<?= e(setting('corp_stat_3_num', '100')) ?>" style="width:70px">
            <input type="text" name="corp_stat_3_sym" placeholder="%" value="<?= e(setting('corp_stat_3_sym', '%')) ?>" style="width:50px">
          </div>
          <?php endif; ?>
          <input type="text" name="corp_stat_3_lbl<?= $suffix ?>" placeholder="Etiket" value="<?= e(setting("corp_stat_3_lbl{$suffix}", 'TEKNİK MÜKEMMELLİK')) ?>">
        </div>
      </div>
    <?php }); ?>
  </div>

  <!-- 2b. ANA SAYFA İÇERİK YÖNETİMİ (ÇOK DİLLİ) -->
  <div class="card">
    <div class="row between" style="margin-bottom:14px">
      <h2><i class="fa-solid fa-house" style="color:var(--gold)"></i> Ana Sayfa İçerik Yönetimi</h2>
      <span class="muted">/index.html hero slaytları ve öne çıkan projeler başlıkları</span>
    </div>

    <?php echo lang_tabs('home_page_tabs', function($langCode, $isDefault) { ?>
      <?php $suffix = $isDefault ? '' : "_{$langCode}"; ?>
      <?php for ($i = 1; $i <= 4; $i++): ?>
        <h3 style="font-size:1rem;color:var(--gold);margin:16px 0 10px"><i class="fa-solid fa-images"></i> Hero Slayt <?= $i ?> (<?= strtoupper($langCode) ?>)</h3>
        <div class="grid g2" style="margin-bottom:10px">
          <div class="field">
            <label>Üst Etiket</label>
            <input type="text" name="home_slide_<?= $i ?>_tag<?= $suffix ?>" value="<?= e(setting("home_slide_{$i}_tag{$suffix}")) ?>" placeholder="SERDİVAN / SAKARYA">
          </div>
          <div class="field">
            <label>Başlık</label>
            <input type="text" name="home_slide_<?= $i ?>_title<?= $suffix ?>" value="<?= e(setting("home_slide_{$i}_title{$suffix}")) ?>" placeholder="BUYAPI INTENSE">
          </div>
        </div>
        <div class="field" style="margin-bottom:10px">
          <label>Açıklama</label>
          <textarea name="home_slide_<?= $i ?>_desc<?= $suffix ?>" style="min-height:50px"><?= e(setting("home_slide_{$i}_desc{$suffix}")) ?></textarea>
        </div>
        <?php if ($isDefault): ?>
          <div class="field" style="margin-bottom:14px">
            <label>Arka Plan Görseli (dosya yolu veya tam adres)</label>
            <input type="text" name="home_slide_<?= $i ?>_image" value="<?= e(setting("home_slide_{$i}_image")) ?>" placeholder="images/banner-1.png">
          </div>
        <?php endif; ?>
      <?php endfor; ?>

      <h3 style="font-size:1rem;color:var(--gold);margin:16px 0 10px"><i class="fa-solid fa-star"></i> Öne Çıkan Projeler Bölümü (<?= strtoupper($langCode) ?>)</h3>
      <div class="grid g2" style="margin-bottom:10px">
        <div class="field">
          <label>Bölüm Üst Etiketi</label>
          <input type="text" name="home_featured_tag<?= $suffix ?>" value="<?= e(setting("home_featured_tag{$suffix}")) ?>" placeholder="Öne Çıkan">
        </div>
        <div class="field">
          <label>Bölüm Başlığı</label>
          <input type="text" name="home_featured_title<?= $suffix ?>" value="<?= e(setting("home_featured_title{$suffix}")) ?>" placeholder="Satıştaki Projelerimiz">
        </div>
      </div>
      <div class="field" style="margin-bottom:14px">
        <label>Tüm Projeler Butonu Metni</label>
        <input type="text" name="home_cta_text<?= $suffix ?>" value="<?= e(setting("home_cta_text{$suffix}")) ?>" placeholder="TÜM PROJELERİ İNCELEYİN">
      </div>
    <?php }); ?>
    <p class="hint">Boş bırakılan alanlarda sayfanın kendi varsayılan metni görünür. Görsel alanı yalnızca Türkçe sekmesinde bulunur ve tüm dillerde geçerlidir.</p>
  </div>

  <!-- 2c. İLETİŞİM SAYFASI & FOOTER METİNLERİ (ÇOK DİLLİ) -->
  <div class="card">
    <div class="row between" style="margin-bottom:14px">
      <h2><i class="fa-solid fa-envelope-open-text" style="color:var(--gold)"></i> İletişim Sayfası & Footer Metinleri</h2>
      <span class="muted">/iletisim.html başlıkları ve tüm sayfalardaki alt bilgi (footer) alanı</span>
    </div>

    <?php echo lang_tabs('contact_footer_tabs', function($langCode, $isDefault) { ?>
      <?php $suffix = $isDefault ? '' : "_{$langCode}"; ?>
      <h3 style="font-size:1rem;color:var(--gold);margin:6px 0 10px"><i class="fa-solid fa-address-book"></i> İletişim Sayfası (<?= strtoupper($langCode) ?>)</h3>
      <div class="grid g2" style="margin-bottom:10px">
        <div class="field">
          <label>Banner Üst Etiketi</label>
          <input type="text" name="contact_banner_tag<?= $suffix ?>" value="<?= e(setting("contact_banner_tag{$suffix}")) ?>" placeholder="BİZİMLE İLETİŞİME GEÇİN">
        </div>
        <div class="field">
          <label>Sayfa Başlığı</label>
          <input type="text" name="contact_page_title<?= $suffix ?>" value="<?= e(setting("contact_page_title{$suffix}")) ?>" placeholder="İletişim & Ofislerimiz">
        </div>
      </div>
      <div class="field" style="margin-bottom:10px">
        <label>Form Bölümü Başlığı</label>
        <input type="text" name="contact_form_title<?= $suffix ?>" value="<?= e(setting("contact_form_title{$suffix}")) ?>" placeholder="Projenizi Birlikte Başlatalım">
      </div>
      <div class="field" style="margin-bottom:14px">
        <label>Form Bölümü Açıklaması</label>
        <textarea name="contact_form_desc<?= $suffix ?>" style="min-height:50px" placeholder="Hayalinizdeki konut, iş merkezi veya mimari yatırımlarınız için ekibimize mesaj bırakabilirsiniz."><?= e(setting("contact_form_desc{$suffix}")) ?></textarea>
      </div>

      <h3 style="font-size:1rem;color:var(--gold);margin:16px 0 10px"><i class="fa-solid fa-shoe-prints"></i> Footer (<?= strtoupper($langCode) ?>)</h3>
      <div class="grid g2" style="margin-bottom:10px">
        <div class="field">
          <label>Üst Etiket</label>
          <input type="text" name="footer_section_tag<?= $suffix ?>" value="<?= e(setting("footer_section_tag{$suffix}")) ?>" placeholder="PROJE & İŞ BİRLİĞİ">
        </div>
        <div class="field">
          <label>Buton Metni</label>
          <input type="text" name="footer_cta_btn_text<?= $suffix ?>" value="<?= e(setting("footer_cta_btn_text{$suffix}")) ?>" placeholder="Projeyi Başlatın">
        </div>
      </div>
      <div class="field" style="margin-bottom:10px">
        <label>Büyük Başlık</label>
        <input type="text" name="footer_cta_title<?= $suffix ?>" value="<?= e(setting("footer_cta_title{$suffix}")) ?>">
      </div>
      <div class="field" style="margin-bottom:10px">
        <label>Başlık Altı Açıklama</label>
        <textarea name="footer_cta_desc<?= $suffix ?>" style="min-height:50px"><?= e(setting("footer_cta_desc{$suffix}")) ?></textarea>
      </div>
      <div class="field" style="margin-bottom:10px">
        <label>Marka Açıklaması (logo altı)</label>
        <textarea name="footer_brand_desc<?= $suffix ?>" style="min-height:50px"><?= e(setting("footer_brand_desc{$suffix}")) ?></textarea>
      </div>
      <div class="grid g2" style="margin-bottom:14px">
        <div class="field">
          <label>Telif Metni</label>
          <input type="text" name="footer_copy_text<?= $suffix ?>" value="<?= e(setting("footer_copy_text{$suffix}")) ?>" placeholder="© 2026 BUYAPI İNŞAAT A.Ş. • TÜM HAKLARI SAKLIDIR.">
        </div>
        <div class="field">
          <label>Slogan (en alt satır)</label>
          <input type="text" name="footer_tagline<?= $suffix ?>" value="<?= e(setting("footer_tagline{$suffix}")) ?>" placeholder="ARCHITECTURAL PRESTIGE & ENGINEERING">
        </div>
      </div>
      <?php if ($isDefault): ?>
        <div class="field" style="margin-bottom:14px">
          <label>Footer Buton Bağlantısı (tüm dillerde geçerli)</label>
          <input type="text" name="footer_cta_btn_link" value="<?= e(setting('footer_cta_btn_link')) ?>" placeholder="iletisim.html">
        </div>
      <?php endif; ?>
    <?php }); ?>
    <p class="hint">Boş bırakılan alanlarda sayfanın kendi varsayılan metni görünür. Telefon, e-posta ve sosyal medya bağlantıları yukarıdaki genel ayarlardan gelir.</p>
  </div>

  <!-- 3. OFİSLER & HARİTA DETAYLARI -->
  <div class="card">
    <div class="row between" style="margin-bottom:14px">
      <h2><i class="fa-solid fa-map-location-dot" style="color:var(--gold)"></i> Ofisler & Harita Bilgileri</h2>
      <span class="muted">Sakarya ve İstanbul ofis kartları, Google Maps embed bağlantıları</span>
    </div>

    <div class="grid g2" style="gap:20px">
      <!-- Sakarya -->
      <div style="background:rgba(255,255,255,0.02);padding:16px;border-radius:8px;border:1px solid var(--line)">
        <strong style="color:var(--gold);display:block;margin-bottom:10px">Sakarya Genel Merkez</strong>
        <div class="field" style="margin-bottom:8px">
          <label>Rozet Metni</label>
          <input type="text" name="office_sakarya_badge" value="<?= e(setting('office_sakarya_badge', 'SAKARYA GENEL MERKEZ')) ?>">
        </div>
        <div class="field" style="margin-bottom:8px">
          <label>Ofis Başlığı</label>
          <input type="text" name="office_sakarya_title" value="<?= e(setting('office_sakarya_title', 'Serdivan Genel Merkez')) ?>">
        </div>
        <div class="field" style="margin-bottom:8px">
          <label>Adres</label>
          <textarea name="office_sakarya_address" style="min-height:50px"><?= e(setting('office_sakarya_address', 'Arabacıalanı Mh. Çark Cd. Benli Sk. No: 1/B Serdivan / SAKARYA')) ?></textarea>
        </div>
        <div class="field" style="margin-bottom:8px">
          <label>Haritada Gör Linki</label>
          <input type="url" name="office_sakarya_map_link" value="<?= e(setting('office_sakarya_map_link')) ?>">
        </div>
        <div class="field">
          <label>Google Maps Embed (iframe src)</label>
          <textarea name="office_sakarya_embed" style="min-height:60px"><?= e(setting('office_sakarya_embed')) ?></textarea>
        </div>
      </div>

      <!-- İstanbul -->
      <div style="background:rgba(255,255,255,0.02);padding:16px;border-radius:8px;border:1px solid var(--line)">
        <strong style="color:var(--gold);display:block;margin-bottom:10px">İstanbul Bölge Ofisi</strong>
        <div class="field" style="margin-bottom:8px">
          <label>Rozet Metni</label>
          <input type="text" name="office_istanbul_badge" value="<?= e(setting('office_istanbul_badge', 'İSTANBUL BÖLGE OFİSİ')) ?>">
        </div>
        <div class="field" style="margin-bottom:8px">
          <label>Ofis Başlığı</label>
          <input type="text" name="office_istanbul_title" value="<?= e(setting('office_istanbul_title', 'Kadıköy Bölge Ofisi')) ?>">
        </div>
        <div class="field" style="margin-bottom:8px">
          <label>Adres</label>
          <textarea name="office_istanbul_address" style="min-height:50px"><?= e(setting('office_istanbul_address', 'Caddebostan Mh. Cemil Topuzlu Cd. 58 /1/4 Kadıköy / İSTANBUL')) ?></textarea>
        </div>
        <div class="field" style="margin-bottom:8px">
          <label>Haritada Gör Linki</label>
          <input type="url" name="office_istanbul_map_link" value="<?= e(setting('office_istanbul_map_link')) ?>">
        </div>
        <div class="field">
          <label>Google Maps Embed (iframe src)</label>
          <textarea name="office_istanbul_embed" style="min-height:60px"><?= e(setting('office_istanbul_embed')) ?></textarea>
        </div>
      </div>
    </div>
  </div>

  <div>
    <button class="btn"><i class="fa-solid fa-floppy-disk"></i> Tüm Ayarları Kaydet</button>
  </div>
</form>

<?php admin_footer(); ?>
