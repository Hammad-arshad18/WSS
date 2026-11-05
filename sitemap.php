<?php

declare(strict_types=1);

header('Content-Type: application/xml; charset=UTF-8');

$base = rtrim((string) config('app.url'), '/');
$today = gmdate('Y-m-d');

$urls = [
    ['loc' => '/', 'priority' => '1.0', 'changefreq' => 'monthly'],
    ['loc' => '/about', 'priority' => '0.8', 'changefreq' => 'yearly'],
    ['loc' => '/sectors', 'priority' => '0.9', 'changefreq' => 'yearly'],
    ['loc' => '/contact', 'priority' => '0.7', 'changefreq' => 'yearly'],
    ['loc' => '/privacy', 'priority' => '0.3', 'changefreq' => 'yearly'],
];

foreach ((array) content('sectors', []) as $sector) {
    $urls[] = [
        'loc' => '/sectors/' . $sector['slug'],
        'priority' => '0.7',
        'changefreq' => 'yearly',
    ];
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

foreach ($urls as $url) {
    echo "  <url>\n";
    echo '    <loc>' . e($base . $url['loc']) . "</loc>\n";
    echo '    <lastmod>' . e($today) . "</lastmod>\n";
    echo '    <changefreq>' . e($url['changefreq']) . "</changefreq>\n";
    echo '    <priority>' . e($url['priority']) . "</priority>\n";
    echo "  </url>\n";
}

echo '</urlset>' . "\n";
