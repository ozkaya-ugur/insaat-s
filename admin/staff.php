<?php
require_once __DIR__ . '/_layout.php';
$me = require_perm('staff', 'view');
$tree = admin_perm_tree();
$canCreate = admin_can('staff', 'create');
$canEdit = admin_can('staff', 'edit');
$canDelete = admin_can('staff', 'delete');
$canPerm = admin_can('staff', 'permissions');
$isOwner = admin_is_owner($me);

// Formdan gelen yetkileri ağaca göre süzer (uydurma anahtar kaydedilmez)
function staff_collect_perms(array $post, array $tree): array {
    $out = [];
    foreach ($tree as $page => $meta) {
        $sent = $post['perm'][$page] ?? [];
        if (!is_array($sent)) continue;
        $valid = array_values(array_intersect(array_keys($meta['sections']), array_map('strval', $sent)));
        if ($valid) $out[$page] = $valid;
    }
    return $out;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $act = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    try {
        if ($act === 'create') {
            if (!$canCreate) admin_deny();
            $u = trim((string)($_POST['username'] ?? ''));
            $p = (string)($_POST['password'] ?? '');
            $name = trim((string)($_POST['full_name'] ?? ''));
            if (!preg_match('/^[A-Za-z0-9_.-]{3,40}$/', $u)) throw new RuntimeException('Kullanıcı adı 3-40 karakter olmalı; harf, rakam, nokta, tire ve alt çizgi kullanın.');
            if (strlen($p) < 8) throw new RuntimeException('Şifre en az 8 karakter olmalı.');
            if (q('SELECT id FROM admins WHERE username=?', [$u])->fetch()) throw new RuntimeException('Bu kullanıcı adı kullanımda.');
            $role = ($isOwner && ($_POST['role'] ?? 'staff') === 'owner') ? 'owner' : 'staff';
            $perms = $role === 'owner' ? null : json_encode(staff_collect_perms($_POST, $tree), JSON_UNESCAPED_UNICODE);
            q('INSERT INTO admins (username,password_hash,full_name,role,permissions,is_active,must_change_password) VALUES (?,?,?,?,?,?,?)', [
                $u, password_hash($p, PASSWORD_DEFAULT), $name, $role, $perms,
                isset($_POST['is_active']) ? 1 : 0, isset($_POST['must_change']) ? 1 : 0,
            ]);
            flash('Personel eklendi: ' . $u);
        } elseif ($act === 'update') {
            if (!$canEdit) admin_deny();
            $row = q('SELECT * FROM admins WHERE id=?', [$id])->fetch();
            if (!$row) throw new RuntimeException('Kayıt bulunamadı.');
            if ($row['role'] === 'owner' && !$isOwner) throw new RuntimeException('Sahip hesabını yalnızca sahip düzenleyebilir.');
            if ($id === (int)$me['id']) throw new RuntimeException('Kendi kaydınızı buradan düzenleyemezsiniz; Hesabım sayfasını kullanın.');
            $u = trim((string)($_POST['username'] ?? ''));
            $name = trim((string)($_POST['full_name'] ?? ''));
            if (!preg_match('/^[A-Za-z0-9_.-]{3,40}$/', $u)) throw new RuntimeException('Geçersiz kullanıcı adı.');
            if (q('SELECT id FROM admins WHERE username=? AND id<>?', [$u, $id])->fetch()) throw new RuntimeException('Bu kullanıcı adı kullanımda.');

            $role = $row['role'];
            if ($isOwner && isset($_POST['role'])) $role = $_POST['role'] === 'owner' ? 'owner' : 'staff';
            if ($row['role'] === 'owner' && $role !== 'owner') {
                $owners = (int)q("SELECT COUNT(*) FROM admins WHERE role='owner' AND is_active=1")->fetchColumn();
                if ($owners <= 1) throw new RuntimeException('Sistemde en az bir aktif sahip hesabı kalmalı.');
            }
            $active = isset($_POST['is_active']) ? 1 : 0;
            if ($id === (int)$me['id'] && !$active) throw new RuntimeException('Kendi hesabınızı pasife alamazsınız.');

            $perms = $row['permissions'];
            if ($role === 'owner') $perms = null;
            elseif ($canPerm) $perms = json_encode(staff_collect_perms($_POST, $tree), JSON_UNESCAPED_UNICODE);

            $p = (string)($_POST['password'] ?? '');
            if ($p !== '') {
                if (strlen($p) < 8) throw new RuntimeException('Yeni şifre en az 8 karakter olmalı.');
                q('UPDATE admins SET username=?,full_name=?,role=?,permissions=?,is_active=?,must_change_password=?,password_hash=? WHERE id=?', [
                    $u, $name, $role, $perms, $active, isset($_POST['must_change']) ? 1 : 0, password_hash($p, PASSWORD_DEFAULT), $id,
                ]);
                flash('Personel güncellendi ve şifresi yenilendi: ' . $u);
            } else {
                q('UPDATE admins SET username=?,full_name=?,role=?,permissions=?,is_active=?,must_change_password=? WHERE id=?', [
                    $u, $name, $role, $perms, $active, isset($_POST['must_change']) ? 1 : 0, $id,
                ]);
                flash('Personel güncellendi: ' . $u);
            }
        } elseif ($act === 'delete') {
            if (!$canDelete) admin_deny();
            if ($id === (int)$me['id']) throw new RuntimeException('Kendi hesabınızı silemezsiniz.');
            $row = q('SELECT * FROM admins WHERE id=?', [$id])->fetch();
            if (!$row) throw new RuntimeException('Kayıt bulunamadı.');
            if ($row['role'] === 'owner') {
                if (!$isOwner) throw new RuntimeException('Sahip hesabını yalnızca sahip silebilir.');
                $owners = (int)q("SELECT COUNT(*) FROM admins WHERE role='owner'")->fetchColumn();
                if ($owners <= 1) throw new RuntimeException('Son sahip hesabı silinemez.');
            }
            q('DELETE FROM admins WHERE id=?', [$id]);
            flash('Personel silindi: ' . $row['username']);
        }
    } catch (Throwable $ex) { flash($ex->getMessage(), 'err'); }
    redirect('staff.php' . (isset($_POST['back_edit']) ? '?edit=' . (int)$_POST['back_edit'] : ''));
}

$editId = (int)($_GET['edit'] ?? 0);
$editRow = null;
if ($editId && $canEdit) {
    $editRow = q('SELECT * FROM admins WHERE id=?', [$editId])->fetch() ?: null;
    if ($editRow && $editRow['role'] === 'owner' && !$isOwner) $editRow = null;
}
$rows = q('SELECT id,username,full_name,role,permissions,is_active,last_login,created_at FROM admins ORDER BY (role=\'owner\') DESC, id')->fetchAll();

// Yetki matrisi (checkbox ızgarası)
function staff_perm_matrix(array $tree, array $selected, bool $disabled = false): string {
    $h = '<div class="perm-matrix">';
    foreach ($tree as $page => $meta) {
        $sel = $selected[$page] ?? [];
        $h .= '<div class="perm-row"><div class="perm-page"><b>' . e($meta['label']) . '</b><label class="perm-all"><input type="checkbox" data-all="' . e($page) . '"' . ($disabled ? ' disabled' : '') . '> tümü</label></div><div class="perm-secs">';
        foreach ($meta['sections'] as $sec => $label) {
            $on = in_array($sec, $sel, true) ? ' checked' : '';
            $h .= '<label class="perm-sec"><input type="checkbox" name="perm[' . e($page) . '][]" value="' . e($sec) . '"' . $on . ($disabled ? ' disabled' : '') . '> ' . e($label) . '</label>';
        }
        $h .= '</div></div>';
    }
    return $h . '</div>';
}

admin_header($editRow ? 'Personel Düzenle' : 'Personel & Yetkiler', 'staff');
?>
<style>
.perm-matrix{border:1px solid rgba(255,255,255,.08);border-radius:10px;overflow:hidden}
.perm-row{display:flex;gap:14px;align-items:flex-start;padding:10px 14px;border-bottom:1px solid rgba(255,255,255,.06);flex-wrap:wrap}
.perm-row:last-child{border-bottom:0}
.perm-page{min-width:210px;display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.perm-all{font-size:12px;color:var(--gold);display:inline-flex;align-items:center;gap:5px;cursor:pointer}
.perm-secs{display:flex;gap:14px;flex-wrap:wrap;flex:1}
.perm-sec{display:inline-flex;align-items:center;gap:6px;font-size:13px;cursor:pointer}
.perm-sec input,.perm-all input{accent-color:var(--gold)}
.role-pill{font-size:11px;padding:3px 9px;border-radius:999px;font-weight:700;letter-spacing:.4px}
.role-owner{background:rgba(212,175,55,.16);color:var(--gold)}
.role-staff{background:rgba(120,160,255,.14);color:#9db8ff}
.st-off{background:rgba(220,80,80,.16);color:#ff8f8f}
.st-on{background:rgba(80,200,120,.16);color:#6ede9b}
</style>

<?php if ($editRow): $sel = admin_perms(['permissions' => $editRow['permissions']]); ?>
<div class="card">
  <div class="row between" style="margin-bottom:14px">
    <h2><i class="fa-solid fa-user-pen" style="color:var(--gold)"></i> <?= e($editRow['username']) ?> — Düzenle</h2>
    <a class="btn ghost sm" href="staff.php">Listeye dön</a>
  </div>
  <form method="post"><?= csrf_field() ?>
    <input type="hidden" name="action" value="update">
    <input type="hidden" name="id" value="<?= (int)$editRow['id'] ?>">
    <input type="hidden" name="back_edit" value="<?= (int)$editRow['id'] ?>">
    <div class="grid g2">
      <div class="field"><label>Ad Soyad</label><input type="text" name="full_name" value="<?= e($editRow['full_name']) ?>"></div>
      <div class="field"><label>Kullanıcı Adı</label><input type="text" name="username" value="<?= e($editRow['username']) ?>" required></div>
      <div class="field"><label>Yeni Şifre <span class="muted">(boş bırakılırsa değişmez)</span></label><input type="text" name="password" autocomplete="new-password" placeholder="En az 8 karakter"></div>
      <?php if ($isOwner): ?>
      <div class="field"><label>Rol</label>
        <select name="role">
          <option value="staff" <?= $editRow['role'] === 'staff' ? 'selected' : '' ?>>Personel (yetkilerle sınırlı)</option>
          <option value="owner" <?= $editRow['role'] === 'owner' ? 'selected' : '' ?>>Sahip (tüm yetkiler)</option>
        </select>
      </div>
      <?php endif; ?>
    </div>
    <div class="row" style="gap:22px;margin:6px 0 18px">
      <label class="perm-sec"><input type="checkbox" name="is_active" <?= (int)$editRow['is_active'] === 1 ? 'checked' : '' ?>> Hesap aktif (giriş yapabilir)</label>
      <label class="perm-sec"><input type="checkbox" name="must_change" <?= (int)$editRow['must_change_password'] === 1 ? 'checked' : '' ?>> İlk girişte şifre değiştirme zorunlu</label>
    </div>
    <?php if ($canPerm && $editRow['role'] !== 'owner'): ?>
      <h3 style="margin:0 0 10px;font-size:15px">Sayfa ve Bölüm Yetkileri</h3>
      <?= staff_perm_matrix($tree, $sel) ?>
    <?php elseif ($editRow['role'] === 'owner'): ?>
      <p class="muted">Sahip rolü tüm sayfa ve bölümlere otomatik olarak erişir.</p>
    <?php endif; ?>
    <div class="row" style="margin-top:18px"><button class="btn"><i class="fa-solid fa-floppy-disk"></i> Kaydet</button></div>
  </form>
</div>
<?php endif; ?>

<?php if ($canCreate): ?>
<div class="card" style="margin-top:24px">
  <h2><i class="fa-solid fa-user-plus" style="color:var(--gold)"></i> Yeni Personel Ekle</h2>
  <p class="muted" style="margin-top:-6px">Şifreyi siz belirlersiniz; personel ilk girişte değiştirebilir.</p>
  <form method="post"><?= csrf_field() ?>
    <input type="hidden" name="action" value="create">
    <div class="grid g2">
      <div class="field"><label>Ad Soyad</label><input type="text" name="full_name"></div>
      <div class="field"><label>Kullanıcı Adı</label><input type="text" name="username" required placeholder="ornek: ahmet.yilmaz"></div>
      <div class="field"><label>Şifre <span class="muted">(en az 8 karakter)</span></label><input type="text" name="password" required autocomplete="new-password"></div>
      <?php if ($isOwner): ?>
      <div class="field"><label>Rol</label>
        <select name="role"><option value="staff">Personel (yetkilerle sınırlı)</option><option value="owner">Sahip (tüm yetkiler)</option></select>
      </div>
      <?php endif; ?>
    </div>
    <div class="row" style="gap:22px;margin:6px 0 18px">
      <label class="perm-sec"><input type="checkbox" name="is_active" checked> Hesap aktif</label>
      <label class="perm-sec"><input type="checkbox" name="must_change"> İlk girişte şifre değiştirme zorunlu</label>
    </div>
    <?php if ($canPerm): ?>
      <h3 style="margin:0 0 10px;font-size:15px">Sayfa ve Bölüm Yetkileri</h3>
      <?= staff_perm_matrix($tree, []) ?>
    <?php endif; ?>
    <div class="row" style="margin-top:18px"><button class="btn"><i class="fa-solid fa-user-plus"></i> Personel Ekle</button></div>
  </form>
</div>
<?php endif; ?>

<div class="card" style="margin-top:24px">
  <h2><i class="fa-solid fa-users" style="color:var(--gold)"></i> Personel Listesi</h2>
  <table class="resp"><thead><tr><th>Kullanıcı</th><th>Ad Soyad</th><th>Rol</th><th>Durum</th><th>Yetki</th><th>Son Giriş</th><th></th></tr></thead><tbody>
  <?php foreach ($rows as $r): $rp = admin_perms(['permissions' => $r['permissions']]); $cnt = 0; foreach ($rp as $v) $cnt += is_array($v) ? count($v) : 0; ?>
    <tr>
      <td><b><?= e($r['username']) ?></b><?= (int)$r['id'] === (int)$me['id'] ? ' <span class="muted">(siz)</span>' : '' ?></td>
      <td><?= e($r['full_name']) ?></td>
      <td><span class="role-pill <?= $r['role'] === 'owner' ? 'role-owner' : 'role-staff' ?>"><?= $r['role'] === 'owner' ? 'SAHİP' : 'PERSONEL' ?></span></td>
      <td><span class="role-pill <?= (int)$r['is_active'] === 1 ? 'st-on' : 'st-off' ?>"><?= (int)$r['is_active'] === 1 ? 'AKTİF' : 'PASİF' ?></span></td>
      <td class="muted"><?= $r['role'] === 'owner' ? 'Tümü' : ($cnt . ' bölüm / ' . count($rp) . ' sayfa') ?></td>
      <td class="muted"><?= $r['last_login'] ? e(date('d.m.Y H:i', strtotime($r['last_login']))) : '—' ?></td>
      <td style="text-align:right;white-space:nowrap">
        <?php if ($canEdit && (int)$r['id'] !== (int)$me['id'] && ($r['role'] !== 'owner' || $isOwner)): ?>
          <a class="btn ghost sm" href="staff.php?edit=<?= (int)$r['id'] ?>">Düzenle</a>
        <?php endif; ?>
        <?php if ($canDelete && (int)$r['id'] !== (int)$me['id'] && ($r['role'] !== 'owner' || $isOwner)): ?>
          <form method="post" style="display:inline" data-confirm="<?= e($r['username']) ?> silinsin mi?"><?= csrf_field() ?>
            <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
            <button class="btn danger sm">Sil</button>
          </form>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody></table>
</div>

<script>
document.querySelectorAll('input[data-all]').forEach(master=>{
  const page = master.dataset.all;
  const boxes = () => document.querySelectorAll('input[name="perm['+page+'][]"]');
  const sync = () => { const b=[...boxes()]; master.checked = b.length>0 && b.every(x=>x.checked); };
  master.addEventListener('change', ()=>{ boxes().forEach(x=>{ if(!x.disabled) x.checked = master.checked; }); });
  boxes().forEach(x=>x.addEventListener('change', sync));
  sync();
});
</script>
<?php admin_footer(); ?>
