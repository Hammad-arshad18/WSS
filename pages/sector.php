<?php

declare(strict_types=1);

$slug = (string) ($params['slug'] ?? '');
$sectors = (array) content('sectors', []);
$sector = null;
$index = 0;

foreach ($sectors as $position => $candidate) {
    if ($candidate['slug'] === $slug) {
        $sector = $candidate;
        $index = $position;
        break;
    }
}

if ($sector === null) {
    abort(404, 'That practice could not be found.');
}

$related = [];

foreach ($sectors as $position => $candidate) {
    if ($position !== $index) {
        $related[] = $candidate;
    }
}

$related = array_slice($related, 0, 2);

$title = 'Strategy Consulting in Dubai | White Sea Star';
$metaDescription = 'White Sea Star is a Dubai-based strategy practice. Market entry, growth, commercial advisory, partnerships and operating models for the UAE, GCC and beyond.';
$bodyClass = 'page-sector';
$company = content('company');
$services = (array) content('services', []);
$breadcrumbs = [
    ['name' => 'Our practice', 'path' => '/sectors'],
    ['name' => 'Strategy consulting', 'path' => '/sectors/strategy'],
];
$pageSchema = [
    [
        '@type' => 'Service',
        '@id' => absolute_url('/sectors/strategy') . '#service',
        'name' => 'Strategy consulting',
        'serviceType' => 'Strategy consulting',
        'description' => $sector['short'],
        'provider' => ['@id' => absolute_url('/') . '#organization'],
        'areaServed' => ['United Arab Emirates', 'GCC Region', 'Africa', 'South Asia'],
        'url' => absolute_url('/sectors/strategy'),
    ],
];

require includes_path('header.php');
?>
<section class="page-hero page-hero--navy page-hero--sector">
    <div class="page-hero__ambient" aria-hidden="true"></div>
    <span class="page-hero__index" aria-hidden="true"><?= e($sector['number']) ?></span>
    <div class="container page-hero__inner">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="/">Home</a>
            <?= icon('chevron') ?>
            <a href="/sectors">Our practice</a>
            <?= icon('chevron') ?>
            <span><?= e($sector['name']) ?></span>
        </nav>
        <div class="page-hero__sector-head">
            <span class="page-hero__sector-icon"><?= icon($sector['icon']) ?></span>
            <div>
                <p class="eyebrow eyebrow--light">Practice <?= e($sector['number']) ?></p>
                <h1 class="page-hero__title"><?= e($sector['name']) ?></h1>
            </div>
        </div>
        <p class="page-hero__lead"><?= e($sector['lead']) ?></p>
    </div>
</section>

<section class="section section--paper">
    <div class="container">
        <div class="sector-detail">
            <div class="sector-detail__main reveal">
                <p class="eyebrow eyebrow--dark">Overview</p>
                <h2 class="section-title"><?= e($sector['name']) ?>,<br><em>considered in detail.</em></h2>

                <?php foreach ($sector['description'] as $paragraph): ?>
                    <p class="sector-detail__paragraph"><?= e($paragraph) ?></p>
                <?php endforeach; ?>

                <div class="supply-list">
                    <p class="eyebrow eyebrow--dark">What we do</p>
                    <ul>
                        <?php foreach ($sector['bullets'] as $bullet): ?>
                            <li><?= icon('check') ?><span><?= e($bullet) ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <aside class="sector-aside reveal">
                <div class="sector-aside__card">
                    <p class="eyebrow eyebrow--dark">At a glance</p>
                    <dl>
                        <div><dt>Practice</dt><dd><?= e($sector['number']) ?></dd></div>
                        <div><dt>Established</dt><dd>2009</dd></div>
                        <div><dt>Markets</dt><dd>UAE · GCC · Africa · South Asia</dd></div>
                        <div><dt>Enquiries</dt><dd><a href="mailto:<?= e($company['email']) ?>"><?= e($company['email']) ?></a></dd></div>
                    </dl>
                    <a class="btn btn--gold btn--full" href="/contact?sector=<?= e(urlencode($sector['name'])) ?>">Start a conversation <?= icon('arrow') ?></a>
                    <a class="sector-aside__phone" href="tel:<?= e($company['phone_href']) ?>"><?= icon('phone') ?> <?= e($company['phone']) ?></a>
                </div>

                <div class="sector-aside__note">
                    <span aria-hidden="true"><?= icon('diamond') ?></span>
                    <p>Strategy, facilitation and execution support from our Dubai office.</p>
                </div>
            </aside>
        </div>
    </div>
</section>

<?php if ($services !== []): ?>
<section class="section section--cream">
    <div class="container">
        <div class="section-heading reveal">
            <p class="eyebrow eyebrow--dark">Continue exploring</p>
            <h2 class="section-title">The five <em>disciplines.</em></h2>
            <p class="section-intro">Each discipline has its own page, with the questions we answer and the work we deliver.</p>
        </div>

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
<?php endif; ?>
<?php require includes_path('footer.php');
