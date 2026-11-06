<?php
$company = content('company');
$services = (array) content('services', []);
?>
</main>

<?php require __DIR__ . '/flash.php'; ?>

<section class="pre-footer">
    <div class="container pre-footer__inner">
        <div class="pre-footer__seal" aria-hidden="true">
            <img src="<?= e(asset('img/mark.png')) ?>" alt="" width="58" height="41" loading="lazy" decoding="async">
        </div>
        <div class="pre-footer__copy">
            <p class="eyebrow eyebrow--dark">A conversation, not a transaction</p>
            <h2 class="pre-footer__title">Let's build the next <em>supply partnership.</em></h2>
        </div>
        <div class="pre-footer__actions">
            <a class="btn btn--gold" href="/contact">Start a conversation <?= icon('arrow') ?></a>
            <a class="pre-footer__mail" href="mailto:<?= e($company['email']) ?>"><?= e($company['email']) ?></a>
        </div>
    </div>
</section>

<footer class="site-footer">
    <div class="container">
        <div class="footer__grid">
            <div class="footer__brand">
                <a class="footer__logo" href="/" aria-label="White Sea Star General Trading L.L.C — home">
                    <img src="<?= e(asset('img/logo-light.png')) ?>" alt="" width="260" height="74" decoding="async">
                </a>
                <p class="footer__blurb">A Dubai-based sourcing and supply partner connecting clients with manufacturers and suppliers since 2009.</p>
                <p class="footer__est">Established 16 June 2009</p>
            </div>

            <div class="footer__col">
                <h3>Navigate</h3>
                <ul>
                    <li><a href="/">Home</a></li>
                    <li><a href="/about">The company</a></li>
                    <li><a href="/services">Services</a></li>
                    <li><a href="/contact">Contact</a></li>
                    <li><a href="/login">Partner login</a></li>
                </ul>
            </div>

            <div class="footer__col">
                <h3>Services</h3>
                <ul>
                    <?php foreach ($services as $service): ?>
                        <li><a href="/services/<?= e($service['slug']) ?>"><?= e($service['footer_name'] ?? $service['name']) ?></a></li>
                    <?php endforeach; ?>
                    <li><a class="footer__more" href="/services">All services <?= icon('arrow') ?></a></li>
                </ul>
            </div>

            <div class="footer__col footer__contact">
                <h3>Dubai office</h3>
                <address>
                    <p><?= icon('pin') ?><span><?= e($company['address']) ?></span></p>
                    <p><?= icon('phone') ?><a href="tel:<?= e($company['phone_href']) ?>"><?= e($company['phone']) ?></a></p>
                    <p><?= icon('mail') ?><a href="mailto:<?= e($company['email']) ?>"><?= e($company['email']) ?></a></p>
                    <p><?= icon('clock') ?><span><?= e($company['hours']) ?></span></p>
                </address>
                <a class="footer__map" href="<?= e($company['maps_url']) ?>" target="_blank" rel="noopener noreferrer">View on map <?= icon('external') ?></a>
            </div>
        </div>

        <div class="footer__bottom">
            <p>© <?= date('Y') ?> <?= e($company['name']) ?>. All rights reserved.</p>
            <div class="footer__legal">
                <a href="/privacy">Privacy notice</a>
                <span aria-hidden="true">·</span>
                <span>Trade licence registered in Dubai, UAE</span>
            </div>
            <a class="footer__top" href="#main">Back to top <?= icon('arrow') ?></a>
        </div>
    </div>
</footer>

<script src="<?= e(asset('js/app.js')) ?>" defer></script>
</body>
</html>
<?php clear_form_state(); ?>
