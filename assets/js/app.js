/* White Sea Star — interface behaviour. No inline handlers, no third-party scripts. */
(() => {
    'use strict';

    const root = document.documentElement;
    root.classList.remove('no-js');
    root.classList.add('js');

    const $ = (selector, scope = document) => scope.querySelector(selector);
    const $$ = (selector, scope = document) => Array.from(scope.querySelectorAll(selector));

    /* Header state ------------------------------------------------------- */
    const header = $('[data-header]');
    if (header) {
        const onScroll = () => header.classList.toggle('is-scrolled', window.scrollY > 12);
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
    }

    /* Mobile navigation -------------------------------------------------- */
    const navToggle = $('[data-nav-toggle]');
    const siteNav = $('#site-nav');

    const closeNav = () => {
        if (!navToggle || !siteNav) return;
        navToggle.setAttribute('aria-expanded', 'false');
        navToggle.setAttribute('aria-label', 'Open menu');
        siteNav.classList.remove('is-open');
        document.body.classList.remove('nav-open');
    };

    if (navToggle && siteNav) {
        navToggle.addEventListener('click', () => {
            const opening = navToggle.getAttribute('aria-expanded') !== 'true';
            navToggle.setAttribute('aria-expanded', String(opening));
            navToggle.setAttribute('aria-label', opening ? 'Close menu' : 'Open menu');
            siteNav.classList.toggle('is-open', opening);
            document.body.classList.toggle('nav-open', opening);
        });

        $$('.site-nav__link', siteNav).forEach((link) => link.addEventListener('click', closeNav));

        const navClose = $('[data-nav-close]');
        if (navClose) navClose.addEventListener('click', closeNav);

        document.addEventListener('click', (event) => {
            if (!document.body.classList.contains('nav-open')) return;
            if (siteNav.contains(event.target) || navToggle.contains(event.target)) return;
            closeNav();
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') closeNav();
        });
        window.addEventListener('resize', () => {
            if (window.innerWidth > 1024) closeNav();
        });
    }

    /* Panel navigation --------------------------------------------------- */
    const panelToggle = $('[data-panel-toggle]');
    const panelSidebar = $('[data-panel-sidebar]');
    const panelOverlay = $('[data-panel-overlay]');

    const closePanel = () => {
        if (!panelToggle || !panelSidebar) return;
        panelToggle.setAttribute('aria-expanded', 'false');
        panelSidebar.classList.remove('is-open');
        if (panelOverlay) panelOverlay.hidden = true;
        document.body.classList.remove('panel-open');
    };

    if (panelToggle && panelSidebar) {
        panelToggle.addEventListener('click', () => {
            const opening = panelToggle.getAttribute('aria-expanded') !== 'true';
            panelToggle.setAttribute('aria-expanded', String(opening));
            panelSidebar.classList.toggle('is-open', opening);
            if (panelOverlay) panelOverlay.hidden = !opening;
            document.body.classList.toggle('panel-open', opening);
        });

        if (panelOverlay) panelOverlay.addEventListener('click', closePanel);

        const panelClose = $('[data-panel-close]');
        if (panelClose) panelClose.addEventListener('click', closePanel);

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') closePanel();
        });
        window.addEventListener('resize', () => {
            if (window.innerWidth > 1024) closePanel();
        });
    }

    /* Password visibility ------------------------------------------------ */
    $$('[data-password-toggle]').forEach((button) => {
        const input = document.getElementById(button.dataset.passwordToggle);
        if (!input) return;

        button.addEventListener('click', () => {
            const showing = input.type === 'text';
            input.type = showing ? 'password' : 'text';
            button.classList.toggle('is-visible', !showing);
            button.setAttribute('aria-pressed', String(!showing));
            button.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
        });
    });

    /* Password generator ------------------------------------------------ */
    $$('[data-generate-password]').forEach((button) => {
        button.addEventListener('click', () => {
            const target = document.getElementById(button.dataset.generatePassword);
            if (!target || !window.crypto || !window.crypto.getRandomValues) return;

            const lower = 'abcdefghjkmnpqrstuvwxyz';
            const upper = 'ABCDEFGHJKMNPQRSTUVWXYZ';
            const digits = '23456789';
            const symbols = '!@#$%&*+-?';
            const all = lower + upper + digits + symbols;
            const random = (max) => {
                const values = new Uint32Array(1);
                window.crypto.getRandomValues(values);
                return values[0] % max;
            };
            const chars = [
                lower[random(lower.length)],
                upper[random(upper.length)],
                digits[random(digits.length)],
                symbols[random(symbols.length)],
            ];

            while (chars.length < 20) chars.push(all[random(all.length)]);

            for (let i = chars.length - 1; i > 0; i -= 1) {
                const j = random(i + 1);
                [chars[i], chars[j]] = [chars[j], chars[i]];
            }

            const password = chars.join('');
            target.value = password;

            const confirmation = document.getElementById(target.id + '_confirmation');
            if (confirmation) confirmation.value = password;

            [target, confirmation].forEach((input) => {
                if (input) input.type = 'text';
            });

            const original = button.textContent;
            button.textContent = 'Password generated';
            window.setTimeout(() => { button.textContent = original; }, 2400);
        });
    });

    /* File inputs -------------------------------------------------------- */
    $$('[data-file-input]').forEach((input) => {
        const drop = input.closest('[data-file-drop]');
        const label = drop ? $('[data-file-name]', drop) : null;

        const update = () => {
            if (!label) return;
            const file = input.files && input.files[0];
            if (!file) return;
            const size = file.size >= 1024 * 1024
                ? (file.size / (1024 * 1024)).toFixed(1) + ' MB'
                : Math.max(1, Math.round(file.size / 1024)) + ' KB';
            label.textContent = file.name + ' · ' + size;
            if (drop) drop.classList.add('has-file');
        };

        input.addEventListener('change', update);

        if (drop) {
            ['dragenter', 'dragover'].forEach((name) => drop.addEventListener(name, (event) => {
                event.preventDefault();
                drop.classList.add('is-dragging');
            }));
            ['dragleave', 'drop'].forEach((name) => drop.addEventListener(name, (event) => {
                event.preventDefault();
                drop.classList.remove('is-dragging');
            }));
        }
    });

    /* Confirmation prompts ---------------------------------------------- */
    $$('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const message = form.dataset.confirm || 'Are you sure?';
            if (!window.confirm(message)) event.preventDefault();
        });
    });

    /* Flash messages ----------------------------------------------------- */
    $$('[data-flash]').forEach((flash) => {
        const close = $('[data-flash-close]', flash);

        const dismiss = () => {
            flash.classList.add('is-leaving');
            window.setTimeout(() => flash.remove(), 260);
        };

        if (close) close.addEventListener('click', dismiss);
        window.setTimeout(dismiss, 6500);
    });

    /* Scroll reveal ------------------------------------------------------ */
    const revealItems = $$('.reveal');

    if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-inview');
                    observer.unobserve(entry.target);
                }
            });
        }, { rootMargin: '0px 0px -8% 0px', threshold: 0.06 });

        revealItems.forEach((item) => observer.observe(item));
    } else {
        revealItems.forEach((item) => item.classList.add('is-inview'));
    }

    /* Mark current year where needed (none currently) -------------------- */
})();
