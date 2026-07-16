<?php
/** @var array $paper */
$href = url('/papers/' . $paper['slug']);
?>
<article class="paper-card reveal">
    <div class="paper-card-top">
        <span class="badge <?= (int)$paper['is_free'] === 1 ? 'badge-free' : 'badge-premium' ?>">
            <?= (int)$paper['is_free'] === 1 ? 'Free' : money($paper['price']) ?>
        </span>
        <span class="meta"><?= e($paper['category_name'] ?? '') ?></span>
    </div>
    <h3><a href="<?= $href ?>"><?= e($paper['title']) ?></a></h3>
    <p class="authors"><?= e($paper['authors']) ?></p>
    <p class="excerpt"><?= e(mb_strimwidth(strip_tags($paper['abstract']), 0, 140, '…')) ?></p>
    <div class="paper-card-footer">
        <span><?= e((string) ($paper['publication_year'] ?? 'n/a')) ?></span>
        <a class="text-link" href="<?= $href ?>">View details →</a>
    </div>
</article>
