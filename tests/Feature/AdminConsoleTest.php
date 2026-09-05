<?php

namespace Tests\Feature;

use App\Enums\ComplianceStatus;
use App\Models\ComplianceClaim;
use App\Models\JobOpening;
use App\Models\Metric;
use App\Models\PlatformReference;
use App\Models\RfpSubmission;
use App\Models\Setting;
use App\Models\TeamMember;
use App\Models\User;
use App\Support\PublicationGates;
use App\Support\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminConsoleTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    /**
     * @return array<int, string>
     */
    private function guardedRoutes(): array
    {
        return [
            '/admin',
            '/admin/settings',
            '/admin/metrics',
            '/admin/compliance',
            '/admin/platforms',
            '/admin/insights',
            '/admin/team',
            '/admin/jobs',
            '/admin/rfp',
        ];
    }

    public function test_every_console_route_is_closed_to_guests(): void
    {
        $this->seed();

        foreach ($this->guardedRoutes() as $route) {
            $this->get($route)->assertRedirect(route('admin.login'));
        }
    }

    public function test_a_signed_in_non_admin_is_refused(): void
    {
        $this->seed();

        $user = User::factory()->create(['is_admin' => false]);

        foreach ($this->guardedRoutes() as $route) {
            $this->actingAs($user)->get($route)->assertForbidden();
        }
    }

    public function test_setup_creates_the_first_administrator_then_closes_permanently(): void
    {
        $this->seed();

        $this->get('/admin/setup')->assertOk()->assertSee('Create the first administrator');

        $this->post('/admin/setup', [
            'name' => 'A. Kiprotich',
            'email' => 'admin@jiranisoko.com',
            'password' => 'Str0ng!Passphrase#2026',
            'password_confirmation' => 'Str0ng!Passphrase#2026',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertTrue(User::query()->where('email', 'admin@jiranisoko.com')->sole()->is_admin);

        // The window is now shut for good.
        $this->post('/admin/logout');
        $this->get('/admin/setup')->assertNotFound();
        $this->post('/admin/setup', [])->assertNotFound();
    }

    public function test_setup_rejects_a_weak_password(): void
    {
        $this->seed();

        $this->post('/admin/setup', [
            'name' => 'A. Kiprotich',
            'email' => 'admin@jiranisoko.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors('password');

        $this->assertSame(0, User::query()->where('is_admin', true)->count());
    }

    public function test_the_dashboard_reports_the_live_gate_state(): void
    {
        $this->seed();

        $this->actingAs($this->admin())
            ->get('/admin')
            ->assertOk()
            ->assertSee('Publication gates')
            ->assertSee('G-01')
            ->assertSee('G-10');
    }

    public function test_a_metric_cannot_be_saved_without_a_basis(): void
    {
        $this->seed();

        $metric = Metric::query()->first();

        $this->actingAs($this->admin())
            ->put(route('admin.metrics.update', $metric), [
                'label' => $metric->label,
                'value' => $metric->value,
                'basis' => '',
            ])
            ->assertSessionHasErrors('basis');
    }

    public function test_publishing_a_metric_from_the_console_closes_gate_g01(): void
    {
        $this->seed();

        $this->assertGreaterThan(0, collect(PublicationGates::all())->firstWhere('id', 'G-01')['closed'] ? 0 : 1);

        $admin = $this->admin();

        foreach (Metric::query()->where('is_published', false)->get() as $metric) {
            $this->actingAs($admin)->put(route('admin.metrics.update', $metric), [
                'label' => $metric->label,
                'value' => $metric->value,
                'basis' => 'Ratified by the board on 2026-09-01.',
                'substantiation_ref' => 'BOARD-2026-114',
                'is_published' => '1',
            ])->assertSessionHasNoErrors();
        }

        $this->assertTrue(collect(PublicationGates::all())->firstWhere('id', 'G-01')['closed']);
    }

    public function test_a_certified_standard_requires_evidence(): void
    {
        $this->seed();

        $claim = ComplianceClaim::query()->first();

        $this->actingAs($this->admin())
            ->put(route('admin.compliance.update', $claim), [
                'status' => ComplianceStatus::Certified->value,
                'evidence_url' => '',
                'reviewed_on' => now()->toDateString(),
            ])
            ->assertSessionHasErrors('evidence_url');

        $this->assertNotSame(ComplianceStatus::Certified, $claim->fresh()->status);
    }

    public function test_settings_written_in_the_console_reach_the_public_footer(): void
    {
        $this->seed();

        $this->actingAs($this->admin())
            ->put(route('admin.settings.update'), [
                'company_registered_address' => 'Kenyatta Street, Eldoret',
                'company_email_enquiries' => 'enquiries@jiranisoko.com',
                'company_parent_registration_number' => 'PVT-YQ195JQY',
            ])
            ->assertSessionHasNoErrors();

        // config/company.php is overlaid from the settings table on boot. Every real
        // HTTP request boots the framework fresh, so production picks this up with no
        // .env edit and no deploy. Re-running the overlay here reproduces that without
        // rebuilding the application, which would drop the in-memory test database.
        SiteSettings::applyToConfig();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Kenyatta Street, Eldoret')
            ->assertSee('enquiries@jiranisoko.com')
            ->assertDontSee('Gate G-07');
    }

    public function test_the_settings_form_prefills_from_live_config_not_only_the_table(): void
    {
        $this->seed();

        // Nothing is in the settings table yet, but the site is already using values
        // from .env. Showing blanks would invite an administrator to save over them.
        $this->assertSame(0, Setting::query()->count());

        $this->actingAs($this->admin())
            ->get(route('admin.settings.edit'))
            ->assertOk()
            ->assertSee('value="Jiranisoko Market Ltd"', escape: false)
            ->assertSee('value="PVT-YQ195JQY"', escape: false)
            ->assertSee('value="Eldoret"', escape: false);
    }

    public function test_settings_close_gate_g07(): void
    {
        $this->seed();

        $this->assertFalse(collect(PublicationGates::all())->firstWhere('id', 'G-07')['closed']);

        Setting::put('company_registered_address', 'Kenyatta Street, Eldoret');
        Setting::put('company_email_enquiries', 'enquiries@jiranisoko.com');
        SiteSettings::applyToConfig();

        $this->assertTrue(collect(PublicationGates::all())->firstWhere('id', 'G-07')['closed']);
    }

    public function test_publishing_a_vacancy_puts_it_on_the_careers_page(): void
    {
        $this->seed();

        $job = JobOpening::query()->where('slug', 'senior-payments-engineer')->sole();

        $this->get(route('company.careers'))->assertOk()->assertDontSee($job->title);

        $this->actingAs($this->admin())->put(route('admin.jobs.update', $job), [
            'title' => $job->title,
            'level' => $job->level,
            'location' => $job->location,
            'arrangement' => $job->arrangement,
            'summary' => $job->summary,
            'is_published' => '1',
        ])->assertSessionHasNoErrors();

        $this->assertNotNull($job->fresh()->posted_at);
        $this->get(route('company.careers'))->assertOk()->assertSee($job->title);
    }

    public function test_naming_a_leader_removes_the_vacancy_wording(): void
    {
        $this->seed();

        $member = TeamMember::query()->first();

        $this->actingAs($this->admin())->put(route('admin.team.update', $member), [
            'name' => 'A. Kiprotich',
            'role_title' => $member->role_title,
            'accountability' => $member->accountability,
            'is_published' => '1',
        ])->assertSessionHasNoErrors();

        $this->get(route('company.leadership'))->assertOk()->assertSee('A. Kiprotich');
    }

    public function test_clearing_a_platform_reveals_its_scale_figures(): void
    {
        $this->seed();

        $platform = PlatformReference::query()->where('slug', 'jiranisoko-marketplace')->sole();

        $this->actingAs($this->admin())->put(route('admin.platforms.update', $platform), [
            'title' => $platform->title,
            'system_context' => $platform->system_context,
            'scale_metrics' => [['label' => 'Registered users', 'value' => '12,400']],
            'cleared_for_disclosure' => '1',
        ])->assertSessionHasNoErrors();

        $this->get(route('platforms.show', $platform))
            ->assertOk()
            ->assertSee('Registered users')
            ->assertDontSee('Publication gate G-03');
    }

    public function test_the_rfp_inbox_lists_and_acknowledges_submissions(): void
    {
        $this->seed();

        $submission = RfpSubmission::factory()->create([
            'reference' => 'JTS-RFP-20260905-ABC12',
            'organisation' => 'Rift Valley Commercial Bank',
            'acknowledged_at' => null,
        ]);

        $admin = $this->admin();

        $this->actingAs($admin)->get('/admin/rfp')->assertOk()->assertSee('Rift Valley Commercial Bank');
        $this->actingAs($admin)->get(route('admin.rfp.show', $submission))->assertOk()->assertSee('JTS-RFP-20260905-ABC12');

        $this->actingAs($admin)->put(route('admin.rfp.acknowledge', $submission));
        $this->assertNotNull($submission->fresh()->acknowledged_at);
    }

    public function test_the_console_is_not_indexable(): void
    {
        $this->seed();

        $this->actingAs($this->admin())
            ->get('/admin')
            ->assertOk()
            ->assertSee('noindex, nofollow', escape: false);
    }
}
