<section class="section">
    <div class="container narrow">
        <a class="text-link" href="<?= url('/papers') ?>">← Back to catalog</a>

        <article class="paper-detail reveal">
            <div class="paper-detail-meta">
                <span class="badge <?= (int)$paper['is_free'] === 1 ? 'badge-free' : 'badge-premium' ?>">
                    <?= (int)$paper['is_free'] === 1 ? 'Free access' : 'Premium · ' . money($paper['price']) ?>
                </span>
                <span><?= e($paper['category_name']) ?></span>
                <?php if (!empty($paper['publication_year'])): ?>
                    <span><?= e((string)$paper['publication_year']) ?></span>
                <?php endif; ?>
            </div>

            <h1><?= e($paper['title']) ?></h1>
            <p class="authors"><?= e($paper['authors']) ?></p>

            <div class="detail-grid">
                <div>
                    <h2>Abstract</h2>
                    <p class="abstract"><?= nl2br(e($paper['abstract'])) ?></p>

                    <?php if (!empty($paper['keywords'])): ?>
                        <h3>Keywords</h3>
                        <p class="muted"><?= e($paper['keywords']) ?></p>
                    <?php endif; ?>
                </div>

                <aside class="access-panel">
                    <h3>Document access</h3>
                    <ul class="meta-list">
                        <li><span>File</span><strong><?= e($paper['file_name']) ?></strong></li>
                        <li><span>Size</span><strong><?= e(format_bytes((int)$paper['file_size'])) ?></strong></li>
                        <li><span>Downloads</span><strong><?= (int)$paper['download_count'] ?></strong></li>
                    </ul>

                    <?php if (!is_authenticated()): ?>
                        <p class="muted">Sign in to download free papers or purchase premium titles.</p>
                        <a class="btn btn-primary btn-block" href="<?= url('/login') ?>">Sign in</a>
                        <a class="btn btn-secondary btn-block" href="<?= url('/register') ?>">Create account</a>
                    <?php elseif ($isOwned || is_admin()): ?>
                        <p class="success-text">You have access to this document.</p>
                        <a class="btn btn-primary btn-block" href="<?= url('/download/' . (int)$paper['id']) ?>">Download paper</a>
                    <?php elseif ((int)$paper['is_free'] === 1): ?>
                        <a class="btn btn-primary btn-block" href="<?= url('/download/' . (int)$paper['id']) ?>">Download free paper</a>
                    <?php else: ?>
                        <p class="muted">Secure checkout via Paystack. Access unlocks after payment verification.</p>
                        <form method="post" action="<?= url('/papers/' . (int)$paper['id'] . '/checkout') ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="slug" value="<?= e($paper['slug']) ?>">
                            <button type="submit" class="btn btn-primary btn-block">
                                Purchase · <?= money($paper['price']) ?>
                            </button>
                        </form>
                    <?php endif; ?>
                </aside>
            </div>
        </article>
    </div>
</section>
