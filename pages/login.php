<?php

declare(strict_types=1);

if (is_post()) {
    csrf_verify();

    $email = mb_strtolower(input_string('email'));
    $password = raw_input('password');

    $errors = validate(
        ['email' => $email, 'password' => $password],
        ['email' => 'required|email|max:190', 'password' => 'required|max:255']
    );

    if ($errors !== []) {
        flash_errors($errors);
        flash_input(['email' => $email]);
        flash('error', 'Enter your email address and password.');
        redirect('/login');
    }

    $status = auth_attempt($email, $password);

    if ($status === 'throttled') {
        flash('error', 'Too many failed attempts. Please wait 15 minutes before trying again.');
        redirect('/login');
    }

    if ($status !== 'ok') {
        flash_input(['email' => $email]);
        flash('error', 'The email address or password is incorrect.');
        redirect('/login');
    }

    $intended = $_SESSION['intended'] ?? null;
    unset($_SESSION['intended']);

    $user = current_user();
    flash('success', 'Welcome back, ' . ($user['name'] ?? 'partner') . '.');

    if (is_string($intended) && valid_redirect($intended) && !in_array($intended, ['/login', '/members/login'], true)) {
        redirect($intended);
    }

    redirect(is_admin() ? '/admin' : '/members');
}

$title = 'Partner, supplier & customer sign in';
$metaDescription = 'Sign in to the White Sea Star partner and client portal.';
$bodyClass = 'page-login';

require includes_path('auth-header.php');
?>
<div class="auth-card__inner">
    <p class="eyebrow eyebrow--dark">Partner &amp; client portal</p>
    <h2 class="auth-title">Welcome <em>back.</em></h2>
    <p class="auth-intro">One secure sign-in for partners, suppliers and customers. Use the account provided by your White Sea Star contact.</p>

    <div class="auth-roles" aria-label="Account types">
        <span>Partners</span>
        <span>Suppliers</span>
        <span>Customers</span>
    </div>

    <?php if (form_error('email') !== '' || form_error('password') !== ''): ?>
        <div class="form-alert" role="alert">
            <?= icon('alert') ?>
            <p>We could not sign you in with those details. Please try again.</p>
        </div>
    <?php endif; ?>

    <form class="form form--auth" method="post" action="/login" novalidate>
        <?= csrf_field() ?>

        <div class="field<?= form_error('email') !== '' ? ' has-error' : '' ?>">
            <label for="email">Email address</label>
            <input id="email" name="email" type="email" value="<?= e(old('email')) ?>" required maxlength="190" autocomplete="username" autofocus>
            <?php if (form_error('email') !== ''): ?><p class="field__error"><?= e(form_error('email')) ?></p><?php endif; ?>
        </div>

        <div class="field<?= form_error('password') !== '' ? ' has-error' : '' ?>">
            <label for="password">Password</label>
            <div class="password-wrap">
                <input id="password" name="password" type="password" required maxlength="255" autocomplete="current-password">
                <button class="password-toggle" type="button" aria-label="Show password" aria-pressed="false" data-password-toggle="password">
                    <span class="password-toggle__show"><?= icon('eye') ?></span>
                    <span class="password-toggle__hide"><?= icon('eye-off') ?></span>
                </button>
            </div>
            <?php if (form_error('password') !== ''): ?><p class="field__error"><?= e(form_error('password')) ?></p><?php endif; ?>
        </div>

        <button class="btn btn--dark btn--full" type="submit">Sign in <?= icon('arrow') ?></button>
    </form>

    <div class="auth-note">
        <?= icon('lock') ?>
        <p>Accounts are issued by invitation. Partners, suppliers and customers can request access through <a href="/contact">our team</a>.</p>
    </div>

    <div class="auth-security">
        <span><?= icon('shield') ?></span>
        <p>Protected by secure sessions, rate limiting and encrypted password storage.</p>
    </div>
</div>
<?php require includes_path('auth-footer.php');
