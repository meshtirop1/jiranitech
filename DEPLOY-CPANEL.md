# Deploying to cPanel

Jiranisoko Tech Solutions — Laravel 13, PHP 8.3+, MySQL.

The application is built so that a shared cPanel account can run it with no Node
runtime, no queue worker, no Redis and no persistent background process. Compiled
CSS, JS and fonts ship in `public/build`; sessions, cache and queue all run on the
database that cPanel already gives you.

---

## 1. Server requirements

Set these in **cPanel → Select PHP Version** before uploading.

| Requirement | Value |
| --- | --- |
| PHP | 8.3 or 8.4 |
| Database | MySQL 5.7+ / MariaDB 10.3+ |
| Extensions | `bcmath` `ctype` `curl` `dom` `fileinfo` `json` `mbstring` `openssl` `pcre` `pdo_mysql` `session` `tokenizer` `xml` |
| Apache modules | `mod_rewrite` |

If `mod_rewrite` is unavailable the site will serve the homepage and 404 everything
else. That is the first thing to check if only `/` works.

---

## 2. Choose a layout

### Option A — point the document root at `public/` (recommended)

Upload the application **outside** `public_html`, then repoint the domain.

```
/home/<account>/
├── jiranisoko-tech/          <- the whole application
│   ├── app/  bootstrap/  config/  database/  routes/
│   ├── public/               <- document root points here
│   ├── storage/  vendor/
│   └── .env
└── public_html/              <- unused for this domain
```

In **cPanel → Domains**, edit the domain and set its document root to
`jiranisoko-tech/public`. Nothing above `public/` is then reachable over HTTP.

This is the only layout that is secure by construction. Use it if your host allows
editing the document root — most do, for addon and subdomains at minimum.

### Option B — `public_html` is fixed and cannot be repointed

Put the application alongside `public_html` and move only the public directory's
contents into it.

```
/home/<account>/
├── jiranisoko-tech/          <- application, no public/ directory needed
└── public_html/              <- contents of public/ live here
    ├── build/
    ├── .htaccess
    ├── index.php             <- edited, see below
    ├── favicon.ico
    └── robots.txt
```

Then replace the two `require` paths in `public_html/index.php` so they resolve to
the application directory:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Edited for cPanel: the application lives one level up, outside public_html.
$app = __DIR__.'/../jiranisoko-tech';

if (file_exists($maintenance = $app.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $app.'/vendor/autoload.php';

(require_once $app.'/bootstrap/app.php')
    ->handleRequest(Request::capture());
```

Leave `public/.htaccess` exactly as shipped; it becomes `public_html/.htaccess`.

> The root `.htaccess` in this repository is a safety net for the third case — the
> whole application accidentally uploaded into `public_html`. It denies access to
> `.env`, `storage`, `vendor` and the source tree. It is a backstop, not a layout.
> Do not rely on it.

---

## 3. Upload

Package locally first, so the release carries the compiled assets and (if your host
has no Composer) the dependencies:

```bash
npm run build
composer install --no-dev --optimize-autoloader
```

Then upload everything **except** `node_modules/`, `tests/`, `.git/` and any local
`.env`. A zip uploaded through **cPanel → File Manager** and extracted in place is
faster and far more reliable than FTP for a tree this size.

`public/build` must be included. The server has no Node, so it cannot regenerate the
stylesheet, the JavaScript or the self-hosted font files. A missing `public/build`
produces an unstyled page and a `Vite manifest not found` error.

---

## 4. Database

**cPanel → MySQL Databases**: create a database and a user, then add the user to the
database with **ALL PRIVILEGES**. cPanel prefixes both names with the account name.

---

## 5. Configure and initialise

Using **cPanel → Terminal** (or SSH):

```bash
cd ~/jiranisoko-tech
cp .env.example .env
```

Edit `.env` and set at minimum:

- `APP_URL` — the real https URL
- `APP_ENV=production` and `APP_DEBUG=false`
- `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` — the prefixed cPanel values
- the `COMPANY_*` block — see the publication gates below

Then:

```bash
php artisan key:generate
php artisan migrate --force --seed
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

`--seed` loads the capability taxonomy: six pillars, twenty-two service lines, three
engagement models, four industries, the compliance register and the metric records.
Without it every page renders empty.

### No Terminal on your plan?

Everything above except `key:generate` can be done from the File Manager and
phpMyAdmin, but the migrations cannot. Ask your host to enable Terminal or SSH — it
is a standard request. Failing that, run the migrations locally against a database
you can reach, export the schema and data with `mysqldump`, and import the `.sql`
through **cPanel → phpMyAdmin**. Generate `APP_KEY` locally with
`php artisan key:generate --show` and paste the value into `.env`.

---

## 6. Permissions

```bash
chmod -R 775 storage bootstrap/cache
```

If the site returns a 500 immediately after deployment, this is the most likely
cause. Check `storage/logs/laravel.log`.

---

## 7. Verify

```bash
curl -sI https://your-domain/ | head -1                       # expect 200
curl -s https://your-domain/services | grep -c "Pillar"       # expect > 0
curl -sI https://your-domain/contact/request-for-proposal | head -1
```

Then in a browser: submit the RFP form and confirm you land on a confirmation page
carrying a `JTS-RFP-…` reference. That exercises the database write, the session,
CSRF and signed URLs in one action.

---

## 8. Publication gates — do these before launch

The site enforces these in code. Until each is resolved the affected content either
stays hidden or renders a visible marker, which is deliberate: the alternative is
shipping an unsubstantiated claim into a procurement review.

| Gate | What is outstanding | Where it shows |
| --- | --- | --- |
| **G-01** | Two hero metrics ship unpublished — contracted availability and group platform count. Substantiate each, record the basis on the `metrics` row, set `is_published`. | Hero metric bar |
| **G-02** | No compliance claim is set to *Certified*. Every standard renders "Aligned — not certified". Promote a row only when the certificate is held and in date. | Assurance strip, `/company/governance` |
| **G-03** | Marketplace scale figures are not cleared for disclosure. Set `cleared_for_disclosure` and replace the placeholders once the group clears them in writing. | `/platforms/jiranisoko-marketplace` |
| **G-04** | The cost-advantage claim carries no figure or comparison basis. Publish it with a basis or leave the qualitative wording. | Homepage, regional advantage |
| **G-05** | SLA tier values are a structural illustration. Ratify against the underlying cloud providers' composite SLAs before they are stated as commitments. | Homepage, every service page, `/company/delivery-model` |
| **G-06** | The Data Processing Addendum and Privacy Notice have not been drafted. Both need counsel. Several pages already assert a DPA exists. | `/legal/*` |
| **G-07** | Registered office, registration number and contact addresses are unset. The footer shows a marker on every page until they are. | Footer, `/contact/engagement-desk` |
| **G-08** | Three engineering notes are published so the insights rail renders. Case studies must not be added until they describe real engagements. | Homepage, `/insights` |
| **G-09** | All eight leadership posts render "Appointment to be announced". Set `name` and `photo_path` on each `team_members` row for the real appointee, with their consent to external publication. | `/company/leadership` |
| **G-10** | Four job listings are seeded but unpublished, so the careers page shows its empty state. Confirm each role is open and funded, set `posted_at`, then `is_published`. | `/company/careers` |

G-07 is the fastest to close and the most visible — it is `.env` values only.

G-09 and G-10 are the two that most often get shipped wrong. Procurement reviewers
verify the names on a leadership page, and a candidate who applies to a role that was
never open tells other engineers about it. Both pages are built to look finished
without the content, so there is no design pressure to fill them with invention.

---

## 9. Updating an existing deployment

```bash
cd ~/jiranisoko-tech
php artisan down                    # optional
# upload changed files, including public/build if assets changed
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
php artisan up
```

Always re-run the three cache commands after changing `.env` or any file in
`config/`. A cached config ignores `.env` entirely, which is the usual explanation
for "I changed the setting and nothing happened".

---

## 10. Hardening

- **HTTPS.** Issue the certificate in **cPanel → SSL/TLS Status** (AutoSSL) and force
  redirect. `SESSION_SECURE_COOKIE=true` in `.env.example` assumes HTTPS; the site
  will not hold a session over plain HTTP with it set.
- **Never leave `APP_DEBUG=true`.** A stack trace discloses paths, configuration keys
  and query structure.
- **Confirm `.env` is unreachable.** `curl -sI https://your-domain/.env` must not
  return 200. If it does, your layout is wrong — go back to section 2.
- **Back up.** The `rfp_submissions` table holds live enquiries and is the only table
  whose contents cannot be regenerated by re-seeding.

---

## 11. Optional — scheduled tasks

The site needs no cron. If you later add scheduled work, add one cron entry in
**cPanel → Cron Jobs**, running each minute:

```
/usr/local/bin/php /home/<account>/jiranisoko-tech/artisan schedule:run >> /dev/null 2>&1
```

Confirm the PHP binary path with `which php` in Terminal first; on many cPanel hosts
it is `/opt/cpanel/ea-php83/root/usr/bin/php`.
