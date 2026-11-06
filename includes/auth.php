<?php

declare(strict_types=1);

/**
 * Authentication, session checks and login throttling.
 */
final class AuthState
{
    public static ?array $user = null;
    public static bool $resolved = false;
}

function auth_attempt(string $email, string $password): string
{
    $email = mb_strtolower(trim($email));
    $ip = client_ip();
    $window = (int) config('security.login_window_minutes', 15);

    $emailFailures = login_attempt_failures_for_email($email, $window);
    $ipFailures = login_attempt_failures_for_ip($ip, $window);

    if (
        $emailFailures >= (int) config('security.max_login_attempts', 5)
        || $ipFailures >= (int) config('security.max_login_attempts_ip', 20)
    ) {
        return 'throttled';
    }

    $user = user_find_by_email($email);

    if ($user === null) {
        dummy_verify($password);
        login_attempt_record($email, $ip, false);

        return 'invalid';
    }

    if (!password_verify($password, (string) $user['password_hash'])) {
        login_attempt_record($email, $ip, false);

        return 'invalid';
    }

    if (!(int) $user['is_active']) {
        login_attempt_record($email, $ip, false);

        return 'invalid';
    }

    if (password_needs_rehash((string) $user['password_hash'], password_algorithm())) {
        user_update_password((int) $user['id'], hash_password($password), false);
    }

    login_attempt_record($email, $ip, true);
    login_attempt_clear_failures($email, $ip);
    auth_login($user);

    return 'ok';
}

function auth_login(array $user): void
{
    session_regenerate_id(true);

    $_SESSION['auth_id'] = (int) $user['id'];
    $_SESSION['auth_time'] = time();
    $_SESSION['auth_password_at'] = $user['password_changed_at'] ?? null;
    $_SESSION['last_activity'] = time();

    csrf_rotate();

    AuthState::$user = $user;
    AuthState::$resolved = true;

    user_touch_login((int) $user['id']);
    audit_record((int) $user['id'], 'auth.login', 'Signed in from ' . client_ip());
}

function auth_logout(): void
{
    $user = current_user();

    if ($user !== null) {
        audit_record((int) $user['id'], 'auth.logout', 'Signed out');
    }

    unset($_SESSION['auth_id'], $_SESSION['auth_time'], $_SESSION['auth_password_at'], $_SESSION['last_activity']);

    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }

    csrf_rotate();

    AuthState::$user = null;
    AuthState::$resolved = true;
}

function auth_check(): bool
{
    return current_user() !== null;
}

function current_user(): ?array
{
    if (AuthState::$resolved) {
        return AuthState::$user;
    }

    AuthState::$resolved = true;

    if (empty($_SESSION['auth_id'])) {
        return AuthState::$user = null;
    }

    $timeout = (int) config('security.session_lifetime', 3600);
    $lastActivity = (int) ($_SESSION['last_activity'] ?? 0);

    if ($lastActivity > 0 && (time() - $lastActivity) > $timeout) {
        auth_forget();
        flash('info', 'Your session expired for security. Please sign in again.');

        return null;
    }

    $user = user_find((int) $_SESSION['auth_id']);

    if ($user === null || !(int) $user['is_active']) {
        auth_forget();

        return null;
    }

    $currentStamp = (string) ($user['password_changed_at'] ?? '');
    $sessionStamp = (string) ($_SESSION['auth_password_at'] ?? '');

    if ($sessionStamp !== $currentStamp) {
        auth_forget();
        flash('info', 'Your password was updated. Please sign in again.');

        return null;
    }

    $_SESSION['last_activity'] = time();

    return AuthState::$user = $user;
}

function auth_id(): ?int
{
    $user = current_user();

    return $user !== null ? (int) $user['id'] : null;
}

function is_admin(): bool
{
    $user = current_user();

    return $user !== null && $user['role'] === 'super_admin';
}

function auth_require_login(): void
{
    if (auth_check()) {
        return;
    }

    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
        $path = auth_relative_path();

        if ($path !== '/login' && $path !== '/members/login') {
            $_SESSION['intended'] = $path;
        }
    }

    flash('info', 'Please sign in to reach the partner portal.');
    redirect('/login');
}

function auth_require_admin(): void
{
    auth_require_login();

    if (!is_admin()) {
        abort(403, 'This area is reserved for the site administrator.');
    }
}

function auth_require_guest(): void
{
    if (auth_check()) {
        redirect(is_admin() ? '/admin' : '/members');
    }
}

function auth_forget(): void
{
    unset($_SESSION['auth_id'], $_SESSION['auth_time'], $_SESSION['auth_password_at'], $_SESSION['last_activity']);

    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }

    AuthState::$user = null;
    AuthState::$resolved = true;
}

function auth_relative_path(): string
{
    $path = request_path();
    $base = (string) config('app.base_path', '');

    if ($base !== '' && str_starts_with($path, $base)) {
        $path = substr($path, strlen($base)) ?: '/';
    }

    return $path;
}
