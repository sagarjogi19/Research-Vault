<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'ResearchVault') ?> · <?= e(config('app.name')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body>
    <?php \App\Core\View::partial('partials/header'); ?>

    <main class="site-main">
        <?php \App\Core\View::partial('partials/flash'); ?>
        <?= $content ?>
    </main>

    <?php \App\Core\View::partial('partials/footer'); ?>
    <script src="<?= asset('js/app.js') ?>" defer></script>
</body>
</html>
