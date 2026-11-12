<?php
require_once __DIR__ . '/includes/core.php';
$rows = db()->query('SELECT id, slug, title_tr FROM projects')->fetchAll();
echo "Existing projects: " . count($rows) . "\n";
foreach ($rows as $r) {
    echo "  {$r['id']}: {$r['slug']} => {$r['title_tr']}\n";
}
