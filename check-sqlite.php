<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

$errors = [];
set_error_handler(function ($severity, $message, $file, $line) use (&$errors) {
    $errors[] = [
        'type' => 'PHP Error',
        'message' => $message,
        'file' => $file,
        'line' => $line,
    ];
    return false; // allow normal handling too
});

$results = [];

function format_bool(bool $value): string
{
    return $value ? 'Yes' : 'No';
}

function format_perms(string $path): string
{
    $perms = @fileperms($path);
    if ($perms === false) {
        return 'Unknown';
    }

    return substr(sprintf('%o', $perms), -4);
}

// 1. Check if SQLite3 extension is loaded
$results['sqlite_extension'] = [
    'loaded' => extension_loaded('sqlite3'),
    'message' => extension_loaded('sqlite3') ? 'SQLite3 extension is loaded.' : 'SQLite3 extension is NOT loaded.'
];

// 2. Attempt to create a test database
$testDbPath = __DIR__ . '/sqlite-test.db';
$dbMessage = '';
$dbCreated = false;

if ($results['sqlite_extension']['loaded']) {
    try {
        if (file_exists($testDbPath) && !unlink($testDbPath)) {
            throw new RuntimeException('Could not remove existing test database file.');
        }

        $db = new SQLite3($testDbPath);
        $db->enableExceptions(true);
        $db->exec('CREATE TABLE IF NOT EXISTS health_check (id INTEGER PRIMARY KEY, created_at TEXT)');
        $db->exec("INSERT INTO health_check (created_at) VALUES (datetime('now'))");
        $row = $db->querySingle('SELECT created_at FROM health_check ORDER BY id DESC LIMIT 1');
        $db->close();

        $dbCreated = true;
        $dbMessage = 'Test database created successfully. Last entry at: ' . $row;
    } catch (Throwable $e) {
        $dbMessage = 'Database creation failed: ' . $e->getMessage();
        $errors[] = [
            'type' => get_class($e),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ];
    }
} else {
    $dbMessage = 'Skipped database test because SQLite3 extension is missing.';
}

$results['database_test'] = [
    'success' => $dbCreated,
    'message' => $dbMessage,
    'path' => $testDbPath,
    'permissions' => format_perms($testDbPath)
];

// 3. PHP version
$results['php_version'] = phpversion();

// 4. File permissions capabilities
$currentDir = __DIR__;
$testFilePath = $currentDir . '/file-permissions-test.txt';
$fileTestMessage = '';
$fileTestSuccess = false;

try {
    $isWritable = is_writable($currentDir);
    $fileTestMessage .= 'Current directory writable: ' . format_bool($isWritable) . "\n";

    if ($isWritable) {
        $bytes = @file_put_contents($testFilePath, 'File permissions test at ' . date('c'));
        if ($bytes !== false) {
            $fileTestSuccess = true;
            $fileTestMessage .= 'Wrote ' . $bytes . " bytes to test file.\n";
            $fileTestMessage .= 'Test file permissions: ' . format_perms($testFilePath) . "\n";
        } else {
            $fileTestMessage .= 'Failed to write test file.\n';
        }
    } else {
        $fileTestMessage .= 'Current directory is not writable.\n';
    }
} catch (Throwable $e) {
    $fileTestMessage .= 'File permissions test failed: ' . $e->getMessage();
    $errors[] = [
        'type' => get_class($e),
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
    ];
}

$results['file_permissions'] = [
    'directory' => $currentDir,
    'directory_permissions' => format_perms($currentDir),
    'test_file' => $testFilePath,
    'test_file_created' => $fileTestSuccess,
    'details' => $fileTestMessage
];

// 5. Output
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQLite3 Hostinger Check</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; padding: 20px; line-height: 1.6; }
        .container { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
        h1 { margin-top: 0; }
        pre { background: #f0f0f0; padding: 10px; border-radius: 4px; overflow-x: auto; }
        .status { padding: 6px 10px; border-radius: 4px; display: inline-block; }
        .ok { background: #e1f5e4; color: #256029; }
        .fail { background: #fdecea; color: #8a1f17; }
        .section { margin-bottom: 20px; }
        ul { padding-left: 20px; }
        code { background: #f0f0f0; padding: 2px 4px; border-radius: 4px; }
    </style>
</head>
<body>
<div class="container">
    <h1>SQLite3 Diagnostic</h1>

    <div class="section">
        <h2>SQLite3 Extension</h2>
        <span class="status <?php echo $results['sqlite_extension']['loaded'] ? 'ok' : 'fail'; ?>">
            <?php echo htmlspecialchars($results['sqlite_extension']['message']); ?>
        </span>
    </div>

    <div class="section">
        <h2>Database Test</h2>
        <span class="status <?php echo $results['database_test']['success'] ? 'ok' : 'fail'; ?>">
            <?php echo htmlspecialchars($results['database_test']['message']); ?>
        </span>
        <p><strong>Test DB Path:</strong> <code><?php echo htmlspecialchars($results['database_test']['path']); ?></code></p>
        <p><strong>Test DB Permissions:</strong> <?php echo htmlspecialchars($results['database_test']['permissions']); ?></p>
    </div>

    <div class="section">
        <h2>PHP Version</h2>
        <p><?php echo htmlspecialchars($results['php_version']); ?></p>
    </div>

    <div class="section">
        <h2>File Permissions</h2>
        <p><strong>Current Directory:</strong> <code><?php echo htmlspecialchars($results['file_permissions']['directory']); ?></code></p>
        <p><strong>Directory Permissions:</strong> <?php echo htmlspecialchars($results['file_permissions']['directory_permissions']); ?></p>
        <p><strong>Test File:</strong> <code><?php echo htmlspecialchars($results['file_permissions']['test_file']); ?></code></p>
        <p><strong>Test File Created:</strong> <?php echo format_bool($results['file_permissions']['test_file_created']); ?></p>
        <pre><?php echo htmlspecialchars(trim($results['file_permissions']['details'])); ?></pre>
    </div>

    <div class="section">
        <h2>Errors</h2>
        <?php if (!$errors): ?>
            <p>No PHP errors captured.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error['type'] . ': ' . $error['message'] . ' in ' . $error['file'] . ' on line ' . $error['line']); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
