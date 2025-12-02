<?php include __DIR__ . '/partials/header.php'; ?>
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
            <?php echo $sanitizedContent; ?>
        </div>
    </article>
<?php endif; ?>
<?php include __DIR__ . '/partials/footer.php'; ?>
