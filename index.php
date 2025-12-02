<?php
require_once __DIR__ . '/bootstrap.php';

use App\Services\PostService;

$pdo = get_db();
$postService = PostService::build($pdo, __DIR__ . '/topics.json');
$postService->ensureTopicsSeeded();
$posts = $postService->getLatestPosts();

include __DIR__ . '/views/index.php';
