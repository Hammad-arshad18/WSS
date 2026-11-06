<?php

$company = content('company');
$services = (array) content('services', []);
$currentUser = current_user();
?>
<!doctype html>
<html lang="en" class="no-js">
<head>
    <?php require __DIR__ . '/head.php'; ?>
</head>
<body class="<?= e($bodyClass) ?>">
<a class="skip-link" href="#main">Skip to content</a>

<div class="topbar">
    <div class="container topbar__inner">
        <p class="topbar__note"><?= icon('pin') ?> Deira, Dubai · United Arab Emirates</p>
        <div class="topbar__links">
            <a href="tel:<?= e($company['phone_href']) ?>"><?= icon('phone') ?> <?= e($company['phone']) ?></a>
            <span class="topbar__sep" aria-hidden="true"></span>
            <a href="mailto:<?= e($company['email']) ?>"><?= icon('mail') ?> <?= e($company['email']) ?></a>
        </div>
    </div>
</div>

<header class="site-header" data-header>
    <div class="container header__inner">
        <a class="brand logo-tip" href="/" aria-label="White Sea Star General Trading L.L.C — home" data-tooltip="<?= e($company['name']) ?>">
            <img class="brand__logo" src="<?= e(asset('img/wss.png')) ?>" alt="" width="118" height="50" fetchpriority="high" decoding="async">
        </a>

        <nav class="site-nav" id="site-nav" aria-label="Primary navigation">
            <button class="site-nav__close" type="button" aria-label="Close menu" data-nav-close>
                <?= icon('close') ?>
            </button>
            <a class="site-nav__link<?= nav_active('/', true) ?>" href="/">Home</a>
            <a class="site-nav__link<?= nav_active('/about') ?>" href="/about">Company</a>
            <a class="site-nav__link<?= nav_active('/services') ?>" href="/services">Services</a>
            <a class="site-nav__link<?= nav_active('/contact') ?>" href="/contact">Contact</a>
            <a class="site-nav__link site-nav__link--portal<?= nav_active('/members') ?>" href="<?= $currentUser ? e(url(is_admin() ? '/admin' : '/members')) : '/login' ?>">
                <?= $currentUser ? 'Client portal' : 'Partner login' ?>
                <?= icon('arrow') ?>
            </a>
        </nav>

        <div class="header__actions">
            <?php if ($currentUser): ?>
                <a class="btn btn--small btn--outline header__cta" href="<?= e(url(is_admin() ? '/admin' : '/members')) ?>">
                    Open portal <?= icon('arrow') ?>
                </a>
            <?php else: ?>
                <a class="btn btn--small btn--outline header__cta" href="/login">
                    Portal sign in <?= icon('lock') ?>
                </a>
            <?php endif; ?>
            <button class="nav-toggle" type="button" aria-controls="site-nav" aria-expanded="false" aria-label="Open menu" data-nav-toggle>
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>

<main id="main">
