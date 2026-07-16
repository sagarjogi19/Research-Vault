<section class="hero">
    <div class="hero-media" aria-hidden="true"></div>
    <div class="container hero-content">
        <p class="brand-hero"><?= e(config('app.name')) ?></p>
        <h1>Find the research that moves your work forward</h1>
        <p class="hero-lead">Search, preview, and access academic papers curated for students — free and premium titles in one secure portal.</p>
        <div class="hero-actions">
            <a href="<?= url('/papers') ?>" class="btn btn-primary">Browse papers</a>
            <?php if (!is_authenticated()): ?>
                <a href="<?= url('/register') ?>" class="btn btn-secondary">Create free account</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head">
            <h2>Search the catalog</h2>
            <p>Filter by topic, access type, or year to narrow results quickly.</p>
        </div>
        <form class="search-panel" action="<?= url('/papers') ?>" method="get">
            <input type="search" name="q" placeholder="Title, author, keyword…" aria-label="Search papers">
            <select name="category_id" aria-label="Category">
                <option value="">All categories</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int)$category['id'] ?>"><?= e($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="is_free" aria-label="Access type">
                <option value="">Free &amp; premium</option>
                <option value="1">Free only</option>
                <option value="0">Premium only</option>
            </select>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>
</section>

<section class="section section-muted">
    <div class="container">
        <div class="section-head">
            <h2>Recently added</h2>
            <p>Fresh uploads from the research library.</p>
        </div>
        <?php if ($recentPapers === []): ?>
            <div class="empty-state">
                <p>No papers yet. Admins can upload the first collection from the dashboard.</p>
            </div>
        <?php else: ?>
            <div class="paper-grid">
                <?php foreach ($recentPapers as $paper): ?>
                    <?php \App\Core\View::partial('partials/paper_card', ['paper' => $paper]); ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head">
            <h2>Browse by discipline</h2>
            <p>Jump into a subject area that matches your studies.</p>
        </div>
        <div class="category-grid">
            <?php foreach ($categories as $category): ?>
                <a class="category-item reveal" href="<?= url('/papers?category_id=' . (int)$category['id']) ?>">
                    <strong><?= e($category['name']) ?></strong>
                    <span><?= (int)$category['paper_count'] ?> papers</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
