<?php

$company = content('company');
$currentUser = $user ?? current_user();
$isAdmin = is_admin();
$newEnquiries = $isAdmin ? enquiry_count_new() : 0;
$currentPath = request_path();
$basePath = (string) config('app.base_path', '');

if ($basePath !== '' && str_starts_with($currentPath, $basePath)) {
    $currentPath = substr($currentPath, strlen($basePath)) ?: '/';
}

$isDashboard = $currentPath === '/members' || $currentPath === '/admin';
$isDocuments = str_starts_with($currentPath, '/members/documents') || str_starts_with($currentPath, '/admin/documents');
$isProfile = str_starts_with($currentPath, '/profile');
$roleLabel = role_label((string) ($currentUser['role'] ?? 'partner'));
?>
<!doctype html>
<html lang="en" class="no-js">
<head>
    <?php require __DIR__ . '/head.php'; ?>
</head>
<body class="panel-body <?= e($bodyClass) ?>">
<a class="skip-link" href="#main">Skip to content</a>

<div class="panel-shell">
    <aside class="panel-sidebar" id="panel-sidebar" data-panel-sidebar>
        <a class="panel-brand" href="/" aria-label="White Sea Star client portal — home">
            <img src="<?= e(asset('img/mark-light.png')) ?>" alt="" width="46" height="42">
            <span>
                <strong>White Sea Star</strong>
                <small>Client portal</small>
            </span>
        </a>

        <button class="panel-close" type="button" aria-label="Close navigation" data-panel-close>
            <?= icon('close') ?>
        </button>

        <nav class="panel-nav" aria-label="Portal navigation">
            <p class="panel-nav__label">Workspace</p>
            <a class="panel-nav__link<?= $isDashboard ? ' is-active' : '' ?>" href="<?= $isAdmin ? '/admin' : '/members' ?>">
                <?= icon('dashboard') ?><span>Dashboard</span>
            </a>
            <a class="panel-nav__link<?= $isDocuments ? ' is-active' : '' ?>" href="<?= $isAdmin ? '/admin/documents' : '/members/documents' ?>">
                <?= icon('folder') ?><span><?= $isAdmin ? 'Documents' : 'Resource library' ?></span>
            </a>
            <a class="panel-nav__link<?= $isProfile ? ' is-active' : '' ?>" href="/profile">
                <?= icon('user') ?><span>Account</span>
            </a>

            <?php if ($isAdmin): ?>
                <p class="panel-nav__label panel-nav__label--spaced">Administration</p>
                <a class="panel-nav__link<?= str_starts_with($currentPath, '/admin/members') ? ' is-active' : '' ?>" href="/admin/members">
                    <?= icon('users') ?><span>Accounts</span>
                </a>
                <a class="panel-nav__link<?= str_starts_with($currentPath, '/admin/enquiries') ? ' is-active' : '' ?>" href="/admin/enquiries">
                    <?= icon('inbox') ?><span>Enquiries</span>
                    <?php if ($newEnquiries > 0): ?>
                        <span class="panel-nav__count"><?= (int) $newEnquiries ?></span>
                    <?php endif; ?>
                </a>
                <a class="panel-nav__link<?= str_starts_with($currentPath, '/admin/audit') ? ' is-active' : '' ?>" href="/admin/audit">
                    <?= icon('shield') ?><span>Activity log</span>
                </a>
            <?php endif; ?>
        </nav>

        <div class="panel-sidebar__foot">
            <div class="panel-user">
                <span class="avatar"><?= e(initials((string) ($currentUser['name'] ?? 'W'))) ?></span>
                <span class="panel-user__text">
                    <strong><?= e($currentUser['name'] ?? '') ?></strong>
                    <small><?= e($roleLabel) ?></small>
                </span>
            </div>
            <form method="post" action="/logout">
                <?= csrf_field() ?>
                <button class="panel-logout" type="submit"><?= icon('logout') ?> Sign out</button>
            </form>
        </div>
    </aside>

    <div class="panel-overlay" data-panel-overlay hidden></div>

    <div class="panel-main">
        <header class="panel-topbar">
            <div class="panel-topbar__left">
                <button class="panel-toggle" type="button" aria-controls="panel-sidebar" aria-expanded="false" aria-label="Open navigation" data-panel-toggle>
                    <?= icon('menu') ?>
                </button>
                <nav class="panel-crumbs" aria-label="Breadcrumb">
                    <a href="/">White Sea Star</a>
                    <?= icon('chevron') ?>
                    <span><?= e($title) ?></span>
                </nav>
            </div>
            <div class="panel-topbar__right">
                <span class="panel-secure"><?= icon('lock') ?> Secure session</span>
                <span class="avatar avatar--sm" title="<?= e($currentUser['name'] ?? '') ?>"><?= e(initials((string) ($currentUser['name'] ?? 'W'))) ?></span>
            </div>
        </header>

        <main class="panel-content" id="main">
            <?php require __DIR__ . '/flash.php'; ?>
