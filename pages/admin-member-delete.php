<?php

declare(strict_types=1);

csrf_verify();

$id = (int) ($params['id'] ?? 0);
$member = user_find($id);

if ($member === null) {
    abort(404, 'That member could not be found.');
}

if ($id === auth_id()) {
    flash('error', 'You cannot delete your own account.');
    redirect('/admin/members');
}

if ($member['role'] === 'super_admin' && user_count_super_admins(false) <= 1) {
    flash('error', 'The last super-admin account cannot be deleted.');
    redirect('/admin/members');
}

user_delete($id);

audit_record(
    auth_id(),
    'member.deleted',
    'Deleted account #' . $id . ' (' . $member['email'] . ')'
);

flash('success', 'Member account removed.');
redirect('/admin/members');
