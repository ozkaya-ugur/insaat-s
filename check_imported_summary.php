<?php
require_once __DIR__ . '/includes/core.php';

$pCount = db()->query('SELECT COUNT(*) FROM projects')->fetchColumn();
$mCount = db()->query('SELECT COUNT(*) FROM project_media')->fetchColumn();

echo "Total Projects in DB: $pCount\n";
echo "Total Gallery Images in DB: $mCount\n";

$rows = db()->query('SELECT id, slug, title_tr, type, loc, status, cover_image FROM projects ORDER BY id ASC LIMIT 10')->fetchAll();
foreach ($rows as $r) {
    echo "ID {$r['id']}: {$r['title_tr']} | {$r['type']} | {$r['loc']} | {$r['status']} | Cover: " . substr($r['cover_image'], 0, 40) . "\n";
}
