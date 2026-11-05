<?php

declare(strict_types=1);

$title = 'Strategy';
$metaDescription = 'White Sea Star is a Dubai-based strategy practice working across market entry, growth, commercial advisory, partnerships and operating models.';
$bodyClass = 'page-sectors';
$sectors = (array) content('sectors', []);
$services = (array) content('services', []);

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
        <h1 class="page-hero__title">One practice.<br><em>Five disciplines.</em></h1>
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
                <article class="value-card reveal">
                    <span class="value-card__num"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                    <h3><?= e($service['name']) ?></h3>
                    <p><?= e($service['short']) ?></p>
                </article>
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
<?php require includes_path('footer.php');
