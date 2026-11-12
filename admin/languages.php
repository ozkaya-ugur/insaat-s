<?php
require_once __DIR__ . '/_layout.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $act = $_POST['action'] ?? '';

    // Yeni Dil Ekle
    if ($act === 'add') {
        $code = strtolower(trim($_POST['code'] ?? ''));
        $name = trim($_POST['name'] ?? '');
        $flag = trim($_POST['flag'] ?? '🌐');
        $dir = in_array($_POST['direction'] ?? 'ltr', ['ltr', 'rtl'], true) ? $_POST['direction'] : 'ltr';
        $order = (int)($_POST['sort_order'] ?? 0);
        $active = !empty($_POST['is_active']) ? 1 : 0;

        if (!preg_match('/^[a-z]{2,10}$/', $code)) {
            flash('Dil kodu 2-10 karakter arası küçük harf olmalıdır (ör: de, fr, ru).', 'err');
        } elseif ($name === '') {
            flash('Dil adı boş bırakılamaz.', 'err');
        } else {
            $exists = q('SELECT id FROM languages WHERE code = ?', [$code])->fetch();
            if ($exists) {
                flash("'$code' koduyla zaten bir dil kayıtlı.", 'err');
            } else {
                q('INSERT INTO languages (code, name, flag, direction, is_default, is_active, sort_order) VALUES (?, ?, ?, ?, 0, ?, ?)',
                  [$code, $name, $flag, $dir, $active, $order]);

                // Bu dil için varsayılan çeviri anahtarlarını hazırla
                $keys = q('SELECT DISTINCT t_key FROM translations')->fetchAll(PDO::FETCH_COLUMN);
                foreach ($keys as $k) {
                    q('INSERT IGNORE INTO translations (lang, t_key, t_value) VALUES (?, ?, ?)', [$code, $k, '']);
                }

                // Varsayılan SEO satırını oluştur
                q('INSERT IGNORE INTO seo_settings (lang, title, description, keywords) VALUES (?, "", "", "")', [$code]);

                flash("'$name' ($code) dili sisteme başarıyla eklendi.");
            }
        }
        redirect('languages.php');
    }

    // Dil Güncelle
    if ($act === 'edit') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $flag = trim($_POST['flag'] ?? '🌐');
        $dir = in_array($_POST['direction'] ?? 'ltr', ['ltr', 'rtl'], true) ? $_POST['direction'] : 'ltr';
        $order = (int)($_POST['sort_order'] ?? 0);
        $active = !empty($_POST['is_active']) ? 1 : 0;

        $lang = q('SELECT * FROM languages WHERE id = ?', [$id])->fetch();
        if ($lang) {
            if ($lang['is_default']) $active = 1; // Varsayılan dil pasif yapılamaz
            q('UPDATE languages SET name=?, flag=?, direction=?, is_active=?, sort_order=? WHERE id=?',
              [$name, $flag, $dir, $active, $order, $id]);
            flash("'{$lang['name']}' dili güncellendi.");
        }
        redirect('languages.php');
    }

    // Durum Değiştir (Aktif/Pasif)
    if ($act === 'toggle_active') {
        $id = (int)($_POST['id'] ?? 0);
        $lang = q('SELECT * FROM languages WHERE id = ?', [$id])->fetch();
        if ($lang) {
            if ($lang['is_default']) {
                flash('Varsayılan dil devre dışı bırakılamaz.', 'err');
            } else {
                $newActive = $lang['is_active'] ? 0 : 1;
                q('UPDATE languages SET is_active = ? WHERE id = ?', [$newActive, $id]);
                flash("'{$lang['name']}' dili " . ($newActive ? 'aktif' : 'pasif') . " yapıldı.");
            }
        }
        redirect('languages.php');
    }

    // Dil Sil
    if ($act === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $lang = q('SELECT * FROM languages WHERE id = ?', [$id])->fetch();
        if ($lang) {
            if ($lang['is_default'] || $lang['code'] === 'tr') {
                flash('Varsayılan veya ana dil (Türkçe) silinemez.', 'err');
            } else {
                q('DELETE FROM translations WHERE lang = ?', [$lang['code']]);
                q('DELETE FROM seo_settings WHERE lang = ?', [$lang['code']]);
                q('DELETE FROM languages WHERE id = ?', [$id]);
                flash("'{$lang['name']}' dili ve ilgili çevirileri silindi.");
            }
        }
        redirect('languages.php');
    }
}

admin_header('Dil Yönetimi', 'languages');
$allLangs = q('SELECT * FROM languages ORDER BY sort_order ASC, id ASC')->fetchAll();
?>
<div class="grid g2" style="align-items:start">
  <!-- Yeni Dil Ekle Kartı -->
  <div class="card">
    <h2><i class="fa-solid fa-plus-circle" style="color:var(--gold)"></i> Yeni Dil Ekle</h2>
    <form method="post" autocomplete="off">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="add">
      <div class="grid g2">
        <div class="field">
          <label>Dil Kodu * (ör: de, fr, ru)</label>
          <input type="text" name="code" placeholder="de" maxlength="10" required pattern="[a-zA-Z]{2,10}" style="text-transform:lowercase">
          <div class="hint">ISO 639-1 küçük harf kodu</div>
        </div>
        <div class="field">
          <label>Dil Adı * (ör: Deutsch)</label>
          <input type="text" name="name" placeholder="Deutsch" required>
        </div>
      </div>
      <div class="grid g3">
        <div class="field">
          <label>Bayrak / Simge</label>
          <input type="text" name="flag" value="🌐" placeholder="🇩🇪 veya 🌐">
          <div class="hint">Emoji bayrak veya sembol</div>
        </div>
        <div class="field">
          <label>Yazım Yönü</label>
          <select name="direction">
            <option value="ltr">Soldan Sağa (LTR)</option>
            <option value="rtl">Sağdan Sola (RTL)</option>
          </select>
        </div>
        <div class="field">
          <label>Sıralama</label>
          <input type="number" name="sort_order" value="<?= count($allLangs) + 1 ?>">
        </div>
      </div>
      <div class="field" style="margin-top:8px">
        <label class="chk">
          <input type="checkbox" name="is_active" value="1" checked>
          <span>Bu dili hemen sitede aktif et</span>
        </label>
      </div>
      <div class="row" style="margin-top:16px">
        <button class="btn"><i class="fa-solid fa-check"></i> Dili Sisteme Ekle</button>
      </div>
      <p class="hint" style="margin-top:14px">
        <i class="fa-solid fa-circle-info"></i> Yeni dil eklendiğinde; sitedeki dil seçicisine otomatik eklenir, SEO ve çeviriler sayfasında anında yeni sütunlar ve sekmeler açılır.
      </p>
    </form>
  </div>

  <!-- Sistemdeki Diller Listesi -->
  <div class="card">
    <div class="row between" style="margin-bottom:12px">
      <h2 style="margin:0"><i class="fa-solid fa-list" style="color:var(--gold)"></i> Sistem Dilleri (<?= count($allLangs) ?>)</h2>
      <a class="btn ghost sm" href="translations.php"><i class="fa-solid fa-language"></i> Çevirileri Düzenle</a>
    </div>
    
    <table class="resp">
      <thead>
        <tr>
          <th>Bayrak</th>
          <th>Kod</th>
          <th>Dil Adı</th>
          <th>Yön</th>
          <th>Durum</th>
          <th style="text-align:right">İşlemler</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($allLangs as $l): ?>
        <tr>
          <td style="font-size:1.4rem;line-height:1"><?= e($l['flag']) ?></td>
          <td><strong style="color:var(--gold)"><?= strtoupper(e($l['code'])) ?></strong></td>
          <td>
            <strong><?= e($l['name']) ?></strong>
            <?php if ($l['is_default']): ?>
              <span class="pill" style="margin-left:6px;background:rgba(197,160,89,0.2);color:var(--gold);font-size:0.7rem">Varsayılan</span>
            <?php endif; ?>
          </td>
          <td><span class="pill"><?= strtoupper(e($l['direction'])) ?></span></td>
          <td>
            <?php if ($l['is_active']): ?>
              <span class="pill" style="background:rgba(74,222,128,0.15);color:#4ade80">Aktif</span>
            <?php else: ?>
              <span class="pill" style="background:rgba(239,68,68,0.15);color:#ef4444">Pasif</span>
            <?php endif; ?>
          </td>
          <td style="text-align:right">
            <div class="row" style="justify-content:flex-end;gap:6px">
              <?php if (!$l['is_default']): ?>
                <form method="post" style="display:inline"><?= csrf_field() ?>
                  <input type="hidden" name="action" value="toggle_active">
                  <input type="hidden" name="id" value="<?= (int)$l['id'] ?>">
                  <button class="btn ghost sm" title="<?= $l['is_active'] ? 'Dili Pasifleştir' : 'Dili Aktifleştir' ?>">
                    <i class="fa-solid <?= $l['is_active'] ? 'fa-eye-slash' : 'fa-eye' ?>"></i>
                  </button>
                </form>
                <?php if ($l['code'] !== 'tr'): ?>
                  <form method="post" data-confirm="'<?= e($l['name']) ?>' dilini silmek istediğinize emin misiniz? Bu dile ait çeviriler de silinecektir." style="display:inline"><?= csrf_field() ?>
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int)$l['id'] ?>">
                    <button class="btn danger sm" title="Sil"><i class="fa-solid fa-trash"></i></button>
                  </form>
                <?php endif; ?>
              <?php else: ?>
                <span class="muted" style="font-size:0.75rem">—</span>
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
