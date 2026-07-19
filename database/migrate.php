<?php

declare(strict_types=1);

/**
 * Migration Runner
 * 
 * Usage: php database/migrate.php
 * 
 * Executes all .sql files in database/migrations/ in order,
 * then runs seed files from database/seeds/.
 */

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$config = require __DIR__ . '/../config/database.php';

echo "=== CRM Profesional - Migration Runner ===\n\n";

try {
    // Connect without database first to create it if needed
    $dsn = sprintf('mysql:host=%s;port=%d;charset=%s', $config['host'], $config['port'], $config['charset']);
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `{$config['database']}`");

    echo "✓ Database '{$config['database']}' ready\n\n";

    // Run migrations
    $migrationsDir = __DIR__ . '/migrations';
    $files = glob($migrationsDir . '/*.sql');
    sort($files);

    foreach ($files as $file) {
        $basename = basename($file);
        echo "Running migration: {$basename}... ";
        $sql = file_get_contents($file);
        
        // Split by semicolons for multi-statement SQL files
        $statements = array_filter(
            array_map('trim', explode(';', $sql)),
            fn($s) => !empty($s) && !str_starts_with($s, '--')
        );
        
        foreach ($statements as $statement) {
            $pdo->exec($statement);
        }
        
        echo "✓ done\n";
    }

    echo "\n✓ All migrations executed successfully\n\n";

    // Run seeds
    $seedsDir = __DIR__ . '/seeds';
    $seedFiles = glob($seedsDir . '/*.sql');
    sort($seedFiles);

    foreach ($seedFiles as $file) {
        $basename = basename($file);
        echo "Running seed: {$basename}... ";
        $sql = file_get_contents($file);
        
        $statements = array_filter(
            array_map('trim', explode(';', $sql)),
            fn($s) => !empty($s) && !str_starts_with($s, '--')
        );
        
        foreach ($statements as $statement) {
            $pdo->exec($statement);
        }
        
        echo "✓ done\n";
    }

    echo "\n✓ All seeds executed successfully\n\n";

    // Verify tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables created: " . implode(', ', $tables) . "\n";

} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
