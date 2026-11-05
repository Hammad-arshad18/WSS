<?php

/**
 * Shared <head> contents.
 *
 * Expects $title, $metaDescription and $bodyClass to be set by the page.
 * Optional: $pageType, $breadcrumbs, $pageSchema, $ogImage, $ogImageAlt,
 * $lastModified and $noindex.
 */
$company = content('company');
$fullTitle = str_contains((string) $title, 'White Sea Star')
    ? (string) $title
    : $title . ' · White Sea Star';
$description = $metaDescription !== ''
    ? $metaDescription
    : 'White Sea Star General Trading L.L.C is a Dubai-based strategy practice established in 2009, helping partners with market entry, growth and execution.';
$noindex = $noindex ?? (str_contains($bodyClass, 'page-panel') || str_contains($bodyClass, 'page-login'));
$pageType = $pageType ?? 'website';
$breadcrumbs = $breadcrumbs ?? [];
$pageSchema = $pageSchema ?? [];
$ogImagePath = $ogImage ?? (string) config('seo.default_image', '/assets/img/og.jpg');
$ogImage = absolute_url($ogImagePath);
$ogImageAlt = $ogImageAlt ?? 'White Sea Star General Trading L.L.C — strategy practice in Dubai';
$lastModified = $lastModified ?? (@filemtime(includes_path('content.php')) ?: time());
$basePath = (string) config('app.base_path', '');
$canonicalPath = request_path();

if ($basePath !== '' && str_starts_with($canonicalPath, $basePath)) {
    $canonicalPath = substr($canonicalPath, strlen($basePath)) ?: '/';
}

$canonicalPath = rtrim($canonicalPath, '/') ?: '/';
$canonical = absolute_url($canonicalPath);
$nonce = csp_nonce();
$googleVerification = trim((string) config('seo.google_verification', ''));
$bingVerification = trim((string) config('seo.bing_verification', ''));

$organizationId = absolute_url('/') . '#organization';
$websiteId = absolute_url('/') . '#website';
$webpageId = $canonical . '#webpage';
$breadcrumbId = $canonical . '#breadcrumb';

$services = (array) content('services', []);
$serviceNames = array_map(static fn (array $service): string => $service['name'], $services);

$schemaGraph = [
    [
        '@type' => ['Organization', 'ProfessionalService'],
        '@id' => $organizationId,
        'name' => $company['name'],
        'alternateName' => $company['short_name'],
        'url' => absolute_url('/'),
        'logo' => [
            '@type' => 'ImageObject',
            'url' => absolute_url('/assets/img/logo.png'),
            'width' => 520,
            'height' => 475,
            'caption' => $company['name'],
        ],
        'image' => $ogImage,
        'description' => 'White Sea Star General Trading L.L.C is a Dubai-based strategy practice working across market entry, growth, commercial advisory, partnerships and operating models.',
        'slogan' => $company['tagline'],
        'foundingDate' => '2009-06-16',
        'email' => $company['email'],
        'telephone' => $company['phone'],
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => 'Al Reem Tower, Office 1404, Al Maktoum Road, Riggat Al Buteen, Deira',
            'addressLocality' => 'Dubai',
            'addressRegion' => 'Dubai',
            'addressCountry' => 'AE',
        ],
        'hasMap' => $company['maps_url'],
        'openingHoursSpecification' => [
            [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'],
                'opens' => '09:00',
                'closes' => '18:00',
            ],
        ],
        'areaServed' => [
            ['@type' => 'Country', 'name' => 'United Arab Emirates'],
            ['@type' => 'Place', 'name' => 'GCC Region'],
            ['@type' => 'Place', 'name' => 'Africa'],
            ['@type' => 'Place', 'name' => 'South Asia'],
        ],
        'knowsAbout' => array_values(array_unique(array_merge(
            ['Strategy consulting', 'Market entry strategy', 'Business growth strategy', 'Commercial advisory'],
            $serviceNames
        ))),
        'contactPoint' => [
            [
                '@type' => 'ContactPoint',
                'contactType' => 'business enquiries',
                'telephone' => $company['phone'],
                'email' => $company['email'],
                'areaServed' => 'AE',
                'availableLanguage' => ['English'],
            ],
        ],
    ],
    [
        '@type' => 'WebSite',
        '@id' => $websiteId,
        'url' => absolute_url('/'),
        'name' => $company['short_name'],
        'alternateName' => $company['name'],
        'publisher' => ['@id' => $organizationId],
        'inLanguage' => 'en',
    ],
];

$webPage = [
    '@type' => 'WebPage',
    '@id' => $webpageId,
    'url' => $canonical,
    'name' => $fullTitle,
    'description' => $description,
    'isPartOf' => ['@id' => $websiteId],
    'about' => ['@id' => $organizationId],
    'inLanguage' => 'en',
    'primaryImageOfPage' => [
        '@type' => 'ImageObject',
        'url' => $ogImage,
        'width' => 1200,
        'height' => 630,
        'caption' => $ogImageAlt,
    ],
    'dateModified' => gmdate('c', $lastModified),
];

$crumbItems = [['name' => 'Home', 'path' => '/']];

foreach ($breadcrumbs as $crumb) {
    if (!is_array($crumb) || !isset($crumb['name'])) {
        continue;
    }

    $crumbItems[] = [
        'name' => (string) $crumb['name'],
        'path' => (string) ($crumb['path'] ?? $canonicalPath),
    ];
}

if (count($crumbItems) > 1) {
    $listItems = [];

    foreach ($crumbItems as $position => $crumb) {
        $listItems[] = [
            '@type' => 'ListItem',
            'position' => $position + 1,
            'name' => $crumb['name'],
            'item' => absolute_url($crumb['path']),
        ];
    }

    $schemaGraph[] = [
        '@type' => 'BreadcrumbList',
        '@id' => $breadcrumbId,
        'itemListElement' => $listItems,
    ];

    $webPage['breadcrumb'] = ['@id' => $breadcrumbId];
}

$schemaGraph[] = $webPage;

foreach ($pageSchema as $node) {
    if (is_array($node) && $node !== []) {
        $schemaGraph[] = $node;
    }
}
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($fullTitle) ?></title>
<meta name="description" content="<?= e($description) ?>">
<meta name="author" content="<?= e($company['name']) ?>">
<meta name="geo.region" content="AE-DU">
<meta name="geo.placename" content="Dubai">
<?php if ($googleVerification !== ''): ?>
<meta name="google-site-verification" content="<?= e($googleVerification) ?>">
<?php endif; ?>
<?php if ($bingVerification !== ''): ?>
<meta name="msvalidate.01" content="<?= e($bingVerification) ?>">
<?php endif; ?>
<?php if ($noindex): ?>
<meta name="robots" content="noindex, nofollow, noarchive">
<?php else: ?>
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<link rel="canonical" href="<?= e($canonical) ?>">
<link rel="alternate" hreflang="en" href="<?= e($canonical) ?>">
<link rel="alternate" hreflang="x-default" href="<?= e($canonical) ?>">
<link rel="sitemap" type="application/xml" title="Sitemap" href="<?= e(absolute_url('/sitemap.xml')) ?>">
<meta property="og:locale" content="en_AE">
<meta property="og:type" content="<?= e($pageType) ?>">
<meta property="og:site_name" content="White Sea Star General Trading L.L.C">
<meta property="og:title" content="<?= e($fullTitle) ?>">
<meta property="og:description" content="<?= e($description) ?>">
<meta property="og:image" content="<?= e($ogImage) ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="<?= e($ogImageAlt) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e($fullTitle) ?>">
<meta name="twitter:description" content="<?= e($description) ?>">
<meta name="twitter:image" content="<?= e($ogImage) ?>">
<meta name="twitter:image:alt" content="<?= e($ogImageAlt) ?>">
<script type="application/ld+json" nonce="<?= e($nonce) ?>"><?= json_for_script([
    '@context' => 'https://schema.org',
    '@graph' => $schemaGraph,
]) ?></script>
<?php endif; ?>
<meta name="theme-color" content="#0A1B3D">
<link rel="icon" type="image/svg+xml" href="<?= e(url('/favicon.svg')) ?>">
<link rel="alternate icon" type="image/png" href="<?= e(asset('img/favicon.png')) ?>">
<link rel="apple-touch-icon" href="<?= e(url('/apple-touch-icon.png')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600&family=Manrope:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
<script nonce="<?= e($nonce) ?>">document.documentElement.classList.add('js');</script>
