# White Sea Star — website & partner portal

A small, flat PHP application: one front controller, a route table, a handful of
page files and a shared `includes/` folder. No framework, no build step, no
Composer. Upload it to one.com and it runs.

The layout and design system are unchanged. The geometric WSS monogram is the
logo in the header, footer and sign-in screens, with the gold starfish kept as
the brand seal. The public copy presents White Sea Star as a Dubai-based global sourcing
and supply partner, while the registered company name remains in the legal
details.

---

## Folder structure

```
apple-touch-icon.png     Home-screen icon
site.webmanifest         Web app manifest
assets/                  Public CSS, JS and images
  css/app.css            Design system (do not rename)
  js/app.js              Navigation, password toggles, uploads, reveals
  img/                   WSS monogram, starfish seal, social image, favicon
favicon.svg              Browser icon (embedded PNG tile)
includes/                Shared code — blocked from the web
  bootstrap.php          Configuration, session, headers, error handling
  config.php             App + database settings
  functions.php          Helpers (URLs, CSRF, validation, formatting)
  db.php                 PDO connection and query helpers
  data.php               All database queries
  auth.php               Sign-in, sessions, access guards, throttling
  uploads.php            Private file validation and storage
  content.php            All public copy, services, capabilities, values, markets, FAQs
  icons.php              Inline SVG icon set
  head.php               Shared <head> + SEO metadata and JSON-LD
  header.php / footer.php            Public layout
  panel-header.php / panel-footer.php Member/admin layout
  auth-header.php / auth-footer.php   Sign-in layout
  flash.php, resource-card.php, error.php
  database.sql           Tables + first super-admin (import once)
index.php                Front controller (all requests)
pages/                   One file per page — blocked from the web
  home.php, about.php, services.php, service.php, contact.php, privacy.php
  login.php, logout.php
  member-dashboard.php, member-documents.php, profile.php, download.php
  admin-dashboard.php, admin-members.php, admin-member-form.php,
  admin-member-status.php, admin-member-delete.php,
  admin-documents.php, admin-document-form.php,
  admin-document-publish.php, admin-document-delete.php,
  admin-enquiries.php, admin-enquiry-handled.php, admin-audit.php
router.php               Route table and URL matcher
robots.php               /robots.txt
sitemap.php              /sitemap.xml
storage/                 Private uploads and logs — blocked from the web
  uploads/
  logs/
.htaccess                Clean URLs + folder protection
README.md                This file
```

---

## Requirements

- PHP 8.1 or newer
- `pdo_mysql`, `fileinfo` and `mbstring` extensions
- MySQL 8.0+ or MariaDB 10.5+
- Apache with `mod_rewrite` (one.com provides this)

---

## Local development

The app reads a few optional `WSS_*` environment variables. The defaults in
`includes/config.php` are the live one.com database values, so local runs must
override them:

```bash
WSS_DB_HOST=127.0.0.1 \
WSS_DB_NAME=wss_site \
WSS_DB_USER=root \
WSS_DB_PASS= \
WSS_URL=http://127.0.0.1:8095 \
php -S 127.0.0.1:8095 -t . index.php
```

Open <http://127.0.0.1:8095>.

> On macOS, PHP's built-in server needs the router argument (`index.php`) so
> clean URLs work. Real files under `assets/` are served directly.

To use a different port or hostname, change `WSS_URL` to match.

---

## Database setup

1. Create a database in one.com's control panel (or use the existing
   `whiteseastar_comwsssite`).
2. Open **phpMyAdmin → Import** and upload `includes/database.sql`.
3. That creates all five tables and the first super-admin:

   | Email | Password |
   | --- | --- |
   | `admin@whiteseastar.com` | *(in the seed — change it on first sign-in)* |

The file is safe to re-import structurally: tables use `IF NOT EXISTS` and the
admin insert uses `ON DUPLICATE KEY UPDATE`. However, re-importing the full file
also resets the seeded admin password. If the site is already running, use
`includes/database-migrate-roles.sql` instead — it upgrades the old `member`
role to `partner` and adds `supplier` and `customer` without touching accounts,
passwords or content.

Delete both SQL files from the server after a successful import.

If you prefer the command line:

```bash
mysql -h HOST -u USER -p DATABASE < includes/database.sql
```

---

## Deploying to one.com

1. In the one.com file manager, open the web root (`httpd.www`).
2. Upload **the contents of this folder** so that `index.php`, `assets/`,
   `includes/`, `pages/`, `router.php`, `.htaccess`, etc. sit directly in
   `httpd.www/`.
3. Make sure `storage/uploads/` is writable by PHP (usually `755`; if uploads
   fail, try `775`). The folder ships with a deny-all `.htaccess`.
4. Import `includes/database.sql` through phpMyAdmin (see above).
5. Check `includes/config.php`:
   - `WSS_DB_HOST` default: `whiteseastar.com.mysql`
   - `WSS_DB_NAME` / `WSS_DB_USER`: `whiteseastar_comwsssite`
   - `WSS_DB_PASS`: the database password
   - `WSS_URL` default: `https://www.whiteseastar.com`
6. Visit the site, sign in at `/login`, and change the seed password
   immediately from **Account → Change password**.
7. Delete `includes/database.sql` from the server.

No environment file is required on one.com — the defaults are already correct.

---

## Security model

- Every query uses prepared statements (`includes/db.php`).
- All POST forms carry a CSRF token; tokens rotate on sign-in and sign-out.
- Passwords are hashed with Argon2id where available (fallback: PHP default).
  Passwords require 12+ characters with upper/lowercase, a number and a symbol.
- Sign-in is throttled per email (5) and per IP (20) in a 15-minute window.
- Sessions expire after one hour of inactivity and are invalidated when a
  password changes.
- Uploads are checked by MIME type **and** file signature, renamed randomly,
  and stored in `storage/uploads/` outside the public document flow. Downloads
  are streamed through an authenticated route; drafts are admin-only.
- `includes/`, `pages/` and `storage/` are blocked by `.htaccess` and are never
  linked from the browser.
- Security headers (CSP with nonce, HSTS on HTTPS, frame denial, nosniff) are
  sent by `includes/bootstrap.php`.

There is no public registration. Accounts are created by a super-admin under
**Administration → Accounts**.

---

## Partner, supplier and customer portal

One sign-in screen serves every external account role:

| Role | Typical access |
| --- | --- |
| Partner | All published company documents and partner resources |
| Supplier | Company documents, supplier information, compliance and notices |
| Customer | Company documents, service sheets and compliance |
| Super admin | Full administration |

The role is chosen when a super-admin creates the account. It controls the
resource categories shown in the portal and enforced on download. Partners,
suppliers and customers all use `/login`; there are no separate login forms.

---

## Routes

| Method | Path | Access |
| --- | --- | --- |
| GET | `/` | public |
| GET | `/about` | public |
| GET | `/services` | public |
| GET | `/services/{slug}` | public |
| GET/POST | `/contact` | public |
| GET | `/privacy` | public |
| GET | `/robots.txt` | public |
| GET | `/sitemap.xml` | public |
| GET/POST | `/login` | signed-out |
| GET/POST | `/members/login` | legacy alias for `/login` |
| POST | `/logout` | portal account |
| GET | `/members` | portal account |
| GET | `/members/documents` | portal account |
| GET | `/members/documents/{id}/download` | portal account |
| GET/POST | `/profile` | portal account |
| POST | `/profile/password` | portal account |
| GET | `/admin` | super-admin |
| GET/POST | `/admin/members*` | super-admin |
| GET/POST | `/admin/documents*` | super-admin |
| GET/POST | `/admin/enquiries*` | super-admin |
| GET | `/admin/audit` | super-admin |

Add or change routes in `router.php`; each entry points at a file in `pages/`.

The old `/sectors` and `/expertise` URLs permanently redirect to `/services`
so existing links and search results do not break.

---

## Editing content

- **Public copy, services, capabilities, values, markets, FAQs:** `includes/content.php`
- **Company name, phone, email, address:** the `company` array in
  `includes/content.php`
- **Typography, colours and layout:** `assets/css/app.css` (Source Serif 4 for
  headings, Manrope for interface text; emphasis uses colour and weight rather
  than italics)
- **Small interactions:** `assets/js/app.js`
- **Database credentials or upload limits:** `includes/config.php`

After editing CSS or JS, bump `asset_version` in `includes/config.php` (or set
`WSS_ASSET_VERSION`) so browsers fetch the new file.

---

## Responsive behaviour

The layout adapts in layers, so the desktop composition is unchanged at normal
laptop widths:

| Width | Behaviour |
| --- | --- |
| 320–480px | Single-column grids, full-width hero buttons, 16px form fields (prevents iOS zoom), 40–48px tap targets, scrollable data tables with edge fades |
| 481–760px | Single-column cards and footer, drawer navigation, stacked panel headers |
| 761–1024px | Two-column stat cards, off-canvas portal sidebar, condensed navigation |
| 1025–1919px | Original desktop composition — 1240px container with the original side spacing |
| 1920px+ | Wider 1600px content column with generous side spacing; root font-size steps up 3%; hero artwork is capped by viewport height so the stats stay in view |
| 2200px+ | Content widens to 1760px; root font-size steps up 6% |
| 2560px+ | Content widens to 1960px; root font-size steps up 12.5% for high-density / 4K displays |
| 3000px+ | Content widens to 2200px, keeping comfortable margins on ultra-wide screens |

Root font scaling is relative, so a visitor's browser font-size preference is
still respected. Reduced-motion, high-contrast and reduced-transparency
preferences are also handled.

---

## SEO

The application ships with the technical SEO work already done:

- **One canonical address.** `.htaccess` redirects `http://` and the non-`www`
  host to `https://www.whiteseastar.com`, and strips trailing slashes.
- **Unique metadata.** Every public page sets its own title, meta description,
  canonical URL and Open Graph / Twitter tags.
- **Structured data.** JSON-LD is emitted on every public page:
  `Organization` + `ProfessionalService`, `WebSite`, `WebPage`,
  `BreadcrumbList`, plus `Service`, `FAQPage`, `AboutPage` or `ContactPage`
  where relevant.
- **Sitemap.** `/sitemap.xml` lists all public pages and uses real file
  modification dates. `/robots.txt` points to it and blocks only private areas.
- **Keyword-focused pages.** The five service pillars each have a dedicated page:
  `/services/global-sourcing`, `/services/project-supply`,
  `/services/industrial-commercial`, `/services/import-export-logistics` and
  `/services/partnerships`.
- **Internal linking.** The home page, services page and footer link to every
  service pillar.
- **Performance.** Text compression and long-lived caching for versioned assets
  are enabled in `.htaccess`; fonts use `display=swap`.

### Google Search Console

Code cannot force Google to index a site. Do these steps once after deployment:

1. Open <https://search.google.com/search-console> and add the **Domain**
   property `whiteseastar.com` (DNS verification is the most reliable method).
2. If you prefer the HTML tag method, paste only the token into
   `includes/config.php`:
   ```php
   'seo' => [
       'google_verification' => 'your-token-here',
       ...
   ],
   ```
   or set `WSS_GOOGLE_VERIFICATION` in the environment. The same applies to
   Bing with `WSS_BING_VERIFICATION`.
3. In **Sitemaps**, submit `https://www.whiteseastar.com/sitemap.xml`.
4. Use **URL Inspection** on `/`, `/about`, `/services` and each
   `/services/...` page, then click **Request indexing**.
5. Check **Page indexing** after a few days. New sites and new domains can take
   several weeks to appear even when everything is correct.
6. Create or claim a **Google Business Profile** for the Dubai office and add
   the website URL. Local listings are one of the strongest signals for
   “global sourcing company Dubai” searches.
7. Earn a few genuine links (LinkedIn company page, trade directories,
   partner sites). A new domain with no links is slow to rank regardless of
   on-page SEO.

Do not add `noindex` to public pages. Member/admin pages are intentionally
`noindex, nofollow`.

---

## Troubleshooting

| Symptom | Likely cause |
| --- | --- |
| 500 on every page | Database credentials in `includes/config.php`, or `pdo_mysql` missing |
| 404 on `/about` but `/index.php` works | `mod_rewrite` / `.htaccess` not active |
| Uploads fail | `storage/uploads/` is not writable |
| CSS or images 404 | `assets/` was not uploaded next to `index.php` |
| "Session expired" on a form | Cookies blocked, or the page sat open too long — refresh |
| Signed out unexpectedly | Session expired after one hour of inactivity, or the password changed |

Error details are written to the PHP error log. Set `WSS_DEBUG=true` locally to
show the message on the error page (never enable this in production).
