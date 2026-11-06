<?php

declare(strict_types=1);

/**
 * Front controller. Every request is routed through this file by .htaccess.
 */

// The PHP built-in server serves real static files directly. PHP files are
// never served this way; they always pass through the router below.
if (PHP_SAPI === 'cli-server') {
    $requestPath = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
    $requestPath = is_string($requestPath) ? rawurldecode($requestPath) : '/';
    $root = realpath(__DIR__);

    if ($requestPath !== '/' && $requestPath !== '/index.php') {
        $file = realpath(__DIR__ . $requestPath);

        if (
            $root !== false
            && $file !== false
            && str_starts_with($file, $root . DIRECTORY_SEPARATOR)
            && is_file($file)
            && !str_starts_with(basename($file), '.')
            && strtolower((string) pathinfo($file, PATHINFO_EXTENSION)) !== 'php'
        ) {
            return false;
        }
    }
}

require __DIR__ . '/includes/bootstrap.php';

// The site moved from /sectors and /expertise to /services. Keep old links
// working with a permanent redirect.
$legacyPath = rtrim(request_path(), '/') ?: '/';

if (
    $legacyPath === '/sectors'
    || str_starts_with($legacyPath, '/sectors/')
    || $legacyPath === '/expertise'
    || str_starts_with($legacyPath, '/expertise/')
) {
    redirect('/services', 301);
}

$routes = require __DIR__ . '/router.php';
$match = wss_find_route(
    $routes,
    (string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'),
    request_path()
);

if (isset($match['error'])) {
    if ($match['error'] === 405) {
        header('Allow: ' . implode(', ', (array) ($match['allow'] ?? [])));
        abort(405);
    }

    abort(404);
}

switch ($match['access']) {
    case 'guest':
        auth_require_guest();
        break;
    case 'auth':
        auth_require_login();
        break;
    case 'admin':
        auth_require_admin();
        break;
}

$params = (array) $match['params'];
$action = $match['action'];

// Pages normally live in /pages. The two machine-readable files live at the
// site root so they are easy to find in the project structure.
$pageFile = pages_path($match['page']);

if (!is_file($pageFile)) {
    $pageFile = base_path($match['page']);
}

require $pageFile;
