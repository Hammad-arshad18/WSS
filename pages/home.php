<?php

declare(strict_types=1);

$title = 'Global Sourcing & Supply in Dubai | White Sea Star';
$metaDescription = 'White Sea Star is a Dubai-based sourcing and supply partner established in 2009. Supplier sourcing, procurement, project materials and logistics.';
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
        'url' => absolute_url('/services/' . $service['slug']),
    ];
}

$pageSchema = [
    [
        '@type' => 'ItemList',
        'name' => 'Sourcing and supply services',
        'itemListElement' => $itemListItems,
    ],
];

require includes_path('header.php');
?>
<section class="hero">
    <div class="hero__ambient" aria-hidden="true"></div>
    <div class="hero__grain" aria-hidden="true"></div>
    <span class="hero__side-label" aria-hidden="true">Sourcing · Supply · Dubai</span>

    <div class="container hero__inner">
        <div class="hero__copy">
            <p class="eyebrow eyebrow--light"><span class="eyebrow__rule" aria-hidden="true"></span> Dubai · Established 2009</p>
            <h1 class="hero__title">Built on trust.<br><em>Sourced globally.</em></h1>
            <p class="hero__lead">For more than seventeen years, White Sea Star has connected clients with manufacturers and suppliers across local and international markets — sourcing products, coordinating procurement and delivering project supply with clear accountability.</p>

            <div class="hero__actions">
                <a class="btn btn--gold" href="/services">Explore our services <?= icon('arrow') ?></a>
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

<section class="section section--cream" id="services">
    <div class="container">
        <div class="section-heading reveal">
            <p class="eyebrow eyebrow--dark"><span class="eyebrow__num">01</span> Our services</p>
            <div class="section-heading__row">
                <h2 class="section-title">Five pillars.<br><em>One supply partner.</em></h2>
                <p class="section-intro">From a single product enquiry to a multi-supplier project package, our work is organised around five service pillars.</p>
            </div>
            <a class="text-link section-heading__link" href="/services">All services <?= icon('arrow') ?></a>
        </div>

        <div class="sector-grid">
            <?php foreach ($services as $index => $service): ?>
                <a class="sector-card sector-card--service reveal" href="/services/<?= e($service['slug']) ?>">
                    <span class="sector-card__top">
                        <span class="sector-card__num"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                        <span class="sector-card__icon"><?= icon($service['icon']) ?></span>
                    </span>
                    <h3><?= e($service['name']) ?></h3>
                    <p><?= e($service['short']) ?></p>
                    <span class="sector-card__link">Explore this service <?= icon('arrow') ?></span>
                    <span class="sector-card__line" aria-hidden="true"></span>
                </a>
            <?php endforeach; ?>

            <a class="sector-card sector-card--service sector-card--cta reveal" href="/contact">
                <span class="sector-card__top">
                    <span class="sector-card__num">+</span>
                    <span class="sector-card__icon"><?= icon('arrow') ?></span>
                </span>
                <h3>Start a sourcing conversation</h3>
                <p>Tell us what you need. We will help you find the right supply route.</p>
                <span class="sector-card__link">Talk to us <?= icon('arrow') ?></span>
                <span class="sector-card__line" aria-hidden="true"></span>
            </a>
        </div>
    </div>
</section>

<section class="section section--paper">
    <div class="container">
        <div class="section-heading reveal">
            <p class="eyebrow eyebrow--dark"><span class="eyebrow__num">02</span> The company</p>
            <div class="section-heading__row">
                <h2 class="section-title">A Dubai sourcing house,<br><em>built for the long term.</em></h2>
                <p class="section-intro">Global reach. Specification-led sourcing. One point of coordination from enquiry to delivery.</p>
            </div>
        </div>

        <div class="about-editorial">
            <div class="about-copy reveal">
                <p class="lead">Founded in 2009, White Sea Star General Trading L.L.C has grown into a Dubai sourcing and supply partner built on clear specifications, reliable suppliers and long-term relationships.</p>
                <p>From our office in Deira, we work with clients, contractors, suppliers and manufacturers across the region and beyond. Our focus is simple: understand the requirement, find the right source and coordinate the order through to delivery.</p>
                <p>We operate under UAE commercial regulations and work within our licensed scope. That discipline is part of the service — practical, accountable and grounded in the realities of sourcing and supply here.</p>

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

<section class="manifesto">
    <div class="container manifesto__inner reveal">
        <span class="manifesto__mark" aria-hidden="true"><?= icon('diamond') ?></span>
        <blockquote>Relationships first.<br><em>Everything else follows.</em></blockquote>
        <p>Our work is built on repeat supply, not one-off transactions. That is why clients return, suppliers stay, and our network keeps growing.</p>
    </div>
</section>

<section class="section section--navy markets">
    <div class="container markets__grid">
        <div class="markets__copy reveal">
            <p class="eyebrow eyebrow--light"><span class="eyebrow__num">03</span> Market reach</p>
            <h2 class="section-title">From Dubai,<br><em>to the world.</em></h2>
            <p class="section-intro">At the crossroads of Asia, Africa and Europe, our location gives clients a practical sourcing base — close to suppliers, projects and the markets they serve.</p>

            <div class="markets__coords" aria-hidden="true">
                <span>25.2048° N</span>
                <span class="markets__coords-rule"></span>
                <span>55.2708° E</span>
            </div>

            <a class="btn btn--ghost-light" href="/contact">Discuss your requirements <?= icon('arrow') ?></a>
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
                <p class="section-intro">Supplier access, specification discipline and delivery coordination — built over seventeen years of commercial experience.</p>
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
