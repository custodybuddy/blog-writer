<?php
// Copy this file to config.php and update the values.

// SQLite database path (relative to project root)
const DB_PATH = __DIR__ . '/data/blog.db';

// Public base URL for links (include trailing slash)
const BASE_URL = 'http://localhost:8000/';

// Secret token required to trigger cron.php
const CRON_SECRET_TOKEN = 'replace-with-32-char-token';

// Site settings
const SITE_NAME = 'Custody Buddy Blog';
const TIMEZONE = 'America/New_York';
?>
