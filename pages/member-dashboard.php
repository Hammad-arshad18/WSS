<?php

declare(strict_types=1);

$user = current_user();
$resources = resource_latest_published(4);
$resourceCount = resource_count_published();
$categoryCounts = resource_category_counts();
$categories = (array) content('resource_categories', []);
$firstName = explode(' ', trim((string) $user['name']))[0] ?? 'partner';

$title = 'Member dashboard';
$metaDescription = 'Your White Sea Star member area.';
$bodyClass = 'page-panel';

require includes_path('panel-header.php');
?>
<div class="panel-head">
    <div>
        <p class="eyebrow eyebrow--dark">Dashboard</p>
        <h1 class="panel-title">Welcome back,<br><em><?= e($firstName) ?>.</em></h1>
        <p class="panel-subtitle">Your private library of reports, insights and partner resources.</p>
    </div>
    <div class="panel-head__actions">
        <a class="btn btn--dark btn--small" href="/members/documents">Browse library <?= icon('arrow') ?></a>
    </div>
</div>

<div class="stat-grid stat-grid--three">
    <article class="stat-card">
        <span class="stat-card__icon"><?= icon('folder') ?></span>
        <div>
            <strong><?= (int) $resourceCount ?></strong>
            <span>Published documents</span>
        </div>
    </article>
    <article class="stat-card">
        <span class="stat-card__icon"><?= icon('diamond') ?></span>
        <div>
            <strong><?= count($categoryCounts) ?></strong>
            <span>Resource categories</span>
        </div>
    </article>
    <article class="stat-card">
        <span class="stat-card__icon"><?= icon('clock') ?></span>
        <div>
            <strong class="stat-card__date"><?= e(format_date($user['last_login_at'] ?? null, 'j M')) ?></strong>
            <span>Last sign-in</span>
        </div>
    </article>
</div>

<div class="panel-grid">
    <section class="panel-card panel-card--wide">
        <div class="panel-card__head">
            <div>
                <p class="eyebrow eyebrow--dark">Recently added</p>
                <h2>Latest resources</h2>
            </div>
            <a class="text-link" href="/members/documents">View all <?= icon('arrow') ?></a>
        </div>

        <?php if ($resources === []): ?>
            <div class="empty-state empty-state--compact">
                <span class="empty-state__icon"><?= icon('folder') ?></span>
                <h3>No documents yet</h3>
                <p>New reports and partner documents will appear here as soon as they are published.</p>
            </div>
        <?php else: ?>
            <div class="doc-grid doc-grid--dashboard">
                <?php foreach ($resources as $resource): ?>
                    <?php require includes_path('resource-card.php'); ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <aside class="panel-card">
        <div class="panel-card__head">
            <div>
                <p class="eyebrow eyebrow--dark">Your account</p>
                <h2>Membership</h2>
            </div>
        </div>

        <div class="member-card">
            <span class="avatar avatar--lg"><?= e(initials((string) $user['name'])) ?></span>
            <h3><?= e($user['name']) ?></h3>
            <p><?= e($user['email']) ?></p>
            <span class="badge badge--gold"><?= $user['role'] === 'super_admin' ? 'Super admin' : 'Member' ?></span>
        </div>

        <dl class="meta-list">
            <div>
                <dt>Member since</dt>
                <dd><?= e(format_date($user['created_at'] ?? null)) ?></dd>
            </div>
            <div>
                <dt>Last sign-in</dt>
                <dd><?= e(format_datetime($user['last_login_at'] ?? null)) ?></dd>
            </div>
            <div>
                <dt>Account status</dt>
                <dd><span class="status-badge status-badge--active">Active</span></dd>
            </div>
        </dl>

        <a class="btn btn--outline btn--full btn--small" href="/profile">Account settings <?= icon('arrow') ?></a>
    </aside>
</div>

<div class="panel-card panel-card--note">
    <span class="panel-note__icon"><?= icon('shield') ?></span>
    <div>
        <h2>Confidential by design</h2>
        <p>Documents in this portal are shared for the purpose of our business relationship. Please do not redistribute them without permission.</p>
    </div>
</div>
<?php require includes_path('panel-footer.php');
