<?php
require_once __DIR__ . '/_layout.php';
$me = require_perm('messages', 'view');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);
    $act = $_POST['action'] ?? '';

    if ($act === 'delete' && $id) {
        if (!admin_can('messages', 'delete')) admin_deny();
        q('DELETE FROM messages WHERE id=?', [$id]);
        flash('Mesaj silindi.');
        redirect('messages.php');
    }

    if ($act === 'unread' && $id) {
        if (!admin_can('messages', 'process')) admin_deny();
        q('UPDATE messages SET is_read=0 WHERE id=?', [$id]);
        redirect('messages.php?view=' . $id);
    }

    if ($act === 'all_read') {
        if (!admin_can('messages', 'process')) admin_deny();
        q('UPDATE messages SET is_read=1 WHERE is_read=0');
        flash('Tümü okundu işaretlendi.');
        redirect('messages.php');
    }

    if ($act === 'set_result' && $id) {
        if (!admin_can('messages', 'process')) admin_deny();
        $status = trim((string)($_POST['result_status'] ?? 'beklemede'));
        $note = trim((string)($_POST['result_note'] ?? ''));
        if (!in_array($status, ['beklemede', 'olumlu', 'olumsuz'], true)) {
            flash('Geçersiz sonuç durumu.', 'err');
            redirect('messages.php?view=' . $id);
        }
        if ($status === 'olumsuz' && $note === '') {
            flash('Olumsuz sonuç için neden notu zorunludur.', 'err');
            redirect('messages.php?view=' . $id);
        }
        q('UPDATE messages SET result_status=?, result_note=?, result_admin_id=?, result_at=NOW(), is_read=1, assigned_admin_id=COALESCE(assigned_admin_id, ?), assigned_at=COALESCE(assigned_at, NOW()) WHERE id=?', [
            $status, $note !== '' ? mb_substr($note, 0, 5000) : null, (int)$me['id'], (int)$me['id'], $id,
        ]);
        flash('Talep sonucu güncellendi.');
        redirect('messages.php?view=' . $id);
    }
}

$filter = trim($_GET['filter'] ?? '');

if (isset($_GET['export'])) {
    if (!admin_can('messages', 'export')) admin_deny();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="mesajlar-' . date('Ymd') . '.csv"');
    $o = fopen('php://output', 'w'); fwrite($o, "\xEF\xBB\xBF");
    fputcsv($o, ['ID','Ad','Telefon','E-posta','Proje/Konu','Mesaj','Kaynak','Atanan Personel','Sonuç','Olumsuz Neden','Sonuçlayan','Tarih']);
    if ($filter === 'iletisim') { $sql = "SELECT m.*,a1.username AS assigned_username,a2.username AS result_username FROM messages m LEFT JOIN admins a1 ON a1.id=m.assigned_admin_id LEFT JOIN admins a2 ON a2.id=m.result_admin_id WHERE m.source NOT IN ('kentsel_donusum','proje_detay') ORDER BY m.id DESC"; $params = []; }
    else { $sql = 'SELECT m.*,a1.username AS assigned_username,a2.username AS result_username FROM messages m LEFT JOIN admins a1 ON a1.id=m.assigned_admin_id LEFT JOIN admins a2 ON a2.id=m.result_admin_id ' . ($filter ? 'WHERE m.source = ? ' : '') . 'ORDER BY m.id DESC'; $params = $filter ? [$filter] : []; }
    foreach (q($sql, $params)->fetchAll() as $m) {
        $clean = fn($v) => preg_match('/^[=+\-@]/', (string)$v) ? "'" . $v : $v;
        fputcsv($o, [
            $m['id'], $clean($m['full_name']), $clean($m['phone']), $clean($m['email']), $clean($m['project']),
            $clean($m['message']), $m['source'], $m['assigned_username'] ?: '', $m['result_status'] ?: 'beklemede',
            $m['result_note'] ?: '', $m['result_username'] ?: '', $m['created_at']
        ]);
    }
    exit;
}

admin_header('Mesajlar & Başvurular', 'messages');

if (isset($_GET['view'])) {
    $m = q('SELECT m.*,a1.username AS assigned_username,a2.username AS result_username FROM messages m LEFT JOIN admins a1 ON a1.id=m.assigned_admin_id LEFT JOIN admins a2 ON a2.id=m.result_admin_id WHERE m.id=?', [(int)$_GET['view']])->fetch();
    if ($m) {
        // Mesajı açan personel henüz atanmamışsa otomatik ata
        q('UPDATE messages SET is_read=1, assigned_admin_id=COALESCE(assigned_admin_id, ?), assigned_at=COALESCE(assigned_at, NOW()) WHERE id=?', [(int)$me['id'], (int)$m['id']]);
        $m = q('SELECT m.*,a1.username AS assigned_username,a2.username AS result_username FROM messages m LEFT JOIN admins a1 ON a1.id=m.assigned_admin_id LEFT JOIN admins a2 ON a2.id=m.result_admin_id WHERE m.id=?', [(int)$m['id']])->fetch();
        $isKd = ($m['source'] === 'kentsel_donusum');
        $st = $m['result_status'] ?: 'beklemede';
        ?>
        <div class="card">
          <div class="row between">
            <h2 style="margin:0">
              <?= e($m['full_name']) ?>
              <?php if ($isKd): ?>
                <span class="pill" style="background:rgba(197,160,89,0.25);color:var(--gold);margin-left:10px">Kentsel Dönüşüm Başvurusu</span>
              <?php elseif ($m['source'] === 'proje_detay'): ?>
                <span class="pill" style="background:rgba(111,168,220,0.2);color:#6fa8dc;margin-left:10px">Proje Detay Talebi</span>
              <?php endif; ?>
            </h2>
            <a class="btn ghost sm" href="messages.php<?= $filter ? '?filter=' . urlencode($filter) : '' ?>">← Listeye dön</a>
          </div>
          <p class="muted" style="margin:10px 0 16px"><?= e(date('d.m.Y H:i', strtotime($m['created_at']))) ?> · Kaynak: <strong><?= e($m['source']) ?></strong> · IP: <?= e($m['ip']) ?></p>

          <div class="grid g4" style="margin-bottom:14px">
            <div><label>Atanan Personel</label><strong><?= e($m['assigned_username'] ?: '—') ?></strong></div>
            <div><label>Atama Zamanı</label><?= $m['assigned_at'] ? e(date('d.m.Y H:i', strtotime($m['assigned_at']))) : '—' ?></div>
            <div><label>Sonuç</label>
              <?php if ($st === 'olumlu'): ?><span class="pill" style="background:rgba(80,200,120,0.18);color:#6ede9b">Olumlu</span>
              <?php elseif ($st === 'olumsuz'): ?><span class="pill" style="background:rgba(220,80,80,0.18);color:#ff8f8f">Olumsuz</span>
              <?php else: ?><span class="pill" style="background:rgba(255,255,255,0.08);color:var(--dim)">Beklemede</span><?php endif; ?>
            </div>
            <div><label>Sonuçlayan</label><?= e($m['result_username'] ?: '—') ?></div>
          </div>

          <div class="grid g3" style="margin-bottom:16px">
            <div><label>Telefon</label><?= $m['phone'] ? '<a style="color:var(--gold);font-weight:600" href="tel:' . e(preg_replace('/[^0-9+]/', '', $m['phone'])) . '">' . e($m['phone']) . '</a>' : '—' ?></div>
            <div><label>E-posta</label><?= $m['email'] ? '<a style="color:var(--gold);font-weight:600" href="mailto:' . e($m['email']) . '">' . e($m['email']) . '</a>' : '—' ?></div>
            <div><label>Konu / Proje</label><?= e($m['project'] ?: '—') ?></div>
          </div>
          <label><?= $isKd ? 'Başvuru ve Bina Detayları' : 'Mesaj İçeriği' ?></label>
          <div class="msg-body" style="<?= $isKd ? 'border-color:rgba(197,160,89,0.3);background:rgba(197,160,89,0.03)' : '' ?>"><?= e($m['message']) ?></div>

          <?php if ($st === 'olumsuz' && $m['result_note']): ?>
            <div class="card" style="margin-top:14px;background:rgba(220,80,80,0.06);border-color:rgba(220,80,80,0.26)">
              <label>Olumsuz Neden Notu</label>
              <div class="msg-body" style="margin-top:8px"><?= e($m['result_note']) ?></div>
            </div>
          <?php endif; ?>

          <?php if (admin_can('messages', 'process')): ?>
          <form method="post" class="card" style="margin-top:16px">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="set_result">
            <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
            <h3 style="margin:0 0 10px">Talep Sonucu</h3>
            <div class="grid g2" style="align-items:start">
              <div class="field" style="margin:0"><label>Durum</label>
                <select name="result_status" id="resultStatus" required>
                  <option value="beklemede" <?= $st === 'beklemede' ? 'selected' : '' ?>>Beklemede</option>
                  <option value="olumlu" <?= $st === 'olumlu' ? 'selected' : '' ?>>Olumlu</option>
                  <option value="olumsuz" <?= $st === 'olumsuz' ? 'selected' : '' ?>>Olumsuz</option>
                </select>
              </div>
              <div class="field" style="margin:0"><label>Olumsuz Neden Notu <span id="negReq" class="muted" style="display:none">(zorunlu)</span></label>
                <textarea name="result_note" id="resultNote" rows="3" placeholder="Olumsuz seçilirse neden notu zorunludur."><?= e((string)$m['result_note']) ?></textarea>
              </div>
            </div>
            <div class="row" style="margin-top:12px">
              <button class="btn">Sonucu Kaydet</button>
            </div>
          </form>
          <div class="row" style="margin-top:12px">
            <form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$m['id'] ?>"><button class="btn ghost" name="action" value="unread">Okunmadı yap</button></form>
            <?php if (admin_can('messages', 'delete')): ?><form method="post" data-confirm="Mesaj silinsin mi?"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$m['id'] ?>"><button class="btn danger" name="action" value="delete">Sil</button></form><?php endif; ?>
          </div>
          <script>
          (function(){
            const status = document.getElementById('resultStatus');
            const note = document.getElementById('resultNote');
            const req = document.getElementById('negReq');
            if (!status || !note || !req) return;
            const sync = () => {
              const neg = status.value === 'olumsuz';
              note.required = neg;
              req.style.display = neg ? 'inline' : 'none';
            };
            status.addEventListener('change', sync);
            sync();
          })();
          </script>
          <?php endif; ?>
        </div>
        <?php admin_footer(); exit;
    }
}

$page = max(1, (int)($_GET['p'] ?? 1)); $per = 25;
if ($filter === 'iletisim') { $whereSql = "WHERE m.source NOT IN ('kentsel_donusum','proje_detay') "; $whereParams = []; }
else { $whereSql = $filter ? 'WHERE m.source = ? ' : ''; $whereParams = $filter ? [$filter] : []; }

$total = (int)q("SELECT COUNT(*) FROM messages m $whereSql", $whereParams)->fetchColumn();
$countKd = (int)q("SELECT COUNT(*) FROM messages WHERE source = 'kentsel_donusum'")->fetchColumn();
$countPd = (int)q("SELECT COUNT(*) FROM messages WHERE source = 'proje_detay'")->fetchColumn();
$countContact = (int)q("SELECT COUNT(*) FROM messages WHERE source NOT IN ('kentsel_donusum','proje_detay')")->fetchColumn();

$rows = q("SELECT m.*,a1.username AS assigned_username FROM messages m LEFT JOIN admins a1 ON a1.id=m.assigned_admin_id $whereSql ORDER BY m.id DESC LIMIT $per OFFSET " . (($page - 1) * $per), $whereParams)->fetchAll();
?>
<div class="card">
  <div class="row between" style="margin-bottom:16px">
    <div class="row" style="gap:8px">
      <a class="btn sm <?= $filter === '' ? '' : 'ghost' ?>" href="messages.php">Tümü (<?= $countKd + $countPd + $countContact ?>)</a>
      <a class="btn sm <?= $filter === 'kentsel_donusum' ? '' : 'ghost' ?>" href="messages.php?filter=kentsel_donusum"><i class="fa-solid fa-recycle" style="color:var(--gold)"></i> Kentsel Dönüşüm Başvuruları (<?= $countKd ?>)</a>
      <a class="btn sm <?= $filter === 'proje_detay' ? '' : 'ghost' ?>" href="messages.php?filter=proje_detay"><i class="fa-solid fa-building" style="color:#6fa8dc"></i> Proje Talepleri (<?= $countPd ?>)</a>
      <a class="btn sm <?= $filter === 'iletisim' ? '' : 'ghost' ?>" href="messages.php?filter=iletisim">İletişim Formu (<?= $countContact ?>)</a>
    </div>

    <div class="row">
      <?php if (admin_can('messages', 'export')): ?><a class="btn ghost sm" href="messages.php?export=1<?= $filter ? '&filter=' . urlencode($filter) : '' ?>"><i class="fa-solid fa-file-csv"></i> CSV İndir</a><?php endif; ?>
      <?php if (admin_can('messages', 'process')): ?><form method="post"><?= csrf_field() ?><button class="btn ghost sm" name="action" value="all_read">Tümünü okundu yap</button></form><?php endif; ?>
    </div>
  </div>

  <table class="resp">
    <thead><tr><th>Ad Soyad</th><th>Tür</th><th>Atanan</th><th>Durum</th><th>Telefon</th><th>E-posta</th><th>Konu / Proje</th><th>Tarih</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $m): $isKd = ($m['source'] === 'kentsel_donusum'); $st = $m['result_status'] ?: 'beklemede'; ?>
      <tr class="<?= $m['is_read'] ? '' : 'msg-unread' ?>">
        <td><a href="messages.php?view=<?= (int)$m['id'] ?><?= $filter ? '&filter=' . urlencode($filter) : '' ?>" style="color:var(--text);font-weight:<?= $m['is_read'] ? '500' : '700' ?>"><?= e($m['full_name']) ?></a></td>
        <td>
          <?php if ($isKd): ?><span class="pill" style="background:rgba(197,160,89,0.25);color:var(--gold)">Kentsel Dönüşüm</span>
          <?php elseif ($m['source'] === 'proje_detay'): ?><span class="pill" style="background:rgba(111,168,220,0.2);color:#6fa8dc">Proje Talebi</span>
          <?php else: ?><span class="pill" style="background:rgba(255,255,255,0.06);color:var(--dim)">İletişim</span><?php endif; ?>
        </td>
        <td><?= e($m['assigned_username'] ?: '—') ?></td>
        <td>
          <?php if ($st === 'olumlu'): ?><span class="pill" style="background:rgba(80,200,120,0.18);color:#6ede9b">Olumlu</span>
          <?php elseif ($st === 'olumsuz'): ?><span class="pill" style="background:rgba(220,80,80,0.18);color:#ff8f8f">Olumsuz</span>
          <?php else: ?><span class="pill" style="background:rgba(255,255,255,0.08);color:var(--dim)">Beklemede</span><?php endif; ?>
        </td>
        <td><?= e($m['phone']) ?></td>
        <td><?= e($m['email']) ?></td>
        <td><?= e(mb_strimwidth($m['project'] ?: '—', 0, 30, '...')) ?></td>
        <td class="muted"><?= e(date('d.m.Y H:i', strtotime($m['created_at']))) ?></td>
        <td style="text-align:right"><a class="btn ghost sm" href="messages.php?view=<?= (int)$m['id'] ?><?= $filter ? '&filter=' . urlencode($filter) : '' ?>">Aç</a></td>
      </tr>
    <?php endforeach; if (!$rows): ?>
      <tr><td colspan="9" class="muted" style="text-align:center;padding:30px">Kayıtlı mesaj veya başvuru bulunmamaktadır.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>

  <?php $pages = (int)ceil($total / $per); if ($pages > 1): ?>
    <div class="row" style="margin-top:14px"><?php for ($i = 1; $i <= $pages; $i++): ?><a class="btn sm <?= $i === $page ? '' : 'ghost' ?>" href="messages.php?p=<?= $i ?><?= $filter ? '&filter=' . urlencode($filter) : '' ?>"><?= $i ?></a><?php endfor; ?></div>
  <?php endif; ?>
</div>
<?php admin_footer();
