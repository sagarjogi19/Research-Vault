<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Admin') ?> · <?= e(config('app.name')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body class="admin-body">
    <div class="admin-shell">
        <?php \App\Core\View::partial('partials/admin_sidebar'); ?>
        <div class="admin-content">
            <header class="admin-topbar">
                <button class="nav-toggle" type="button" data-admin-toggle aria-label="Toggle menu">☰</button>
                <div>
                    <strong><?= e($title ?? 'Admin') ?></strong>
                </div>
                <div class="admin-topbar-user">
                    <span><?= e(auth_user()['name'] ?? 'Admin') ?></span>
                    <a href="<?= url('/') ?>" class="btn btn-ghost btn-sm">View site</a>
                </div>
            </header>
            <div class="admin-main">
                <?php \App\Core\View::partial('partials/flash'); ?>
                <?= $content ?>
            </div>
        </div>
    </div>
    <script src="<?= asset('js/app.js') ?>" defer></script>
</body>
</html>
