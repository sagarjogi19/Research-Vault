<aside class="admin-sidebar" data-admin-sidebar>
    <a class="brand" href="<?= url('/admin') ?>">
        <span class="brand-mark" aria-hidden="true"></span>
        <span class="brand-text"><?= e(config('app.name')) ?></span>
    </a>
    <nav>
        <a href="<?= url('/admin') ?>" class="<?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin') && !str_contains($_SERVER['REQUEST_URI'] ?? '', '/papers') ? 'active' : '' ?>">Overview</a>
        <a href="<?= url('/admin/papers') ?>" class="<?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/papers') ? 'active' : '' ?>">Papers</a>
        <a href="<?= url('/admin/papers/create') ?>">Upload</a>
        <a href="<?= url('/papers') ?>">Public catalog</a>
    </nav>
    <form action="<?= url('/logout') ?>" method="post">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-ghost btn-block">Sign out</button>
    </form>
</aside>
