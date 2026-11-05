<?php

declare(strict_types=1);

/**
 * Route map for the whole site.
 *
 * Each entry is: [HTTP method, URL pattern, page file, access level, action].
 * Access levels: public, guest (signed-out only), auth, admin.
 * `{name}` segments are captured into $params.
 */
return [
    // Public website
    ['GET',  '/', 'home.php', 'public'],
    ['GET',  '/about', 'about.php', 'public'],
    ['GET',  '/sectors', 'sectors.php', 'public'],
    ['GET',  '/sectors/{slug}', 'sector.php', 'public'],
    ['GET',  '/expertise', 'expertise.php', 'public'],
    ['GET',  '/expertise/{slug}', 'expertise-detail.php', 'public'],
    ['GET',  '/contact', 'contact.php', 'public'],
    ['POST', '/contact', 'contact.php', 'public'],
    ['GET',  '/privacy', 'privacy.php', 'public'],
    ['GET',  '/robots.txt', 'robots.php', 'public'],
    ['GET',  '/sitemap.xml', 'sitemap.php', 'public'],

    // Sign in / sign out
    ['GET',  '/members/login', 'login.php', 'guest'],
    ['POST', '/members/login', 'login.php', 'guest'],
    ['POST', '/logout', 'logout.php', 'auth'],

    // Member area
    ['GET',  '/members', 'member-dashboard.php', 'auth'],
    ['GET',  '/members/documents', 'member-documents.php', 'auth'],
    ['GET',  '/members/documents/{id}/download', 'download.php', 'auth'],
    ['GET',  '/profile', 'profile.php', 'auth'],
    ['POST', '/profile', 'profile.php', 'auth', 'update_profile'],
    ['POST', '/profile/password', 'profile.php', 'auth', 'update_password'],

    // Administration
    ['GET',  '/admin', 'admin-dashboard.php', 'admin'],

    ['GET',  '/admin/members', 'admin-members.php', 'admin'],
    ['GET',  '/admin/members/create', 'admin-member-form.php', 'admin'],
    ['POST', '/admin/members', 'admin-member-form.php', 'admin', 'store_member'],
    ['GET',  '/admin/members/{id}/edit', 'admin-member-form.php', 'admin'],
    ['POST', '/admin/members/{id}', 'admin-member-form.php', 'admin', 'update_member'],
    ['POST', '/admin/members/{id}/status', 'admin-member-status.php', 'admin'],
    ['POST', '/admin/members/{id}/delete', 'admin-member-delete.php', 'admin'],

    ['GET',  '/admin/documents', 'admin-documents.php', 'admin'],
    ['GET',  '/admin/documents/create', 'admin-document-form.php', 'admin'],
    ['POST', '/admin/documents', 'admin-document-form.php', 'admin', 'store_document'],
    ['GET',  '/admin/documents/{id}/edit', 'admin-document-form.php', 'admin'],
    ['POST', '/admin/documents/{id}', 'admin-document-form.php', 'admin', 'update_document'],
    ['POST', '/admin/documents/{id}/publish', 'admin-document-publish.php', 'admin'],
    ['POST', '/admin/documents/{id}/delete', 'admin-document-delete.php', 'admin'],

    ['GET',  '/admin/enquiries', 'admin-enquiries.php', 'admin'],
    ['POST', '/admin/enquiries/{id}/handled', 'admin-enquiry-handled.php', 'admin'],

    ['GET',  '/admin/audit', 'admin-audit.php', 'admin'],
];

/**
 * Match a request against the route table.
 *
 * @return array{page?:string,access?:string,action?:?string,params?:array<string,string>,error?:int,allow?:list<string>}
 */
function wss_find_route(array $routes, string $method, string $path): array
{
    $method = strtoupper($method);
    $path = wss_normalize_path($path);
    $allowed = [];

    foreach ($routes as $route) {
        [$routeMethod, $pattern, $page, $access] = $route;

        $params = wss_match_pattern($pattern, $path);

        if ($params === null) {
            continue;
        }

        if ($routeMethod !== $method) {
            $allowed[] = $routeMethod;
            continue;
        }

        return [
            'page' => $page,
            'access' => $access,
            'action' => $route[4] ?? null,
            'params' => $params,
        ];
    }

    if ($allowed !== []) {
        return ['error' => 405, 'allow' => array_values(array_unique($allowed))];
    }

    return ['error' => 404];
}

function wss_normalize_path(string $path): string
{
    $path = '/' . trim($path, '/');

    return $path === '/' ? '/' : rtrim($path, '/');
}

/**
 * @return array<string,string>|null Captured parameters, or null when there is no match.
 */
function wss_match_pattern(string $pattern, string $path): ?array
{
    $pattern = wss_normalize_path($pattern);

    if ($pattern === $path) {
        return [];
    }

    if (!str_contains($pattern, '{')) {
        return null;
    }

    $regex = '#^' . preg_replace_callback(
        '/\{([a-z_]+)\}/',
        static fn (array $match): string => '(?P<' . $match[1] . '>[^/]+)',
        $pattern
    ) . '$#';

    if (!preg_match($regex, $path, $matches)) {
        return null;
    }

    $params = [];

    foreach ($matches as $key => $value) {
        if (is_string($key)) {
            $params[$key] = (string) $value;
        }
    }

    return $params;
}
