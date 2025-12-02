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

// API Keys
define('CLAUDE_API_KEY', 'sk-ant-your-key-here');
define('AMAZON_AFFILIATE_ID', 'your-affiliate-id');

// Security
define('CRON_SECRET_TOKEN', '00000000000000000000000000000000');

// Error Reporting (set to 0 in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>
