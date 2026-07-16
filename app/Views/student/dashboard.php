<section class="page-hero">
    <div class="container">
        <h1>Welcome, <?= e(auth_user()['name'] ?? 'Student') ?></h1>
        <p>Track purchases, downloads, and continue where you left off.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <span>Purchased papers</span>
                <strong><?= (int)$stats['purchase_count'] ?></strong>
            </div>
            <div class="stat-card">
                <span>Downloads</span>
                <strong><?= (int)$stats['download_count'] ?></strong>
            </div>
            <div class="stat-card">
                <span>Quick links</span>
                <div class="stat-actions">
                    <a href="<?= url('/papers') ?>">Browse</a>
                    <a href="<?= url('/library') ?>">Library</a>
                </div>
            </div>
        </div>

        <div class="split-panels">
            <div>
                <div class="section-head">
                    <h2>My library</h2>
                    <a class="text-link" href="<?= url('/library') ?>">View all</a>
                </div>
                <?php if ($stats['purchases'] === []): ?>
                    <div class="empty-state"><p>No purchased papers yet. Premium titles appear here after checkout.</p></div>
                <?php else: ?>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Amount</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($stats['purchases'], 0, 5) as $item): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= url('/papers/' . $item['slug']) ?>"><?= e($item['title']) ?></a>
                                            <div class="muted"><?= e($item['authors']) ?></div>
                                        </td>
                                        <td><?= money($item['amount']) ?></td>
                                        <td><a class="btn btn-sm btn-secondary" href="<?= url('/download/' . (int)$item['paper_id']) ?>">Download</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <div>
                <div class="section-head">
                    <h2>Recent downloads</h2>
                </div>
                <?php if ($stats['downloads'] === []): ?>
                    <div class="empty-state"><p>No downloads yet.</p></div>
                <?php else: ?>
                    <ul class="activity-list">
                        <?php foreach ($stats['downloads'] as $download): ?>
                            <li>
                                <a href="<?= url('/papers/' . $download['slug']) ?>"><?= e($download['title']) ?></a>
                                <span class="muted"><?= e(date('M j, Y H:i', strtotime($download['downloaded_at']))) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
