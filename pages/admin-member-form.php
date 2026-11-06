<?php

declare(strict_types=1);

$user = current_user();
$roles = ['partner', 'supplier', 'customer', 'super_admin'];

if (is_post() && $action === 'store_member') {
    csrf_verify();

    $name = input_string('name');
    $email = mb_strtolower(input_string('email'));
    $role = input_string('role', 'partner');
    $password = raw_input('password');
    $confirmation = raw_input('password_confirmation');

    if (!in_array($role, $roles, true)) {
        $role = 'partner';
    }

    $errors = validate(
        [
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'password' => $password,
            'password_confirmation' => $confirmation,
        ],
        [
            'name' => 'required|min:2|max:120',
            'email' => 'required|email|max:190',
            'role' => 'required|in:' . implode(',', $roles),
            'password' => 'required|password|max:255|confirmed',
        ]
    );

    if ($errors === [] && user_email_exists($email)) {
        $errors['email'] = 'An account with that email already exists.';
    }

    if ($errors !== []) {
        flash_errors($errors);
        flash_input(['name' => $name, 'email' => $email, 'role' => $role]);
        flash('error', 'Please review the new account details.');
        redirect('/admin/members/create');
    }

    $id = user_create($name, $email, hash_password($password), $role, auth_id());
    audit_record(auth_id(), 'member.created', 'Created ' . $role . ' account #' . $id . ' (' . $email . ')');

    flash('success', 'Account created for ' . $email . '.');
    redirect('/admin/members');
}

if (is_post() && $action === 'update_member') {
    csrf_verify();

    $id = (int) ($params['id'] ?? 0);
    $member = user_find($id);

    if ($member === null) {
        abort(404, 'That account could not be found.');
    }

    $name = input_string('name');
    $email = mb_strtolower(input_string('email'));
    $role = input_string('role', (string) $member['role']);
    $password = raw_input('password');
    $confirmation = raw_input('password_confirmation');

    if (!in_array($role, $roles, true)) {
        $role = 'partner';
    }

    $rules = [
        'name' => 'required|min:2|max:120',
        'email' => 'required|email|max:190',
        'role' => 'required|in:' . implode(',', $roles),
    ];

    if ($password !== '') {
        $rules['password'] = 'required|password|max:255|confirmed';
    }

    $errors = validate(
        [
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'password' => $password,
            'password_confirmation' => $confirmation,
        ],
        $rules
    );

    if ($errors === [] && user_email_exists($email, $id)) {
        $errors['email'] = 'Another account already uses that email.';
    }

    $isSelf = $id === auth_id();

    if ($errors === [] && $isSelf && $role !== 'super_admin') {
        $errors['role'] = 'You cannot remove your own administrator role.';
    }

    if (
        $errors === []
        && $member['role'] === 'super_admin'
        && $role !== 'super_admin'
        && (int) $member['is_active'] === 1
        && user_count_super_admins() <= 1
    ) {
        $errors['role'] = 'At least one active super-admin must remain.';
    }

    if ($errors !== []) {
        flash_errors($errors);
        flash_input(['name' => $name, 'email' => $email, 'role' => $role]);
        flash('error', 'The account details could not be saved.');
        redirect('/admin/members/' . $id . '/edit');
    }

    user_update($id, $name, $email, $role);

    if ($password !== '') {
        user_update_password($id, hash_password($password));
    }

    audit_record(
        auth_id(),
        'member.updated',
        'Updated account #' . $id . ' (' . $email . ')' . ($password !== '' ? ' and reset password' : '')
    );

    flash('success', 'Account details saved.');
    redirect('/admin/members');
}

$member = null;

if (isset($params['id'])) {
    $member = user_find((int) $params['id']);

    if ($member === null) {
        abort(404, 'That account could not be found.');
    }
}

$isEdit = $member !== null;
$actionUrl = $isEdit ? '/admin/members/' . (int) $member['id'] : '/admin/members';
$roleValue = old('role', $member['role'] ?? 'partner');
if ($roleValue === 'member') {
    $roleValue = 'partner';
}

$title = $isEdit ? 'Edit account' : 'Add account';
$metaDescription = $isEdit ? 'Update account details and access.' : 'Create a partner, supplier or customer account.';
$bodyClass = 'page-panel';

require includes_path('panel-header.php');
?>
<a class="back-link back-link--panel" href="/admin/members"><?= icon('arrow') ?> Back to accounts</a>

<div class="panel-head">
    <div>
        <p class="eyebrow eyebrow--dark"><?= $isEdit ? 'Edit access' : 'New account' ?></p>
        <h1 class="panel-title"><?= $isEdit ? 'Edit account.' : 'Add an account.' ?></h1>
        <p class="panel-subtitle"><?= $isEdit ? 'Update account details, role or password.' : 'Create a partner, supplier or customer account.' ?></p>
    </div>
</div>

<div class="form-layout">
    <div class="form-layout__main">
        <form class="panel-card form" method="post" action="<?= e($actionUrl) ?>" novalidate>
            <?= csrf_field() ?>

            <div class="panel-card__head">
                <div>
                    <p class="eyebrow eyebrow--dark">Identity</p>
                    <h2>Account details</h2>
                </div>
            </div>

            <div class="form__row">
                <div class="field<?= form_error('name') !== '' ? ' has-error' : '' ?>">
                    <label for="name">Full name</label>
                    <input id="name" name="name" type="text" value="<?= e(old('name', $member['name'] ?? '')) ?>" required maxlength="120" autocomplete="name">
                    <?php if (form_error('name') !== ''): ?><p class="field__error"><?= e(form_error('name')) ?></p><?php endif; ?>
                </div>

                <div class="field<?= form_error('email') !== '' ? ' has-error' : '' ?>">
                    <label for="email">Email address</label>
                    <input id="email" name="email" type="email" value="<?= e(old('email', $member['email'] ?? '')) ?>" required maxlength="190" autocomplete="email">
                    <?php if (form_error('email') !== ''): ?><p class="field__error"><?= e(form_error('email')) ?></p><?php endif; ?>
                </div>
            </div>

            <div class="field<?= form_error('role') !== '' ? ' has-error' : '' ?>">
                <label for="role">Access level</label>
                <div class="select-wrap">
                    <select id="role" name="role" required>
                        <option value="partner"<?= selected($roleValue, 'partner') ?>>Partner — partner portal and shared resources</option>
                        <option value="supplier"<?= selected($roleValue, 'supplier') ?>>Supplier — supplier information and compliance</option>
                        <option value="customer"<?= selected($roleValue, 'customer') ?>>Customer — service sheets and account documents</option>
                        <option value="super_admin"<?= selected($roleValue, 'super_admin') ?>>Super admin — full administration</option>
                    </select>
                    <?= icon('chevron') ?>
                </div>
                <?php if (form_error('role') !== ''): ?><p class="field__error"><?= e(form_error('role')) ?></p><?php endif; ?>
                <p class="field__help">All roles use the same sign-in. The role controls which resource categories are visible after sign-in.</p>
            </div>

            <div class="form-divider"></div>

            <div class="panel-card__head">
                <div>
                    <p class="eyebrow eyebrow--dark">Security</p>
                    <h2><?= $isEdit ? 'Reset password' : 'Set a password' ?></h2>
                </div>
                <?php if ($isEdit): ?>
                    <span class="badge badge--soft">Optional</span>
                <?php endif; ?>
            </div>

            <?php if ($isEdit): ?>
                <p class="field__help field__help--top">Leave both password fields blank to keep the current password. Setting a new one signs the account out of other sessions.</p>
            <?php endif; ?>

            <div class="form__row">
                <div class="field<?= form_error('password') !== '' ? ' has-error' : '' ?>">
                    <label for="password"><?= $isEdit ? 'New password' : 'Password' ?></label>
                    <div class="password-wrap">
                        <input id="password" name="password" type="password" <?= $isEdit ? '' : 'required' ?> maxlength="255" autocomplete="new-password">
                        <button class="password-toggle" type="button" aria-label="Show password" aria-pressed="false" data-password-toggle="password">
                            <span class="password-toggle__show"><?= icon('eye') ?></span>
                            <span class="password-toggle__hide"><?= icon('eye-off') ?></span>
                        </button>
                    </div>
                    <?php if (form_error('password') !== ''): ?><p class="field__error"><?= e(form_error('password')) ?></p><?php endif; ?>
                </div>

                <div class="field">
                    <label for="password_confirmation">Confirm password</label>
                    <div class="password-wrap">
                        <input id="password_confirmation" name="password_confirmation" type="password" <?= $isEdit ? '' : 'required' ?> maxlength="255" autocomplete="new-password">
                        <button class="password-toggle" type="button" aria-label="Show password" aria-pressed="false" data-password-toggle="password_confirmation">
                            <span class="password-toggle__show"><?= icon('eye') ?></span>
                            <span class="password-toggle__hide"><?= icon('eye-off') ?></span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="form__actions form__actions--split">
                <p class="field__help">12+ characters with upper and lowercase letters, a number and a symbol.</p>
                <button class="btn btn--outline btn--small" type="button" data-generate-password="password">Generate password</button>
            </div>

            <div class="form__divider"></div>

            <div class="form__actions">
                <button class="btn btn--dark" type="submit"><?= $isEdit ? 'Save account' : 'Create account' ?> <?= icon('check') ?></button>
                <a class="btn btn--ghost" href="/admin/members">Cancel</a>
            </div>
        </form>
    </div>

    <aside class="form-layout__side">
        <?php if ($isEdit): ?>
            <section class="panel-card">
                <div class="panel-card__head">
                    <div>
                        <p class="eyebrow eyebrow--dark">Status</p>
                        <h2>Account access</h2>
                    </div>
                </div>

                <div class="member-card member-card--compact">
                    <span class="avatar avatar--lg"><?= e(initials((string) $member['name'])) ?></span>
                    <h3><?= e($member['name']) ?></h3>
                    <p><?= e($member['email']) ?></p>
                    <span class="status-badge <?= (int) $member['is_active'] ? 'status-badge--active' : 'status-badge--inactive' ?>">
                        <?= (int) $member['is_active'] ? 'Active' : 'Inactive' ?>
                    </span>
                </div>

                <dl class="meta-list">
                    <div><dt>Account since</dt><dd><?= e(format_date($member['created_at'] ?? null)) ?></dd></div>
                    <div><dt>Last sign-in</dt><dd><?= e($member['last_login_at'] ? format_datetime($member['last_login_at']) : 'Never') ?></dd></div>
                </dl>

                <?php if ((int) $member['id'] !== (int) $user['id']): ?>
                    <form method="post" action="/admin/members/<?= (int) $member['id'] ?>/status">
                        <?= csrf_field() ?>
                        <button class="btn btn--outline btn--full btn--small" type="submit">
                            <?= icon((int) $member['is_active'] ? 'eye-off' : 'check') ?>
                            <?= (int) $member['is_active'] ? 'Deactivate account' : 'Reactivate account' ?>
                        </button>
                    </form>
                <?php endif; ?>
            </section>
        <?php else: ?>
            <section class="panel-card panel-card--note">
                <span class="panel-note__icon"><?= icon('mail') ?></span>
                <div>
                    <h2>Share credentials securely</h2>
                    <p>After creating the account, share the email address and password with the account holder through a secure channel. Ask them to change it after first sign-in.</p>
                </div>
            </section>

            <section class="panel-card panel-card--note">
                <span class="panel-note__icon"><?= icon('lock') ?></span>
                <div>
                    <h2>Least privilege</h2>
                    <p>Choose <strong>Partner</strong> for most external accounts. Suppliers and customers see role-specific resources. Reserve <strong>Super admin</strong> for administration access only.</p>
                </div>
            </section>
        <?php endif; ?>
    </aside>
</div>
<?php require includes_path('panel-footer.php');
