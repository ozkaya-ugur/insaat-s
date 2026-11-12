<?php
require_once __DIR__ . '/_layout.php';
require_perm('pages', 'view');
admin_readonly(!admin_can('pages', 'edit'));

$layoutTemplates = [
    'standart'  => 'Standart (banner + tek kolon metin)',
    'genis'     => 'Geniş / Tam Genişlik',
    'iki-kolon' => 'İki Kolon (içerik + yan panel)',
    'galeri'    => 'Galeri / Görsel Ağırlıklı',
    'iletisim'  => 'İletişim Düzeni',
];

$editId = (int)($_GET['edit'] ?? 0);
$editPage = $editId ? q('SELECT * FROM pages WHERE id=?', [$editId])->fetch() : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $act = $_POST['action'] ?? '';
    if ($act === 'delete') { if (!admin_can('pages', 'delete')) admin_deny(); }
    elseif ($act === 'save') { if (!admin_can('pages', 'create') && !admin_can('pages', 'edit')) admin_deny(); }
    elseif (!admin_can('pages', 'edit')) admin_deny();

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
        $layoutTemplate = array_key_exists($_POST['layout_template'] ?? '', $layoutTemplates) ? $_POST['layout_template'] : 'standart';
        $showInMenu = !empty($_POST['show_in_menu']) ? 1 : 0;
        $isActive = !empty($_POST['is_active']) ? 1 : 0;
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $staticFile = trim((string)($_POST['static_file'] ?? ''));
        if ($staticFile === '') {
            $staticFile = null;
        } else {
            $staticFile = ltrim($staticFile, '/');
            $staticFile = preg_replace('/[^a-zA-Z0-9_\-\.\/]/', '', $staticFile);
        }

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
            $layoutTemplate,
            $banner,
            $staticFile,
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

        $cols = 'slug,title_tr,title_en,title_ar,page_type,layout_template,banner_image,static_file,content_tr,content_en,content_ar,seo_title,seo_desc,seo_keywords,show_in_menu,is_active,sort_order';

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

    <form method="post" enctype="multipart/form-data" id="pageEditorForm">
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
        </div>

        <div class="field">
          <label>Sayfa Düzeni Şablonu *</label>
          <select name="layout_template" required>
            <?php foreach ($layoutTemplates as $key => $label): ?>
              <option value="<?= e($key) ?>" <?= ($editPage['layout_template'] ?? 'standart') === $key ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="grid g2">
        <div class="field">
          <label>URL Kısa Adı (Slug)</label>
          <input type="text" name="slug" value="<?= $v('slug') ?>" placeholder="otomatik üretilir (ör: hakkimizda)">
        </div>
        <div class="field">
          <label>Statik Dosya (Opsiyonel)</label>
          <input type="text" name="static_file" value="<?= $v('static_file') ?>" placeholder="ör: kurumsal.html">
          <div class="hint">Doluysa ziyaretçi bu dosyaya yönlendirilir. Boşsa sayfa içerikten render edilir.</div>
        </div>
      </div>

      <?= lang_tabs('page_title') ?>
      <div class="lpane on" data-pane="tr">
        <div class="field">
          <label>Sayfa Başlığı (Türkçe) *</label>
          <input type="text" name="title_tr" value="<?= $v('title_tr') ?>" required placeholder="ör. Sürdürülebilirlik">
        </div>
        <div class="field">
          <label>Sayfa İçeriği (Türkçe)</label>
          <div class="rt-editor" data-lang="tr">
            <div class="rt-toolbar">
              <button type="button" data-cmd="bold">Kalın</button>
              <button type="button" data-cmd="italic">İtalik</button>
              <button type="button" data-cmd="underline">Altı Çizili</button>
              <button type="button" data-cmd="formatBlock" data-val="h2">H2</button>
              <button type="button" data-cmd="formatBlock" data-val="h3">H3</button>
              <button type="button" data-cmd="insertUnorderedList">Madde Listesi</button>
              <button type="button" data-cmd="insertOrderedList">Numaralı Liste</button>
              <button type="button" data-cmd="createLink">Bağlantı</button>
              <button type="button" data-cmd="removeFormat">Temizle</button>
            </div>
            <div class="rt-content" contenteditable="true" data-target="content_tr"><?= $editPage['content_tr'] ?? '' ?></div>
            <textarea name="content_tr" id="content_tr" hidden><?= $editPage['content_tr'] ?? '' ?></textarea>
          </div>
        </div>
      </div>

      <div class="lpane" data-pane="en">
        <div class="field">
          <label>Sayfa Başlığı (English)</label>
          <input type="text" name="title_en" value="<?= $v('title_en') ?>" placeholder="e.g. Sustainability">
        </div>
        <div class="field">
          <label>Sayfa İçeriği (English)</label>
          <div class="rt-editor" data-lang="en">
            <div class="rt-toolbar">
              <button type="button" data-cmd="bold">Kalın</button>
              <button type="button" data-cmd="italic">İtalik</button>
              <button type="button" data-cmd="underline">Altı Çizili</button>
              <button type="button" data-cmd="formatBlock" data-val="h2">H2</button>
              <button type="button" data-cmd="formatBlock" data-val="h3">H3</button>
              <button type="button" data-cmd="insertUnorderedList">Madde Listesi</button>
              <button type="button" data-cmd="insertOrderedList">Numaralı Liste</button>
              <button type="button" data-cmd="createLink">Bağlantı</button>
              <button type="button" data-cmd="removeFormat">Temizle</button>
            </div>
            <div class="rt-content" contenteditable="true" data-target="content_en"><?= $editPage['content_en'] ?? '' ?></div>
            <textarea name="content_en" id="content_en" hidden><?= $editPage['content_en'] ?? '' ?></textarea>
          </div>
        </div>
      </div>

      <div class="lpane rtl" data-pane="ar">
        <div class="field">
          <label>Sayfa Başlığı (العربية)</label>
          <input type="text" name="title_ar" value="<?= $v('title_ar') ?>" dir="rtl">
        </div>
        <div class="field">
          <label>Sayfa İçeriği (العربية)</label>
          <div class="rt-editor" data-lang="ar">
            <div class="rt-toolbar">
              <button type="button" data-cmd="bold">Kalın</button>
              <button type="button" data-cmd="italic">İtalik</button>
              <button type="button" data-cmd="underline">Altı Çizili</button>
              <button type="button" data-cmd="formatBlock" data-val="h2">H2</button>
              <button type="button" data-cmd="formatBlock" data-val="h3">H3</button>
              <button type="button" data-cmd="insertUnorderedList">Madde Listesi</button>
              <button type="button" data-cmd="insertOrderedList">Numaralı Liste</button>
              <button type="button" data-cmd="createLink">Bağlantı</button>
              <button type="button" data-cmd="removeFormat">Temizle</button>
            </div>
            <div class="rt-content" contenteditable="true" data-target="content_ar" dir="rtl"><?= $editPage['content_ar'] ?? '' ?></div>
            <textarea name="content_ar" id="content_ar" hidden><?= $editPage['content_ar'] ?? '' ?></textarea>
          </div>
        </div>
      </div>

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

  <div class="card">
    <div class="row between" style="margin-bottom:14px">
      <h2 style="margin:0"><i class="fa-solid fa-list-check" style="color:var(--gold)"></i> Mevcut Sayfalar (<?= count($pages) ?>)</h2>
      <span class="muted" style="font-size:0.85rem"><i class="fa-solid fa-circle-info"></i> Bu listede aktif/pasif tüm sayfalar görünür.</span>
    </div>

    <table class="resp">
      <thead>
        <tr>
          <th>Başlık</th>
          <th>Sayfa Türü</th>
          <th>Şablon</th>
          <th>Menü</th>
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
            <?php if (!empty($p['static_file'])): ?>
              <small style="display:block;color:#94a3b8">Statik: <?= e($p['static_file']) ?></small>
            <?php endif; ?>
          </td>
          <td>
            <span class="pill" style="background:rgba(255,255,255,0.06);color:var(--text)">
              <?= e(PAGE_TYPES[$p['page_type']] ?? $p['page_type']) ?>
            </span>
          </td>
          <td>
            <span class="pill" style="background:rgba(197,160,89,.2);color:#f3e3b8;border:1px solid rgba(197,160,89,.45)">
              <?= e($layoutTemplates[$p['layout_template'] ?? 'standart'] ?? ($p['layout_template'] ?? 'standart')) ?>
            </span>
          </td>
          <td>
            <form method="post" style="display:inline">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="toggle_menu">
              <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
              <button class="btn ghost sm" type="submit" style="padding:6px 10px;white-space:nowrap;min-width:128px">
                <?= $p['show_in_menu'] ? 'Menüden Gizle' : 'Menüde Göster' ?>
              </button>
            </form>
          </td>
          <td>
            <form method="post" style="display:inline">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="toggle_active">
              <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
              <button class="btn ghost sm" type="submit" style="padding:6px 10px;white-space:nowrap;min-width:128px">
                <?= $p['is_active'] ? 'Yayından Kaldır' : 'Yayına Al' ?>
              </button>
            </form>
          </td>
          <td style="text-align:right">
            <div class="row" style="justify-content:flex-end;gap:6px">
              <?php if (admin_can('pages', 'edit')): ?><a class="btn sm <?= $editId === (int)$p['id'] ? '' : 'ghost' ?>" href="pages.php?edit=<?= (int)$p['id'] ?>" title="Düzenle">
                <i class="fa-solid fa-pen"></i>
              </a><?php endif; ?>
              <?php if (admin_can('pages', 'delete') && !in_array($p['slug'], ['anasayfa', 'projeler', 'kentsel-donusum'], true)): ?>
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

<style>
  .rt-editor{border:1px solid rgba(255,255,255,.12);border-radius:10px;overflow:hidden;background:rgba(12,16,24,.45)}
  .rt-toolbar{display:flex;flex-wrap:wrap;gap:6px;padding:8px;border-bottom:1px solid rgba(255,255,255,.1);background:rgba(255,255,255,.03)}
  .rt-toolbar button{padding:6px 9px;border:1px solid rgba(255,255,255,.2);background:rgba(255,255,255,.06);color:var(--text);border-radius:6px;cursor:pointer;font-size:.78rem}
  .rt-toolbar button:hover{border-color:var(--gold);color:var(--gold)}
  .rt-content{min-height:180px;padding:12px;outline:none;line-height:1.65}
  .rt-content:empty:before{content:'İçeriği buraya yazın...';color:#9ca3af}
</style>

<script>
(function(){
  const editors = Array.from(document.querySelectorAll('.rt-editor'));

  function syncEditor(editor) {
    const content = editor.querySelector('.rt-content');
    if (!content) return;
    const target = content.dataset.target;
    const hidden = document.getElementById(target);
    if (!hidden) return;
    hidden.value = content.innerHTML.trim();
  }

  editors.forEach((editor) => {
    const content = editor.querySelector('.rt-content');
    const toolbar = editor.querySelector('.rt-toolbar');
    if (!content || !toolbar) return;

    toolbar.addEventListener('click', (e) => {
      const btn = e.target.closest('button[data-cmd]');
      if (!btn) return;
      e.preventDefault();
      content.focus();

      const cmd = btn.dataset.cmd;
      const val = btn.dataset.val || null;

      if (cmd === 'createLink') {
        const url = prompt('Bağlantı adresi (https://...)');
        if (!url) return;
        document.execCommand('createLink', false, url);
      } else if (cmd === 'formatBlock' && val) {
        document.execCommand('formatBlock', false, val.toUpperCase());
      } else {
        document.execCommand(cmd, false, val);
      }
      syncEditor(editor);
    });

    content.addEventListener('input', () => syncEditor(editor));
    content.addEventListener('blur', () => syncEditor(editor));
    syncEditor(editor);
  });

  const form = document.getElementById('pageEditorForm');
  if (form) {
    form.addEventListener('submit', () => editors.forEach(syncEditor));
  }
})();
</script>
<?php admin_footer();
