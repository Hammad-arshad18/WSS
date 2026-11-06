<?php

declare(strict_types=1);

$user = current_user();
$allResources = resource_all();
$audit = audit_recent(8);
$memberStats = user_stats();
$counts = [
    'published' => resource_count_published(),
    'resources' => resource_count_all(),
    'newEnquiries' => enquiry_count_new(),
    'enquiries' => enquiry_count_all(),
    'todayActions' => audit_count_today(),
];
$recentMembers = user_recent(5);
$recentResources = array_slice($allResources, 0, 5);
$recentEnquiries = enquiry_recent(4);
$recentAudit = $audit;
$firstName = explode(' ', trim((string) $user['name']))[0] ?? 'admin';

$title = 'Administration';
$metaDescription = 'White Sea Star administration.';
$bodyClass = 'page-panel';

require includes_path('panel-header.php');
?>
<div class="panel-head">
    <div>
        <p class="eyebrow eyebrow--dark">Administration</p>
        <h1 class="panel-title">Good to see you,<br><em><?= e($firstName) ?>.</em></h1>
        <p class="panel-subtitle">A clear view of partner, supplier and customer accounts, published documents and enquiries.</p>
    </div>
    <div class="panel-head__actions">
        <a class="btn btn--outline btn--small" href="/admin/members/create"><?= icon('plus') ?> Add account</a>
        <a class="btn btn--dark btn--small" href="/admin/documents/create"><?= icon('plus') ?> Upload document</a>
    </div>
</div>

<div class="stat-grid">
    <article class="stat-card">
        <span class="stat-card__icon"><?= icon('users') ?></span>
        <div>
            <strong><?= (int) $memberStats['active'] ?></strong>
            <span>Active accounts</span>
        </div>
        <a class="stat-card__link" href="/admin/members" aria-label="View accounts"><?= icon('arrow') ?></a>
    </article>
    <article class="stat-card">
        <span class="stat-card__icon"><?= icon('folder') ?></span>
        <div>
            <strong><?= (int) $counts['published'] ?></strong>
            <span>Published documents</span>
        </div>
        <a class="stat-card__link" href="/admin/documents" aria-label="View documents"><?= icon('arrow') ?></a>
    </article>
    <article class="stat-card">
        <span class="stat-card__icon"><?= icon('inbox') ?></span>
        <div>
            <strong><?= (int) $counts['newEnquiries'] ?></strong>
            <span>New enquiries</span>
        </div>
        <a class="stat-card__link" href="/admin/enquiries" aria-label="View enquiries"><?= icon('arrow') ?></a>
    </article>
    <article class="stat-card">
        <span class="stat-card__icon"><?= icon('shield') ?></span>
        <div>
            <strong><?= (int) $counts['todayActions'] ?></strong>
            <span>Actions today</span>
        </div>
        <a class="stat-card__link" href="/admin/audit" aria-label="View activity log"><?= icon('arrow') ?></a>
    </article>
</div>

<div class="panel-grid">
    <section class="panel-card panel-card--wide">
        <div class="panel-card__head">
            <div>
                <p class="eyebrow eyebrow--dark">Inbox</p>
                <h2>Recent enquiries</h2>
            </div>
            <a class="text-link" href="/admin/enquiries">All enquiries <?= icon('arrow') ?></a>
        </div>

        <?php if ($recentEnquiries === []): ?>
            <div class="empty-state empty-state--compact">
                <span class="empty-state__icon"><?= icon('inbox') ?></span>
                <h3>No enquiries yet</h3>
                <p>Messages sent through the website will appear here.</p>
            </div>
        <?php else: ?>
            <div class="mini-list">
                <?php foreach ($recentEnquiries as $enquiry): ?>
                    <a class="mini-row" href="/admin/enquiries">
                        <span class="avatar avatar--sm"><?= e(initials((string) $enquiry['name'])) ?></span>
                        <span class="mini-row__body">
                            <strong><?= e($enquiry['name']) ?> <?php if ($enquiry['status'] === 'new'): ?><span class="dot-new" title="New"></span><?php endif; ?></strong>
                            <small><?= e($enquiry['subject'] ?: 'General enquiry') ?> · <?= e(excerpt((string) $enquiry['message'], 64)) ?></small>
                        </span>
                        <span class="mini-row__meta"><?= e(time_ago($enquiry['created_at'])) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <aside class="panel-card">
        <div class="panel-card__head">
            <div>
                <p class="eyebrow eyebrow--dark">Shortcuts</p>
                <h2>Quick actions</h2>
            </div>
        </div>

        <div class="quick-actions">
            <a href="/admin/members/create"><?= icon('user') ?><span>Add an account<small>Create portal access</small></span><?= icon('arrow') ?></a>
            <a href="/admin/documents/create"><?= icon('file') ?><span>Upload a document<small>Publish to portal</small></span><?= icon('arrow') ?></a>
            <a href="/admin/enquiries"><?= icon('inbox') ?><span>Review enquiries<small><?= (int) $counts['newEnquiries'] ?> awaiting attention</small></span><?= icon('arrow') ?></a>
            <a href="/admin/audit"><?= icon('shield') ?><span>Activity log<small>Security and admin events</small></span><?= icon('arrow') ?></a>
        </div>
    </aside>
</div>

<div class="panel-grid panel-grid--even">
    <section class="panel-card">
        <div class="panel-card__head">
            <div>
                <p class="eyebrow eyebrow--dark">Library</p>
                <h2>Recent documents</h2>
            </div>
            <a class="text-link" href="/admin/documents">Manage <?= icon('arrow') ?></a>
        </div>

        <?php if ($recentResources === []): ?>
            <div class="empty-state empty-state--compact">
                <span class="empty-state__icon"><?= icon('folder') ?></span>
                <h3>No documents yet</h3>
                <p>Upload the first portal document.</p>
                <a class="btn btn--outline btn--small" href="/admin/documents/create">Upload document</a>
            </div>
        <?php else: ?>
            <div class="mini-list">
                <?php foreach ($recentResources as $resource): ?>
                    <a class="mini-row" href="/admin/documents/<?= (int) $resource['id'] ?>/edit">
                        <span class="doc-icon doc-icon--sm doc-icon--<?= e(file_kind($resource['mime_type'])) ?>"><?= icon(file_kind($resource['mime_type']) === 'sheet' ? 'sheet' : 'file') ?></span>
                        <span class="mini-row__body">
                            <strong><?= e($resource['title']) ?></strong>
                            <small><?= e($resource['category']) ?> · <?= e(human_filesize($resource['file_size'])) ?></small>
                        </span>
                        <span class="badge <?= (int) $resource['is_published'] ? 'badge--gold' : 'badge--muted' ?>"><?= (int) $resource['is_published'] ? 'Published' : 'Draft' ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <section class="panel-card">
        <div class="panel-card__head">
            <div>
                <p class="eyebrow eyebrow--dark">Security</p>
                <h2>Recent activity</h2>
            </div>
            <a class="text-link" href="/admin/audit">Full log <?= icon('arrow') ?></a>
        </div>

        <?php if ($recentAudit === []): ?>
            <div class="empty-state empty-state--compact">
                <span class="empty-state__icon"><?= icon('shield') ?></span>
                <h3>No activity recorded</h3>
            </div>
        <?php else: ?>
            <div class="activity-list">
                <?php foreach ($recentAudit as $log): ?>
                    <div class="activity-row">
                        <span class="activity-row__dot"></span>
                        <span class="activity-row__body">
                            <strong><?= e($log['user_name'] ?: 'System') ?></strong>
                            <small><?= e(ucfirst(str_replace(['.', '_'], [' · ', ' '], $log['action']))) ?></small>
                        </span>
                        <time datetime="<?= e($log['created_at']) ?>"><?= e(time_ago($log['created_at'])) ?></time>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>

<div class="panel-grid panel-grid--even">
    <section class="panel-card">
        <div class="panel-card__head">
            <div>
                <p class="eyebrow eyebrow--dark">Access</p>
                <h2>Newest accounts</h2>
            </div>
            <a class="text-link" href="/admin/members">All accounts <?= icon('arrow') ?></a>
        </div>

        <?php if ($recentMembers === []): ?>
            <div class="empty-state empty-state--compact">
                <span class="empty-state__icon"><?= icon('users') ?></span>
                <h3>No accounts yet</h3>
            </div>
        <?php else: ?>
            <div class="mini-list">
                <?php foreach ($recentMembers as $member): ?>
                    <a class="mini-row" href="/admin/members/<?= (int) $member['id'] ?>/edit">
                        <span class="avatar avatar--sm"><?= e(initials((string) $member['name'])) ?></span>
                        <span class="mini-row__body">
                            <strong><?= e($member['name']) ?></strong>
                            <small><?= e($member['email']) ?></small>
                        </span>
                        <?php
                        $role = (string) $member['role'];
                        $badgeClass = match ($role) {
                            'super_admin' => 'badge--navy',
                            'supplier' => 'badge--soft',
                            'customer' => 'badge--gold',
                            default => 'badge--muted',
                        };
                        ?>
                        <span class="badge <?= e($badgeClass) ?>"><?= e(role_label($role)) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <section class="panel-card panel-card--note panel-card--tall">
        <span class="panel-note__icon"><?= icon('lock') ?></span>
        <div>
            <h2>Security posture</h2>
            <p>All form submissions are CSRF-protected, passwords are hashed with Argon2id, and client documents are stored outside the public web root.</p>
            <a class="text-link" href="/admin/audit">Review the activity log <?= icon('arrow') ?></a>
        </div>
    </section>
</div>
<?php require includes_path('panel-footer.php');
