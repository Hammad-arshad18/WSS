<?php

/** @var array $resource */
$kind = file_kind($resource['mime_type'] ?? '');
$kindIcon = match ($kind) {
    'image' => 'image',
    'sheet' => 'sheet',
    default => 'file',
};
?>
<article class="doc-card">
    <div class="doc-card__top">
        <span class="doc-icon doc-icon--<?= e($kind) ?>"><?= icon($kindIcon) ?></span>
        <span class="doc-card__cat"><?= e($resource['category']) ?></span>
    </div>
    <h3><?= e($resource['title']) ?></h3>
    <?php if (($resource['description'] ?? '') !== ''): ?>
        <p><?= e(excerpt((string) $resource['description'], 120)) ?></p>
    <?php endif; ?>
    <div class="doc-card__foot">
        <span class="doc-card__meta"><?= e(human_filesize($resource['file_size'] ?? 0)) ?> · <?= e(format_date($resource['updated_at'] ?? $resource['created_at'] ?? null)) ?></span>
        <a class="doc-download" href="/members/documents/<?= (int) $resource['id'] ?>/download">
            <?= icon('download') ?> Download
        </a>
    </div>
</article>
