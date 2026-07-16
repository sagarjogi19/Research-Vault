<section class="page-hero">
    <div class="container">
        <h1>My library</h1>
        <p>Papers you have purchased and can download anytime.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if ($purchases === []): ?>
            <div class="empty-state">
                <p>Your library is empty. Explore premium papers in the catalog.</p>
                <a class="btn btn-primary" href="<?= url('/papers?is_free=0') ?>">Browse premium</a>
            </div>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Paper</th>
                            <th>Category</th>
                            <th>Paid</th>
                            <th>Purchased</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($purchases as $item): ?>
                            <tr>
                                <td>
                                    <a href="<?= url('/papers/' . $item['slug']) ?>"><?= e($item['title']) ?></a>
                                    <div class="muted"><?= e($item['authors']) ?></div>
                                </td>
                                <td><?= e($item['category_name']) ?></td>
                                <td><?= money($item['amount']) ?></td>
                                <td><?= e(date('M j, Y', strtotime((string)$item['purchased_at']))) ?></td>
                                <td><a class="btn btn-sm btn-primary" href="<?= url('/download/' . (int)$item['paper_id']) ?>">Download</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>
