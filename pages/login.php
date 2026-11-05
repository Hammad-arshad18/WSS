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
        redirect('/members/login');
    }

    $status = auth_attempt($email, $password);

    if ($status === 'throttled') {
        flash('error', 'Too many failed attempts. Please wait 15 minutes before trying again.');
        redirect('/members/login');
    }

    if ($status !== 'ok') {
        flash_input(['email' => $email]);
        flash('error', 'The email address or password is incorrect.');
        redirect('/members/login');
    }

    $intended = $_SESSION['intended'] ?? null;
    unset($_SESSION['intended']);

    $user = current_user();
    flash('success', 'Welcome back, ' . ($user['name'] ?? 'partner') . '.');

    if (is_string($intended) && valid_redirect($intended) && $intended !== '/members/login') {
        redirect($intended);
    }

    redirect(is_admin() ? '/admin' : '/members');
}

$title = 'Member sign in';
$metaDescription = 'Sign in to the White Sea Star partner portal.';
$bodyClass = 'page-login';

require includes_path('auth-header.php');
?>
<div class="auth-card__inner">
    <p class="eyebrow eyebrow--dark">Member portal</p>
    <h2 class="auth-title">Welcome <em>back.</em></h2>
    <p class="auth-intro">Sign in to access reports, insights and documents shared with our partners.</p>

    <?php if (form_error('email') !== '' || form_error('password') !== ''): ?>
        <div class="form-alert" role="alert">
            <?= icon('alert') ?>
            <p>We could not sign you in with those details. Please try again.</p>
        </div>
    <?php endif; ?>

    <form class="form form--auth" method="post" action="/members/login" novalidate>
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
        <p>Access is by invitation only. To request an account or reset your password, <a href="/contact">speak with our team</a>.</p>
    </div>

    <div class="auth-security">
        <span><?= icon('shield') ?></span>
        <p>Protected by secure sessions, rate limiting and encrypted password storage.</p>
    </div>
</div>
<?php require includes_path('auth-footer.php');
