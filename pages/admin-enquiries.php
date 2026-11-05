<?php

declare(strict_types=1);

$user = current_user();
$status = input_string('status');
$search = mb_substr(input_string('q'), 0, 100);

if (!in_array($status, ['new', 'handled'], true)) {
    $status = '';
}

$enquiries = enquiry_all($status !== '' ? $status : null, $search);
$counts = [
    'all' => enquiry_count_all(),
    'new' => enquiry_count_new(),
];

$title = 'Enquiries';
$metaDescription = 'Website enquiries.';
$bodyClass = 'page-panel';

require includes_path('panel-header.php');
?>
<div class="panel-head">
    <div>
        <p class="eyebrow eyebrow--dark">Inbox</p>
        <h1 class="panel-title">Enquiries.</h1>
        <p class="panel-subtitle">Messages from the website contact form, kept in one place.</p>
    </div>
</div>

<div class="filter-bar">
    <div class="filter-chips" aria-label="Filter by status">
        <a class="chip<?= $status === '' ? ' is-active' : '' ?>" href="/admin/enquiries">All <span><?= (int) $counts['all'] ?></span></a>
        <a class="chip<?= $status === 'new' ? ' is-active' : '' ?>" href="/admin/enquiries?status=new">New <span><?= (int) $counts['new'] ?></span></a>
        <a class="chip<?= $status === 'handled' ? ' is-active' : '' ?>" href="/admin/enquiries?status=handled">Handled <span><?= max(0, (int) $counts['all'] - (int) $counts['new']) ?></span></a>
    </div>

    <form class="search-form" method="get" action="/admin/enquiries" role="search">
        <?php if ($status !== ''): ?>
            <input type="hidden" name="status" value="<?= e($status) ?>">
        <?php endif; ?>
        <span class="search-form__icon"><?= icon('search') ?></span>
        <input type="search" name="q" value="<?= e($search) ?>" placeholder="Search enquiries" aria-label="Search enquiries">
        <button type="submit" class="search-form__submit">Search</button>
    </form>
</div>

<?php if ($enquiries === []): ?>
    <div class="panel-card">
        <div class="empty-state">
            <span class="empty-state__icon"><?= icon('inbox') ?></span>
            <h2>No enquiries found</h2>
            <p><?= $search !== '' || $status !== '' ? 'Try changing the filters or search term.' : 'New messages from the contact form will appear here.' ?></p>
            <?php if ($search !== '' || $status !== ''): ?>
                <a class="btn btn--outline btn--small" href="/admin/enquiries">Clear filters</a>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="enquiry-list">
        <?php foreach ($enquiries as $enquiry): ?>
            <article class="enquiry-card<?= $enquiry['status'] === 'new' ? ' is-new' : '' ?>">
                <header class="enquiry-card__head">
                    <span class="avatar"><?= e(initials((string) $enquiry['name'])) ?></span>
                    <div class="enquiry-card__who">
                        <h2><?= e($enquiry['name']) ?> <?php if ($enquiry['status'] === 'new'): ?><span class="badge badge--gold">New</span><?php endif; ?></h2>
                        <p>
                            <a href="mailto:<?= e($enquiry['email']) ?>"><?= e($enquiry['email']) ?></a>
                            <?php if ($enquiry['company'] !== ''): ?><span>·</span> <?= e($enquiry['company']) ?><?php endif; ?>
                            <?php if ($enquiry['phone'] !== ''): ?><span>·</span> <a href="tel:<?= e($enquiry['phone']) ?>"><?= e($enquiry['phone']) ?></a><?php endif; ?>
                        </p>
                    </div>
                    <div class="enquiry-card__meta">
                        <time datetime="<?= e($enquiry['created_at']) ?>"><?= e(format_datetime($enquiry['created_at'])) ?></time>
                        <span class="status-badge <?= $enquiry['status'] === 'new' ? 'status-badge--new' : 'status-badge--handled' ?>"><?= $enquiry['status'] === 'new' ? 'Awaiting reply' : 'Handled' ?></span>
                    </div>
                </header>

                <div class="enquiry-card__body">
                    <p class="enquiry-card__subject"><?= e($enquiry['subject'] ?: 'General enquiry') ?></p>
                    <p class="enquiry-card__message"><?= nl2br(e($enquiry['message'])) ?></p>
                </div>

                <footer class="enquiry-card__foot">
                    <span class="enquiry-card__ref">Reference #<?= (int) $enquiry['id'] ?> · IP <?= e($enquiry['ip']) ?></span>
                    <div class="enquiry-card__actions">
                        <a class="btn btn--outline btn--small" href="mailto:<?= e($enquiry['email']) ?>?subject=<?= e(urlencode('Re: ' . ($enquiry['subject'] ?: 'Your enquiry to White Sea Star'))) ?>">
                            <?= icon('mail') ?> Reply
                        </a>
                        <form method="post" action="/admin/enquiries/<?= (int) $enquiry['id'] ?>/handled">
                            <?= csrf_field() ?>
                            <button class="btn btn--ghost btn--small" type="submit">
                                <?= icon('check') ?> Mark as <?= $enquiry['status'] === 'new' ? 'handled' : 'new' ?>
                            </button>
                        </form>
                    </div>
                </footer>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php require includes_path('panel-footer.php');
