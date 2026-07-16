<?php if (($pagination['last_page'] ?? 1) > 1): ?>
    <nav class="pagination" aria-label="Pagination">
        <?php
        $query = $_GET;
        for ($i = 1; $i <= $pagination['last_page']; $i++):
            $query['page'] = $i;
            $link = '?' . http_build_query($query);
        ?>
            <a class="<?= $i === (int)$pagination['page'] ? 'active' : '' ?>" href="<?= e($link) ?>"><?= $i ?></a>
        <?php endfor; ?>
    </nav>
<?php endif; ?>
