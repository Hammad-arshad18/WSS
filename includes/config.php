<?php

declare(strict_types=1);

/**
 * Configuration for the White Sea Star website.
 *
 * The defaults below are the production values for one.com. Environment
 * variables prefixed with WSS_ can override them for local development.
 */
$env = static function (string $key, string $default): string {
    $value = getenv($key);

    return $value === false ? $default : $value;
};

$appUrl = rtrim($env('WSS_URL', 'https://www.whiteseastar.com'), '/');

return [
    'app' => [
        'name' => 'White Sea Star General Trading L.L.C',
        'short_name' => 'White Sea Star',
        'url' => $appUrl,
        'base_path' => rtrim($env('WSS_BASE_PATH', ''), '/'),
        'timezone' => 'Asia/Dubai',
        'asset_version' => $env('WSS_ASSET_VERSION', '1.5.0'),
        'debug' => filter_var($env('WSS_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN),
        'trust_proxy' => filter_var($env('WSS_TRUST_PROXY', 'false'), FILTER_VALIDATE_BOOLEAN),
    ],

    'db' => [
        'host' => $env('WSS_DB_HOST', 'whiteseastar.com.mysql'),
        'port' => (int) $env('WSS_DB_PORT', '3306'),
        'database' => $env('WSS_DB_NAME', 'whiteseastar_comwsssite'),
        'username' => $env('WSS_DB_USER', 'whiteseastar_comwsssite'),
        'password' => $env('WSS_DB_PASS', 'Developer18'),
        'charset' => 'utf8mb4',
    ],

    'seo' => [
        'google_verification' => $env('WSS_GOOGLE_VERIFICATION', ''),
        'bing_verification' => $env('WSS_BING_VERIFICATION', ''),
        'default_image' => '/assets/img/og.jpg',
    ],

    'security' => [
        'session_name' => 'wss_session',
        'session_lifetime' => 60 * 60,
        'max_login_attempts' => 5,
        'max_login_attempts_ip' => 20,
        'login_window_minutes' => 15,
        'contact_window_hours' => 1,
        'contact_max_per_ip' => 4,
        'upload_max_bytes' => 10 * 1024 * 1024,
        'allowed_uploads' => [
            'application/pdf' => 'pdf',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        ],
    ],
];
