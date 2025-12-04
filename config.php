<?php
// Site Configuration
define('SITE_NAME', 'Custody Buddy Blog');
define('BASE_URL', 'https://custodybuddy.com/family-law-blog/');
define('SITE_PATH', '/family-law-blog/');
define('TIMEZONE', 'America/New_York'); // Adjust to your timezone
date_default_timezone_set(TIMEZONE);

// Database Configuration (SQLite paths; ensure directory is writable)
define('DB_PATH_RELATIVE', 'database/blog.db');
define('DB_PATH_ABSOLUTE', dirname(__FILE__) . '/database/blog.db');
define('DB_PATH', DB_PATH_ABSOLUTE); // Primary path used by the app

// API Keys (load sensitive values from environment; keep this file free of secrets)
define('CLAUDE_API_KEY', getenv('CLAUDE_API_KEY') ?: '');
define('AMAZON_AFFILIATE_ID', 'custodybuddy-20');

// Security
define('CRON_SECRET_TOKEN', '4f8d0c7a5b69e2f3c1d4b6a8e7f9c2ab');

// Error Reporting (set to 0 in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>
