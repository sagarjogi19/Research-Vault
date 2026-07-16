<section class="auth-section">
    <div class="auth-card reveal">
        <h1>Create student account</h1>
        <p class="muted">Register to search, purchase, and download research papers.</p>

        <form method="post" action="<?= url('/register') ?>" class="form-stack" novalidate>
            <?= csrf_field() ?>
            <label>
                Full name
                <input type="text" name="name" value="<?= e((string) old('name')) ?>" required maxlength="120">
                <?php if (!empty($errors['name'])): ?><span class="field-error"><?= e($errors['name']) ?></span><?php endif; ?>
            </label>
            <label>
                Email
                <input type="email" name="email" value="<?= e((string) old('email')) ?>" required autocomplete="email">
                <?php if (!empty($errors['email'])): ?><span class="field-error"><?= e($errors['email']) ?></span><?php endif; ?>
            </label>
            <label>
                Password
                <input type="password" name="password" required minlength="8" autocomplete="new-password">
                <?php if (!empty($errors['password'])): ?><span class="field-error"><?= e($errors['password']) ?></span><?php endif; ?>
            </label>
            <label>
                Confirm password
                <input type="password" name="password_confirmation" required minlength="8" autocomplete="new-password">
            </label>
            <button type="submit" class="btn btn-primary btn-block">Create account</button>
        </form>

        <p class="auth-switch">Already registered? <a href="<?= url('/login') ?>">Sign in</a></p>
    </div>
</section>
