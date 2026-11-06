<?php

declare(strict_types=1);

$title = 'Global Sourcing & Supply Services in Dubai | White Sea Star';
$metaDescription = 'Global sourcing, procurement, project materials, industrial supplies, import/export and logistics coordination from Dubai. Explore our five service pillars.';
$bodyClass = 'page-services';
$services = (array) content('services', []);
$capabilities = (array) content('capabilities', []);
$faqs = (array) content('faqs', []);
$meta = content('services_meta', []);
$breadcrumbs = [['name' => 'Services', 'path' => '/services']];
$organizationId = absolute_url('/') . '#organization';
$serviceUrl = absolute_url('/services');

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
            'url' => absolute_url('/services/' . $service['slug']),
        ],
    ];
}

$pageSchema = [
    [
        '@type' => 'Service',
        '@id' => $serviceUrl . '#service',
        'name' => 'Global sourcing and supply',
        'serviceType' => 'Global sourcing and supply',
        'description' => 'Global sourcing, procurement, project supply, industrial and commercial supplies, import/export and logistics coordination, delivered from Dubai.',
        'provider' => ['@id' => $organizationId],
        'areaServed' => ['United Arab Emirates', 'GCC Region', 'Africa', 'South Asia'],
        'hasOfferCatalog' => [
            '@type' => 'OfferCatalog',
            'name' => 'Sourcing and supply services',
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
            <span>Services</span>
        </nav>
        <p class="eyebrow eyebrow--light"><span class="eyebrow__num">02</span> Our services</p>
        <h1 class="page-hero__title">Global sourcing.<br><em>Delivered with certainty.</em></h1>
        <p class="page-hero__lead">Connecting clients with manufacturers and suppliers in local and international markets — with supplier identification, quotation comparisons, specification checks and commercial negotiation.</p>
    </div>
</section>

<section class="section section--paper">
    <div class="container">
        <div class="section-heading reveal">
            <p class="eyebrow eyebrow--dark"><span class="eyebrow__num">03</span> What we do</p>
            <div class="section-heading__row">
                <h2 class="section-title">Five pillars.<br><em>One point of coordination.</em></h2>
                <p class="section-intro"><?= e($meta['text'] ?? 'Every requirement draws on one or more of our service pillars, shaped around the products, specification and timeline in front of us.') ?></p>
            </div>
        </div>

        <div class="sector-list">
            <?php foreach ($services as $service): ?>
                <a class="sector-row reveal" href="/services/<?= e($service['slug']) ?>">
                    <span class="sector-row__num"><?= e($service['number']) ?></span>
                    <span class="sector-row__icon"><?= icon($service['icon']) ?></span>
                    <span class="sector-row__body">
                        <span class="sector-row__heading">
                            <h2><?= e($service['name']) ?></h2>
                            <span class="sector-row__tag"><?= e($service['lead']) ?></span>
                        </span>
                        <p><?= e($service['short']) ?></p>
                    </span>
                    <span class="sector-row__action">Explore this service <?= icon('arrow') ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if ($capabilities !== []): ?>
            <div class="capability-tags reveal">
                <p class="eyebrow eyebrow--dark">Requirements we handle</p>
                <ul>
                    <?php foreach ($capabilities as $capability): ?>
                        <li><?= e($capability) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section section--cream">
    <div class="container">
        <div class="process-grid">
            <div class="process-intro reveal">
                <p class="eyebrow eyebrow--dark">How we work</p>
                <h2 class="section-title">A clear path<br><em>from requirement to delivery.</em></h2>
                <p class="section-intro">The same disciplined approach applies to every requirement — whether it is a single product, a project package or a recurring supply arrangement.</p>
            </div>
            <div class="process-steps">
                <article class="process-step reveal">
                    <span>01</span>
                    <h3>Understand</h3>
                    <p>We start with the product, specification, quantity, destination and timeline.</p>
                </article>
                <article class="process-step reveal">
                    <span>02</span>
                    <h3>Source &amp; verify</h3>
                    <p>We identify suppliers, compare quotations and check the specification before you commit.</p>
                </article>
                <article class="process-step reveal">
                    <span>03</span>
                    <h3>Coordinate</h3>
                    <p>We coordinate the order, documentation and delivery through to the agreed destination.</p>
                </article>
            </div>
        </div>
    </div>
</section>

<section class="section section--paper">
    <div class="container">
        <div class="section-heading reveal">
            <p class="eyebrow eyebrow--dark"><span class="eyebrow__num">04</span> Common questions</p>
            <div class="section-heading__row">
                <h2 class="section-title">Answers, before<br><em>the first enquiry.</em></h2>
                <p class="section-intro">A few practical questions we are often asked about sourcing and supply.</p>
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
