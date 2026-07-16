<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <div class="brand brand-footer">
                <span class="brand-mark" aria-hidden="true"></span>
                <span class="brand-text"><?= e(config('app.name')) ?></span>
            </div>
            <p class="muted">A secure research repository and digital library for academic discovery.</p>
        </div>
        <div>
            <h4>Explore</h4>
            <a href="<?= url('/papers') ?>">All papers</a>
            <a href="<?= url('/register') ?>">Create account</a>
        </div>
        <div>
            <h4>Access</h4>
            <a href="<?= url('/login') ?>">Student login</a>
            <a href="<?= url('/login') ?>">Admin login</a>
        </div>
    </div>
    <div class="container footer-bottom">
        <small>&copy; <?= date('Y') ?> <?= e(config('app.name')) ?>. Built with PHP &amp; MySQL.</small>
    </div>
</footer>
