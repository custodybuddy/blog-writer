<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

date_default_timezone_set(TIMEZONE);

try {
    if (!extension_loaded('sqlite3')) {
        throw new RuntimeException('SQLite3 extension is not enabled in PHP.');
    }

    if (!in_array('sqlite', PDO::getAvailableDrivers(), true)) {
        throw new RuntimeException('PDO SQLite driver is unavailable.');
    }

    $pdo = get_db();
    ensure_topics_loaded($pdo);
    echo "Database initialized at " . DB_PATH . "\n";
    $perms = substr(sprintf('%o', fileperms(DB_PATH)), -4);
    echo "Current permissions: $perms (set to 0644 or 0666 if writes fail)\n";
} catch (Throwable $e) {
    http_response_code(500);
    echo "Initialization failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
