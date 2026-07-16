<?php
$isEdit = $paper !== null;
$action = $isEdit ? url('/admin/papers/' . (int)$paper['id']) : url('/admin/papers');
?>
<div class="section-head">
    <h1><?= $isEdit ? 'Edit paper' : 'Upload research paper' ?></h1>
    <a class="text-link" href="<?= url('/admin/papers') ?>">← Back to list</a>
</div>

<form method="post" action="<?= $action ?>" enctype="multipart/form-data" class="form-panel" novalidate>
    <?= csrf_field() ?>

    <div class="form-grid">
        <label class="full">
            Title
            <input type="text" name="title" required maxlength="255"
                   value="<?= e((string) old('title', $paper['title'] ?? '')) ?>">
            <?php if (!empty($errors['title'])): ?><span class="field-error"><?= e($errors['title']) ?></span><?php endif; ?>
        </label>

        <label class="full">
            Authors
            <input type="text" name="authors" required maxlength="255"
                   value="<?= e((string) old('authors', $paper['authors'] ?? '')) ?>"
                   placeholder="e.g. Ada Okafor, James Mensah">
            <?php if (!empty($errors['authors'])): ?><span class="field-error"><?= e($errors['authors']) ?></span><?php endif; ?>
        </label>

        <label class="full">
            Abstract
            <textarea name="abstract" rows="7" required><?= e((string) old('abstract', $paper['abstract'] ?? '')) ?></textarea>
            <?php if (!empty($errors['abstract'])): ?><span class="field-error"><?= e($errors['abstract']) ?></span><?php endif; ?>
        </label>

        <label>
            Category
            <select name="category_id" required>
                <option value="">Select category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int)$category['id'] ?>"
                        <?= (string) old('category_id', $paper['category_id'] ?? '') === (string)$category['id'] ? 'selected' : '' ?>>
                        <?= e($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($errors['category_id'])): ?><span class="field-error"><?= e($errors['category_id']) ?></span><?php endif; ?>
        </label>

        <label>
            Publication year
            <input type="number" name="publication_year" min="1900" max="<?= date('Y') + 1 ?>"
                   value="<?= e((string) old('publication_year', $paper['publication_year'] ?? '')) ?>">
        </label>

        <label>
            Access type
            <select name="is_free" id="is_free" required>
                <?php $isFree = (string) old('is_free', isset($paper) ? (string)$paper['is_free'] : '1'); ?>
                <option value="1" <?= $isFree === '1' ? 'selected' : '' ?>>Free</option>
                <option value="0" <?= $isFree === '0' ? 'selected' : '' ?>>Premium (paid)</option>
            </select>
        </label>

        <label>
            Price (<?= e(config('app.currency')) ?>)
            <input type="number" name="price" id="price" step="0.01" min="0"
                   value="<?= e((string) old('price', $paper['price'] ?? '0')) ?>">
            <?php if (!empty($errors['price'])): ?><span class="field-error"><?= e($errors['price']) ?></span><?php endif; ?>
        </label>

        <label>
            Status
            <select name="status">
                <?php $status = (string) old('status', $paper['status'] ?? 'published'); ?>
                <?php foreach (['published', 'draft', 'archived'] as $option): ?>
                    <option value="<?= $option ?>" <?= $status === $option ? 'selected' : '' ?>><?= ucfirst($option) ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label class="full">
            Keywords
            <input type="text" name="keywords"
                   value="<?= e((string) old('keywords', $paper['keywords'] ?? '')) ?>"
                   placeholder="machine learning, education, Nigeria">
        </label>

        <label class="full">
            Document file (PDF, DOC, DOCX)
            <input type="file" name="file" accept=".pdf,.doc,.docx,application/pdf" <?= $isEdit ? '' : 'required' ?>>
            <?php if ($isEdit): ?>
                <span class="muted">Current file: <?= e($paper['file_name']) ?>. Leave empty to keep it.</span>
            <?php endif; ?>
            <?php if (!empty($errors['file'])): ?><span class="field-error"><?= e($errors['file']) ?></span><?php endif; ?>
        </label>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Save changes' : 'Upload paper' ?></button>
    </div>
</form>
