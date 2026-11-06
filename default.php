<?php
// ============================================================
// Maintenance Page Configuration
// ============================================================

$siteName     = 'WHITE SEA STAR';
$siteTagline  = 'We appreciate your patience.';
$supportEmail = 'info@whiteseastar.com'; // Set to '' to hide the support link.

$siteNameEsc = htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8');
$taglineEsc  = htmlspecialchars($siteTagline, ENT_QUOTES, 'UTF-8');
$supportEsc  = htmlspecialchars($supportEmail, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f7f8fa">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo $siteNameEsc; ?> — Under Maintenance</title>
    <style>
        :root {
            color-scheme: light;
            --ink: #172033;
            --muted: #687386;
            --line: #e7eaf0;
            --accent: #3659d9;
            --accent-dark: #2948bd;
            --surface: #ffffff;
            --background: #f7f8fa;
        }

        * { box-sizing: border-box; }

        html, body { min-height: 100%; }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background:
                radial-gradient(ellipse at 50% 0%, rgba(54, 89, 217, .09), transparent 42%),
                var(--background);
            color: var(--ink);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .topbar {
            width: min(1120px, calc(100% - 48px));
            margin: 0 auto;
            min-height: 88px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            color: inherit;
            text-decoration: none;
            min-width: 0;
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            flex: 0 0 auto;
            border-radius: 13px;
            color: #fff;
            background: linear-gradient(145deg, #496bea, #2948bd);
            box-shadow: 0 7px 18px rgba(54, 89, 217, .2);
        }

        .brand-mark svg { width: 21px; height: 21px; }

        .brand-name {
            font-size: 15px;
            font-weight: 700;
            letter-spacing: -.02em;
            overflow-wrap: anywhere;
        }

        .status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border: 1px solid var(--line);
            border-radius: 999px;
            background: rgba(255,255,255,.8);
            color: #596477;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #d69a36;
        }

        main {
            flex: 1;
            display: grid;
            place-items: center;
            padding: 54px 24px 76px;
        }

        .content {
            width: min(100%, 760px);
            text-align: center;
            animation: enter .65s ease both;
        }

        .illustration {
            position: relative;
            width: 184px;
            height: 184px;
            margin: 0 auto 34px;
            display: grid;
            place-items: center;
            border: 1px solid rgba(54, 89, 217, .1);
            border-radius: 48px;
            background: linear-gradient(145deg, #fff, #eef2ff);
            box-shadow: 0 22px 55px rgba(29, 48, 99, .08);
        }

        .illustration::before,
        .illustration::after {
            content: "";
            position: absolute;
            border: 1px solid rgba(54, 89, 217, .13);
            border-radius: 50%;
        }

        .illustration::before { inset: 17px; }
        .illustration::after { inset: 34px; border-style: dashed; }

        .illustration svg {
            position: relative;
            z-index: 1;
            width: 76px;
            height: 76px;
            color: var(--accent);
        }

        .eyebrow {
            margin: 0 0 16px;
            color: var(--accent);
            font-size: 11px;
            font-weight: 750;
            letter-spacing: .16em;
            text-transform: uppercase;
        }

        h1 {
            max-width: 680px;
            margin: 0 auto;
            color: var(--ink);
            font-size: clamp(36px, 6vw, 58px);
            line-height: 1.08;
            letter-spacing: -.055em;
            font-weight: 760;
        }

        .description {
            max-width: 540px;
            margin: 22px auto 0;
            color: var(--muted);
            font-size: 16px;
            line-height: 1.8;
        }

        .divider {
            width: 48px;
            height: 3px;
            margin: 30px auto;
            border-radius: 99px;
            background: linear-gradient(90deg, #8da2f4, var(--accent));
        }

        .thanks {
            margin: 0;
            color: #566174;
            font-size: 14px;
            line-height: 1.7;
        }

        .support {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 28px;
            padding: 11px 16px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: rgba(255,255,255,.75);
            color: #46536a;
            font-size: 13px;
            font-weight: 650;
            text-decoration: none;
            transition: border-color .2s ease, color .2s ease, transform .2s ease;
        }

        .support:hover {
            border-color: #bdc9f7;
            color: var(--accent-dark);
            transform: translateY(-1px);
        }

        .support svg { width: 16px; height: 16px; }

        footer {
            width: min(1120px, calc(100% - 48px));
            margin: 0 auto;
            padding: 22px 0 26px;
            border-top: 1px solid var(--line);
            color: #929aaa;
            text-align: center;
            font-size: 12px;
        }

        @keyframes enter {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: .01ms !important;
                scroll-behavior: auto !important;
            }
        }

        @media (max-width: 560px) {
            .topbar {
                width: min(100% - 32px, 1120px);
                min-height: 76px;
            }

            .brand-mark { width: 38px; height: 38px; }
            .brand-name { font-size: 14px; }
            .status { padding: 7px 9px; font-size: 10px; }

            main { padding: 48px 22px 64px; }
            .illustration {
                width: 150px;
                height: 150px;
                margin-bottom: 30px;
                border-radius: 40px;
            }
            .illustration svg { width: 64px; height: 64px; }
            .description { font-size: 15px; }
            footer { width: calc(100% - 32px); }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <a class="brand" href="/" aria-label="<?php echo $siteNameEsc; ?> home">
            <span class="brand-mark" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M14.2 6.1a5.1 5.1 0 0 0-6.5 6.5l-4.2 4.2a2 2 0 0 0 2.8 2.8l4.2-4.2a5.1 5.1 0 0 0 6.5-6.5l-3 3-3.1-3.1 3.3-2.7Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <span class="brand-name"><?php echo $siteNameEsc; ?></span>
        </a>

        <div class="status">
            <span class="status-dot" aria-hidden="true"></span>
            Temporarily unavailable
        </div>
    </header>

    <main>
        <section class="content" aria-labelledby="page-title">
            <div class="illustration" aria-hidden="true">
                <svg viewBox="0 0 96 96" fill="none">
                    <path d="M56.8 19.8a21.3 21.3 0 0 0-27.1 27.1L12.2 64.4a8.3 8.3 0 0 0 11.7 11.7l17.5-17.5a21.3 21.3 0 0 0 27.1-27.1L56 43l-13-13 13.8-10.2Z" fill="#DCE5FF" stroke="#3659D9" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="m65 64 4.5 4.5M74 55l4 1M55 74l1 4" stroke="#7890E8" stroke-width="3" stroke-linecap="round"/>
                    <circle cx="75" cy="24" r="4" fill="#A9B9F8"/>
                    <circle cx="20" cy="25" r="3" fill="#A9B9F8"/>
                </svg>
            </div>

            <p class="eyebrow">A little maintenance</p>
            <h1 id="page-title">We'll be back soon.</h1>
            <p class="description">
                We're making some improvements behind the scenes.
                Our website is temporarily unavailable while we work on it.
            </p>
            <div class="divider" aria-hidden="true"></div>
            <p class="thanks"><?php echo $taglineEsc; ?></p>

            <?php if (filter_var($supportEmail, FILTER_VALIDATE_EMAIL)): ?>
                <a class="support" href="mailto:<?php echo $supportEsc; ?>">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4 6.5h16v11H4z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                        <path d="m4.5 7 7.5 6 7.5-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Contact support
                </a>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        &copy; <?php echo date('Y'); ?> <?php echo $siteNameEsc; ?>. All rights reserved.
    </footer>
</body>
</html>