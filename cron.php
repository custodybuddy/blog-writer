<?php
require_once __DIR__ . '/bootstrap.php';

use App\Services\PostService;

$token = $_GET['token'] ?? '';
if ($token !== CRON_SECRET_TOKEN) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

$pdo = get_db();
$postService = PostService::build($pdo, __DIR__ . '/topics.json');
$postService->ensureTopicsSeeded();
$post = $postService->generatePostFromNextTopic();

if ($post) {
    echo 'Post created: ' . htmlspecialchars($post['title']) . "\n";
    echo BASE_URL . 'post/' . urlencode($post['slug']);
} else {
    http_response_code(500);
    echo 'No unused topics left. Add more to topics.json or reset the topics table.';
}
