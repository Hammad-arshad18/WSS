<?php

declare(strict_types=1);

$title = 'Our Expertise | Strategy & Advisory';
$metaDescription = 'Explore the White Sea Star strategy disciplines: market entry, growth strategy, commercial advisory, partnerships and operating models, delivered from Dubai.';
$bodyClass = 'page-expertise';
$services = (array) content('services', []);
$company = content('company');
$meta = content('expertise_meta', []);
$breadcrumbs = [['name' => 'Expertise', 'path' => '/expertise']];
$collectionUrl = absolute_url('/expertise');
$items = [];

foreach ($services as $position => $service) {
    $items[] = [
        '@type' => 'ListItem',
        'position' => $position + 1,
        'name' => $service['name'],
        'url' => absolute_url('/expertise/' . $service['slug']),
    ];
}

$pageSchema = [
    [
        '@type' => 'CollectionPage',
        '@id' => $collectionUrl . '#collection',
        'url' => $collectionUrl,
        'name' => 'Strategy expertise',
        'description' => $meta['text'] ?? '',
        'isPartOf' => ['@id' => absolute_url('/') . '#website'],
        'mainEntity' => [
            '@type' => 'ItemList',
            'itemListElement' => $items,
        ],
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
            <span>Expertise</span>
        </nav>
        <p class="eyebrow eyebrow--light"><span class="eyebrow__num">02</span> Our expertise</p>
        <h1 class="page-hero__title">Five disciplines.<br><em>One standard.</em></h1>
        <p class="page-hero__lead"><?= e($meta['text'] ?? 'Every engagement draws on one or more of our disciplines, shaped around the decision in front of you.') ?></p>
    </div>
</section>

<section class="section section--paper">
    <div class="container">
        <div class="sector-grid">
            <?php foreach ($services as $service): ?>
                <a class="sector-card sector-card--service reveal" href="/expertise/<?= e($service['slug']) ?>">
                    <span class="sector-card__top">
                        <span class="sector-card__num"><?= e($service['number']) ?></span>
                        <span class="sector-card__icon"><?= icon($service['icon']) ?></span>
                    </span>
                    <h3><?= e($service['name']) ?></h3>
                    <p><?= e($service['short']) ?></p>
                    <span class="sector-card__link">Explore this discipline <?= icon('arrow') ?></span>
                    <span class="sector-card__line" aria-hidden="true"></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section--cream">
    <div class="container">
        <div class="process-grid">
            <div class="process-intro reveal">
                <p class="eyebrow eyebrow--dark">Why Dubai</p>
                <h2 class="section-title">A regional base.<br><em>A wider view.</em></h2>
                <p class="section-intro">From our office in Deira, we work across the UAE, the GCC, Africa, South Asia and selected global markets. That position gives our clients something practical: access to decision-makers, suppliers and partners in one of the world's most connected trading cities.</p>
                <a class="text-link" href="/about">About the company <?= icon('arrow') ?></a>
            </div>
            <div class="process-steps">
                <article class="process-step reveal">
                    <span>01</span>
                    <h3>Evidence</h3>
                    <p>We start with the market as it is, not as a report describes it.</p>
                </article>
                <article class="process-step reveal">
                    <span>02</span>
                    <h3>Clarity</h3>
                    <p>We turn complexity into a choice your team can act on.</p>
                </article>
                <article class="process-step reveal">
                    <span>03</span>
                    <h3>Execution</h3>
                    <p>We stay close enough to help the plan survive contact with reality.</p>
                </article>
            </div>
        </div>
    </div>
</section>
<?php require includes_path('footer.php');
