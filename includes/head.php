<?php

/**
 * Shared <head> contents.
 *
 * Expects $title, $metaDescription and $bodyClass to be set by the page.
 */
$company = content('company');
$fullTitle = str_contains((string) $title, 'White Sea Star')
    ? (string) $title
    : $title . ' · White Sea Star';
$description = $metaDescription !== ''
    ? $metaDescription
    : 'White Sea Star General Trading L.L.C — a Dubai-based strategy practice established in 2009.';
$noindex = str_contains($bodyClass, 'page-panel') || str_contains($bodyClass, 'page-login');
$canonicalPath = request_path();
$basePath = (string) config('app.base_path', '');

if ($basePath !== '' && str_starts_with($canonicalPath, $basePath)) {
    $canonicalPath = substr($canonicalPath, strlen($basePath)) ?: '/';
}

$canonical = (string) config('app.url') . ($canonicalPath === '/' ? '/' : $canonicalPath);
$ogImage = url('/assets/img/og.jpg');
$nonce = csp_nonce();
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($fullTitle) ?></title>
<meta name="description" content="<?= e($description) ?>">
<?php if ($noindex): ?>
<meta name="robots" content="noindex, nofollow, noarchive">
<?php else: ?>
<link rel="canonical" href="<?= e($canonical) ?>">
<meta property="og:type" content="website">
<meta property="og:site_name" content="White Sea Star General Trading L.L.C">
<meta property="og:title" content="<?= e($fullTitle) ?>">
<meta property="og:description" content="<?= e($description) ?>">
<meta property="og:image" content="<?= e($ogImage) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta name="twitter:card" content="summary_large_image">
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
