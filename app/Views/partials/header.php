<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="<?= url('/') ?>">
            <span class="brand-mark" aria-hidden="true"></span>
            <span class="brand-text"><?= e(config('app.name')) ?></span>
        </a>

        <button class="nav-toggle" type="button" data-nav-toggle aria-label="Toggle navigation">☰</button>

        <nav class="site-nav" data-nav>
            <a href="<?= url('/papers') ?>">Browse</a>
            <?php if (is_authenticated()): ?>
                <?php if (is_admin()): ?>
                    <a href="<?= url('/admin') ?>">Admin</a>
                <?php else: ?>
                    <a href="<?= url('/dashboard') ?>">Dashboard</a>
                    <a href="<?= url('/library') ?>">My Library</a>
                <?php endif; ?>
                <form action="<?= url('/logout') ?>" method="post" class="inline-form">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-ghost btn-sm">Sign out</button>
                </form>
            <?php else: ?>
                <a href="<?= url('/login') ?>">Sign in</a>
                <a href="<?= url('/register') ?>" class="btn btn-primary btn-sm">Join free</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
