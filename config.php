<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'u123456789_custody_blog');
define('DB_USER', 'u123456789_custody_user');
define('DB_PASS', 'your_strong_password_here');

// API Keys
define('CLAUDE_API_KEY', 'sk-ant-your-key-here');
define('AMAZON_AFFILIATE_ID', 'custodybudd0c-20');

// Security
define('CRON_SECRET_TOKEN', 'A7F3kP9XQ2mD6R8bEJZNYw4UHaC5LTSV');

// Site Configuration
define('BASE_URL', 'https://custodybuddy.com/family-law-blog/');
define('SITE_PATH', '/family-law-blog/');
define('TIMEZONE', 'America/New_York'); // Adjust to your timezone

// Error Reporting (set to 0 in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>
