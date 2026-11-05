<?php

$company = content('company');
$nonce = csp_nonce();
?>
<!doctype html>
<html lang="en" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($errorStatus) ?> · <?= e($errorTitle) ?> · White Sea Star</title>
    <link rel="icon" type="image/svg+xml" href="<?= e(url('/favicon.svg')) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=Manrope:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body class="error-body">
    <div class="error-shell">
        <a class="error-brand" href="/" aria-label="White Sea Star home">
            <img src="<?= e(asset('img/mark-light.png')) ?>" alt="" width="82" height="86">
        </a>

        <div class="error-content">
            <p class="eyebrow eyebrow--light">Error <?= e($errorStatus) ?></p>
            <p class="error-code" aria-hidden="true"><?= e($errorStatus) ?></p>
            <h1><?= e($errorTitle) ?></h1>
            <p class="error-message"><?= e($errorMessage) ?></p>
            <div class="error-actions">
                <a class="btn btn--gold" href="/">Back to home <?= icon('arrow') ?></a>
                <a class="btn btn--ghost-light" href="/contact">Contact us</a>
            </div>
            <p class="error-ref">Reference: <?= e($requestId) ?></p>
        </div>

        <footer class="error-footer">
            <span><?= e($company['name']) ?></span>
            <span><?= e($company['phone']) ?> · <?= e($company['email']) ?></span>
        </footer>
    </div>
</body>
</html>
