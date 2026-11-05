<?php

declare(strict_types=1);

$user = current_user();
$search = mb_substr(input_string('q'), 0, 100);
$members = user_all($search);
$memberStats = user_stats();

$title = 'Members';
$metaDescription = 'Manage member access.';
$bodyClass = 'page-panel';

require includes_path('panel-header.php');
?>
<div class="panel-head">
    <div>
        <p class="eyebrow eyebrow--dark">Access</p>
        <h1 class="panel-title">Members.</h1>
        <p class="panel-subtitle">Create accounts, control access and review who can reach the partner library.</p>
    </div>
    <div class="panel-head__actions">
        <a class="btn btn--dark btn--small" href="/admin/members/create"><?= icon('plus') ?> Add member</a>
    </div>
</div>

<div class="stat-grid stat-grid--three">
    <article class="stat-card">
        <span class="stat-card__icon"><?= icon('users') ?></span>
        <div><strong><?= (int) $memberStats['total'] ?></strong><span>Total accounts</span></div>
    </article>
    <article class="stat-card">
        <span class="stat-card__icon"><?= icon('check') ?></span>
        <div><strong><?= (int) $memberStats['active'] ?></strong><span>Active accounts</span></div>
    </article>
    <article class="stat-card">
        <span class="stat-card__icon"><?= icon('shield') ?></span>
        <div><strong><?= (int) $memberStats['admins'] ?></strong><span>Administrators</span></div>
    </article>
</div>

<div class="panel-card">
    <div class="table-toolbar">
        <form class="search-form" method="get" action="/admin/members" role="search">
            <span class="search-form__icon"><?= icon('search') ?></span>
            <input type="search" name="q" value="<?= e($search) ?>" placeholder="Search by name or email" aria-label="Search members">
            <button type="submit" class="search-form__submit">Search</button>
        </form>
        <?php if ($search !== ''): ?>
            <a class="text-link text-link--muted" href="/admin/members">Clear search</a>
        <?php endif; ?>
    </div>

    <?php if ($members === []): ?>
        <div class="empty-state">
            <span class="empty-state__icon"><?= icon('users') ?></span>
            <h2>No members found</h2>
            <p><?= $search !== '' ? 'No account matches that search.' : 'Create the first member account to share the partner library.' ?></p>
            <?php if ($search !== ''): ?>
                <a class="btn btn--outline btn--small" href="/admin/members">Clear search</a>
            <?php else: ?>
                <a class="btn btn--dark btn--small" href="/admin/members/create"><?= icon('plus') ?> Add member</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th scope="col">Member</th>
                        <th scope="col">Role</th>
                        <th scope="col">Status</th>
                        <th scope="col">Last sign-in</th>
                        <th scope="col" class="data-table__actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($members as $member): ?>
                        <?php $isSelf = (int) $member['id'] === (int) $user['id']; ?>
                        <tr>
                            <td>
                                <div class="table-person">
                                    <span class="avatar avatar--sm"><?= e(initials((string) $member['name'])) ?></span>
                                    <span>
                                        <strong><?= e($member['name']) ?><?= $isSelf ? ' <em>(you)</em>' : '' ?></strong>
                                        <small><?= e($member['email']) ?></small>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="badge <?= $member['role'] === 'super_admin' ? 'badge--navy' : 'badge--muted' ?>">
                                    <?= $member['role'] === 'super_admin' ? 'Super admin' : 'Member' ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge <?= (int) $member['is_active'] ? 'status-badge--active' : 'status-badge--inactive' ?>">
                                    <?= (int) $member['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td class="data-table__muted"><?= e($member['last_login_at'] ? time_ago($member['last_login_at']) : 'Never') ?></td>
                            <td class="data-table__actions">
                                <div class="row-actions">
                                    <a class="icon-btn" href="/admin/members/<?= (int) $member['id'] ?>/edit" title="Edit member" aria-label="Edit <?= e($member['name']) ?>">
                                        <?= icon('edit') ?>
                                    </a>

                                    <?php if (!$isSelf): ?>
                                        <form method="post" action="/admin/members/<?= (int) $member['id'] ?>/status">
                                            <?= csrf_field() ?>
                                            <button class="icon-btn" type="submit" title="<?= (int) $member['is_active'] ? 'Deactivate' : 'Activate' ?>" aria-label="<?= (int) $member['is_active'] ? 'Deactivate' : 'Activate' ?> <?= e($member['name']) ?>">
                                                <?= icon((int) $member['is_active'] ? 'eye-off' : 'check') ?>
                                            </button>
                                        </form>
                                        <form method="post" action="/admin/members/<?= (int) $member['id'] ?>/delete" data-confirm="Remove <?= e($member['name']) ?> permanently? This cannot be undone.">
                                            <?= csrf_field() ?>
                                            <button class="icon-btn icon-btn--danger" type="submit" title="Remove member" aria-label="Remove <?= e($member['name']) ?>">
                                                <?= icon('trash') ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>
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
        <h2>Access by invitation</h2>
        <p>There is no public registration. Members are created here by a super-admin, and accounts can be deactivated at any time without deleting their history.</p>
    </div>
</div>
<?php require includes_path('panel-footer.php');
