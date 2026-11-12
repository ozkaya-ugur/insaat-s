<?php
require_once __DIR__ . '/_layout.php';
require_perm('translations', 'view');
admin_readonly(!admin_can('translations', 'edit'));

$activeLangs = get_languages();
$LANGS = [];
foreach ($activeLangs as $al) {
    $LANGS[$al['code']] = $al['flag'] . ' ' . $al['name'] . ' (' . strtoupper($al['code']) . ')';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (!admin_can('translations', 'edit')) admin_deny();
    $act = $_POST['action'] ?? '';
    if ($act === 'save') {
        // Anahtarlarda nokta olduğu için (hero.tag) indeks bazlı dizilerle gönderilir.
        $keys = $_POST['k'] ?? [];
        $n = 0;
        foreach ($keys as $i => $key) {
            $key = trim($key);
            if (!preg_match('/^[A-Za-z0-9_.\-]{1,120}$/', $key)) continue;
            foreach (array_keys($LANGS) as $l) {
                $val = trim((string)($_POST[$l][$i] ?? ''));
                q('INSERT INTO translations (lang,t_key,t_value) VALUES (?,?,?) ON DUPLICATE KEY UPDATE t_value=VALUES(t_value)', [$l, $key, $val]);
            }
            $n++;
        }
        flash("$n çeviri anahtarı kaydedildi.");
    } elseif ($act === 'add') {
        $key = trim($_POST['new_key'] ?? '');
        if (!preg_match('/^[A-Za-z0-9_.\-]{1,120}$/', $key)) flash('Anahtar yalnızca harf, rakam, nokta, tire ve alt çizgi içerebilir.', 'err');
        else {
            foreach (array_keys($LANGS) as $l) q('INSERT IGNORE INTO translations (lang,t_key,t_value) VALUES (?,?,?)', [$l, $key, '']);
            flash('Anahtar eklendi. Metinleri doldurup kaydedin.');
        }
    } elseif ($act === 'delete') {
        q('DELETE FROM translations WHERE t_key=?', [trim($_POST['key'] ?? '')]);
        flash('Anahtar silindi.');
    }
    redirect('translations.php');
}

admin_header('Çeviriler', 'translations');
$data = [];
foreach (q('SELECT lang,t_key,t_value FROM translations ORDER BY t_key')->fetchAll() as $r) $data[$r['t_key']][$r['lang']] = $r['t_value'];
ksort($data);
?>
<div class="card">
  <div class="row between">
    <h2 style="margin:0"><i class="fa-solid fa-plus-circle" style="color:var(--gold)"></i> Yeni Çeviri Anahtarı</h2>
    <a class="btn ghost sm" href="languages.php"><i class="fa-solid fa-globe"></i> Dil Yönetimi</a>
  </div>
  <form method="post" class="row" style="margin-top:14px"><?= csrf_field() ?>
    <input type="text" name="new_key" placeholder="ör. nav.urban_renewal" style="max-width:320px" required>
    <button class="btn ghost" name="action" value="add"><i class="fa-solid fa-plus"></i> Ekle</button>
  </form>
  <p class="hint">Ön yüzde <code>data-i18n="anahtar"</code> özniteliği taşıyan her öğenin metni, kullanıcının seçtiği dile göre otomatik buradan gelir.</p>
</div>

<form method="post" class="card tr-form"><?= csrf_field() ?>
  <div class="row between" style="margin-bottom:12px">
    <h2 style="margin:0"><i class="fa-solid fa-language" style="color:var(--gold)"></i> Site Çevirileri (<?= count($data) ?>)</h2>
    <button class="btn" name="action" value="save"><i class="fa-solid fa-floppy-disk"></i> Tümünü Kaydet</button>
  </div>
  <div style="overflow-x:auto">
    <table class="resp trans-table">
      <thead>
        <tr>
          <th style="width:20%">Anahtar (Key)</th>
          <?php foreach ($LANGS as $l => $n): ?>
            <th><?= e($n) ?></th>
          <?php endforeach; ?>
          <th style="width:60px"></th>
        </tr>
      </thead>
      <tbody>
      <?php $i = 0; foreach ($data as $key => $vals): ?>
        <tr>
          <td data-label="Anahtar">
            <input type="hidden" name="k[<?= $i ?>]" value="<?= e($key) ?>">
            <code><?= e($key) ?></code>
          </td>
          <?php foreach (array_keys($LANGS) as $l): $langTitle = $LANGS[$l] ?? strtoupper($l); ?>
            <td data-label="<?= e($langTitle) ?>">
              <input type="text" name="<?= $l ?>[<?= $i ?>]" value="<?= e($vals[$l] ?? '') ?>" <?= in_array($l, ['ar','fa','ur'], true) ? 'dir="rtl"' : '' ?> placeholder="<?= !empty($vals['tr']) && $l !== 'tr' ? 'Varsayılan: ' . e($vals['tr']) : '' ?>">
            </td>
          <?php endforeach; ?>
          <td data-label="İşlem">
            <button class="btn danger sm" form="del-<?= $i ?>" type="submit" title="Sil"><i class="fa-solid fa-trash"></i><span>Sil</span></button>
          </td>
        </tr>
      <?php $i++; endforeach; if (!$data): ?><tr><td colspan="<?= count($LANGS) + 2 ?>" class="muted">Henüz çeviri yok.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="row" style="margin-top:16px"><button class="btn" name="action" value="save"><i class="fa-solid fa-floppy-disk"></i> Tümünü Kaydet</button></div>
</form>

<?php $i = 0; foreach ($data as $key => $_): ?>
<form id="del-<?= $i ?>" method="post" data-confirm="'<?= e($key) ?>' anahtarı tüm dillerden silinsin mi?"><?= csrf_field() ?>
  <input type="hidden" name="action" value="delete"><input type="hidden" name="key" value="<?= e($key) ?>"></form>
<?php $i++; endforeach; ?>
<?php admin_footer();
