<?php

declare(strict_types=1);

$title = 'Privacy Notice | White Sea Star';
$metaDescription = 'How White Sea Star General Trading L.L.C collects, uses and protects information shared through this website.';
$bodyClass = 'page-privacy';
$company = content('company');
$breadcrumbs = [['name' => 'Privacy notice', 'path' => '/privacy']];

require includes_path('header.php');
?>
<section class="page-hero page-hero--navy page-hero--compact">
    <div class="page-hero__ambient" aria-hidden="true"></div>
    <div class="container page-hero__inner">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="/">Home</a>
            <?= icon('chevron') ?>
            <span>Privacy notice</span>
        </nav>
        <p class="eyebrow eyebrow--light">Legal</p>
        <h1 class="page-hero__title">Privacy <em>notice.</em></h1>
        <p class="page-hero__lead">How we handle information shared with White Sea Star General Trading L.L.C through this website and the client portal.</p>
    </div>
</section>

<section class="section section--paper">
    <div class="container">
        <div class="prose-layout">
            <aside class="prose-aside">
                <p class="eyebrow eyebrow--dark">At a glance</p>
                <ul>
                    <li>We collect only what you provide.</li>
                    <li>We use it to respond and to operate your account.</li>
                    <li>We do not sell personal information.</li>
                    <li>You can ask us to correct or remove your data.</li>
                </ul>
                <p class="prose-aside__updated">Last updated: <?= date('F Y') ?></p>
            </aside>

            <div class="prose">
                <h2>Who we are</h2>
                <p>This website is operated by <?= e($company['name']) ?>, a company registered in Dubai, United Arab Emirates, with its office at <?= e($company['address']) ?>. For any privacy question, contact <a href="mailto:<?= e($company['email']) ?>"><?= e($company['email']) ?></a>.</p>

                <h2>Information we collect</h2>
                <p>When you send a message through our contact form, we collect the name, email address, telephone number, company name and message you choose to provide, along with technical information such as your IP address and browser user agent for security and abuse prevention.</p>
                <p>When an account is created for you, we store your name, email address, a securely hashed password, your role and account activity. We never store passwords in readable form.</p>
                <p>We may also collect basic technical information required to serve the website securely, such as session identifiers used to keep you signed in.</p>

                <h2>How we use it</h2>
                <ul>
                    <li>To respond to your enquiry and maintain our business relationship.</li>
                    <li>To create, secure and administer partner, supplier and customer accounts.</li>
                    <li>To protect the website against fraud, abuse and unauthorised access.</li>
                    <li>To keep records required for legitimate business and legal purposes.</li>
                </ul>

                <h2>Legal basis and retention</h2>
                <p>We process information because it is necessary to respond to your request, perform a contract or take steps at your request, comply with legal obligations, and pursue legitimate business interests such as security and record-keeping. We retain enquiry and account records only for as long as needed for those purposes or as required by applicable law.</p>

                <h2>Security</h2>
                <p>We apply administrative and technical safeguards, including encrypted password hashing, access controls, session protection and restricted storage for client documents. No method of transmission over the internet is completely secure, but we work to protect information against unauthorised access, alteration or loss.</p>

                <h2>Sharing</h2>
                <p>We do not sell personal information. Information may be shared with service providers who support our website and operations, where required by law, or where necessary to protect our rights. Access is limited to people who need it.</p>

                <h2>Your choices</h2>
                <p>You may ask us to access, correct or delete the personal information we hold about you, or to stop using it for direct communication. Contact us at <a href="mailto:<?= e($company['email']) ?>"><?= e($company['email']) ?></a> and we will respond within a reasonable period. Account holders can update their name, email and password from the account settings page.</p>

                <h2>Cookies and sessions</h2>
                <p>We use a strictly necessary session cookie to keep signed-in users authenticated and to protect forms against cross-site request forgery. We do not use advertising or third-party tracking cookies on this site.</p>

                <h2>Changes</h2>
                <p>We may update this notice from time to time. The revised version will be published on this page with a new date.</p>
            </div>
        </div>
    </div>
</section>
<?php require includes_path('footer.php');
