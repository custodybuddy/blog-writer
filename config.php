<?php
// Site Configuration
define('SITE_NAME', 'Custody Buddy Blog');
define('BASE_URL', 'https://custodybuddy.com/family-law-blog/');
define('SITE_PATH', '/family-law-blog/');
define('TIMEZONE', 'America/New_York'); // Adjust to your timezone

// Database Configuration (SQLite path; ensure directory is writable)
define('DB_PATH', dirname(__FILE__) . '/database/blog.db');

// API Keys
define('CLAUDE_API_KEY', 'sk-ant-your-key-here');
define('AMAZON_AFFILIATE_ID', 'custodybudd0c-20');

// Security
define('CRON_SECRET_TOKEN', 'A7F3kP9XQ2mD6R8bEJZNYw4UHaC5LTSV');

// Error Reporting (set to 0 in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>
