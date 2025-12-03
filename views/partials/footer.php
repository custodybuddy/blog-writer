</main>
<footer class="site-footer">
    <div class="container footer-grid">
        <div class="footer-brand">
            <h2><?php echo SITE_NAME; ?></h2>
            <p class="footer-tagline">A calm corner for scripts, boundaries, and de-escalation language.</p>
            <div class="footer-actions">
                <a class="cb-btn cb-btn-primary" href="#latest-posts">Read the latest</a>
                <a class="cb-btn cb-btn-secondary" href="#about">About the project</a>
            </div>
        </div>
        <div class="footer-nav" aria-label="Footer navigation">
            <h3>Navigation</h3>
            <ul>
                <li><a href="<?php echo BASE_URL; ?>">Home</a></li>
                <li><a href="#latest-posts">Latest posts</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#top">Back to top</a></li>
            </ul>
        </div>
        <div class="footer-meta">
            <h3>Context</h3>
            <p>Disclaimer: This blog is informational and not legal or clinical advice. Consult professionals for your situation.</p>
            <div class="footer-meta-list">
                <div>
                    <p class="meta-label">Coverage</p>
                    <p class="meta-value">Co-parenting scripts, documentation cues, and tone resets.</p>
                </div>
                <div>
                    <p class="meta-label">Updates</p>
                    <p class="meta-value">Automatically refreshed alongside scheduled cron runs.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom container">
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?></p>
        <p class="footer-note">Made for caregivers who need practical, calm language fast.</p>
    </div>
</footer>
<script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>
</body>
</html>
