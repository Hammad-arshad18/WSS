<?php

declare(strict_types=1);

csrf_verify();

$id = (int) ($params['id'] ?? 0);
$member = user_find($id);

if ($member === null) {
    abort(404, 'That account could not be found.');
}

if ($id === auth_id()) {
    flash('error', 'You cannot change the status of your own account.');
    redirect('/admin/members');
}

$isActive = (int) $member['is_active'] === 1;

if ($isActive && $member['role'] === 'super_admin' && user_count_super_admins() <= 1) {
    flash('error', 'The last active super-admin cannot be deactivated.');
    redirect('/admin/members');
}

user_set_active($id, !$isActive);

audit_record(
    auth_id(),
    'member.status_changed',
    ($isActive ? 'Deactivated' : 'Activated') . ' account #' . $id . ' (' . $member['email'] . ')'
);

flash('success', $isActive ? 'Account deactivated.' : 'Account activated.');
redirect('/admin/members');
