<?php

declare(strict_types=1);

header('Content-Type: text/plain; charset=UTF-8');

$sitemap = rtrim((string) config('app.url'), '/') . '/sitemap.xml';

echo "User-agent: *\n";
echo "Disallow: /members/\n";
echo "Disallow: /admin/\n";
echo "Disallow: /profile\n";
echo "Disallow: /logout\n";
echo "\n";
echo 'Sitemap: ' . $sitemap . "\n";
