<?php
require_once __DIR__ . '/_layout.php';
require_perm('settings', 'view');
admin_readonly(!admin_can('settings', 'general') && !admin_can('settings', 'mail'));

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

    // Test e-postası gönderimi
    if (($_POST['action'] ?? '') === 'test_mail') {
        if (!admin_can('settings', 'mail')) admin_deny();
        $to = trim((string)($_POST['test_to'] ?? ''));
        if ($to === '') $to = setting('notify_email');
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            flash('Geçerli bir test e-posta adresi girin.', 'err');
        } else {
            $res = send_mail($to, 'BUYAPI - Test e-postası', "Bu mesaj BUYAPI yönetim panelinden gönderilen bir test e-postasıdır.\nTarih: " . date('d.m.Y H:i'));
            if ($res['ok']) flash('Test e-postası gönderildi: ' . $to . ' (' . ($res['transport'] ?? '') . ')');
            else flash('Test e-postası gönderilemedi: ' . ($res['response'] ?? 'bilinmeyen hata'), 'err');
        }
        redirect('settings.php');
    }

    if (!admin_can('settings', 'general')) admin_deny();

    // 1. İletişim & Sosyal Medya
    foreach ($FIELDS_CONTACT as $k => [$l, $t]) {
        // Alan formda yoksa mevcut değeri koru (kısmi gönderimlerde veri kaybını önler)
        if (!array_key_exists($k, $_POST)) continue;
        $val = trim((string)($_POST[$k] ?? ''));
        if ($t === 'url' && $val !== '' && !preg_match('#^https?://#i', $val)) { flash("$l: http(s):// ile başlamalı.", 'err'); redirect('settings.php'); }
        if ($t === 'email' && $val !== '' && !filter_var($val, FILTER_VALIDATE_EMAIL)) { flash("$l: geçersiz e-posta.", 'err'); redirect('settings.php'); }
        q('INSERT INTO site_settings (s_key,s_value) VALUES (?,?) ON DUPLICATE KEY UPDATE s_value=VALUES(s_value)', [$k, $val]);
    }

    // 1b. Analitik & Takip (Google Analytics 4, Meta Pixel)
    $trackRules = [
        'google_analytics_id' => ['Google Analytics Ölçüm Kimliği', '/^G-[A-Z0-9]{4,15}$/'],
        'meta_pixel_id'       => ['Meta Pixel Kimliği', '/^\d{8,20}$/'],
    ];
    foreach ($trackRules as $k => [$l, $re]) {
        if (!array_key_exists($k, $_POST)) continue;
        $val = strtoupper(trim((string)$_POST[$k]));
        if ($val !== '' && !preg_match($re, $val)) {
            flash($k === 'google_analytics_id' ? "$l geçersiz. Örnek: G-ABC123XYZ" : "$l geçersiz. Yalnızca rakamlardan oluşmalı (ör. 123456789012345).", 'err');
            redirect('settings.php');
        }
        q('INSERT INTO site_settings (s_key,s_value) VALUES (?,?) ON DUPLICATE KEY UPDATE s_value=VALUES(s_value)', [$k, $val]);
    }

    // 2. Ofis Bilgileri & Haritalar
    $officeKeys = [
        'office_sakarya_badge', 'office_sakarya_city', 'office_sakarya_title', 'office_sakarya_address', 'office_sakarya_map_link', 'office_sakarya_embed',
        'office_istanbul_badge', 'office_istanbul_city', 'office_istanbul_title', 'office_istanbul_address', 'office_istanbul_map_link', 'office_istanbul_embed'
    ];

    $pp = (int)($_POST['projects_per_page'] ?? 9);
    if ($pp < 3) $pp = 3;
    if ($pp > 48) $pp = 48;
    q('INSERT INTO site_settings (s_key,s_value) VALUES (?,?) ON DUPLICATE KEY UPDATE s_value=VALUES(s_value)', ['projects_per_page', (string)$pp]);

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

<form method="post" style="display:flex;flex-direction:column;gap:24px;"<?= admin_can('settings', 'general') ? '' : ' data-ro="1"' ?>><?= csrf_field() ?>

  <!-- ANALİTİK & TAKİP -->
  <div class="card">
    <h2><i class="fa-solid fa-chart-line" style="color:var(--gold)"></i> Google Analytics & Meta Pixel</h2>
    <div class="grid g2">
      <div class="field">
        <label>Google Analytics 4 Ölçüm Kimliği</label>
        <input type="text" name="google_analytics_id" value="<?= e(setting('google_analytics_id')) ?>" placeholder="G-XXXXXXXXXX" pattern="[Gg]-[A-Za-z0-9]{4,15}">
        <div class="hint">Boş bırakılırsa Google Analytics kodu sitede yüklenmez.</div>
      </div>
      <div class="field">
        <label>Meta (Facebook) Pixel Kimliği</label>
        <input type="text" name="meta_pixel_id" value="<?= e(setting('meta_pixel_id')) ?>" placeholder="123456789012345" pattern="\d{8,20}" inputmode="numeric">
        <div class="hint">Boş bırakılırsa Meta Pixel kodu sitede yüklenmez.</div>
      </div>
    </div>
  </div>

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

  <!-- 2. İLETİŞİM SAYFASI & FOOTER METİNLERİ (ÇOK DİLLİ) -->
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

  <!-- 3. PROJELER & KENTSEL DÖNÜŞÜM SAYFA METİNLERİ (ÇOK DİLLİ) -->
  <div class="card">
    <div class="row between" style="margin-bottom:14px">
      <h2><i class="fa-solid fa-layer-group" style="color:var(--gold)"></i> Projeler & Kentsel Dönüşüm Sayfa Metinleri</h2>
      <div class="field" style="margin-top:10px;max-width:280px">
        <label>Projeler Sayfası - Sayfa Başına Proje</label>
        <input type="number" name="projects_per_page" min="3" max="48" step="1" value="<?= e(setting('projects_per_page', '9')) ?>">
        <div class="hint">İzin verilen aralık: 3 - 48. Varsayılan: 9</div>
      </div>
      <span class="muted">/projeler.html banner alanı ve /kentsel-donusum.html bölüm başlıkları</span>
    </div>

    <?php echo lang_tabs('page_texts_tabs', function($langCode, $isDefault) { ?>
      <?php $suffix = $isDefault ? '' : "_{$langCode}"; ?>
      <h3 style="font-size:1rem;color:var(--gold);margin:6px 0 10px"><i class="fa-solid fa-folder-open"></i> Projeler Sayfası (<?= strtoupper($langCode) ?>)</h3>
      <div class="grid g2" style="margin-bottom:16px">
        <div class="field">
          <label>Banner Üst Etiketi</label>
          <input type="text" name="projects_banner_tag<?= $suffix ?>" value="<?= e(setting("projects_banner_tag{$suffix}")) ?>" placeholder="MİMARİ PORTFÖY">
        </div>
        <div class="field">
          <label>Sayfa Başlığı</label>
          <input type="text" name="projects_banner_title<?= $suffix ?>" value="<?= e(setting("projects_banner_title{$suffix}")) ?>" placeholder="Proje Kataloğu">
        </div>
      </div>

      <h3 style="font-size:1rem;color:var(--gold);margin:16px 0 10px"><i class="fa-solid fa-building-shield"></i> Kentsel Dönüşüm Sayfası (<?= strtoupper($langCode) ?>)</h3>
      <div class="grid g2" style="margin-bottom:10px">
        <div class="field">
          <label>Kanun Bölümü Etiketi</label>
          <input type="text" name="kd_law_tag<?= $suffix ?>" value="<?= e(setting("kd_law_tag{$suffix}")) ?>" placeholder="YASAL MEVZUAT VE DESTEKLER">
        </div>
        <div class="field">
          <label>Kanun Bölümü Başlığı</label>
          <input type="text" name="kd_law_title<?= $suffix ?>" value="<?= e(setting("kd_law_title{$suffix}")) ?>" placeholder="6306 Sayılı Kanun Kapsamında Haklarınız">
        </div>
      </div>
      <div class="grid g2" style="margin-bottom:10px">
        <div class="field">
          <label>Projeler Bölümü Etiketi</label>
          <input type="text" name="kd_projects_tag<?= $suffix ?>" value="<?= e(setting("kd_projects_tag{$suffix}")) ?>" placeholder="PRESTİJ VE DEĞER YARATAN İMZALAR">
        </div>
        <div class="field">
          <label>Projeler Bölümü Başlığı</label>
          <input type="text" name="kd_projects_title<?= $suffix ?>" value="<?= e(setting("kd_projects_title{$suffix}")) ?>" placeholder="Kentsel Dönüşüm Projelerimiz">
        </div>
      </div>
      <div class="grid g2" style="margin-bottom:10px">
        <div class="field">
          <label>Karşılaştırma Bölümü Etiketi</label>
          <input type="text" name="kd_compare_tag<?= $suffix ?>" value="<?= e(setting("kd_compare_tag{$suffix}")) ?>" placeholder="NEDEN YENİLEMELİSİNİZ?">
        </div>
        <div class="field">
          <label>Karşılaştırma Bölümü Başlığı</label>
          <input type="text" name="kd_compare_title<?= $suffix ?>" value="<?= e(setting("kd_compare_title{$suffix}")) ?>" placeholder="Eski Yapı vs. BUYAPI Kentsel Dönüşüm">
        </div>
      </div>
      <div class="grid g2" style="margin-bottom:10px">
        <div class="field">
          <label>SSS Bölümü Etiketi</label>
          <input type="text" name="kd_faq_tag<?= $suffix ?>" value="<?= e(setting("kd_faq_tag{$suffix}")) ?>" placeholder="MERAK EDİLENLER">
        </div>
        <div class="field">
          <label>SSS Bölümü Başlığı</label>
          <input type="text" name="kd_faq_title<?= $suffix ?>" value="<?= e(setting("kd_faq_title{$suffix}")) ?>" placeholder="Sıkça Sorulan Sorular">
        </div>
      </div>
      <div class="grid g2" style="margin-bottom:14px">
        <div class="field">
          <label>Başvuru Formu Etiketi</label>
          <input type="text" name="kd_form_tag<?= $suffix ?>" value="<?= e(setting("kd_form_tag{$suffix}")) ?>" placeholder="KENTSEL DÖNÜŞÜM ÖN BAŞVURU">
        </div>
        <div class="field">
          <label>Başvuru Formu Başlığı</label>
          <input type="text" name="kd_form_title<?= $suffix ?>" value="<?= e(setting("kd_form_title{$suffix}")) ?>" placeholder="Binanızı Yenilemek İçin İlk Adımı Atın">
        </div>
      </div>
    <?php }); ?>
    <p class="hint">Boş bırakılan alanlarda sayfanın kendi varsayılan metni görünür. Kentsel dönüşüm hero slaytları, istatistikler ve SSS kayıtları ayrı bölümlerden yönetilir.</p>
  </div>

  <!-- 4. OFİSLER & HARİTA DETAYLARI -->
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

<?php
$mailLog = [];
try { $mailLog = q('SELECT * FROM mail_log ORDER BY id DESC LIMIT 8')->fetchAll(); } catch (Throwable $ex) {}
?>
<?php if (admin_can('settings', 'mail')): ?>
<div class="card" style="margin-top:24px">
  <div class="row between" style="margin-bottom:14px">
    <h2><i class="fa-solid fa-paper-plane" style="color:var(--gold)"></i> Bildirim E-postası Testi</h2>
    <span class="muted">Form mesajları bu adrese iletilir</span>
  </div>
  <form method="post" class="row" style="gap:10px;align-items:flex-end;flex-wrap:wrap">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="test_mail">
    <div class="field" style="flex:1;min-width:260px;margin:0">
      <label>Test Alıcısı</label>
      <input type="email" name="test_to" value="<?= e(setting('notify_email')) ?>" placeholder="ornek@alanadi.com">
    </div>
    <button class="btn"><i class="fa-solid fa-paper-plane"></i> Test Maili Gönder</button>
  </form>
  <table class="resp" style="margin-top:16px">
    <thead><tr><th>Tarih</th><th>Alıcı</th><th>Durum</th><th>Kanall</th><th>Yanıt</th></tr></thead>
    <tbody>
    <?php foreach ($mailLog as $ml): ?>
      <tr>
        <td class="muted"><?= e(date('d.m.Y H:i', strtotime($ml['created_at']))) ?></td>
        <td><?= e($ml['to_email']) ?></td>
        <td><span class="pill" style="background:<?= $ml['status'] === 'sent' ? 'rgba(80,200,120,0.18)' : 'rgba(220,80,80,0.18)' ?>;color:<?= $ml['status'] === 'sent' ? '#6ede9b' : '#ff8f8f' ?>"><?= $ml['status'] === 'sent' ? 'Gönderildi' : 'Başarısız' ?></span></td>
        <td class="muted"><?= e($ml['transport']) ?></td>
        <td class="muted" style="max-width:320px;overflow:hidden;text-overflow:ellipsis"><?= e($ml['response']) ?></td>
      </tr>
    <?php endforeach; if (!$mailLog): ?>
      <tr><td colspan="5" class="muted" style="text-align:center;padding:20px">Henüz e-posta kaydı yok.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<?php admin_footer(); ?>
