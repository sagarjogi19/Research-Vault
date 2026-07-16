<?php
$success = flash('success');
$error = flash('error');
?>
<?php if ($success || $error): ?>
    <div class="container flash-wrap">
        <?php if ($success): ?>
            <div class="alert alert-success" role="status"><?= e($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error" role="alert"><?= e($error) ?></div>
        <?php endif; ?>
    </div>
<?php endif; ?>
