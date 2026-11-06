<?php

declare(strict_types=1);

/**
 * Small shared helpers. This file deliberately contains no database code so it
 * can be loaded safely during bootstrap and from error handling.
 */

function config(?string $key = null, mixed $default = null): mixed
{
    $config = $GLOBALS['wss_config'] ?? [];

    if ($key === null) {
        return $config;
    }

    $value = $config;

    foreach (explode('.', $key) as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }

        $value = $value[$segment];
    }

    return $value;
}

function content(string $key, mixed $default = null): mixed
{
    $content = $GLOBALS['wss_content'] ?? [];

    return array_key_exists($key, $content) ? $content[$key] : $default;
}

/**
 * Human labels for the account roles used by the single sign-in.
 *
 * @return array<string, string>
 */
function account_role_labels(): array
{
    return [
        'super_admin' => 'Super admin',
        'partner' => 'Partner',
        'supplier' => 'Supplier',
        'customer' => 'Customer',
    ];
}

function role_label(string $role): string
{
    return account_role_labels()[$role] ?? 'Partner';
}

/**
 * Categories a non-admin role may see in the resource library.
 * A null result means no restriction (partners and administrators).
 *
 * @return list<string>|null
 */
function role_resource_categories(string $role): ?array
{
    return match ($role) {
        'supplier' => ['Company Documents', 'Supplier Information', 'Compliance', 'Partner Notices'],
        'customer' => ['Company Documents', 'Service Sheets', 'Compliance'],
        default => null,
    };
}

function base_path(string $path = ''): string
{
    return ROOT_PATH . ($path !== '' ? '/' . ltrim($path, '/') : '');
}

function includes_path(string $path = ''): string
{
    return base_path('includes' . ($path !== '' ? '/' . ltrim($path, '/') : ''));
}

function pages_path(string $path = ''): string
{
    return base_path('pages' . ($path !== '' ? '/' . ltrim($path, '/') : ''));
}

function storage_path(string $path = ''): string
{
    return base_path('storage' . ($path !== '' ? '/' . ltrim($path, '/') : ''));
}

function url(string $path = '/'): string
{
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }

    $base = (string) config('app.base_path', '');
    $path = '/' . ltrim($path, '/');

    if ($path === '/') {
        return $base !== '' ? $base . '/' : '/';
    }

    return $base . $path;
}

/**
 * Build an absolute URL for canonical, Open Graph and structured-data values.
 * Search engines require absolute URLs in these places.
 */
function absolute_url(string $path = '/'): string
{
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }

    return rtrim((string) config('app.url', ''), '/') . url($path);
}

/**
 * Send the bare production host to the configured canonical host.
 *
 * https://whiteseastar.com and https://www.whiteseastar.com both reach this
 * app. Canonical, Open Graph and sitemap URLs use the www host, so the apex
 * host must 301 there before any HTML is sent. Local and preview hosts are
 * left alone.
 */
function enforce_canonical_host(): void
{
    if (PHP_SAPI === 'cli' || headers_sent()) {
        return;
    }

    $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));

    if ($method !== 'GET' && $method !== 'HEAD') {
        return;
    }

    $canonical = rtrim((string) config('app.url', ''), '/');
    $canonicalHost = parse_url($canonical, PHP_URL_HOST);

    if (!is_string($canonicalHost) || $canonicalHost === '') {
        return;
    }

    $requestHost = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
    $requestHost = preg_replace('/:\d+$/', '', $requestHost) ?? $requestHost;

    if ($requestHost === '' || strcasecmp($requestHost, $canonicalHost) === 0) {
        return;
    }

    $bareHost = strtolower((string) preg_replace('/^www\./i', '', $canonicalHost));

    if ($requestHost !== $bareHost) {
        return;
    }

    $uri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
    $path = parse_url($uri, PHP_URL_PATH);
    $path = is_string($path) && str_starts_with($path, '/') && !str_starts_with($path, '//') ? $path : '/';
    $query = parse_url($uri, PHP_URL_QUERY);
    $target = $canonical . $path . (is_string($query) && $query !== '' ? '?' . $query : '');

    header_remove('X-Powered-By');
    header('Location: ' . $target, true, 301);
    header('Cache-Control: public, max-age=86400');
    exit;
}

function asset(string $path): string
{
    $path = ltrim($path, '/');
    $version = (string) config('app.asset_version', '1');

    return url('/assets/' . $path) . '?v=' . rawurlencode($version);
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf']) || !is_string($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
}

function csrf_rotate(): void
{
    $_SESSION['_csrf'] = bin2hex(random_bytes(32));
}

function csrf_verify(): void
{
    $stored = $_SESSION['_csrf'] ?? '';
    $sent = $_POST['_token'] ?? null;

    if (!is_string($stored) || $stored === '' || !is_string($sent) || !hash_equals($stored, $sent)) {
        abort(419, 'Your session has expired. Please refresh the page and try again.');
    }
}

function flash(string $type, string $message): void
{
    $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
}

function flash_messages(): array
{
    $messages = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);

    return is_array($messages) ? $messages : [];
}

function flash_input(array $input): void
{
    unset($input['password'], $input['password_confirmation'], $input['current_password'], $input['_token']);
    $_SESSION['_old'] = $input;
}

function old(string $key, mixed $default = ''): mixed
{
    $value = $_SESSION['_old'][$key] ?? $default;

    return is_string($value) ? $value : $default;
}

function flash_errors(array $errors): void
{
    $_SESSION['_errors'] = $errors;
}

function form_errors(): array
{
    $errors = $_SESSION['_errors'] ?? [];

    return is_array($errors) ? $errors : [];
}

function form_error(string $field): string
{
    return (string) (form_errors()[$field] ?? '');
}

function clear_form_state(): void
{
    unset($_SESSION['_old'], $_SESSION['_errors']);
}

function redirect(string $path, int $status = 302): void
{
    if (!headers_sent()) {
        header('Location: ' . (preg_match('#^https?://#i', $path) ? $path : url($path)), true, $status);
    }

    exit;
}

function input_string(string $key, string $default = ''): string
{
    $value = $_POST[$key] ?? $_GET[$key] ?? $default;

    if (!is_string($value)) {
        return $default;
    }

    $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value) ?? '';
    $value = trim(str_replace(["\r\n", "\r"], "\n", $value));

    return $value;
}

function raw_input(string $key, string $default = ''): string
{
    $value = $_POST[$key] ?? $default;

    return is_string($value) ? $value : $default;
}

function selected(mixed $value, mixed $expected): string
{
    return (string) $value === (string) $expected ? ' selected' : '';
}

function checked(bool $condition): string
{
    return $condition ? ' checked' : '';
}

function format_date(?string $datetime, string $format = 'j M Y'): string
{
    if ($datetime === null || $datetime === '') {
        return '—';
    }

    try {
        $date = new DateTimeImmutable($datetime, new DateTimeZone('UTC'));

        return $date->setTimezone(new DateTimeZone((string) config('app.timezone', 'Asia/Dubai')))->format($format);
    } catch (Throwable) {
        return '—';
    }
}

function format_datetime(?string $datetime): string
{
    return format_date($datetime, 'j M Y, H:i') . ' GST';
}

function time_ago(?string $datetime): string
{
    if ($datetime === null || $datetime === '') {
        return '—';
    }

    try {
        $then = new DateTimeImmutable($datetime, new DateTimeZone('UTC'));
        $diff = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->getTimestamp() - $then->getTimestamp();
    } catch (Throwable) {
        return '—';
    }

    if ($diff < 60) {
        return 'just now';
    }

    $units = [
        31536000 => 'year',
        2592000 => 'month',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
    ];

    foreach ($units as $seconds => $label) {
        if ($diff >= $seconds) {
            $count = (int) floor($diff / $seconds);

            return $count . ' ' . $label . ($count === 1 ? '' : 's') . ' ago';
        }
    }

    return 'just now';
}

function client_ip(): string
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

    if (config('app.trust_proxy', false) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $forwarded = explode(',', (string) $_SERVER['HTTP_X_FORWARDED_FOR']);
        $candidate = trim($forwarded[0]);

        if (filter_var($candidate, FILTER_VALIDATE_IP)) {
            $ip = $candidate;
        }
    }

    return filter_var($ip, FILTER_VALIDATE_IP) ? (string) $ip : '0.0.0.0';
}

function user_agent(): string
{
    return mb_substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255);
}

function request_path(): string
{
    $uri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
    $path = parse_url($uri, PHP_URL_PATH);
    $path = is_string($path) ? rawurldecode($path) : '/';

    if ($path === '' || $path[0] !== '/') {
        $path = '/' . $path;
    }

    return $path;
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function is_https(): bool
{
    if (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') {
        return true;
    }

    return str_starts_with((string) config('app.url', ''), 'https://');
}

function csp_nonce(): string
{
    static $nonce = null;

    if ($nonce === null) {
        $nonce = rtrim(strtr(base64_encode(random_bytes(18)), '+/', '-_'), '=');
    }

    return $nonce;
}

function nav_active(string $prefix, bool $exact = false): string
{
    $current = request_path();
    $base = (string) config('app.base_path', '');

    if ($base !== '' && str_starts_with($current, $base)) {
        $current = substr($current, strlen($base)) ?: '/';
    }

    if ($exact) {
        return $current === $prefix ? ' is-active' : '';
    }

    return ($current === $prefix || str_starts_with($current, rtrim($prefix, '/') . '/')) ? ' is-active' : '';
}

function initials(string $name): string
{
    $parts = preg_split('/\s+/', trim($name)) ?: [];
    $initials = '';

    foreach (array_slice($parts, 0, 2) as $part) {
        $initials .= mb_strtoupper(mb_substr($part, 0, 1));
    }

    return $initials !== '' ? $initials : 'W';
}

function human_filesize(int|string|null $bytes): string
{
    $size = (int) $bytes;

    if ($size < 1024) {
        return $size . ' B';
    }

    $units = ['KB', 'MB', 'GB'];
    $size /= 1024;

    foreach ($units as $unit) {
        if ($size < 1024 || $unit === 'GB') {
            return number_format($size, $size >= 10 ? 0 : 1) . ' ' . $unit;
        }

        $size /= 1024;
    }

    return '0 B';
}

function file_kind(?string $mime): string
{
    return match ($mime) {
        'application/pdf' => 'pdf',
        'image/jpeg', 'image/png' => 'image',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'doc',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'sheet',
        default => 'file',
    };
}

function excerpt(string $text, int $limit = 130): string
{
    $text = trim(preg_replace('/\s+/', ' ', $text) ?? '');

    if (mb_strlen($text) <= $limit) {
        return $text;
    }

    return rtrim(mb_substr($text, 0, $limit - 1)) . '…';
}

function json_for_script(mixed $data): string
{
    return json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES) ?: '{}';
}

/**
 * @return array<string, string>
 */
function validate(array $data, array $rules): array
{
    $errors = [];

    foreach ($rules as $field => $ruleSet) {
        $value = isset($data[$field]) && is_scalar($data[$field]) ? trim((string) $data[$field]) : '';

        foreach (explode('|', $ruleSet) as $rule) {
            if ($value === '' && $rule !== 'required' && !str_starts_with($rule, 'confirmed')) {
                continue;
            }

            [$name, $argument] = array_pad(explode(':', $rule, 2), 2, null);

            $error = match ($name) {
                'required' => $value === '' ? 'This field is required.' : null,
                'email' => filter_var($value, FILTER_VALIDATE_EMAIL) === false ? 'Enter a valid email address.' : null,
                'max' => mb_strlen($value) > (int) $argument ? 'Use no more than ' . (int) $argument . ' characters.' : null,
                'min' => mb_strlen($value) < (int) $argument ? 'Use at least ' . (int) $argument . ' characters.' : null,
                'in' => !in_array($value, explode(',', (string) $argument), true) ? 'Choose a valid option.' : null,
                'confirmed' => !isset($data[$field . '_confirmation'])
                    || !is_string($data[$field . '_confirmation'])
                    || !hash_equals($value, $data[$field . '_confirmation']) ? 'The confirmation does not match.' : null,
                'password' => password_rule_error($value),
                default => null,
            };

            if ($error !== null) {
                $errors[$field] = $error;
                break;
            }
        }
    }

    return $errors;
}

function password_algorithm(): string
{
    return defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT;
}

function hash_password(string $password): string
{
    return password_hash($password, password_algorithm());
}

function dummy_verify(string $password): void
{
    password_verify($password, '$argon2id$v=19$m=65536,t=4,p=1$ajFQUFUwc1h0aFR5RVJ0Sg$Bnro5o3/lu+5Z4YqUtFN0zIkHUmVX/LoDK/Yff/sUS4');
}

/**
 * @return list<string>
 */
function password_errors(string $password): array
{
    $errors = [];

    if (mb_strlen($password) < 12) {
        $errors[] = 'Use at least 12 characters.';
    }
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'Include a lowercase letter.';
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Include an uppercase letter.';
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'Include a number.';
    }
    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        $errors[] = 'Include a symbol.';
    }

    return $errors;
}

function password_rule_error(string $password): ?string
{
    return password_errors($password)[0] ?? null;
}

function random_password(int $length = 18): string
{
    $lower = 'abcdefghjkmnpqrstuvwxyz';
    $upper = 'ABCDEFGHJKMNPQRSTUVWXYZ';
    $digits = '23456789';
    $symbols = '!@#$%&*+-?';
    $all = $lower . $upper . $digits . $symbols;

    $password = [
        $lower[random_int(0, strlen($lower) - 1)],
        $upper[random_int(0, strlen($upper) - 1)],
        $digits[random_int(0, strlen($digits) - 1)],
        $symbols[random_int(0, strlen($symbols) - 1)],
    ];

    while (count($password) < $length) {
        $password[] = $all[random_int(0, strlen($all) - 1)];
    }

    for ($i = count($password) - 1; $i > 0; $i--) {
        $j = random_int(0, $i);
        [$password[$i], $password[$j]] = [$password[$j], $password[$i]];
    }

    return implode('', $password);
}

function safe_filename(string $name): string
{
    $name = basename(str_replace('\\', '/', $name));
    $name = preg_replace('/[\x00-\x1F\x7F]/u', '', $name) ?? 'document';
    $name = trim(preg_replace('/\s+/', ' ', $name) ?? 'document');

    if ($name === '' || $name === '.' || $name === '..') {
        $name = 'document';
    }

    return mb_substr($name, 0, 180);
}

function valid_redirect(string $path): bool
{
    return $path !== ''
        && $path[0] === '/'
        && !str_starts_with($path, '//')
        && !str_contains($path, "\n")
        && !str_contains($path, "\r");
}

class HttpError extends RuntimeException
{
    public function __construct(public readonly int $status, string $message = '')
    {
        parent::__construct($message);
    }
}

function render_error(int $status, ?string $message = null): void
{
    if (!headers_sent()) {
        http_response_code($status);
        header('Content-Type: text/html; charset=UTF-8');
    }

    $titles = [
        400 => 'Bad request',
        403 => 'Access restricted',
        404 => 'Page not found',
        405 => 'Method not allowed',
        419 => 'Session expired',
        429 => 'Too many requests',
        500 => 'Something went wrong',
    ];

    $defaultMessages = [
        403 => 'You do not have permission to view this page.',
        404 => 'The page you are looking for may have moved or no longer exists.',
        405 => 'That request method is not supported here.',
        419 => 'For your security, please refresh the page and try again.',
        500 => 'We are looking into it. Please try again in a moment.',
    ];

    $errorStatus = $status;
    $errorTitle = $titles[$status] ?? 'An error occurred';
    $errorMessage = $message ?: ($defaultMessages[$status] ?? 'Please try again.');
    $requestId = defined('REQUEST_ID') ? REQUEST_ID : 'unknown';

    require includes_path('error.php');

    clear_form_state();
    exit;
}

function abort(int $status, ?string $message = null): void
{
    render_error($status, $message);
}
