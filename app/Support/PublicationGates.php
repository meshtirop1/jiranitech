<?php

namespace App\Support;

use App\Enums\ComplianceStatus;
use App\Models\ComplianceClaim;
use App\Models\Insight;
use App\Models\JobOpening;
use App\Models\Metric;
use App\Models\PlatformReference;
use App\Models\Setting;
use App\Models\TeamMember;

/**
 * Live state of the ten publication gates from JTS-WEB-IA-001 section 4.
 *
 * The gates are enforced in the models and templates; this class only reports on them,
 * so the console shows what the site is actually doing rather than a separate checklist
 * that can drift out of step with it.
 *
 * @phpstan-type Gate array{
 *     id: string,
 *     title: string,
 *     closed: bool,
 *     detail: string,
 *     where: string,
 *     route: string|null,
 *     blocking: bool
 * }
 */
class PublicationGates
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            self::g01(),
            self::g02(),
            self::g03(),
            self::g04(),
            self::g05(),
            self::g06(),
            self::g07(),
            self::g08(),
            self::g09(),
            self::g10(),
        ];
    }

    public static function openCount(): int
    {
        return count(array_filter(self::all(), fn (array $g) => ! $g['closed']));
    }

    public static function blockingCount(): int
    {
        return count(array_filter(self::all(), fn (array $g) => ! $g['closed'] && $g['blocking']));
    }

    /**
     * @return array<string, mixed>
     */
    private static function gate(string $id, string $title, bool $closed, string $detail, string $where, ?string $route, bool $blocking = false): array
    {
        return compact('id', 'title', 'closed', 'detail', 'where', 'route', 'blocking');
    }

    private static function g01(): array
    {
        $unpublished = Metric::query()->where('is_published', false)->count();

        return self::gate(
            'G-01',
            'Hero metrics substantiated',
            $unpublished === 0,
            $unpublished === 0
                ? 'Every metric carries a basis and is published.'
                : "{$unpublished} metric(s) withheld until someone records the basis for them.",
            'Homepage hero',
            'admin.metrics.index',
        );
    }

    private static function g02(): array
    {
        $certified = ComplianceClaim::query()->where('status', ComplianceStatus::Certified)->count();
        $total = ComplianceClaim::query()->count();

        return self::gate(
            'G-02',
            'Compliance status accurate',
            $certified > 0,
            $certified > 0
                ? "{$certified} of {$total} standards certified; the rest render as aligned."
                : "No standard is certified, so all {$total} render \"Aligned — not certified\". That is correct unless you hold certificates.",
            'Assurance strip, governance register',
            'admin.compliance.index',
        );
    }

    private static function g03(): array
    {
        $hidden = PlatformReference::query()->where('cleared_for_disclosure', false)->count();

        return self::gate(
            'G-03',
            'Platform figures cleared',
            $hidden === 0,
            $hidden === 0
                ? 'All platform scale figures are cleared for disclosure.'
                : "{$hidden} platform(s) hide their scale figures pending written clearance from the group.",
            'Platform reference pages',
            'admin.platforms.index',
        );
    }

    private static function g04(): array
    {
        $published = Metric::query()->where('key', 'rate-differential')->where('is_published', true)->exists();

        return self::gate(
            'G-04',
            'Cost-advantage claim has a basis',
            $published,
            $published
                ? 'A rate differential is published with its comparison basis.'
                : 'No figure is published, so the page states the advantage qualitatively. That is safe; add a metric keyed "rate-differential" only with a stated comparison basis.',
            'Homepage, regional advantage',
            'admin.metrics.index',
        );
    }

    private static function g05(): array
    {
        return self::gate(
            'G-05',
            'SLA targets ratified',
            (bool) Setting::get('gate_g05_ratified'),
            Setting::get('gate_g05_ratified')
                ? 'SLA tiers confirmed against the underlying provider SLAs.'
                : 'SLA tiers still render as a structural illustration. Confirm they are achievable on your cloud providers and contractually bound, then mark ratified.',
            'Homepage, service pages, delivery model',
            'admin.settings.edit',
            blocking: true,
        );
    }

    private static function g06(): array
    {
        return self::gate(
            'G-06',
            'Data protection instruments signed off',
            (bool) Setting::get('gate_g06_counsel_signed_off'),
            Setting::get('gate_g06_counsel_signed_off')
                ? 'Counsel and the Data Protection Officer have signed off the data protection instruments.'
                : 'The addendum and its sub-processor register are drafted and published as a draft. The '
                    .'privacy notice is not. Neither is executed: until counsel and the DPO record sign-off '
                    .'here, the pages stating that a Data Processing Addendum governs processing are still '
                    .'ahead of the instrument.',
            'Legal pages, governance',
            'admin.settings.edit',
            blocking: true,
        );
    }

    private static function g07(): array
    {
        $address = config('company.registered_address');
        $registration = config('company.parent.registration_number');
        $email = config('company.email.enquiries');

        $missing = collect([
            'registered office' => $address,
            'registration number' => $registration,
            'enquiries address' => $email,
        ])->filter(fn ($v) => blank($v))->keys();

        return self::gate(
            'G-07',
            'Corporate identity published',
            $missing->isEmpty(),
            $missing->isEmpty()
                ? 'Registered office, company number and contact route all published.'
                : 'Missing: '.$missing->implode(', ').'. The footer shows a marker on every page until these are set.',
            'Footer, engagement desk',
            'admin.settings.edit',
            blocking: true,
        );
    }

    private static function g08(): array
    {
        $published = Insight::query()->published()->count();

        return self::gate(
            'G-08',
            'Insights rail populated',
            $published >= 3,
            $published >= 3
                ? "{$published} insights published; the homepage rail renders."
                : "Only {$published} published. The homepage rail suppresses itself below three.",
            'Homepage, insights index',
            'admin.insights.index',
        );
    }

    private static function g09(): array
    {
        $unannounced = TeamMember::query()->whereNull('name')->count();
        $total = TeamMember::query()->count();

        return self::gate(
            'G-09',
            'Leadership appointments named',
            $unannounced === 0,
            $unannounced === 0
                ? "All {$total} posts have a named appointee."
                : "{$unannounced} of {$total} posts show \"Appointment to be announced\". Procurement reviewers verify these names.",
            'Leadership page',
            'admin.team.index',
            blocking: true,
        );
    }

    private static function g10(): array
    {
        $draft = JobOpening::query()->where('is_published', false)->count();
        $live = JobOpening::query()->where('is_published', true)->count();

        return self::gate(
            'G-10',
            'Vacancies confirmed',
            $draft === 0,
            $draft === 0
                ? "{$live} role(s) advertised, none held back."
                : "{$draft} role definition(s) unpublished. Publish only roles that are genuinely open and funded.",
            'Careers page',
            'admin.jobs.index',
        );
    }
}
