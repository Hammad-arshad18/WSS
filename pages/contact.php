<?php

declare(strict_types=1);

$sectors = (array) content('sectors', []);
$subjects = ['General enquiry'];

foreach ($sectors as $sector) {
    $subjects[] = $sector['name'];
}

$subjects[] = 'Partnership';

if (is_post()) {
    csrf_verify();

    // Honeypot: legitimate visitors never fill this hidden field.
    if (input_string('website') !== '') {
        flash('success', 'Thank you — your message has been received.');
        redirect('/contact');
    }

    $ip = client_ip();
    $limit = (int) config('security.contact_max_per_ip', 4);
    $window = (int) config('security.contact_window_hours', 1);

    if (enquiry_count_recent_by_ip($ip, $window) >= $limit) {
        flash('error', 'We have received several messages from this connection. Please try again later or email us directly.');
        redirect('/contact#form');
    }

    $data = [
        'name' => input_string('name'),
        'email' => mb_strtolower(input_string('email')),
        'phone' => input_string('phone'),
        'company' => input_string('company'),
        'subject' => input_string('subject'),
        'message' => input_string('message'),
    ];

    $errors = validate($data, [
        'name' => 'required|min:2|max:120',
        'email' => 'required|email|max:190',
        'phone' => 'max:40',
        'company' => 'max:150',
        'subject' => 'required|in:' . implode(',', $subjects),
        'message' => 'required|min:20|max:5000',
    ]);

    if ($errors !== []) {
        flash_errors($errors);
        flash_input($data);
        flash('error', 'Please review the highlighted fields and try again.');
        redirect('/contact#form');
    }

    $data['ip'] = $ip;
    $data['user_agent'] = user_agent();

    $id = enquiry_create($data);

    audit_record(null, 'enquiry.created', 'Enquiry #' . $id . ' from ' . $data['email']);

    flash('success', 'Thank you — your message has reached our team. We will be in touch shortly.');
    redirect('/contact?sent=1#form');
}

$selected = input_string('sector');

if (!in_array($selected, $subjects, true)) {
    $selected = 'General enquiry';
}

$title = 'Contact us';
$metaDescription = 'Speak with White Sea Star General Trading L.L.C in Dubai about strategy, market entry and partnership opportunities.';
$bodyClass = 'page-contact';
$company = content('company');
$selectedSubject = $selected;

require includes_path('header.php');
?>
<section class="page-hero page-hero--navy page-hero--compact">
    <div class="page-hero__ambient" aria-hidden="true"></div>
    <div class="container page-hero__inner">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="/">Home</a>
            <?= icon('chevron') ?>
            <span>Contact</span>
        </nav>
        <p class="eyebrow eyebrow--light"><span class="eyebrow__num">03</span> Start a conversation</p>
        <h1 class="page-hero__title">Let's talk about<br><em>what you need.</em></h1>
        <p class="page-hero__lead">A strategy question, a market-entry challenge or a potential partnership — our team in Dubai is ready to talk.</p>
    </div>
</section>

<section class="section section--paper" id="form">
    <div class="container">
        <div class="contact-grid">
            <aside class="contact-info reveal">
                <p class="eyebrow eyebrow--dark">Dubai office</p>
                <h2 class="section-title">Direct lines.<br><em>Real people.</em></h2>
                <p class="contact-info__intro">We aim to respond to every enquiry within one business day.</p>

                <div class="contact-cards">
                    <a class="contact-card" href="<?= e($company['maps_url']) ?>" target="_blank" rel="noopener noreferrer">
                        <span class="contact-card__icon"><?= icon('pin') ?></span>
                        <span class="contact-card__body">
                            <span class="contact-card__label">Visit</span>
                            <strong><?= e($company['address']) ?></strong>
                        </span>
                        <?= icon('external', 'contact-card__arrow') ?>
                    </a>

                    <a class="contact-card" href="tel:<?= e($company['phone_href']) ?>">
                        <span class="contact-card__icon"><?= icon('phone') ?></span>
                        <span class="contact-card__body">
                            <span class="contact-card__label">Call</span>
                            <strong><?= e($company['phone']) ?></strong>
                        </span>
                        <?= icon('arrow', 'contact-card__arrow') ?>
                    </a>

                    <a class="contact-card" href="mailto:<?= e($company['email']) ?>">
                        <span class="contact-card__icon"><?= icon('mail') ?></span>
                        <span class="contact-card__body">
                            <span class="contact-card__label">Email</span>
                            <strong><?= e($company['email']) ?></strong>
                        </span>
                        <?= icon('arrow', 'contact-card__arrow') ?>
                    </a>

                    <div class="contact-card contact-card--static">
                        <span class="contact-card__icon"><?= icon('clock') ?></span>
                        <span class="contact-card__body">
                            <span class="contact-card__label">Office hours</span>
                            <strong><?= e($company['hours']) ?></strong>
                        </span>
                    </div>
                </div>

                <div class="contact-member">
                    <span class="contact-member__icon"><?= icon('lock') ?></span>
                    <div>
                        <strong>Already a partner?</strong>
                        <p>Access reports, insights and company documents in the <a href="/members/login">member portal</a>.</p>
                    </div>
                </div>
            </aside>

            <div class="contact-form-wrap reveal">
                <div class="contact-form-head">
                    <p class="eyebrow eyebrow--dark">Enquiry form</p>
                    <h2>Tell us a little<br><em>about your requirement.</em></h2>
                </div>

                <form class="form" method="post" action="/contact" novalidate>
                    <?= csrf_field() ?>

                    <div class="form__hp" aria-hidden="true">
                        <label for="website">Website</label>
                        <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="form__row">
                        <div class="field<?= form_error('name') !== '' ? ' has-error' : '' ?>">
                            <label for="name">Full name <span aria-hidden="true">*</span></label>
                            <input id="name" name="name" type="text" value="<?= e(old('name')) ?>" required maxlength="120" autocomplete="name">
                            <?php if (form_error('name') !== ''): ?><p class="field__error"><?= e(form_error('name')) ?></p><?php endif; ?>
                        </div>
                        <div class="field<?= form_error('email') !== '' ? ' has-error' : '' ?>">
                            <label for="email">Email address <span aria-hidden="true">*</span></label>
                            <input id="email" name="email" type="email" value="<?= e(old('email')) ?>" required maxlength="190" autocomplete="email">
                            <?php if (form_error('email') !== ''): ?><p class="field__error"><?= e(form_error('email')) ?></p><?php endif; ?>
                        </div>
                    </div>

                    <div class="form__row">
                        <div class="field<?= form_error('phone') !== '' ? ' has-error' : '' ?>">
                            <label for="phone">Phone</label>
                            <input id="phone" name="phone" type="tel" value="<?= e(old('phone')) ?>" maxlength="40" autocomplete="tel">
                            <?php if (form_error('phone') !== ''): ?><p class="field__error"><?= e(form_error('phone')) ?></p><?php endif; ?>
                        </div>
                        <div class="field<?= form_error('company') !== '' ? ' has-error' : '' ?>">
                            <label for="company">Company</label>
                            <input id="company" name="company" type="text" value="<?= e(old('company')) ?>" maxlength="150" autocomplete="organization">
                            <?php if (form_error('company') !== ''): ?><p class="field__error"><?= e(form_error('company')) ?></p><?php endif; ?>
                        </div>
                    </div>

                    <div class="field<?= form_error('subject') !== '' ? ' has-error' : '' ?>">
                        <label for="subject">What is this about? <span aria-hidden="true">*</span></label>
                        <div class="select-wrap">
                            <select id="subject" name="subject" required>
                                <?php foreach ($subjects as $subject): ?>
                                    <option value="<?= e($subject) ?>"<?= selected(old('subject', $selectedSubject), $subject) ?>><?= e($subject) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?= icon('chevron') ?>
                        </div>
                        <?php if (form_error('subject') !== ''): ?><p class="field__error"><?= e(form_error('subject')) ?></p><?php endif; ?>
                    </div>

                    <div class="field<?= form_error('message') !== '' ? ' has-error' : '' ?>">
                        <label for="message">Your message <span aria-hidden="true">*</span></label>
                        <textarea id="message" name="message" rows="6" required maxlength="5000" placeholder="The question you are working through, the market you have in mind and the decision ahead — a few details help us respond properly."><?= e(old('message')) ?></textarea>
                        <?php if (form_error('message') !== ''): ?><p class="field__error"><?= e(form_error('message')) ?></p><?php endif; ?>
                    </div>

                    <div class="form__foot">
                        <button class="btn btn--dark" type="submit">Send enquiry <?= icon('arrow') ?></button>
                        <p>By sending this form you agree to our <a href="/privacy">privacy notice</a>. We never share your details.</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?php require includes_path('footer.php');
