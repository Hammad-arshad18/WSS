<?php

declare(strict_types=1);

$slug = (string) ($params['slug'] ?? '');
$services = (array) content('services', []);
$service = null;
$index = 0;

foreach ($services as $position => $candidate) {
    if ($candidate['slug'] === $slug) {
        $service = $candidate;
        $index = $position;
        break;
    }
}

if ($service === null) {
    abort(404, 'That expertise area could not be found.');
}

$related = [];

foreach ($services as $position => $candidate) {
    if ($position !== $index) {
        $related[] = $candidate;
    }
}

$related = array_slice($related, 0, 4);

$title = $service['seo_title'] . ' | White Sea Star';
$metaDescription = $service['meta'];
$bodyClass = 'page-expertise-detail';
$company = content('company');
$breadcrumbs = [
    ['name' => 'Expertise', 'path' => '/expertise'],
    ['name' => $service['name'], 'path' => '/expertise/' . $service['slug']],
];
$serviceUrl = absolute_url('/expertise/' . $service['slug']);
$faqItems = [];

foreach ($service['faqs'] as $faq) {
    $faqItems[] = [
        '@type' => 'Question',
        'name' => $faq['q'],
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text' => $faq['a'],
        ],
    ];
}

$pageSchema = [
    [
        '@type' => 'Service',
        '@id' => $serviceUrl . '#service',
        'name' => $service['name'],
        'serviceType' => $service['name'],
        'description' => $service['meta'],
        'provider' => ['@id' => absolute_url('/') . '#organization'],
        'areaServed' => ['United Arab Emirates', 'GCC Region', 'Africa', 'South Asia'],
        'url' => $serviceUrl,
    ],
    [
        '@type' => 'FAQPage',
        '@id' => $serviceUrl . '#faq',
        'mainEntity' => $faqItems,
    ],
];

require includes_path('header.php');
?>
<section class="page-hero page-hero--navy page-hero--sector">
    <div class="page-hero__ambient" aria-hidden="true"></div>
    <span class="page-hero__index" aria-hidden="true"><?= e($service['number']) ?></span>
    <div class="container page-hero__inner">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="/">Home</a>
            <?= icon('chevron') ?>
            <a href="/expertise">Expertise</a>
            <?= icon('chevron') ?>
            <span><?= e($service['name']) ?></span>
        </nav>
        <div class="page-hero__sector-head">
            <span class="page-hero__sector-icon"><?= icon($service['icon']) ?></span>
            <div>
                <p class="eyebrow eyebrow--light">Discipline <?= e($service['number']) ?></p>
                <h1 class="page-hero__title"><?= e($service['name']) ?></h1>
            </div>
        </div>
        <p class="page-hero__lead"><?= e($service['lead']) ?></p>
    </div>
</section>

<section class="section section--paper">
    <div class="container">
        <div class="sector-detail">
            <div class="sector-detail__main reveal">
                <p class="eyebrow eyebrow--dark">Overview</p>
                <h2 class="section-title"><?= e($service['name']) ?>,<br><em>considered in detail.</em></h2>

                <?php foreach ($service['description'] as $paragraph): ?>
                    <p class="sector-detail__paragraph"><?= e($paragraph) ?></p>
                <?php endforeach; ?>

                <div class="supply-list">
                    <p class="eyebrow eyebrow--dark">What we help with</p>
                    <ul>
                        <?php foreach ($service['bullets'] as $bullet): ?>
                            <li><?= icon('check') ?><span><?= e($bullet) ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="supply-list">
                    <p class="eyebrow eyebrow--dark">Questions we answer</p>
                    <ul>
                        <?php foreach ($service['questions'] as $question): ?>
                            <li><?= icon('arrow') ?><span><?= e($question) ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <aside class="sector-aside reveal">
                <div class="sector-aside__card">
                    <p class="eyebrow eyebrow--dark">At a glance</p>
                    <dl>
                        <div><dt>Discipline</dt><dd><?= e($service['number']) ?> / 05</dd></div>
                        <div><dt>Established</dt><dd>2009</dd></div>
                        <div><dt>Markets</dt><dd>UAE · GCC · Africa · South Asia</dd></div>
                        <div><dt>Enquiries</dt><dd><a href="mailto:<?= e($company['email']) ?>"><?= e($company['email']) ?></a></dd></div>
                    </dl>
                    <a class="btn btn--gold btn--full" href="/contact?sector=<?= e(urlencode($service['name'])) ?>">Discuss this discipline <?= icon('arrow') ?></a>
                    <a class="sector-aside__phone" href="tel:<?= e($company['phone_href']) ?>"><?= icon('phone') ?> <?= e($company['phone']) ?></a>
                </div>

                <div class="sector-aside__note">
                    <span aria-hidden="true"><?= icon('diamond') ?></span>
                    <p>Evidence-led advice and execution support from our Dubai office.</p>
                </div>
            </aside>
        </div>
    </div>
</section>

<section class="section section--cream">
    <div class="container">
        <div class="section-heading reveal">
            <p class="eyebrow eyebrow--dark">Common questions</p>
            <div class="section-heading__row">
                <h2 class="section-title">Practical answers<br><em>on <?= e(mb_strtolower($service['name'])) ?>.</em></h2>
                <p class="section-intro">The questions clients ask most often about this discipline.</p>
            </div>
        </div>

        <div class="faq-list">
            <?php foreach ($service['faqs'] as $faq): ?>
                <details class="faq-item reveal">
                    <summary><span><?= e($faq['q']) ?></span><?= icon('chevron') ?></summary>
                    <p><?= e($faq['a']) ?></p>
                </details>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if ($related !== []): ?>
<section class="section section--paper">
    <div class="container">
        <div class="section-heading reveal">
            <p class="eyebrow eyebrow--dark">Continue exploring</p>
            <h2 class="section-title">Other <em>disciplines.</em></h2>
        </div>

        <div class="related-grid">
            <?php foreach ($related as $item): ?>
                <a class="sector-card sector-card--related reveal" href="/expertise/<?= e($item['slug']) ?>">
                    <span class="sector-card__top">
                        <span class="sector-card__num"><?= e($item['number']) ?></span>
                        <span class="sector-card__icon"><?= icon($item['icon']) ?></span>
                    </span>
                    <h3><?= e($item['name']) ?></h3>
                    <p><?= e($item['short']) ?></p>
                    <span class="sector-card__link">Discover this discipline <?= icon('arrow') ?></span>
                    <span class="sector-card__line" aria-hidden="true"></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
<?php require includes_path('footer.php');
