<?php
/**
 * Clear ThinkPHP schema cache
 * Run this after adding new database columns (ALTER TABLE)
 *
 * Usage: php clear_schema_cache.php
 * Or access via browser if placed in public directory
 */

$schemaPath = __DIR__ . '/runtime/schema/';

if (!is_dir($schemaPath)) {
    echo "Schema cache directory not found: {$schemaPath}\n";
    exit(0);
}

$files = glob($schemaPath . '*');
$count = 0;

foreach ($files as $file) {
    if (is_file($file)) {
        unlink($file);
        $count++;
        echo "Deleted: " . basename($file) . "\n";
    }
}

echo "\nDone! Cleared {$count} schema cache file(s).\n";
echo "New columns will be recognized on next database query.\n";
