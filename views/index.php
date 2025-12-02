<?php include __DIR__ . '/partials/header.php'; ?>
<section class="posts" id="latest-posts">
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
<section class="about" id="about">
    <div class="about-card">
        <h2>How to navigate CustodyBuddy</h2>
        <p>Start with the newest posts for timely scripts and responses. Use the "Latest posts" link above to jump straight into the feed, or return home any time via the site name.</p>
        <p>If you are new here, read the most recent guide first—it contains the freshest templates and safety language. When you are ready to dig deeper, the archives stay accessible through the homepage cards.</p>
        <div class="about-actions">
            <a class="cb-btn cb-btn-primary" href="#latest-posts">Jump to posts</a>
            <a class="cb-btn cb-btn-secondary" href="<?php echo BASE_URL; ?>">Back to homepage</a>
        </div>
    </div>
</section>
<?php include __DIR__ . '/partials/footer.php'; ?>
