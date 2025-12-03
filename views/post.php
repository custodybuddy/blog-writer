<?php include __DIR__ . '/partials/header.php'; ?>
<nav class="page-nav" aria-label="Post navigation">
    <a class="cb-btn cb-btn-secondary" href="<?php echo BASE_URL; ?>#latest-posts">&#8592; Back to posts</a>
    <a class="cb-btn cb-btn-primary" href="<?php echo BASE_URL; ?>">Return home</a>
</nav>

<?php if (!$post): ?>
    <article class="post-card">
        <h2>Post not found</h2>
        <p>The requested post does not exist.</p>
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
