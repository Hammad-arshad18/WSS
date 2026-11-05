<?php

declare(strict_types=1);

$title = 'Strategy & Market Advisory in Dubai | White Sea Star';
$metaDescription = 'Strategy consulting in Dubai. Market entry, growth, commercial advisory, partnerships and operating models across the UAE, GCC and beyond.';
$bodyClass = 'page-sectors';
$sectors = (array) content('sectors', []);
$services = (array) content('services', []);
$faqs = (array) content('faqs', []);
$breadcrumbs = [['name' => 'Strategy', 'path' => '/sectors']];
$organizationId = absolute_url('/') . '#organization';
$serviceUrl = absolute_url('/sectors');

$faqItems = [];

foreach ($faqs as $faq) {
    $faqItems[] = [
        '@type' => 'Question',
        'name' => $faq['q'],
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text' => $faq['a'],
        ],
    ];
}

$catalogItems = [];

foreach ($services as $service) {
    $catalogItems[] = [
        '@type' => 'Offer',
        'itemOffered' => [
            '@type' => 'Service',
            'name' => $service['name'],
            'description' => $service['short'],
            'url' => absolute_url('/expertise/' . $service['slug']),
        ],
    ];
}

$pageSchema = [
    [
        '@type' => 'Service',
        '@id' => $serviceUrl . '#service',
        'name' => 'Strategy consulting',
        'serviceType' => 'Strategy consulting',
        'description' => 'Strategy consulting for market entry, growth, commercial advisory, partnerships and operating models, delivered from Dubai.',
        'provider' => ['@id' => $organizationId],
        'areaServed' => ['United Arab Emirates', 'GCC Region', 'Africa', 'South Asia'],
        'hasOfferCatalog' => [
            '@type' => 'OfferCatalog',
            'name' => 'Strategy disciplines',
            'itemListElement' => $catalogItems,
        ],
    ],
    [
        '@type' => 'FAQPage',
        '@id' => $serviceUrl . '#faq',
        'mainEntity' => $faqItems,
    ],
];

require includes_path('header.php');
?>
<section class="page-hero page-hero--navy page-hero--compact">
    <div class="page-hero__ambient" aria-hidden="true"></div>
    <div class="container page-hero__inner">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="/">Home</a>
            <?= icon('chevron') ?>
            <span>Strategy</span>
        </nav>
        <p class="eyebrow eyebrow--light"><span class="eyebrow__num">02</span> Our practice</p>
        <h1 class="page-hero__title">Strategy consulting<br><em>in Dubai.</em></h1>
        <p class="page-hero__lead">Strategy is the practice. Market entry, growth, commercial advisory, partnerships and operating models are how it shows up for our clients.</p>
    </div>
</section>

<section class="section section--paper">
    <div class="container">
        <div class="sector-list">
            <?php foreach ($sectors as $sector): ?>
                <a class="sector-row reveal" href="/sectors/<?= e($sector['slug']) ?>">
                    <span class="sector-row__num"><?= e($sector['number']) ?></span>
                    <span class="sector-row__icon"><?= icon($sector['icon']) ?></span>
                    <span class="sector-row__body">
                        <span class="sector-row__heading">
                            <h2><?= e($sector['name']) ?></h2>
                            <span class="sector-row__tag"><?= e($sector['lead']) ?></span>
                        </span>
                        <p><?= e($sector['short']) ?></p>
                    </span>
                    <span class="sector-row__action">Explore the practice <?= icon('arrow') ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section--cream">
    <div class="container">
        <div class="section-heading reveal">
            <p class="eyebrow eyebrow--dark"><span class="eyebrow__num">03</span> What we do</p>
            <div class="section-heading__row">
                <h2 class="section-title">Strategy, in<br><em>five disciplines.</em></h2>
                <p class="section-intro">Each engagement draws on one or more of these areas, shaped around the question in front of you.</p>
            </div>
        </div>

        <div class="values-grid">
            <?php foreach ($services as $index => $service): ?>
                <a class="value-card value-card--link reveal" href="/expertise/<?= e($service['slug']) ?>">
                    <span class="value-card__num"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                    <h3><?= e($service['name']) ?></h3>
                    <p><?= e($service['short']) ?></p>
                    <span class="value-card__more">Read more <?= icon('arrow') ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section--paper">
    <div class="container">
        <div class="process-grid">
            <div class="process-intro reveal">
                <p class="eyebrow eyebrow--dark">How we work</p>
                <h2 class="section-title">A clear path<br><em>from question to action.</em></h2>
                <p class="section-intro">The same disciplined approach applies to every engagement — whether the question is a new market, a growth plateau or a partnership decision.</p>
            </div>
            <div class="process-steps">
                <article class="process-step reveal">
                    <span>01</span>
                    <h3>Listen</h3>
                    <p>We start with your context, your constraints and the evidence already around you.</p>
                </article>
                <article class="process-step reveal">
                    <span>02</span>
                    <h3>Frame</h3>
                    <p>We turn complexity into a clear choice, with the trade-offs made explicit.</p>
                </article>
                <article class="process-step reveal">
                    <span>03</span>
                    <h3>Act</h3>
                    <p>We build a practical path forward and stay close enough to help you execute it.</p>
                </article>
            </div>
        </div>
    </div>
</section>

<section class="section section--cream">
    <div class="container">
        <div class="section-heading reveal">
            <p class="eyebrow eyebrow--dark"><span class="eyebrow__num">04</span> Common questions</p>
            <div class="section-heading__row">
                <h2 class="section-title">Answers, before<br><em>the first meeting.</em></h2>
                <p class="section-intro">A few practical questions we are often asked about White Sea Star and how we work.</p>
            </div>
        </div>

        <div class="faq-list">
            <?php foreach ($faqs as $faq): ?>
                <details class="faq-item reveal">
                    <summary><span><?= e($faq['q']) ?></span><?= icon('chevron') ?></summary>
                    <p><?= e($faq['a']) ?></p>
                </details>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php require includes_path('footer.php');
