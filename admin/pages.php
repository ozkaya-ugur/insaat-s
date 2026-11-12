<?php
require_once __DIR__ . '/_layout.php';
require_login();

$editId = (int)($_GET['edit'] ?? 0);
$editPage = $editId ? q('SELECT * FROM pages WHERE id=?', [$editId])->fetch() : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $act = $_POST['action'] ?? '';

    // Menüde Göster/Gizle Toggle
    if ($act === 'toggle_menu') {
        $id = (int)($_POST['id'] ?? 0);
        $pg = q('SELECT * FROM pages WHERE id=?', [$id])->fetch();
        if ($pg) {
            $newMenu = $pg['show_in_menu'] ? 0 : 1;
            q('UPDATE pages SET show_in_menu=? WHERE id=?', [$newMenu, $id]);
            flash("'{$pg['title_tr']}' sayfası menüde " . ($newMenu ? 'gösterilecek.' : 'gizlendi.'));
        }
        redirect('pages.php');
    }

    // Aktif/Pasif Toggle
    if ($act === 'toggle_active') {
        $id = (int)($_POST['id'] ?? 0);
        $pg = q('SELECT * FROM pages WHERE id=?', [$id])->fetch();
        if ($pg) {
            $newActive = $pg['is_active'] ? 0 : 1;
            q('UPDATE pages SET is_active=? WHERE id=?', [$newActive, $id]);
            flash("'{$pg['title_tr']}' sayfası " . ($newActive ? 'yayına alındı.' : 'yayından kaldırıldı.'));
        }
        redirect('pages.php');
    }

    // Sil
    if ($act === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $pg = q('SELECT * FROM pages WHERE id=?', [$id])->fetch();
        if ($pg) {
            if (in_array($pg['slug'], ['anasayfa', 'projeler', 'kentsel-donusum'], true)) {
                flash('Kritik sistem sayfaları silinemez.', 'err');
            } else {
                delete_upload($pg['banner_image']);
                q('DELETE FROM pages WHERE id=?', [$id]);
                flash("'{$pg['title_tr']}' sayfası silindi.");
            }
        }
        redirect('pages.php');
    }

    // Kaydet (Ekle / Güncelle)
    if ($act === 'save') {
        $titleTr = trim($_POST['title_tr'] ?? '');
        if ($titleTr === '') {
            flash('Türkçe sayfa başlığı zorunludur.', 'err');
            redirect('pages.php' . ($editId ? "?edit=$editId" : ''));
        }

        $slug = slugify(trim($_POST['slug'] ?? '') ?: $titleTr);
        $dup = q('SELECT id FROM pages WHERE slug=? AND id<>?', [$slug, $editId])->fetch();
        if ($dup) $slug .= '-' . substr(bin2hex(random_bytes(2)), 0, 4);

        $pageType = array_key_exists($_POST['page_type'] ?? '', PAGE_TYPES) ? $_POST['page_type'] : 'standart';
        $showInMenu = !empty($_POST['show_in_menu']) ? 1 : 0;
        $isActive = !empty($_POST['is_active']) ? 1 : 0;
        $sortOrder = (int)($_POST['sort_order'] ?? 0);

        $banner = $editPage['banner_image'] ?? '';
        if ($newBanner = upload_file('banner_file', ['jpg','jpeg','png','webp','gif'], 'banners')) {
            if ($banner) delete_upload($banner);
            $banner = $newBanner;
        } elseif (!empty($_POST['banner_path'])) {
            $banner = trim($_POST['banner_path']);
        }
        if (!empty($_POST['banner_remove'])) {
            delete_upload($banner);
            $banner = '';
        }

        $data = [
            $slug,
            $titleTr,
            trim($_POST['title_en'] ?? ''),
            trim($_POST['title_ar'] ?? ''),
            $pageType,
            $banner,
            trim($_POST['content_tr'] ?? ''),
            trim($_POST['content_en'] ?? ''),
            trim($_POST['content_ar'] ?? ''),
            trim($_POST['seo_title'] ?? ''),
            trim($_POST['seo_desc'] ?? ''),
            trim($_POST['seo_keywords'] ?? ''),
            $showInMenu,
            $isActive,
            $sortOrder,
        ];

        $cols = 'slug,title_tr,title_en,title_ar,page_type,banner_image,content_tr,content_en,content_ar,seo_title,seo_desc,seo_keywords,show_in_menu,is_active,sort_order';

        if ($editPage) {
            $set = implode(',', array_map(fn($c) => "$c=?", explode(',', $cols)));
            q("UPDATE pages SET $set WHERE id=?", [...$data, $editId]);
            flash("'{$titleTr}' sayfası güncellendi.");
        } else {
            $ph = implode(',', array_fill(0, count($data), '?'));
            q("INSERT INTO pages ($cols) VALUES ($ph)", $data);
            flash("'{$titleTr}' sayfası başarıyla oluşturuldu.");
        }
        redirect('pages.php');
    }
}

admin_header('Sayfa Yönetimi', 'pages');
$pages = q('SELECT * FROM pages ORDER BY sort_order ASC, id ASC')->fetchAll();
$v = fn($k, $d = '') => e($editPage[$k] ?? $d);
?>
<div class="grid g2" style="align-items:start">

  <!-- Sayfa Ekle / Düzenle Formu -->
  <div class="card">
    <div class="row between" style="margin-bottom:12px">
      <h2 style="margin:0">
        <i class="fa-solid <?= $editPage ? 'fa-pen-to-square' : 'fa-plus-circle' ?>" style="color:var(--gold)"></i>
        <?= $editPage ? 'Sayfayı Düzenle: ' . e($editPage['title_tr']) : 'Yeni Sayfa Ekle' ?>
      </h2>
      <?php if ($editPage): ?>
        <a class="btn ghost sm" href="pages.php">Yeni Ekle Moduna Dön</a>
      <?php endif; ?>
    </div>

    <form method="post" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="save">

      <div class="grid g2">
        <div class="field">
          <label>Sayfa Türü *</label>
          <select name="page_type" required>
            <?php foreach (PAGE_TYPES as $k => $lbl): ?>
              <option value="<?= e($k) ?>" <?= ($editPage['page_type'] ?? 'standart') === $k ? 'selected' : '' ?>><?= e($lbl) ?></option>
            <?php endforeach; ?>
          </select>
          <div class="hint">İletişim, Hakkımızda, Projeler, Kentsel Dönüşüm vb.</div>
        </div>

        <div class="field">
          <label>URL Kısa Adı (Slug)</label>
          <input type="text" name="slug" value="<?= $v('slug') ?>" placeholder="otomatik üretilir (ör: hakkimizda)">
        </div>
      </div>

      <!-- Çok Dilli Başlıklar -->
      <?= lang_tabs('page_title') ?>
      <div class="lpane on" data-pane="tr">
        <div class="field">
          <label>Sayfa Başlığı (Türkçe) *</label>
          <input type="text" name="title_tr" value="<?= $v('title_tr') ?>" required placeholder="ör. Sürdürülebilirlik">
        </div>
        <div class="field">
          <label>Sayfa İçeriği / Açıklama (Türkçe)</label>
          <textarea name="content_tr" style="min-height:100px" placeholder="Sayfa hakkında genel metin veya bilgi..."><?= $v('content_tr') ?></textarea>
        </div>
      </div>

      <div class="lpane" data-pane="en">
        <div class="field">
          <label>Sayfa Başlığı (English)</label>
          <input type="text" name="title_en" value="<?= $v('title_en') ?>" placeholder="e.g. Sustainability">
        </div>
        <div class="field">
          <label>Sayfa İçeriği (English)</label>
          <textarea name="content_en" style="min-height:100px"><?= $v('content_en') ?></textarea>
        </div>
      </div>

      <div class="lpane rtl" data-pane="ar">
        <div class="field">
          <label>Sayfa Başlığı (العربية)</label>
          <input type="text" name="title_ar" value="<?= $v('title_ar') ?>" dir="rtl">
        </div>
        <div class="field">
          <label>Sayfa İçeriği (العربية)</label>
          <textarea name="content_ar" style="min-height:100px" dir="rtl"><?= $v('content_ar') ?></textarea>
        </div>
      </div>

      <!-- Banner Görseli -->
      <div class="field" style="margin-top:14px">
        <label>Sayfa Üst Banner Görseli</label>
        <?php if (!empty($editPage['banner_image'])): ?>
          <div style="margin-bottom:8px">
            <img src="../<?= e($editPage['banner_image']) ?>" style="height:70px;border-radius:4px;object-fit:cover" alt="">
            <label class="chk" style="margin-top:4px"><input type="checkbox" name="banner_remove" value="1"><span>Banner'ı kaldır</span></label>
          </div>
        <?php endif; ?>
        <input type="file" name="banner_file" accept="image/*">
      </div>

      <!-- Ayarlar: Menüde Göster, Aktif, Sıralama -->
      <div class="grid g3" style="margin-top:10px">
        <div class="field">
          <label>Sıralama</label>
          <input type="number" name="sort_order" value="<?= $v('sort_order', count($pages) + 1) ?>">
        </div>
        <div class="field" style="display:flex;align-items:flex-end">
          <label class="chk">
            <input type="checkbox" name="show_in_menu" value="1" <?= ($editPage['show_in_menu'] ?? 1) ? 'checked' : '' ?>>
            <span><strong>Menüde Göster</strong></span>
          </label>
        </div>
        <div class="field" style="display:flex;align-items:flex-end">
          <label class="chk">
            <input type="checkbox" name="is_active" value="1" <?= ($editPage['is_active'] ?? 1) ? 'checked' : '' ?>>
            <span>Yayında (Aktif)</span>
          </label>
        </div>
      </div>

      <!-- SEO -->
      <div class="field" style="margin-top:10px">
        <label>SEO Başlığı</label>
        <input type="text" name="seo_title" value="<?= $v('seo_title') ?>" placeholder="Arama motoru başlığı (İsteğe bağlı)">
      </div>
      <div class="field">
        <label>SEO Meta Açıklama</label>
        <textarea name="seo_desc" style="min-height:60px" placeholder="Meta açıklama (İsteğe bağlı)"><?= $v('seo_desc') ?></textarea>
      </div>

      <div class="row" style="margin-top:16px">
        <button class="btn"><i class="fa-solid fa-floppy-disk"></i> <?= $editPage ? 'Değişiklikleri Kaydet' : 'Sayfayı Oluştur' ?></button>
        <?php if ($editPage): ?>
          <a class="btn ghost" href="pages.php">Vazgeç</a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <!-- Sistemdeki Sayfalar Listesi -->
  <div class="card">
    <div class="row between" style="margin-bottom:14px">
      <h2 style="margin:0"><i class="fa-solid fa-list-check" style="color:var(--gold)"></i> Mevcut Sayfalar (<?= count($pages) ?>)</h2>
      <span class="muted" style="font-size:0.85rem"><i class="fa-solid fa-circle-info"></i> Menüde göster seçeneği sitenin üst gezinme çubuğunu belirler.</span>
    </div>

    <table class="resp">
      <thead>
        <tr>
          <th>Başlık</th>
          <th>Sayfa Türü</th>
          <th>Menüde</th>
          <th>Durum</th>
          <th style="text-align:right">İşlemler</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($pages as $p): ?>
        <tr>
          <td>
            <strong><?= e($p['title_tr']) ?></strong>
            <small style="display:block;color:var(--dim)">/<?= e($p['slug']) ?></small>
          </td>
          <td>
            <span class="pill" style="background:rgba(255,255,255,0.06);color:var(--text)">
              <?= e(PAGE_TYPES[$p['page_type']] ?? $p['page_type']) ?>
            </span>
          </td>
          <td>
            <form method="post" style="display:inline">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="toggle_menu">
              <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
              <button class="btn ghost sm" title="Menüde göster/gizle" style="padding:4px 8px">
                <?php if ($p['show_in_menu']): ?>
                  <span style="color:#4ade80"><i class="fa-solid fa-check"></i> Menüde</span>
                <?php else: ?>
                  <span style="color:var(--dim)"><i class="fa-solid fa-eye-slash"></i> Gizli</span>
                <?php endif; ?>
              </button>
            </form>
          </td>
          <td>
            <form method="post" style="display:inline">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="toggle_active">
              <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
              <button class="btn ghost sm" style="padding:4px 8px">
                <?php if ($p['is_active']): ?>
                  <span style="color:#4ade80">Aktif</span>
                <?php else: ?>
                  <span style="color:#ef4444">Pasif</span>
                <?php endif; ?>
              </button>
            </form>
          </td>
          <td style="text-align:right">
            <div class="row" style="justify-content:flex-end;gap:6px">
              <a class="btn sm <?= $editId === (int)$p['id'] ? '' : 'ghost' ?>" href="pages.php?edit=<?= (int)$p['id'] ?>" title="Düzenle">
                <i class="fa-solid fa-pen"></i>
              </a>
              <?php if (!in_array($p['slug'], ['anasayfa', 'projeler', 'kentsel-donusum'], true)): ?>
                <form method="post" data-confirm="'<?= e($p['title_tr']) ?>' sayfasını silmek istediğinize emin misiniz?" style="display:inline">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                  <button class="btn danger sm" title="Sil"><i class="fa-solid fa-trash"></i></button>
                </form>
              <?php endif; ?>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php admin_footer();
