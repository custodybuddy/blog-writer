<?php include __DIR__ . '/partials/header.php'; ?>
<section class="section-block" id="latest-posts">
    <div class="section-heading">
        <p class="eyebrow">Latest posts</p>
        <h2>Fresh scripts and response templates</h2>
        <p class="lede">A curated stream of prompts that help you de-escalate, document, and keep co-parenting plans on track.</p>
    </div>
    <?php if ($posts): ?>
        <div class="posts-toolbar" aria-label="Post controls">
            <div class="toolbar-stat">
                <p class="stat-label">Post library</p>
                <p class="stat-value" aria-live="polite"><span data-results-count><?php echo count($posts); ?></span> of <span data-post-count><?php echo count($posts); ?></span> ready</p>
                <p class="stat-caption">Use the filter to jump straight to a template you need.</p>
            </div>
            <div class="toolbar-search">
                <label for="post-filter" class="sr-only">Filter posts by keyword</label>
                <div class="search-input">
                    <input id="post-filter" type="search" name="filter" placeholder="Filter by topic, tone, or keyword" data-filter-posts>
                    <button type="button" class="cb-btn cb-btn-secondary" data-clear-filter>Clear</button>
                </div>
                <p class="search-hint">Live filtering — press Esc to clear and show everything again.</p>
            </div>
        </div>
    <?php endif; ?>
    <div class="posts-grid">
        <?php if (!$posts): ?>
            <article class="post-card">
                <h2>No posts yet</h2>
                <p>The cron job will create a new post automatically. You can also trigger it manually via cron.php.</p>
            </article>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <article class="post-card" data-post-card data-search-text="<?php echo htmlspecialchars(strtolower($post['title'] . ' ' . $post['summary'] . ' ' . $post['topic_title'])); ?>">
                    <p class="date eyebrow"><?php echo date('F j, Y', strtotime($post['created_at'])); ?></p>
                    <h3 class="card-title"><a href="<?php echo BASE_URL . 'post/' . urlencode($post['slug']); ?>"><?php echo htmlspecialchars($post['title']); ?></a></h3>
                    <p class="card-summary"><?php echo htmlspecialchars($post['summary']); ?></p>
                    <?php if (!empty($post['topic_title'])): ?>
                        <p class="card-topic" aria-label="Topic">Topic: <?php echo htmlspecialchars($post['topic_title']); ?></p>
                    <?php endif; ?>
                    <a class="read-more" href="<?php echo BASE_URL . 'post/' . urlencode($post['slug']); ?>">Read full post</a>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php if ($posts): ?>
        <div class="post-filter-empty" data-no-results hidden>
            <p>No posts match that keyword yet. Clear the filter to see the full library.</p>
            <button type="button" class="cb-btn cb-btn-secondary" data-clear-filter>Reset filter</button>
        </div>
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
