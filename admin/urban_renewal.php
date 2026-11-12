<?php
require_once __DIR__ . '/_layout.php';
require_login();

$activeLangs = get_languages();

// 1. POST İşlemleri
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $act = $_POST['action'] ?? '';

    // Slayt Ekle
    if ($act === 'add_slide') {
        $img = upload_file('slide_file', ['jpg','jpeg','png','webp'], 'urban_renewal');
        if (!$img && !empty($_POST['slide_path'])) {
            $img = trim($_POST['slide_path']);
        }

        if ($img) {
            $title = trim($_POST['title_tr'] ?? '');
            $subtitle = trim($_POST['subtitle_tr'] ?? '');
            $max = (int)q('SELECT COALESCE(MAX(sort_order),0) FROM urban_renewal_slides')->fetchColumn();
            q('INSERT INTO urban_renewal_slides (image_url, title_tr, subtitle_tr, sort_order, is_active) VALUES (?, ?, ?, ?, 1)',
              [$img, $title, $subtitle, $max + 1]);
            flash('Yeni arka plan slider görseli eklendi.');
        } else {
            flash('Görsel seçilmedi.', 'err');
        }
        redirect('urban_renewal.php#slides');
    }

    // Slayt Sil
    if ($act === 'del_slide') {
        $id = (int)($_POST['id'] ?? 0);
        $s = q('SELECT * FROM urban_renewal_slides WHERE id=?', [$id])->fetch();
        if ($s) {
            delete_upload($s['image_url']);
            q('DELETE FROM urban_renewal_slides WHERE id=?', [$id]);
            flash('Slider görseli silindi.');
        }
        redirect('urban_renewal.php#slides');
    }

    // SSS (FAQ) Ekle veya Güncelle
    if ($act === 'save_faq') {
        $faqId = (int)($_POST['faq_id'] ?? 0);
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        $qTr = trim($_POST['question_tr'] ?? '');
        $aTr = trim($_POST['answer_tr'] ?? '');

        if ($qTr === '' || $aTr === '') {
            flash('Türkçe soru ve cevap zorunludur.', 'err');
            redirect('urban_renewal.php#faqs');
        }

        // Dinamik diller için alanları topla
        $fields = ['sort_order' => $sortOrder, 'is_active' => $isActive];
        foreach ($activeLangs as $l) {
            $code = $l['code'];
            $fields["question_{$code}"] = trim($_POST["question_{$code}"] ?? '');
            $fields["answer_{$code}"] = trim($_POST["answer_{$code}"] ?? '');
        }

        if ($faqId > 0) {
            // Güncelle
            $setParts = [];
            $vals = [];
            foreach ($fields as $col => $v) {
                $setParts[] = "{$col} = ?";
                $vals[] = $v;
            }
            $vals[] = $faqId;
            q('UPDATE urban_renewal_faqs SET ' . implode(', ', $setParts) . ' WHERE id=?', $vals);
            flash('Soru ve cevap başarıyla güncellendi.');
        } else {
            // Yeni Ekle
            $cols = array_keys($fields);
            $placeholders = implode(',', array_fill(0, count($cols), '?'));
            q('INSERT INTO urban_renewal_faqs (' . implode(',', $cols) . ') VALUES (' . $placeholders . ')', array_values($fields));
            flash('Yeni sıkça sorulan soru eklendi.');
        }
        redirect('urban_renewal.php#faqs');
    }

    // SSS Sil
    if ($act === 'del_faq') {
        $id = (int)($_POST['id'] ?? 0);
        q('DELETE FROM urban_renewal_faqs WHERE id=?', [$id]);
        flash('Soru silindi.');
        redirect('urban_renewal.php#faqs');
    }

    // SSS Durum Değiştir (Aktif / Pasif)
    if ($act === 'toggle_faq') {
        $id = (int)($_POST['id'] ?? 0);
        q('UPDATE urban_renewal_faqs SET is_active = IF(is_active=1,0,1) WHERE id=?', [$id]);
        flash('Soru durumu güncellendi.');
        redirect('urban_renewal.php#faqs');
    }

    // Sayfa Metinleri & Ayarlarını Kaydet (Hero, İstatistikler, Yasa, Avantajlar)
    if ($act === 'save_settings') {
        $fields = [
            'kd_hero_badge', 'kd_hero_title', 'kd_hero_desc',
            'kd_stat_1_num', 'kd_stat_1_lbl', 'kd_stat_2_num', 'kd_stat_2_lbl',
            'kd_stat_3_num', 'kd_stat_3_lbl', 'kd_stat_4_num', 'kd_stat_4_lbl',
            'kd_law_title', 'kd_law_info', 'kd_contact_phone', 'kd_contact_email',
            'kd_benefit_1_title', 'kd_benefit_1_desc',
            'kd_benefit_2_title', 'kd_benefit_2_desc',
            'kd_benefit_3_title', 'kd_benefit_3_desc',
            'kd_benefit_4_title', 'kd_benefit_4_desc'
        ];

        foreach ($fields as $key) {
            // Ana alan veya Türkçe
            if (isset($_POST[$key])) {
                $val = trim((string)$_POST[$key]);
                q('INSERT INTO site_settings (s_key, s_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE s_value=VALUES(s_value)', [$key, $val]);
            }
            // Dil bazlı varyasyonlar (kd_hero_title_en, kd_hero_title_ar vb.)
            foreach ($activeLangs as $l) {
                $langKey = "{$key}_{$l['code']}";
                if (isset($_POST[$langKey])) {
                    $val = trim((string)$_POST[$langKey]);
                    q('INSERT INTO site_settings (s_key, s_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE s_value=VALUES(s_value)', [$langKey, $val]);
                }
            }
        }

        flash('Kentsel Dönüşüm sayfa metinleri ve avantaj kartları başarıyla güncellendi.');
        redirect('urban_renewal.php');
    }
}

admin_header('Kentsel Dönüşüm Yönetimi', 'urban_renewal');

$slides = q('SELECT * FROM urban_renewal_slides ORDER BY sort_order ASC, id ASC')->fetchAll();
$faqs = q('SELECT * FROM urban_renewal_faqs ORDER BY sort_order ASC, id ASC')->fetchAll();
$s = fn($k, $d = '') => e(setting($k, $d));

// Düzenleme modunda olan FAQ var mı?
$editFaq = null;
if (!empty($_GET['edit_faq'])) {
    $efId = (int)$_GET['edit_faq'];
    $editFaq = q('SELECT * FROM urban_renewal_faqs WHERE id=?', [$efId])->fetch();
}
?>

<div class="row between" style="margin-bottom:20px">
  <div>
    <h1 style="margin:0"><i class="fa-solid fa-building-shield" style="color:var(--gold)"></i> Kentsel Dönüşüm Sayfa Yönetimi</h1>
    <span class="muted">Hero slider, 6306 sayılı kanun rehberi, avantaj kartları ve sıkça sorulan soruları tam yönetin.</span>
  </div>
  <a class="btn ghost" href="../kentsel-donusum.html" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> Canlı Sayfayı Gör</a>
</div>

<!-- 1. ÜST KISIM: SLIDER & HERO METİNLERİ (2 KOLON) -->
<div class="grid g2" style="align-items:start;margin-bottom:25px">

  <!-- A. Arka Plan Slider Görselleri (Sürükle - Bırak Destekli) -->
  <div class="card" id="slides">
    <div class="row between" style="margin-bottom:12px">
      <h2 style="margin:0"><i class="fa-solid fa-panorama" style="color:var(--gold)"></i> Tam Ekran Hero Slider (<?= count($slides) ?>)</h2>
      <span class="muted" style="font-size:0.85rem">100vh Slayt Geçişleri</span>
    </div>

    <!-- Mevcut Slaytlar -->
    <?php if ($slides): ?>
      <div class="media-grid" style="margin-bottom:20px">
        <?php foreach ($slides as $sl):
            $imgSrc = strpos($sl['image_url'], 'uploads/') === 0 ? '../' . $sl['image_url'] : rtrim(SITE_URL, '/') . '/' . $sl['image_url'];
        ?>
          <div class="media-item">
            <img src="<?= e($imgSrc) ?>" alt="" style="height:110px">
            <div class="cap"><?= e($sl['title_tr'] ?: basename($sl['image_url'])) ?></div>
            <form method="post" data-confirm="Bu slider görseli silinsin mi?">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="del_slide">
              <input type="hidden" name="id" value="<?= (int)$sl['id'] ?>">
              <button class="x" title="Sil">×</button>
            </form>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="muted" style="margin-bottom:16px">Henüz özel arka plan slaytı eklenmemiş.</p>
    <?php endif; ?>

    <!-- Yeni Slider Görseli Ekleme (Dropzone) -->
    <form method="post" enctype="multipart/form-data" style="background:rgba(255,255,255,0.02);padding:16px;border-radius:8px;border:1px solid var(--line)">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="add_slide">
      <label>Yeni Arka Plan Görseli Yükle</label>

      <div class="dropzone" id="dz-slide" style="margin-bottom:12px">
        <i class="fa-solid fa-cloud-arrow-up"></i>
        <p>Görseli buraya sürükleyin veya <span>seçin</span></p>
        <small class="file-list-preview"></small>
        <input type="file" name="slide_file" accept="image/*">
      </div>

      <div class="grid g2">
        <div class="field" style="margin-bottom:8px">
          <label>Slayt Başlığı (İsteğe bağlı)</label>
          <input type="text" name="title_tr" placeholder="ör. Depreme Dayanıklı Mimari">
        </div>
        <div class="field" style="margin-bottom:8px">
          <label>Alt Açıklama (İsteğe bağlı)</label>
          <input type="text" name="subtitle_tr" placeholder="ör. 35 yıllık tecrübeyle...">
        </div>
      </div>

      <button class="btn" style="width:100%;justify-content:center;margin-top:10px">
        <i class="fa-solid fa-plus"></i> Slayt Görselini Ekle
      </button>
    </form>
  </div>

  <!-- B. Hero Başlık & İstatistik Ayarları -->
  <form method="post" class="card">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save_settings">

    <div class="row between" style="margin-bottom:14px">
      <h2 style="margin:0"><i class="fa-solid fa-pen-fancy" style="color:var(--gold)"></i> Hero Metinleri & İstatistikler</h2>
    </div>

    <div class="field">
      <label>Üst Rozet Metni</label>
      <input type="text" name="kd_hero_badge" value="<?= $s('kd_hero_badge', 'GÜVENLİ GELECEK & DEĞER KATAN MİMARİ') ?>">
    </div>

    <div class="field">
      <label>Hero Ana Başlık</label>
      <input type="text" name="kd_hero_title" value="<?= $s('kd_hero_title', 'Geleceğe Güvenle Bakan Kentsel Dönüşüm Projeleri') ?>">
    </div>

    <div class="field">
      <label>Hero Açıklama Metni</label>
      <textarea name="kd_hero_desc" style="min-height:90px"><?= $s('kd_hero_desc', 'BUYAPI, riskli ve yıpranmış yapıları 35 yılı aşkın ileri mühendislik tecrübesi, şeffaf hukuki danışmanlık ve yüksek mimari estetikle depreme dayanıklı, çağdaş prestij eserlerine dönüştürüyor.') ?></textarea>
    </div>

    <!-- İstatistik Rozetleri -->
    <label style="margin-top:14px;color:var(--gold);font-weight:700">Öne Çıkan 4 Sayaç (İstatistik)</label>
    <div class="grid g2">
      <div class="field">
        <label>Gösterge 1</label>
        <div class="row" style="gap:6px">
          <input type="text" name="kd_stat_1_num" value="<?= $s('kd_stat_1_num', '35+') ?>" style="width:80px">
          <input type="text" name="kd_stat_1_lbl" value="<?= $s('kd_stat_1_lbl', 'Yıllık Mühendislik Deneyimi') ?>" style="flex:1">
        </div>
      </div>
      <div class="field">
        <label>Gösterge 2</label>
        <div class="row" style="gap:6px">
          <input type="text" name="kd_stat_2_num" value="<?= $s('kd_stat_2_num', '%100') ?>" style="width:80px">
          <input type="text" name="kd_stat_2_lbl" value="<?= $s('kd_stat_2_lbl', 'Hukuki ve Yasal Güvence') ?>" style="flex:1">
        </div>
      </div>
      <div class="field">
        <label>Gösterge 3</label>
        <div class="row" style="gap:6px">
          <input type="text" name="kd_stat_3_num" value="<?= $s('kd_stat_3_num', '0 Risk') ?>" style="width:80px">
          <input type="text" name="kd_stat_3_lbl" value="<?= $s('kd_stat_3_lbl', 'Hak Sahipleri Koruma Modeli') ?>" style="flex:1">
        </div>
      </div>
      <div class="field">
        <label>Gösterge 4</label>
        <div class="row" style="gap:6px">
          <input type="text" name="kd_stat_4_num" value="<?= $s('kd_stat_4_num', 'A++') ?>" style="width:80px">
          <input type="text" name="kd_stat_4_lbl" value="<?= $s('kd_stat_4_lbl', 'Statik ve Deprem Güvenliği') ?>" style="flex:1">
        </div>
      </div>
    </div>

    <!-- İletişim Bilgileri -->
    <div class="grid g2" style="margin-top:10px">
      <div class="field">
        <label>Kentsel Dönüşüm Danışma Hattı</label>
        <input type="text" name="kd_contact_phone" value="<?= $s('kd_contact_phone', '444 33 43') ?>">
      </div>
      <div class="field">
        <label>Kentsel Dönüşüm E-postası</label>
        <input type="email" name="kd_contact_email" value="<?= $s('kd_contact_email', 'kentsel@buyapi.com.tr') ?>">
      </div>
    </div>

    <div class="row" style="margin-top:16px">
      <button class="btn"><i class="fa-solid fa-floppy-disk"></i> Hero & İstatistikleri Kaydet</button>
    </div>
  </form>

</div>

<!-- 2. ORTA KISIM: 6306 YASA REHBERİ VE 4 AVANTAJ KARTI YÖNETİMİ -->
<form method="post" class="card" style="margin-bottom:25px" id="law-section">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="save_settings">

  <div class="row between" style="margin-bottom:14px">
    <h2 style="margin:0"><i class="fa-solid fa-scale-balanced" style="color:var(--gold)"></i> 6306 Sayılı Kanun & 4 Avantaj Kartı Yönetimi</h2>
    <span class="muted" style="font-size:0.85rem">Yasa metni ve hak sahipleri avantaj kartları</span>
  </div>

  <div class="grid g2" style="margin-bottom:16px">
    <div class="field">
      <label>Yasa Bölüm Başlığı</label>
      <input type="text" name="kd_law_title" value="<?= $s('kd_law_title', 'Yeni %50+1 Salt Çoğunluk Kuralı') ?>">
    </div>
    <div class="field">
      <label>Yasa Açıklama & Muafiyet Bilgi Metni</label>
      <textarea name="kd_law_info" style="min-height:75px"><?= $s('kd_law_info', '6306 Sayılı Kanun kapsamında tapu harcı, noter masrafları ve belediye harçlarından %100 muafiyet sağlanmakta; hak sahiplerine kira yardımı ve uygun faizli dönüşüm kredisi imkanları sunulmaktadır.') ?></textarea>
    </div>
  </div>

  <h3 style="font-size:1rem;color:var(--gold);margin:16px 0 10px"><i class="fa-solid fa-gem"></i> 4 Avantaj Kartı İçerikleri</h3>
  
  <div class="grid g2" style="gap:16px">
    <!-- Kart 1 -->
    <div style="background:rgba(255,255,255,0.02);padding:14px;border-radius:6px;border:1px solid var(--line)">
      <strong style="color:#fff;display:block;margin-bottom:8px">1. Kart (Harç Muafiyeti)</strong>
      <div class="field" style="margin-bottom:8px">
        <label>Başlık</label>
        <input type="text" name="kd_benefit_1_title" value="<?= $s('kd_benefit_1_title', '%100 Harç & Vergi Muafiyeti') ?>">
      </div>
      <div class="field">
        <label>Açıklama</label>
        <textarea name="kd_benefit_1_desc" style="min-height:60px"><?= $s('kd_benefit_1_desc', 'Tapu kadastro harcı, noter masrafı, damga vergisi, veraset-intikal ve belediye ruhsat harçlarının tamamından muaf olarak yüzbinlerce liralık tasarruf sağlarsınız.') ?></textarea>
      </div>
    </div>

    <!-- Kart 2 -->
    <div style="background:rgba(255,255,255,0.02);padding:14px;border-radius:6px;border:1px solid var(--line)">
      <strong style="color:#fff;display:block;margin-bottom:8px">2. Kart (Kira Yardımı)</strong>
      <div class="field" style="margin-bottom:8px">
        <label>Başlık</label>
        <input type="text" name="kd_benefit_2_title" value="<?= $s('kd_benefit_2_title', 'Düzenli Kira Yardımı Desteği') ?>">
      </div>
      <div class="field">
        <label>Açıklama</label>
        <textarea name="kd_benefit_2_desc" style="min-height:60px"><?= $s('kd_benefit_2_desc', 'İnşaat süresince Çevre, Şehircilik ve İklim Değişikliği Bakanlığı tarafından hak sahiplerine karşılıksız aylık kira yardımı sağlanır. Başvuru işlemlerini BUYAPI takip eder.') ?></textarea>
      </div>
    </div>

    <!-- Kart 3 -->
    <div style="background:rgba(255,255,255,0.02);padding:14px;border-radius:6px;border:1px solid var(--line)">
      <strong style="color:#fff;display:block;margin-bottom:8px">3. Kart (Dönüşüm Kredisi)</strong>
      <div class="field" style="margin-bottom:8px">
        <label>Başlık</label>
        <input type="text" name="kd_benefit_3_title" value="<?= $s('kd_benefit_3_title', 'Düşük Faizli Dönüşüm Kredisi') ?>">
      </div>
      <div class="field">
        <label>Açıklama</label>
        <textarea name="kd_benefit_3_desc" style="min-height:60px"><?= $s('kd_benefit_3_desc', 'Kamu ve anlaşmalı bankalar aracılığıyla devlet faiz destekli, 120 aya varan vadelerle uygun koşullu kentsel dönüşüm kredisi imkanından yararlanabilirsiniz.') ?></textarea>
      </div>
    </div>

    <!-- Kart 4 -->
    <div style="background:rgba(255,255,255,0.02);padding:14px;border-radius:6px;border:1px solid var(--line)">
      <strong style="color:#fff;display:block;margin-bottom:8px">4. Kart (Noter Sözleşmesi)</strong>
      <div class="field" style="margin-bottom:8px">
        <label>Başlık</label>
        <input type="text" name="kd_benefit_4_title" value="<?= $s('kd_benefit_4_title', 'Noter Tasdikli Şeffaf Sözleşme') ?>">
      </div>
      <div class="field">
        <label>Açıklama</label>
        <textarea name="kd_benefit_4_desc" style="min-height:60px"><?= $s('kd_benefit_4_desc', 'Bağımsız bölümlerin büyüklüğü, şerefiye payları ve malzeme teknik şartnamesi noter güvencesiyle kayıt altına alınır; hiçbir hak kaybı riski oluşmaz.') ?></textarea>
      </div>
    </div>
  </div>

  <div class="row" style="margin-top:16px">
    <button class="btn"><i class="fa-solid fa-floppy-disk"></i> Yasa & Avantaj Kartlarını Kaydet</button>
  </div>
</form>

<!-- 3. ALT KISIM: SIKÇA SORULAN SORULAR (SSS / FAQ) TAM YÖNETİMİ -->
<div class="card" id="faqs">
  <div class="row between" style="margin-bottom:16px">
    <div>
      <h2 style="margin:0"><i class="fa-solid fa-circle-question" style="color:var(--gold)"></i> Sıkça Sorulan Sorular (SSS) Yönetimi (<?= count($faqs) ?>)</h2>
      <span class="muted">Kentsel dönüşüm sayfasında yer alan akordeon sorularını ekleyin, düzenleyin ve sıralayın.</span>
    </div>
    <?php if ($editFaq): ?>
      <a href="urban_renewal.php#faqs" class="btn ghost sm"><i class="fa-solid fa-plus"></i> Yeni Soru Ekleme Moduna Dön</a>
    <?php endif; ?>
  </div>

  <!-- SSS Ekleme / Düzenleme Formu (Dinamik Dil Sekmeleri Destekli) -->
  <form method="post" style="background:rgba(255,255,255,0.02);padding:20px;border-radius:8px;border:1px solid var(--line);margin-bottom:24px">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save_faq">
    <input type="hidden" name="faq_id" value="<?= (int)($editFaq['id'] ?? 0) ?>">

    <div class="row between" style="margin-bottom:12px">
      <strong style="color:var(--gold);font-size:1.05rem">
        <?= $editFaq ? '<i class="fa-solid fa-pen-to-square"></i> Soruyu Düzenle (#' . (int)$editFaq['id'] . ')' : '<i class="fa-solid fa-plus-circle"></i> Yeni Sıkça Sorulan Soru Ekle' ?>
      </strong>
      <div class="row" style="gap:14px">
        <div class="row" style="gap:6px">
          <label style="margin:0;font-size:0.85rem">Sıra:</label>
          <input type="number" name="sort_order" value="<?= (int)($editFaq['sort_order'] ?? (count($faqs) + 1)) ?>" style="width:60px;padding:4px 8px">
        </div>
        <label class="row" style="gap:6px;margin:0;cursor:pointer;font-size:0.85rem">
          <input type="checkbox" name="is_active" value="1" <?= (!isset($editFaq['is_active']) || $editFaq['is_active']) ? 'checked' : '' ?>>
          <span>Aktif</span>
        </label>
      </div>
    </div>

    <!-- DİL SEKMELERİ -->
    <?php lang_tabs('faq_edit_tabs', function($langCode, $isDefault) use ($editFaq) { ?>
      <div class="field" style="margin-bottom:10px">
        <label>Soru Metni (<?= strtoupper($langCode) ?>) <?= $isDefault ? '<span class="req">*</span>' : '' ?></label>
        <input type="text" name="question_<?= $langCode ?>" value="<?= e($editFaq['question_' . $langCode] ?? '') ?>" placeholder="ör. Kentsel dönüşüme başlamak için ne kadar onay gerekir?">
      </div>
      <div class="field">
        <label>Cevap Metni (<?= strtoupper($langCode) ?>) <?= $isDefault ? '<span class="req">*</span>' : '' ?></label>
        <textarea name="answer_<?= $langCode ?>" style="min-height:90px" placeholder="Detaylı cevabı giriniz..."><?= e($editFaq['answer_' . $langCode] ?? '') ?></textarea>
      </div>
    <?php }); ?>

    <div class="row" style="margin-top:14px;gap:10px">
      <button class="btn"><i class="fa-solid fa-check"></i> <?= $editFaq ? 'Değişiklikleri Kaydet' : 'Soruyu Kaydet ve Ekle' ?></button>
      <?php if ($editFaq): ?>
        <a href="urban_renewal.php#faqs" class="btn ghost">İptal</a>
      <?php endif; ?>
    </div>
  </form>

  <!-- Mevcut SSS Tablosu -->
  <table class="table" style="width:100%">
    <thead>
      <tr>
        <th style="width:60px">Sıra</th>
        <th>Soru & Cevap (TR)</th>
        <th style="width:120px">Diğer Diller</th>
        <th style="width:80px">Durum</th>
        <th style="width:140px;text-align:right">İşlemler</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($faqs): ?>
        <?php foreach ($faqs as $f): ?>
          <tr>
            <td><strong>#<?= (int)$f['sort_order'] ?></strong></td>
            <td>
              <div style="font-weight:700;color:#fff;margin-bottom:4px"><?= e($f['question_tr']) ?></div>
              <div class="muted" style="font-size:0.85rem"><?= e(mb_strimwidth($f['answer_tr'], 0, 110, '...')) ?></div>
            </td>
            <td>
              <div class="row" style="gap:4px">
                <?php foreach ($activeLangs as $al): if ($al['code'] === 'tr') continue; ?>
                  <span class="badge <?= !empty($f['question_' . $al['code']]) ? 'green' : 'gray' ?>" title="<?= strtoupper($al['code']) ?>">
                    <?= strtoupper($al['code']) ?>
                  </span>
                <?php endforeach; ?>
              </div>
            </td>
            <td>
              <form method="post" style="display:inline">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="toggle_faq">
                <input type="hidden" name="id" value="<?= (int)$f['id'] ?>">
                <button type="submit" class="badge <?= $f['is_active'] ? 'green' : 'gray' ?>" style="cursor:pointer;border:none">
                  <?= $f['is_active'] ? 'Aktif' : 'Pasif' ?>
                </button>
              </form>
            </td>
            <td style="text-align:right">
              <a href="urban_renewal.php?edit_faq=<?= (int)$f['id'] ?>#faqs" class="btn sm ghost" title="Düzenle"><i class="fa-solid fa-pen"></i></a>
              <form method="post" style="display:inline" data-confirm="Bu soru ve cevabı silmek istediğinize emin misiniz?">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="del_faq">
                <input type="hidden" name="id" value="<?= (int)$f['id'] ?>">
                <button type="submit" class="btn sm danger" title="Sil"><i class="fa-solid fa-trash"></i></button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="5" class="muted" style="text-align:center;padding:24px">Kayıtlı soru bulunmuyor.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script>
// Sürükle Bırak (Dropzone) Dinleyici
document.querySelectorAll('.dropzone').forEach(dz => {
    const input = dz.querySelector('input[type="file"]');
    const preview = dz.querySelector('.file-list-preview');

    function updateLabel(files) {
        if (!files || files.length === 0) { preview.textContent = ''; return; }
        preview.textContent = '✓ Seçildi: ' + files[0].name + ' (' + (files[0].size / 1024 / 1024).toFixed(2) + ' MB)';
    }

    dz.addEventListener('dragover', (e) => { e.preventDefault(); dz.classList.add('dragover'); });
    dz.addEventListener('dragleave', (e) => { e.preventDefault(); dz.classList.remove('dragover'); });
    dz.addEventListener('drop', (e) => {
        e.preventDefault(); dz.classList.remove('dragover');
        if (e.dataTransfer && e.dataTransfer.files.length > 0) {
            input.files = e.dataTransfer.files;
            updateLabel(input.files);
        }
    });
    input.addEventListener('change', () => updateLabel(input.files));
});
</script>

<?php admin_footer(); ?>
