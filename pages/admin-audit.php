<?php

declare(strict_types=1);

$user = current_user();
$logs = audit_recent(200);

$title = 'Activity log';
$metaDescription = 'Security and administration activity.';
$bodyClass = 'page-panel';

require includes_path('panel-header.php');
?>
<div class="panel-head">
    <div>
        <p class="eyebrow eyebrow--dark">Security</p>
        <h1 class="panel-title">Activity log.</h1>
        <p class="panel-subtitle">A chronological record of sign-ins, account changes and document activity.</p>
    </div>
</div>

<div class="panel-card">
    <div class="panel-card__head">
        <div>
            <p class="eyebrow eyebrow--dark">Audit trail</p>
            <h2>Latest <?= count($logs) ?> events</h2>
        </div>
        <span class="badge badge--soft"><?= icon('shield') ?> Read-only</span>
    </div>

    <?php if ($logs === []): ?>
        <div class="empty-state">
            <span class="empty-state__icon"><?= icon('shield') ?></span>
            <h2>No activity yet</h2>
            <p>Sign-ins and administration actions will be recorded here.</p>
        </div>
    <?php else: ?>
        <div class="table-wrap">
            <table class="data-table data-table--log">
                <thead>
                    <tr>
                        <th scope="col">When</th>
                        <th scope="col">User</th>
                        <th scope="col">Action</th>
                        <th scope="col">Details</th>
                        <th scope="col">IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td class="data-table__muted data-table__nowrap"><?= e(format_datetime($log['created_at'])) ?></td>
                            <td>
                                <?php if ($log['user_name']): ?>
                                    <div class="table-person table-person--compact">
                                        <span class="avatar avatar--xs"><?= e(initials((string) $log['user_name'])) ?></span>
                                        <span>
                                            <strong><?= e($log['user_name']) ?></strong>
                                            <small><?= e($log['user_email']) ?></small>
                                        </span>
                                    </div>
                                <?php else: ?>
                                    <span class="data-table__muted">System / guest</span>
                                <?php endif; ?>
                            </td>
                            <td><span class="action-tag"><?= e(str_replace(['.', '_'], [' · ', ' '], $log['action'])) ?></span></td>
                            <td class="data-table__muted"><?= e($log['details'] ?: '—') ?></td>
                            <td class="data-table__muted data-table__mono"><?= e($log['ip']) ?></td>
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
        <h2>Retention</h2>
        <p>The activity log is append-only through the application. Entries are linked to accounts where possible; deleting an account preserves the event but removes the account link.</p>
    </div>
</div>
<?php require includes_path('panel-footer.php');
