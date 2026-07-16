<div class="section-head">
    <h1>Manage papers</h1>
    <a class="btn btn-primary" href="<?= url('/admin/papers/create') ?>">Upload new</a>
</div>

<form class="search-panel" method="get" action="<?= url('/admin/papers') ?>">
    <input type="search" name="q" value="<?= e($filters['q'] ?? '') ?>" placeholder="Search papers…">
    <select name="category_id">
        <option value="">All categories</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?= (int)$category['id'] ?>" <?= (string)$filters['category_id'] === (string)$category['id'] ? 'selected' : '' ?>>
                <?= e($category['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <select name="status">
        <option value="">All statuses</option>
        <?php foreach (['published', 'draft', 'archived'] as $status): ?>
            <option value="<?= $status ?>" <?= ($filters['status'] ?? '') === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
        <?php endforeach; ?>
    </select>
    <select name="is_free">
        <option value="">All access</option>
        <option value="1" <?= (string)($filters['is_free'] ?? '') === '1' ? 'selected' : '' ?>>Free</option>
        <option value="0" <?= (string)($filters['is_free'] ?? '') === '0' ? 'selected' : '' ?>>Premium</option>
    </select>
    <button class="btn btn-primary" type="submit">Filter</button>
</form>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Access</th>
                <th>Status</th>
                <th>Downloads</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php if ($papers === []): ?>
                <tr><td colspan="6">No papers found.</td></tr>
            <?php endif; ?>
            <?php foreach ($papers as $paper): ?>
                <tr>
                    <td>
                        <a href="<?= url('/papers/' . $paper['slug']) ?>"><?= e($paper['title']) ?></a>
                        <div class="muted"><?= e($paper['authors']) ?></div>
                    </td>
                    <td><?= e($paper['category_name']) ?></td>
                    <td><?= (int)$paper['is_free'] === 1 ? 'Free' : money($paper['price']) ?></td>
                    <td><span class="badge"><?= e($paper['status']) ?></span></td>
                    <td><?= (int)$paper['download_count'] ?></td>
                    <td class="table-actions">
                        <a class="btn btn-sm btn-secondary" href="<?= url('/admin/papers/' . (int)$paper['id'] . '/edit') ?>">Edit</a>
                        <form method="post" action="<?= url('/admin/papers/' . (int)$paper['id'] . '/delete') ?>" onsubmit="return confirm('Delete this paper?');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php \App\Core\View::partial('partials/pagination', ['pagination' => $pagination]); ?>
