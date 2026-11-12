<?php
require_once __DIR__ . '/includes/core.php';
$rows = db()->query('SELECT * FROM project_media')->fetchAll();
echo "Count: " . count($rows) . "\n";
foreach (array_slice($rows, 0, 10) as $r) {
    echo "  PID: {$r['project_id']} | Kind: {$r['kind']} | Path: {$r['file_path']} | Caption: {$r['caption']}\n";
}
