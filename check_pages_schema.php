<?php
require_once __DIR__ . '/includes/core.php';
$cols = db()->query('DESCRIBE pages')->fetchAll();
foreach ($cols as $c) {
    echo "{$c['Field']} ({$c['Type']})\n";
}
