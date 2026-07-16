<section class="auth-section">
    <div class="auth-card reveal">
        <h1>Sign in</h1>
        <p class="muted">Access your library, downloads, and purchases.</p>

        <form method="post" action="<?= url('/login') ?>" class="form-stack" novalidate>
            <?= csrf_field() ?>
            <label>
                Email
                <input type="email" name="email" value="<?= e((string) old('email')) ?>" required autocomplete="email">
                <?php if (!empty($errors['email'])): ?><span class="field-error"><?= e($errors['email']) ?></span><?php endif; ?>
            </label>
            <label>
                Password
                <input type="password" name="password" required autocomplete="current-password">
                <?php if (!empty($errors['password'])): ?><span class="field-error"><?= e($errors['password']) ?></span><?php endif; ?>
            </label>
            <button type="submit" class="btn btn-primary btn-block">Sign in</button>
        </form>

        <p class="auth-switch">New student? <a href="<?= url('/register') ?>">Create an account</a></p>
        <div class="demo-box">
            <strong>Demo accounts</strong>
            <p>Admin: admin@researchvault.test / Admin@12345</p>
            <p>Student: student@researchvault.test / Student@12345</p>
        </div>
    </div>
</section>
