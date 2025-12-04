<?php include __DIR__ . '/partials/header.php'; ?>
<section class="section-block" id="latest-posts">
    <div class="section-heading">
        <p class="eyebrow">Latest posts</p>
        <h2>Fresh scripts and response templates</h2>
        <p class="lede">A curated stream of prompts that help you de-escalate, document, and keep co-parenting plans on track.</p>
    </div>
    <?php if ($posts): ?>
        <?php
        $topicNames = [];
        foreach ($posts as $post) {
            if (!empty($post['topic_title'])) {
                $topicNames[] = $post['topic_title'];
            }
        }
        $topicNames = array_values(array_unique($topicNames));
        sort($topicNames, SORT_NATURAL | SORT_FLAG_CASE);
        ?>
        <div class="posts-toolbar" aria-label="Post controls">
            <div class="toolbar-stat">
                <p class="stat-label">Post library</p>
                <p class="stat-value" aria-live="polite">
                    Showing <span data-results-count><?php echo count($posts); ?></span> of <span data-post-count><?php echo count($posts); ?></span>
                </p>
                <p class="stat-caption">Use topic tabs or search to jump straight to the right template.</p>
            </div>
            <div class="toolbar-filters">
                <div class="toolbar-search filter-card">
                    <div class="filter-heading">
                        <p class="filter-label">Search the library</p>
                        <p class="filter-caption">Find titles, tone cues, or topics in one step.</p>
                    </div>
                    <div class="search-input">
                        <input id="post-filter" type="search" name="filter" placeholder="Search posts by keyword, tone, or topic" data-post-search>
                        <button type="button" class="cb-btn cb-btn-secondary" data-clear-filter>Clear</button>
                    </div>
                    <p class="search-hint">Filters update instantly — press Esc to clear.</p>
                </div>
                <div class="topic-filter filter-card">
                    <div class="filter-heading">
                        <p class="filter-label">Browse by topic</p>
                        <p class="filter-caption">Tap a tab or pick from the dropdown on mobile.</p>
                    </div>
                    <div class="topic-tabs" role="tablist" aria-label="Filter posts by topic">
                        <button type="button" class="topic-tab is-active" role="tab" aria-selected="true" data-topic-tab data-topic-value="all">All</button>
                        <?php foreach ($topicNames as $topic): ?>
                            <?php $topicValue = strtolower($topic); ?>
                            <button type="button" class="topic-tab" role="tab" aria-selected="false" data-topic-tab data-topic-value="<?php echo htmlspecialchars($topicValue); ?>">
                                <?php echo htmlspecialchars($topic); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <label class="sr-only" for="topic-select">Choose a topic</label>
                    <select id="topic-select" class="topic-select" data-topic-select aria-label="Choose a topic">
                        <option value="all" selected>All topics</option>
                        <?php foreach ($topicNames as $topic): ?>
                            <?php $topicValue = strtolower($topic); ?>
                            <option value="<?php echo htmlspecialchars($topicValue); ?>"><?php echo htmlspecialchars($topic); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
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
                <article class="post-card" data-post-card data-topic="<?php echo htmlspecialchars(strtolower($post['topic_title'] ?? '')); ?>" data-search-text="<?php echo htmlspecialchars(strtolower($post['title'] . ' ' . $post['summary'] . ' ' . $post['topic_title'])); ?>">
                    <p class="date eyebrow"><?php echo date('F j, Y', strtotime($post['created_at'])); ?></p>
                    <h3 class="card-title"><a href="<?php echo BASE_URL . 'post/' . urlencode($post['slug']); ?>"><?php echo htmlspecialchars($post['title']); ?></a></h3>
                    <?php if (!empty($post['summary'])): ?>
                        <p class="card-subhead"><?php echo htmlspecialchars($post['summary']); ?></p>
                    <?php endif; ?>
                    <p class="card-summary">
                        <?php if (!empty($post['topic_title'])): ?>
                            Covers <?php echo htmlspecialchars($post['topic_title']); ?> — open to see tone cues, boundaries, and documentation notes.
                        <?php else: ?>
                            A ready-to-use script with tone cues and documentation notes inside.
                        <?php endif; ?>
                    </p>
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
