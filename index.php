<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

date_default_timezone_set(TIMEZONE);
$pdo = get_db();
ensure_topics_loaded($pdo);
$posts = latest_posts($pdo);
include __DIR__ . '/includes/header.php';
?>
<section class="posts">
    <?php if (!$posts): ?>
        <article class="post-card">
            <h2>No posts yet</h2>
            <p>The cron job will create a new post automatically. You can also trigger it manually via cron.php.</p>
        </article>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <article class="post-card">
                <p class="date"><?php echo date('F j, Y', strtotime($post['created_at'])); ?></p>
                <h2><a href="<?php echo BASE_URL . 'post/' . urlencode($post['slug']); ?>"><?php echo htmlspecialchars($post['title']); ?></a></h2>
                <p><?php echo htmlspecialchars($post['summary']); ?></p>
                <a class="read-more" href="<?php echo BASE_URL . 'post/' . urlencode($post['slug']); ?>">Read full post</a>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
