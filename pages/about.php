<?php

declare(strict_types=1);

$title = 'About White Sea Star | Dubai Strategy Practice Since 2009';
$metaDescription = 'Established in Dubai in 2009, White Sea Star General Trading L.L.C is a strategy practice helping leaders navigate complex markets.';
$bodyClass = 'page-about';
$company = content('company');
$values = (array) content('values', []);
$advantages = (array) content('advantages', []);
$stats = (array) content('stats', []);
$markets = (array) content('markets', []);
$breadcrumbs = [['name' => 'The company', 'path' => '/about']];
$pageSchema = [
    [
        '@type' => 'AboutPage',
        '@id' => absolute_url('/about') . '#about',
        'url' => absolute_url('/about'),
        'name' => 'About White Sea Star General Trading L.L.C',
        'description' => $metaDescription,
        'mainEntity' => ['@id' => absolute_url('/') . '#organization'],
    ],
];

require includes_path('header.php');
?>
<section class="page-hero page-hero--navy">
    <div class="page-hero__ambient" aria-hidden="true"></div>
    <div class="container page-hero__inner">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="/">Home</a>
            <?= icon('chevron') ?>
            <span>The company</span>
        </nav>
        <p class="eyebrow eyebrow--light"><span class="eyebrow__num">01</span> Company overview</p>
        <h1 class="page-hero__title">A strategy practice built<br>on <em>relationships.</em></h1>
        <p class="page-hero__lead">Established in Dubai in 2009, White Sea Star General Trading L.L.C works with founders, investors and leadership teams to turn complex market questions into clear, executable strategy.</p>
    </div>
</section>

<section class="section section--paper">
    <div class="container">
        <div class="story-grid">
            <div class="story-copy reveal">
                <p class="eyebrow eyebrow--dark">Our story</p>
                <h2 class="section-title">Seventeen years of<br><em>steady progress.</em></h2>
                <p class="lead">What began as a focused Dubai commercial practice has grown into a strategy house serving clients across the region and beyond.</p>
                <p>Over the past decade and more, we have built a reputation for clear thinking, practical advice and long-term relationships. Our cross-sector perspective allows us to see the connections others miss — and to turn them into a plan that works.</p>
                <p>We operate under UAE commercial regulations and maintain full compliance with applicable business and licensing requirements. That discipline is part of the advice: practical, accountable and grounded in the realities of doing business here.</p>

                <div class="story-facts">
                    <div>
                        <span>Established</span>
                        <strong>16 June 2009</strong>
                    </div>
                    <div>
                        <span>Head office</span>
                        <strong>Deira, Dubai</strong>
                    </div>
                    <div>
                        <span>Experience</span>
                        <strong>17+ years</strong>
                    </div>
                    <div>
                        <span>Focus</span>
                        <strong>Strategy</strong>
                    </div>
                </div>
            </div>

            <aside class="story-card reveal">
                <div class="story-card__head">
                    <img src="<?= e(asset('img/mark.png')) ?>" alt="" width="70" height="73" loading="lazy" decoding="async">
                    <span class="story-card__est">Est.<br><strong>2009</strong></span>
                </div>
                <blockquote>"Clarity is not a deliverable. It is where every good engagement begins."</blockquote>
                <div class="story-card__address">
                    <p class="eyebrow eyebrow--dark">Dubai office</p>
                    <address><?= e($company['address']) ?></address>
                    <a class="text-link" href="<?= e($company['maps_url']) ?>" target="_blank" rel="noopener noreferrer">Open in maps <?= icon('external') ?></a>
                </div>
            </aside>
        </div>
    </div>
</section>

<section class="section section--cream">
    <div class="container">
        <div class="section-heading reveal">
            <p class="eyebrow eyebrow--dark"><span class="eyebrow__num">02</span> Vision &amp; mission</p>
            <h2 class="section-title">What we are<br><em>working toward.</em></h2>
        </div>

        <div class="vm-grid">
            <article class="vm-card reveal">
                <span class="vm-card__label">Vision</span>
                <h3>To be a trusted strategy partner for ambitious businesses navigating complex markets.</h3>
                <span class="vm-card__icon" aria-hidden="true"><?= icon('diamond') ?></span>
            </article>
            <article class="vm-card vm-card--accent reveal">
                <span class="vm-card__label">Mission</span>
                <h3>To turn market complexity into clear, practical direction — and stay close enough to help our partners execute it.</h3>
                <span class="vm-card__icon" aria-hidden="true"><?= icon('spark') ?></span>
            </article>
        </div>
    </div>
</section>

<section class="section section--paper">
    <div class="container">
        <div class="section-heading reveal">
            <p class="eyebrow eyebrow--dark"><span class="eyebrow__num">03</span> Core values</p>
            <div class="section-heading__row">
                <h2 class="section-title">The principles<br><em>behind the practice.</em></h2>
                <p class="section-intro">Five commitments guide how we advise, partner and deliver — with every client and every engagement.</p>
            </div>
        </div>

        <div class="values-grid">
            <?php foreach ($values as $index => $value): ?>
                <article class="value-card reveal">
                    <span class="value-card__num"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                    <h3><?= e($value['name']) ?></h3>
                    <p><?= e($value['text']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section--navy">
    <div class="container">
        <div class="section-heading section-heading--light reveal">
            <p class="eyebrow eyebrow--light"><span class="eyebrow__num">04</span> Capabilities</p>
            <div class="section-heading__row">
                <h2 class="section-title">Built around<br><em>execution.</em></h2>
                <p class="section-intro">Research, strategic framing and execution support — the foundations of our practice.</p>
            </div>
        </div>

        <div class="capability-list">
            <?php foreach ($advantages as $index => $advantage): ?>
                <article class="capability reveal">
                    <span class="capability__num"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                    <div class="capability__body">
                        <h3><?= e($advantage['title']) ?></h3>
                        <p><?= e($advantage['text']) ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section--cream">
    <div class="container">
        <div class="section-heading reveal">
            <p class="eyebrow eyebrow--dark"><span class="eyebrow__num">05</span> Target markets</p>
            <h2 class="section-title">A growing<br><em>international perspective.</em></h2>
        </div>

        <div class="market-cards">
            <?php foreach ($markets as $market): ?>
                <article class="market-card reveal">
                    <span class="market-card__code"><?= e($market['code']) ?></span>
                    <h3><?= e($market['name']) ?></h3>
                    <p><?= e($market['description']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php require includes_path('footer.php');
