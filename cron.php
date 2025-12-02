<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

date_default_timezone_set(TIMEZONE);
$token = $_GET['token'] ?? '';
if ($token !== CRON_SECRET_TOKEN) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

$pdo = get_db();
ensure_topics_loaded($pdo);
$topic = next_topic($pdo);
if (!$topic) {
    echo 'No unused topics left. Add more to topics.json or reset the topics table.';
    exit;
}

$post = create_post_from_topic($pdo, $topic);
if ($post) {
    echo 'Post created: ' . htmlspecialchars($post['title']) . "\n";
    echo BASE_URL . 'post.php?slug=' . urlencode($post['slug']);
} else {
    http_response_code(500);
    echo 'Failed to create post.';
}
?>
