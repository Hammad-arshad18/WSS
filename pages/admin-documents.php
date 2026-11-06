<?php

declare(strict_types=1);

$user = current_user();
$status = input_string('status');
$search = mb_substr(input_string('q'), 0, 100);

if (!in_array($status, ['published', 'draft'], true)) {
    $status = '';
}

$resources = resource_all($status !== '' ? $status : null, $search);
$counts = [
    'all' => resource_count_all(),
    'published' => resource_count_published(),
];
$categories = (array) content('resource_categories', []);

$title = 'Portal documents';
$metaDescription = 'Manage the client resource library.';
$bodyClass = 'page-panel';

require includes_path('panel-header.php');
?>
<div class="panel-head">
    <div>
        <p class="eyebrow eyebrow--dark">Document library</p>
        <h1 class="panel-title">Documents.</h1>
        <p class="panel-subtitle">Publish company documents, service sheets and notices to the partner portal.</p>
    </div>
    <div class="panel-head__actions">
        <a class="btn btn--dark btn--small" href="/admin/documents/create"><?= icon('plus') ?> Upload document</a>
    </div>
</div>

<div class="filter-bar">
    <div class="filter-chips" aria-label="Filter by status">
        <a class="chip<?= $status === '' ? ' is-active' : '' ?>" href="/admin/documents">All <span><?= (int) $counts['all'] ?></span></a>
        <a class="chip<?= $status === 'published' ? ' is-active' : '' ?>" href="/admin/documents?status=published">Published <span><?= (int) $counts['published'] ?></span></a>
        <a class="chip<?= $status === 'draft' ? ' is-active' : '' ?>" href="/admin/documents?status=draft">Drafts <span><?= max(0, (int) $counts['all'] - (int) $counts['published']) ?></span></a>
    </div>

    <form class="search-form" method="get" action="/admin/documents" role="search">
        <?php if ($status !== ''): ?>
            <input type="hidden" name="status" value="<?= e($status) ?>">
        <?php endif; ?>
        <span class="search-form__icon"><?= icon('search') ?></span>
        <input type="search" name="q" value="<?= e($search) ?>" placeholder="Search documents" aria-label="Search documents">
        <button type="submit" class="search-form__submit">Search</button>
    </form>
</div>

<div class="panel-card">
    <?php if ($resources === []): ?>
        <div class="empty-state">
            <span class="empty-state__icon"><?= icon('folder') ?></span>
            <h2>No documents found</h2>
            <p><?= $search !== '' || $status !== '' ? 'Try changing the filters or search term.' : 'Upload the first document for the client portal.' ?></p>
            <?php if ($search !== '' || $status !== ''): ?>
                <a class="btn btn--outline btn--small" href="/admin/documents">Clear filters</a>
            <?php else: ?>
                <a class="btn btn--dark btn--small" href="/admin/documents/create"><?= icon('plus') ?> Upload document</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th scope="col">Document</th>
                        <th scope="col">Category</th>
                        <th scope="col">Size</th>
                        <th scope="col">Status</th>
                        <th scope="col">Updated</th>
                        <th scope="col" class="data-table__actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resources as $resource): ?>
                        <tr>
                            <td>
                                <div class="table-person">
                                    <span class="doc-icon doc-icon--sm doc-icon--<?= e(file_kind($resource['mime_type'])) ?>"><?= icon(file_kind($resource['mime_type']) === 'sheet' ? 'sheet' : 'file') ?></span>
                                    <span>
                                        <strong><?= e($resource['title']) ?></strong>
                                        <small><?= e($resource['original_name']) ?></small>
                                    </span>
                                </div>
                            </td>
                            <td class="data-table__muted"><?= e($resource['category']) ?></td>
                            <td class="data-table__muted"><?= e(human_filesize($resource['file_size'])) ?></td>
                            <td>
                                <span class="status-badge <?= (int) $resource['is_published'] ? 'status-badge--active' : 'status-badge--draft' ?>">
                                    <?= (int) $resource['is_published'] ? 'Published' : 'Draft' ?>
                                </span>
                            </td>
                            <td class="data-table__muted"><?= e(format_date($resource['updated_at'] ?? $resource['created_at'])) ?></td>
                            <td class="data-table__actions">
                                <div class="row-actions">
                                    <a class="icon-btn" href="/admin/documents/<?= (int) $resource['id'] ?>/edit" title="Edit document" aria-label="Edit <?= e($resource['title']) ?>"><?= icon('edit') ?></a>
                                    <form method="post" action="/admin/documents/<?= (int) $resource['id'] ?>/publish">
                                        <?= csrf_field() ?>
                                        <button class="icon-btn" type="submit" title="<?= (int) $resource['is_published'] ? 'Move to drafts' : 'Publish' ?>" aria-label="<?= (int) $resource['is_published'] ? 'Unpublish' : 'Publish' ?> <?= e($resource['title']) ?>">
                                            <?= icon((int) $resource['is_published'] ? 'eye-off' : 'eye') ?>
                                        </button>
                                    </form>
                                    <form method="post" action="/admin/documents/<?= (int) $resource['id'] ?>/delete" data-confirm="Delete “<?= e($resource['title']) ?>” permanently?">
                                        <?= csrf_field() ?>
                                        <button class="icon-btn icon-btn--danger" type="submit" title="Delete document" aria-label="Delete <?= e($resource['title']) ?>"><?= icon('trash') ?></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<div class="panel-card panel-card--note">
    <span class="panel-note__icon"><?= icon('shield') ?></span>
    <div>
        <h2>Private by default</h2>
        <p>Uploaded files are stored outside the public web root and served only through an authenticated download route. Drafts are never visible to account holders.</p>
    </div>
</div>
<?php require includes_path('panel-footer.php');
