<?php

declare(strict_types=1);

$user = current_user();
$categories = (array) content('resource_categories', []);
$category = input_string('category');
$search = mb_substr(input_string('q'), 0, 100);

if ($category !== '' && !in_array($category, $categories, true)) {
    $category = '';
}

$resources = resource_published($category !== '' ? $category : null, $search);
$categoryCounts = resource_category_counts();

$title = 'Resource library';
$metaDescription = 'Member-only reports, insights and company documents.';
$bodyClass = 'page-panel';

require includes_path('panel-header.php');
?>
<div class="panel-head">
    <div>
        <p class="eyebrow eyebrow--dark">Resource library</p>
        <h1 class="panel-title">Documents,<br><em>kept in one place.</em></h1>
        <p class="panel-subtitle">Reports, insights and company documents published for our partners.</p>
    </div>
</div>

<h2 class="sr-only">Published documents</h2>

<div class="filter-bar">
    <div class="filter-chips" aria-label="Filter by category">
        <a class="chip<?= $category === '' ? ' is-active' : '' ?>" href="/members/documents">All <span><?= array_sum($categoryCounts) ?></span></a>
        <?php foreach ($categories as $cat): ?>
            <?php if (($categoryCounts[$cat] ?? 0) > 0): ?>
                <a class="chip<?= $category === $cat ? ' is-active' : '' ?>" href="/members/documents?category=<?= e(urlencode($cat)) ?>">
                    <?= e($cat) ?> <span><?= (int) ($categoryCounts[$cat] ?? 0) ?></span>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <form class="search-form" method="get" action="/members/documents" role="search">
        <?php if ($category !== ''): ?>
            <input type="hidden" name="category" value="<?= e($category) ?>">
        <?php endif; ?>
        <span class="search-form__icon"><?= icon('search') ?></span>
        <input type="search" name="q" value="<?= e($search) ?>" placeholder="Search documents" aria-label="Search documents">
        <button type="submit" class="search-form__submit">Search</button>
    </form>
</div>

<?php if ($resources === []): ?>
    <div class="empty-state">
        <span class="empty-state__icon"><?= icon('folder') ?></span>
        <h2>No documents found</h2>
        <p><?= $search !== '' ? 'Try a different search term or clear the filters.' : 'Documents will appear here once they are published to the member library.' ?></p>
        <a class="btn btn--outline btn--small" href="/members/documents">Clear filters</a>
    </div>
<?php else: ?>
    <p class="result-count"><?= count($resources) ?> document<?= count($resources) === 1 ? '' : 's' ?><?= $category !== '' ? ' in ' . e($category) : '' ?></p>
    <div class="doc-grid">
        <?php foreach ($resources as $resource): ?>
            <?php require includes_path('resource-card.php'); ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="panel-card panel-card--note">
    <span class="panel-note__icon"><?= icon('shield') ?></span>
    <div>
        <h2>For partner use</h2>
        <p>These documents are confidential and provided for the purpose of our business relationship. Contact us if you need access to something that is not listed.</p>
    </div>
</div>
<?php require includes_path('panel-footer.php');
