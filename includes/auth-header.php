<?php

$company = content('company');
$nonce = csp_nonce();
?>
<!doctype html>
<html lang="en" class="no-js">
<head>
    <?php require __DIR__ . '/head.php'; ?>
</head>
<body class="auth-body <?= e($bodyClass) ?>">
<a class="skip-link" href="#main">Skip to content</a>

<div class="auth-shell">
    <section class="auth-brand" aria-label="White Sea Star">
        <div class="auth-brand__pattern" aria-hidden="true"></div>
        <a class="auth-brand__logo" href="/" aria-label="White Sea Star home">
            <img src="<?= e(asset('img/mark-light.png')) ?>" alt="" width="74" height="78">
        </a>

        <div class="auth-brand__content">
            <p class="eyebrow eyebrow--light">Partner access · Dubai</p>
            <h1>Built on trust.<br><em>Driven by strategy.</em></h1>
            <p>Reports, insights and company documents — kept in one private place for our partners.</p>
        </div>

        <div class="auth-brand__foot">
            <p><?= icon('pin') ?> <?= e($company['address_short']) ?></p>
            <p><?= icon('mail') ?> <a href="mailto:<?= e($company['email']) ?>"><?= e($company['email']) ?></a></p>
            <p class="auth-brand__copy">© <?= date('Y') ?> <?= e($company['name']) ?></p>
        </div>
    </section>

    <section class="auth-panel">
        <div class="auth-top">
            <a class="back-link" href="/"><?= icon('arrow') ?> Back to website</a>
            <span class="auth-top__tag">Secure area</span>
        </div>

        <main class="auth-card" id="main">
