<?php

declare(strict_types=1);

$title = 'Dubai Strategy & Advisory | White Sea Star';
$metaDescription = 'White Sea Star is a Dubai-based strategy practice established in 2009. Market entry, growth strategy, commercial advisory, partnerships and operating models.';
$bodyClass = 'page-home';
$services = (array) content('services', []);
$values = (array) content('values', []);
$stats = (array) content('stats', []);
$markets = (array) content('markets', []);
$advantages = (array) content('advantages', []);
$pageType = 'website';
$itemListItems = [];

foreach ($services as $position => $service) {
    $itemListItems[] = [
        '@type' => 'ListItem',
        'position' => $position + 1,
        'name' => $service['name'],
        'url' => absolute_url('/expertise/' . $service['slug']),
    ];
}

$pageSchema = [
    [
        '@type' => 'ItemList',
        'name' => 'Strategy disciplines',
        'itemListElement' => $itemListItems,
    ],
];

require includes_path('header.php');
?>
<section class="hero">
    <div class="hero__ambient" aria-hidden="true"></div>
    <div class="hero__grain" aria-hidden="true"></div>
    <span class="hero__side-label" aria-hidden="true">Strategy · Dubai · UAE</span>

    <div class="container hero__inner">
        <div class="hero__copy">
            <p class="eyebrow eyebrow--light"><span class="eyebrow__rule" aria-hidden="true"></span> Dubai · Established 2009</p>
            <h1 class="hero__title">Built on trust.<br><em>Driven by strategy.</em></h1>
            <p class="hero__lead">For more than seventeen years, White Sea Star has helped founders, investors and leadership teams turn complex market questions into clear, practical strategy — from our home in Dubai to partners across the region and beyond.</p>

            <div class="hero__actions">
                <a class="btn btn--gold" href="/sectors">Explore our approach <?= icon('arrow') ?></a>
                <a class="btn btn--ghost-light" href="/about">The company</a>
            </div>

        </div>

        <div class="hero__visual" aria-hidden="true">
            <div class="hero__orbit hero__orbit--outer"></div>
            <div class="hero__orbit hero__orbit--inner"></div>
            <div class="hero__orbit hero__orbit--dashed"></div>
            <div class="hero__monogram">
                <img src="<?= e(asset('img/mark-light.png')) ?>" alt="" width="230" height="242">
            </div>
            <div class="hero__badge hero__badge--top">
                <span>Established</span>
                <strong>2009</strong>
            </div>
            <div class="hero__badge hero__badge--bottom">
                <span class="hero__pulse"></span> Deira, Dubai
            </div>
            <span class="hero__coordinate hero__coordinate--top">25.2048° N</span>
            <span class="hero__coordinate hero__coordinate--bottom">55.2708° E</span>
        </div>
    </div>

    <div class="container">
        <dl class="hero-stats">
            <?php foreach ($stats as $stat): ?>
                <div class="hero-stat">
                    <dt><?= e($stat['label']) ?></dt>
                    <dd><?= e($stat['value']) ?></dd>
                </div>
            <?php endforeach; ?>
        </dl>
    </div>
</section>

<div class="sector-ticker" aria-hidden="true">
    <div class="sector-ticker__track">
        <?php for ($pass = 0; $pass < 2; $pass++): ?>
            <?php foreach ($services as $service): ?>
                <span class="sector-ticker__item"><?= e($service['name']) ?></span>
                <span class="sector-ticker__star"><?= icon('diamond') ?></span>
            <?php endforeach; ?>
        <?php endfor; ?>
    </div>
</div>

<section class="section section--paper">
    <div class="container">
        <div class="section-heading reveal">
            <p class="eyebrow eyebrow--dark"><span class="eyebrow__num">01</span> The company</p>
            <div class="section-heading__row">
                <h2 class="section-title">A Dubai strategy house,<br><em>built for the long term.</em></h2>
                <p class="section-intro">Clarity by design. Disciplined by habit. White Sea Star brings research, strategy and execution together under one standard of practicality.</p>
            </div>
        </div>

        <div class="about-editorial">
            <div class="about-copy reveal">
                <p class="lead">Founded in 2009, White Sea Star General Trading L.L.C has grown into a Dubai strategy practice built on clear thinking, practical advice and long-term relationships.</p>
                <p>From our office in Deira, we work with founders, investors and leadership teams across the region and beyond. Our focus is simple: understand the market, frame the choice and build a path that can be executed.</p>
                <p>We operate under UAE commercial regulations and maintain full compliance with applicable business and licensing requirements. That discipline is part of the advice — practical, accountable and grounded in the realities of doing business here.</p>

                <div class="about-signature">
                    <img src="<?= e(asset('img/mark.png')) ?>" alt="" width="56" height="58" loading="lazy" decoding="async">
                    <span>
                        <strong>Established 16 June 2009</strong>
                        <small>Dubai, United Arab Emirates</small>
                    </span>
                </div>

                <a class="text-link" href="/about">Read our company story <?= icon('arrow') ?></a>
            </div>

            <div class="value-list reveal">
                <?php foreach ($values as $index => $value): ?>
                    <div class="value-row">
                        <span class="value-row__num"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                        <div class="value-row__body">
                            <h3><?= e($value['name']) ?></h3>
                            <p><?= e($value['text']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<section class="section section--cream" id="activities">
    <div class="container">
        <div class="section-heading reveal">
            <p class="eyebrow eyebrow--dark"><span class="eyebrow__num">02</span> Our practice</p>
            <div class="section-heading__row">
                <h2 class="section-title">One practice.<br><em>Five disciplines.</em></h2>
                <p class="section-intro">Strategy is the practice. Market entry, growth, commercial advisory, partnerships and operating models are how it shows up.</p>
            </div>
            <a class="text-link section-heading__link" href="/sectors">Explore our approach <?= icon('arrow') ?></a>
        </div>

        <div class="sector-grid">
            <?php foreach ($services as $index => $service): ?>
                <a class="sector-card sector-card--service reveal" href="/expertise/<?= e($service['slug']) ?>">
                    <span class="sector-card__top">
                        <span class="sector-card__num"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                        <span class="sector-card__icon"><?= icon($service['icon']) ?></span>
                    </span>
                    <h3><?= e($service['name']) ?></h3>
                    <p><?= e($service['short']) ?></p>
                    <span class="sector-card__link">Explore this discipline <?= icon('arrow') ?></span>
                    <span class="sector-card__line" aria-hidden="true"></span>
                </a>
            <?php endforeach; ?>

            <a class="sector-card sector-card--service sector-card--cta reveal" href="/contact">
                <span class="sector-card__top">
                    <span class="sector-card__num">+</span>
                    <span class="sector-card__icon"><?= icon('arrow') ?></span>
                </span>
                <h3>Start a conversation</h3>
                <p>Bring us the question. We will help you frame the next move.</p>
                <span class="sector-card__link">Talk to us <?= icon('arrow') ?></span>
                <span class="sector-card__line" aria-hidden="true"></span>
            </a>
        </div>
    </div>
</section>

<section class="manifesto">
    <div class="container manifesto__inner reveal">
        <span class="manifesto__mark" aria-hidden="true"><?= icon('diamond') ?></span>
        <blockquote>Relationships first.<br><em>Everything else follows.</em></blockquote>
        <p>Our work is built on repeat partnerships, not one-off engagements. That is why clients return, relationships deepen, and our network keeps growing.</p>
    </div>
</section>

<section class="section section--navy markets">
    <div class="container markets__grid">
        <div class="markets__copy reveal">
            <p class="eyebrow eyebrow--light"><span class="eyebrow__num">03</span> Market reach</p>
            <h2 class="section-title">From Dubai,<br><em>to the world.</em></h2>
            <p class="section-intro">At the crossroads of Asia, Africa and Europe, our location gives clients a practical vantage point on markets, partners and opportunity.</p>

            <div class="markets__coords" aria-hidden="true">
                <span>25.2048° N</span>
                <span class="markets__coords-rule"></span>
                <span>55.2708° E</span>
            </div>

            <a class="btn btn--ghost-light" href="/contact">Discuss your market <?= icon('arrow') ?></a>
        </div>

        <ol class="market-list reveal">
            <?php foreach ($markets as $market): ?>
                <li class="market-row">
                    <span class="market-row__code"><?= e($market['code']) ?></span>
                    <span class="market-row__body">
                        <strong><?= e($market['name']) ?></strong>
                        <small><?= e($market['description']) ?></small>
                    </span>
                    <span class="market-row__arrow" aria-hidden="true"><?= icon('arrow') ?></span>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>

<section class="section section--paper">
    <div class="container">
        <div class="section-heading reveal">
            <p class="eyebrow eyebrow--dark"><span class="eyebrow__num">04</span> Why White Sea Star</p>
            <div class="section-heading__row">
                <h2 class="section-title">The advantage<br><em>of experience.</em></h2>
                <p class="section-intro">Research, strategic clarity and execution support — built over seventeen years of commercial experience.</p>
            </div>
        </div>

        <div class="advantage-grid">
            <?php foreach ($advantages as $index => $advantage): ?>
                <article class="advantage-card reveal">
                    <span class="advantage-card__num"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                    <h3><?= e($advantage['title']) ?></h3>
                    <p><?= e($advantage['text']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php require includes_path('footer.php');
