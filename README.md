# Jiranisoko Tech Solutions — corporate website

The public website for Jiranisoko Tech Solutions, the enterprise technology division of
Jiranisoko Market Ltd, Eldoret, Kenya.

Laravel 13 · PHP 8.3+ · MySQL · built to run on shared cPanel hosting with no Node
runtime, no queue worker and no Redis.

---

## Quick start

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install && npm run build
php artisan serve
```

`--seed` loads the site's content: six capability pillars, twenty-two service lines,
three engagement models, four industries, the compliance register, the metric records,
eight leadership posts and four job role definitions. Without it every page renders empty.

---

## What is here

| Area | Detail |
| --- | --- |
| Pages | 41 routes — homepage, service taxonomy, 22 service pages, engagement models, industries, platforms, insights, company, contact, RFP intake, legal |
| Content model | Structured Eloquent types rather than a page builder, so twenty-two service pages stay comparable and maintainable |
| Conversion | Structured RFP intake with track routing, validation, and a signed, expiring confirmation link |
| Tests | 35 feature tests, including a link-integrity crawl that asserts no page ships a dead or placeholder link |

Architecture, URL taxonomy and the homepage section specification are recorded in
document **JTS-WEB-IA-001**; page templates carry their reference (T-01 … T-14) in a
comment at the top of the file.

---

## Publication gates

Ten content gates are enforced in code rather than by editorial discipline. Until each
is resolved the affected content either stays hidden or renders a visible marker.

That is deliberate. The alternative is shipping an unsubstantiated availability figure,
an uncertified compliance claim or an invented executive into a procurement review,
which is the fastest way for a firm this size to be disqualified.

- **G-01** hero metrics carry a `basis` and stay unpublished without one
- **G-02** compliance claims render "Aligned — not certified" until a certificate exists
- **G-03** platform scale figures hide until cleared for disclosure
- **G-04** the cost-advantage claim needs a comparison basis or stays qualitative
- **G-05** SLA targets are illustrative until ratified against provider SLAs
- **G-06** the DPA and privacy notice need counsel
- **G-07** registered office and company number show a marker until configured
- **G-08** the insights rail suppresses itself below three published items
- **G-09** leadership posts show "Appointment to be announced" until a name is supplied
- **G-10** job listings stay unpublished until the role is confirmed open and funded

See [DEPLOY-CPANEL.md](DEPLOY-CPANEL.md) for what each one needs and where it appears.

---

## Design

One typeface across the site — **PT Sans**, self-hosted from the build output so no
visitor IP address is disclosed to a third-party font host. Palette is pine `#0E5C45`
as the institutional primary, brass `#8E6520` for structural markers, clay `#8E2F1F`
reserved for conversion and compliance flags. Light and dark are both defined at token
level in `resources/css/app.css`.

---

## Deployment

See **[DEPLOY-CPANEL.md](DEPLOY-CPANEL.md)**. In short: point the domain's document root
at `public/`, upload everything else above it, and run migrations from cPanel Terminal.

`public/build` is committed on purpose — the server has no Node and cannot regenerate
the stylesheet, JavaScript or font files. Run `npm run build` before packaging a release.

---

## Tests

```bash
php artisan test
vendor/bin/pint --test
```
