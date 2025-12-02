<?php
require_once __DIR__ . '/bootstrap.php';

use App\Services\PostService;

$pdo = get_db();
$postService = PostService::build($pdo, __DIR__ . '/topics.json');
$slug = $_GET['slug'] ?? '';
$post = $slug ? $postService->getPostBySlug($slug) : null;
$sanitizedContent = $post ? $postService->sanitizeContent($post['content']) : '';

include __DIR__ . '/views/post.php';
