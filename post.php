<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

date_default_timezone_set(TIMEZONE);
$pdo = get_db();
$slug = $_GET['slug'] ?? '';
$post = $slug ? find_post($pdo, $slug) : null;
include __DIR__ . '/includes/header.php';
?>
<?php if (!$post): ?>
    <article class="post-card">
        <h2>Post not found</h2>
        <p>The requested post does not exist. <a href="<?php echo BASE_URL; ?>">Return home</a>.</p>
    </article>
<?php else: ?>
    <article class="post">
        <p class="date"><?php echo date('F j, Y', strtotime($post['created_at'])); ?></p>
        <h1><?php echo htmlspecialchars($post['title']); ?></h1>
        <div class="content">
            <?php echo sanitize_post_content($post['content']); ?>
        </div>
    </article>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
