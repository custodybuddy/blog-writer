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
        <header class="post-hero">
            <p class="date eyebrow"><?php echo date('F j, Y', strtotime($post['created_at'])); ?></p>
            <h1><?php echo htmlspecialchars($post['title']); ?></h1>
            <?php if (!empty($post['summary'])): ?>
                <p class="post-summary"><?php echo htmlspecialchars($post['summary']); ?></p>
            <?php endif; ?>
        </header>
        <div class="divider" aria-hidden="true"></div>
        <div class="content post-body">
            <?php echo $sanitizedContent; ?>
        </div>
    </article>
<?php endif; ?>
<?php include __DIR__ . '/partials/footer.php'; ?>
