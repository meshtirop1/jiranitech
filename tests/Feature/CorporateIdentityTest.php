<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Support\PublicationGates;
use App\Support\SiteSettings;
use App\Support\StructuredData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The identity the site asserts about itself, held to the registry record.
 *
 * Source documents, both dated 30 March 2026:
 *   CR12 official search, PVT-YQ195JQY  registered office, company number, director
 *   KRA PIN certificate, P052526482R    the same address, and the tax number
 *
 * These are legal representations. A vendor-registry check compares them against
 * the Business Registration Service's own record, and a footer that disagrees
 * with the CR12 fails that check — so the values are asserted here rather than
 * left to whoever next edits a template.
 */
class CorporateIdentityTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_footer_states_the_registered_office_from_the_cr12(): void
    {
        $this->seed();

        $footer = $this->get(route('home'))->assertOk();

        // Street, then the locality and country the config adds to it.
        $footer->assertSee('Building 12, Soy-Kabenes Road, Eldoret West, Eldoret, Kenya');
        $footer->assertSee('P.O. Box 91-30105, Soy');

        // The address and registration markers are gone. The enquiries marker is
        // not — no corporate mailbox exists yet, and the registry's contact is a
        // personal Gmail, which is not one.
        $footer->assertDontSee('Gate G-07 — not configured');
        $footer->assertSee('Gate G-07 — enquiries address not configured');
    }

    public function test_registration_and_tax_numbers_are_attributed_to_the_holding_company(): void
    {
        $this->seed();

        $content = $this->get(route('home'))->assertOk()->getContent();

        // The division is not separately incorporated. Both numbers have to sit
        // under the parent's name, or the footer implies a legal entity that does
        // not exist.
        $this->assertMatchesRegularExpression(
            '/Jiranisoko Market Ltd<\/strong>,\s*company no\.\s*PVT-YQ195JQY/s',
            $content,
            'The company number is not attributed to the holding company.',
        );

        $this->assertStringContainsString('KRA PIN P052526482R', $content);
    }

    public function test_the_incorporation_date_matches_the_certificate(): void
    {
        $this->seed();

        $this->assertSame('2026-03-30', config('company.parent.incorporated_on'));
    }

    public function test_gate_g07_is_closed_once_the_address_and_a_contact_route_are_set(): void
    {
        $this->seed();

        // The address is configured; the enquiries address is not, and G-07 counts
        // it, so the gate is still open and still says which part is missing.
        $gate = collect(PublicationGates::all())->firstWhere('id', 'G-07');

        $this->assertFalse($gate['closed']);
        $this->assertStringContainsString('enquiries address', $gate['detail']);
        $this->assertStringNotContainsString('registered office', $gate['detail']);

        Setting::put('company_email_enquiries', 'enquiries@jiranisoko.com');
        SiteSettings::applyToConfig();

        $this->assertTrue(collect(PublicationGates::all())->firstWhere('id', 'G-07')['closed']);
    }

    public function test_the_registered_office_reaches_the_structured_data(): void
    {
        $this->seed();

        $organisation = StructuredData::organisation();

        $this->assertSame(
            'Building 12, Soy-Kabenes Road, Eldoret West',
            $organisation['address']['streetAddress'],
        );
        $this->assertSame('Eldoret', $organisation['address']['addressLocality']);
        $this->assertSame('Kenya', $organisation['address']['addressCountry']);
        $this->assertSame('PVT-YQ195JQY', $organisation['parentOrganization']['identifier']);
    }

    /**
     * The CR12 and the PIN certificate both carry the director's personal mobile
     * and personal Gmail address. Neither is a corporate contact route, and the
     * site must not start publishing them because they happened to be in a
     * document that was handed over for the address.
     */
    public function test_the_directors_personal_contact_details_are_not_published(): void
    {
        $this->seed();

        foreach ([
            '/',
            '/contact',
            '/contact/engagement-desk',
            '/company/governance',
            '/legal/privacy-notice',
        ] as $path) {
            $content = $this->get($path)->assertOk()->getContent();

            $this->assertStringNotContainsString('meshacktirop345', $content, "A personal address is published on {$path}.");
            $this->assertStringNotContainsString('723636377', $content, "A personal number is published on {$path}.");
        }
    }

    public function test_the_tax_number_can_be_withdrawn_from_the_footer(): void
    {
        $this->seed();

        // It is published because Kenyan procurement asks for it, not because the
        // template hard-codes it. Clearing the setting has to remove it.
        Setting::put('company_parent_tax_pin', '');
        SiteSettings::applyToConfig();
        config(['company.parent.tax_pin' => null]);

        $this->get(route('home'))->assertOk()->assertDontSee('KRA PIN');
    }
}
