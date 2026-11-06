<?php

declare(strict_types=1);

$user = current_user();

if (is_post() && $action === 'update_profile') {
    csrf_verify();

    $name = input_string('name');
    $email = mb_strtolower(input_string('email'));

    $errors = validate(
        ['name' => $name, 'email' => $email],
        ['name' => 'required|min:2|max:120', 'email' => 'required|email|max:190']
    );

    if ($errors === [] && user_email_exists($email, (int) $user['id'])) {
        $errors['email'] = 'That email address is already in use.';
    }

    if ($errors !== []) {
        flash_errors($errors);
        flash_input(['name' => $name, 'email' => $email]);
        flash('error', 'Please review your account details.');
        redirect('/profile');
    }

    user_update((int) $user['id'], $name, $email, (string) $user['role']);
    audit_record((int) $user['id'], 'account.updated', 'Updated own account details');

    flash('success', 'Your account details have been saved.');
    redirect('/profile');
}

if (is_post() && $action === 'update_password') {
    csrf_verify();

    $current = raw_input('current_password');
    $password = raw_input('password');
    $confirmation = raw_input('password_confirmation');

    $errors = validate(
        [
            'current_password' => $current,
            'password' => $password,
            'password_confirmation' => $confirmation,
        ],
        [
            'current_password' => 'required|max:255',
            'password' => 'required|password|max:255|confirmed',
        ]
    );

    if ($errors === [] && !password_verify($current, (string) $user['password_hash'])) {
        $errors['current_password'] = 'Your current password is incorrect.';
    }

    if ($errors !== []) {
        flash_errors($errors);
        flash('error', 'Your password could not be changed.');
        redirect('/profile#password');
    }

    user_update_password((int) $user['id'], hash_password($password));

    // Keep this session valid; sessions issued earlier are now invalid.
    $refreshed = user_find((int) $user['id']);
    $_SESSION['auth_time'] = time();
    $_SESSION['auth_password_at'] = $refreshed['password_changed_at'] ?? null;
    csrf_rotate();

    audit_record((int) $user['id'], 'account.password_changed', 'Changed own password');

    flash('success', 'Your password has been updated.');
    redirect('/profile#password');
}

$title = 'Account settings';
$metaDescription = 'Manage your White Sea Star account.';
$bodyClass = 'page-panel';

require includes_path('panel-header.php');
?>
<div class="panel-head">
    <div>
        <p class="eyebrow eyebrow--dark">Account</p>
        <h1 class="panel-title">Your <em>settings.</em></h1>
        <p class="panel-subtitle">Manage your details and keep your account secure.</p>
    </div>
</div>

<div class="settings-grid">
    <div class="settings-main">
        <section class="panel-card">
            <div class="panel-card__head">
                <div>
                    <p class="eyebrow eyebrow--dark">Profile</p>
                    <h2>Account details</h2>
                </div>
            </div>

            <form class="form" method="post" action="/profile" novalidate>
                <?= csrf_field() ?>
                <div class="field<?= form_error('name') !== '' ? ' has-error' : '' ?>">
                    <label for="name">Full name</label>
                    <input id="name" name="name" type="text" value="<?= e(old('name', $user['name'])) ?>" required maxlength="120" autocomplete="name">
                    <?php if (form_error('name') !== ''): ?><p class="field__error"><?= e(form_error('name')) ?></p><?php endif; ?>
                </div>

                <div class="field<?= form_error('email') !== '' ? ' has-error' : '' ?>">
                    <label for="email">Email address</label>
                    <input id="email" name="email" type="email" value="<?= e(old('email', $user['email'])) ?>" required maxlength="190" autocomplete="email">
                    <?php if (form_error('email') !== ''): ?><p class="field__error"><?= e(form_error('email')) ?></p><?php endif; ?>
                </div>

                <div class="form__actions">
                    <button class="btn btn--dark btn--small" type="submit">Save details <?= icon('check') ?></button>
                </div>
            </form>
        </section>

        <section class="panel-card" id="password">
            <div class="panel-card__head">
                <div>
                    <p class="eyebrow eyebrow--dark">Security</p>
                    <h2>Change password</h2>
                </div>
                <span class="badge badge--soft"><?= icon('lock') ?> Encrypted</span>
            </div>

            <form class="form" method="post" action="/profile/password" novalidate>
                <?= csrf_field() ?>

                <div class="field<?= form_error('current_password') !== '' ? ' has-error' : '' ?>">
                    <label for="current_password">Current password</label>
                    <div class="password-wrap">
                        <input id="current_password" name="current_password" type="password" required maxlength="255" autocomplete="current-password">
                        <button class="password-toggle" type="button" aria-label="Show password" aria-pressed="false" data-password-toggle="current_password">
                            <span class="password-toggle__show"><?= icon('eye') ?></span>
                            <span class="password-toggle__hide"><?= icon('eye-off') ?></span>
                        </button>
                    </div>
                    <?php if (form_error('current_password') !== ''): ?><p class="field__error"><?= e(form_error('current_password')) ?></p><?php endif; ?>
                </div>

                <div class="form__row">
                    <div class="field<?= form_error('password') !== '' ? ' has-error' : '' ?>">
                        <label for="password">New password</label>
                        <div class="password-wrap">
                            <input id="password" name="password" type="password" required maxlength="255" autocomplete="new-password">
                            <button class="password-toggle" type="button" aria-label="Show password" aria-pressed="false" data-password-toggle="password">
                                <span class="password-toggle__show"><?= icon('eye') ?></span>
                                <span class="password-toggle__hide"><?= icon('eye-off') ?></span>
                            </button>
                        </div>
                        <?php if (form_error('password') !== ''): ?><p class="field__error"><?= e(form_error('password')) ?></p><?php endif; ?>
                    </div>

                    <div class="field">
                        <label for="password_confirmation">Confirm new password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required maxlength="255" autocomplete="new-password">
                    </div>
                </div>

                <p class="field__help">At least 12 characters, with upper and lowercase letters, a number and a symbol. Other active sessions will be signed out.</p>

                <div class="form__actions">
                    <button class="btn btn--dark btn--small" type="submit">Update password <?= icon('key') ?></button>
                </div>
            </form>
        </section>
    </div>

    <aside class="settings-side">
        <section class="panel-card">
            <div class="member-card member-card--compact">
                <span class="avatar avatar--lg"><?= e(initials((string) $user['name'])) ?></span>
                <h3><?= e($user['name']) ?></h3>
                <p><?= e($user['email']) ?></p>
                <span class="badge badge--gold"><?= e(role_label((string) $user['role'])) ?></span>
            </div>

            <dl class="meta-list">
                <div>
                    <dt>Account since</dt>
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
        </section>

        <section class="panel-card panel-card--note">
            <span class="panel-note__icon"><?= icon('shield') ?></span>
            <div>
                <h3>Session security</h3>
                <p>For your protection, sessions expire after one hour of inactivity. Changing your password signs out other active sessions.</p>
            </div>
        </section>
    </aside>
</div>
<?php require includes_path('panel-footer.php');
