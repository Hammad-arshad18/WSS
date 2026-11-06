<?php

declare(strict_types=1);

header('Content-Type: application/xml; charset=UTF-8');

$base = rtrim((string) config('app.url'), '/');
$contentModified = @filemtime(includes_path('content.php')) ?: time();

$urls = [
    ['loc' => '/', 'file' => 'pages/home.php', 'priority' => '1.0', 'changefreq' => 'monthly'],
    ['loc' => '/about', 'file' => 'pages/about.php', 'priority' => '0.8', 'changefreq' => 'yearly'],
    ['loc' => '/services', 'file' => 'pages/services.php', 'priority' => '0.9', 'changefreq' => 'monthly'],
    ['loc' => '/contact', 'file' => 'pages/contact.php', 'priority' => '0.7', 'changefreq' => 'yearly'],
    ['loc' => '/privacy', 'file' => 'pages/privacy.php', 'priority' => '0.3', 'changefreq' => 'yearly'],
];

foreach ((array) content('services', []) as $service) {
    $urls[] = [
        'loc' => '/services/' . $service['slug'],
        'file' => 'pages/service.php',
        'priority' => '0.8',
        'changefreq' => 'monthly',
    ];
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

foreach ($urls as $url) {
    $fileModified = @filemtime(base_path($url['file'])) ?: 0;
    $lastModified = max($contentModified, $fileModified);

    echo "  <url>\n";
    echo '    <loc>' . e($base . $url['loc']) . "</loc>\n";
    echo '    <lastmod>' . e(gmdate('Y-m-d', $lastModified)) . "</lastmod>\n";
    echo '    <changefreq>' . e($url['changefreq']) . "</changefreq>\n";
    echo '    <priority>' . e($url['priority']) . "</priority>\n";
    echo "  </url>\n";
}

echo '</urlset>' . "\n";
