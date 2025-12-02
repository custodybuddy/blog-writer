<?php
require_once __DIR__ . '/../config.php';

function get_db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        if (!defined('DB_PATH')) {
            throw new RuntimeException('DB_PATH is not defined. Copy config.sample.php to config.php.');
        }
        $dir = dirname(DB_PATH);
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true) && !is_dir($dir)) {
                throw new RuntimeException('Failed to create database directory: ' . $dir);
            }
        }

        if (!chmod($dir, 0755)) {
            error_log('Could not set permissions on database directory: ' . $dir);
        }

        if (!file_exists(DB_PATH)) {
            $handle = @fopen(DB_PATH, 'c');
            if ($handle === false) {
                throw new RuntimeException('Unable to create SQLite database file at ' . DB_PATH . '. Check directory permissions.');
            }
            fclose($handle);
            @chmod(DB_PATH, 0644);
        }

        if (!is_writable(DB_PATH)) {
            throw new RuntimeException('Database file is not writable: ' . DB_PATH . '. Ensure it is 644 or 666 on shared hosting.');
        }

        try {
            $pdo = new PDO('sqlite:' . DB_PATH, null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            $pdo->exec('PRAGMA foreign_keys = ON;');
            $pdo->exec('PRAGMA busy_timeout = 5000;');
            initialize_schema($pdo);
            @chmod(DB_PATH, 0644);
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            throw new RuntimeException('Unable to connect to the database. Please check DB_PATH permissions.');
        }
    }
    return $pdo;
}

function initialize_schema(PDO $pdo): void {
    $pdo->exec('CREATE TABLE IF NOT EXISTS posts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        slug TEXT UNIQUE NOT NULL,
        title TEXT NOT NULL,
        summary TEXT NOT NULL,
        content TEXT NOT NULL,
        created_at DATETIME NOT NULL,
        topic_title TEXT,
        topic_description TEXT
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS topics (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        description TEXT NOT NULL,
        used INTEGER DEFAULT 0
    )');
}
?>
