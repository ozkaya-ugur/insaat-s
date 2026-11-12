<?php
require_once __DIR__ . '/includes/core.php';

$pCols = db()->query('DESCRIBE projects')->fetchAll();
echo "=== PROJECTS TABLE ===\n";
foreach ($pCols as $c) {
    echo "  {$c['Field']} ({$c['Type']})\n";
}

$mCols = db()->query('DESCRIBE project_media')->fetchAll();
echo "\n=== PROJECT_MEDIA TABLE ===\n";
foreach ($mCols as $c) {
    echo "  {$c['Field']} ({$c['Type']})\n";
}
