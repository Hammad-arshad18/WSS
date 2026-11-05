<?php

declare(strict_types=1);

/**
 * Shared bootstrap for every request. It loads configuration, helpers, the
 * database layer and starts a secure PHP session.
 */
if (defined('WSS_BOOTSTRAPPED')) {
    return;
}

define('WSS_BOOTSTRAPPED', true);
define('ROOT_PATH', dirname(__DIR__));
define('REQUEST_ID', bin2hex(random_bytes(6)));

require __DIR__ . '/functions.php';
require __DIR__ . '/icons.php';

$GLOBALS['wss_config'] = require __DIR__ . '/config.php';
$GLOBALS['wss_content'] = require __DIR__ . '/content.php';

require __DIR__ . '/db.php';
require __DIR__ . '/data.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/uploads.php';

date_default_timezone_set((string) config('app.timezone', 'Asia/Dubai'));

error_reporting(E_ALL);
ini_set('display_errors', config('app.debug', false) ? '1' : '0');
ini_set('log_errors', '1');

set_exception_handler(static function (Throwable $e): void {
    if (PHP_SAPI === 'cli') {
        fwrite(STDERR, '[' . REQUEST_ID . '] ' . $e->getMessage() . PHP_EOL);
        fwrite(STDERR, $e->getTraceAsString() . PHP_EOL);
        exit(1);
    }

    $status = $e instanceof HttpError ? $e->status : 500;

    if ($status >= 500) {
        error_log(sprintf(
            "[%s] Unhandled error: %s in %s:%d\n%s\n",
            REQUEST_ID,
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        ));
    } elseif (in_array($status, [403, 419, 429], true)) {
        error_log(sprintf('[%s] HTTP %d from %s: %s', REQUEST_ID, $status, client_ip(), $e->getMessage()));
    }

    if (headers_sent()) {
        echo '<p style="font-family:sans-serif;padding:2rem">An unexpected error occurred. Reference: ' . e(REQUEST_ID) . '</p>';

        return;
    }

    $message = $e instanceof HttpError ? $e->getMessage() : null;

    if ($status >= 500 && config('app.debug', false)) {
        $message = $e->getMessage() . ' (' . basename($e->getFile()) . ':' . $e->getLine() . ')';
    }

    render_error($status, $message);
});

if (!headers_sent()) {
    header_remove('X-Powered-By');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=()');
    header('Cross-Origin-Opener-Policy: same-origin');
    header('Cross-Origin-Resource-Policy: same-origin');
    header('X-Permitted-Cross-Domain-Policies: none');
    header('X-Request-Id: ' . REQUEST_ID);

    if (is_https()) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }

    $nonce = csp_nonce();
    $csp = [
        "default-src 'self'",
        "base-uri 'self'",
        "form-action 'self'",
        "frame-ancestors 'none'",
        "object-src 'none'",
        "img-src 'self' data:",
        "font-src 'self' https://fonts.gstatic.com",
        "style-src 'self' https://fonts.googleapis.com",
        "script-src 'self' 'nonce-" . $nonce . "'",
        "connect-src 'self'",
        "manifest-src 'self'",
    ];

    if (is_https()) {
        $csp[] = 'upgrade-insecure-requests';
    }

    header('Content-Security-Policy: ' . implode('; ', $csp));
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');

    session_name((string) config('security.session_name', 'wss_session'));
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => is_https(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}
