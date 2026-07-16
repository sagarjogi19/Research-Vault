<section class="page-hero">
    <div class="container">
        <h1>Browse research papers</h1>
        <p>Search the repository and filter by category, access type, or year.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <form class="search-panel" method="get" action="<?= url('/papers') ?>">
            <input type="search" name="q" value="<?= e($filters['q'] ?? '') ?>" placeholder="Search title, author, keywords…">
            <select name="category_id">
                <option value="">All categories</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int)$category['id'] ?>" <?= (string)$filters['category_id'] === (string)$category['id'] ? 'selected' : '' ?>>
                        <?= e($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="is_free">
                <option value="">All access</option>
                <option value="1" <?= (string)$filters['is_free'] === '1' ? 'selected' : '' ?>>Free</option>
                <option value="0" <?= (string)$filters['is_free'] === '0' ? 'selected' : '' ?>>Premium</option>
            </select>
            <select name="year">
                <option value="">All years</option>
                <?php foreach ($years as $year): ?>
                    <option value="<?= e((string)$year) ?>" <?= (string)$filters['year'] === (string)$year ? 'selected' : '' ?>>
                        <?= e((string)$year) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-primary" type="submit">Apply filters</button>
        </form>

        <p class="result-count muted"><?= (int)$pagination['total'] ?> result<?= (int)$pagination['total'] === 1 ? '' : 's' ?></p>

        <?php if ($papers === []): ?>
            <div class="empty-state">
                <p>No papers matched your filters. Try a broader search.</p>
            </div>
        <?php else: ?>
            <div class="paper-grid">
                <?php foreach ($papers as $paper): ?>
                    <?php \App\Core\View::partial('partials/paper_card', ['paper' => $paper]); ?>
                <?php endforeach; ?>
            </div>
            <?php \App\Core\View::partial('partials/pagination', ['pagination' => $pagination]); ?>
        <?php endif; ?>
    </div>
</section>
