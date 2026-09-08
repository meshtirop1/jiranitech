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

        // Every G-07 marker is gone: the CR12 supplied the office and the number,
        // and the role mailboxes supplied the contact route.
        $footer->assertDontSee('Gate G-07');
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

    public function test_gate_g07_names_whichever_part_is_missing(): void
    {
        $this->seed();

        // Closed, now that the office, the number and a contact route are all set.
        $this->assertTrue(collect(PublicationGates::all())->firstWhere('id', 'G-07')['closed']);

        // Withdraw the contact route and the gate has to reopen and say so — the
        // marker is what stops a half-configured identity shipping quietly.
        config(['company.email.enquiries' => null]);

        $gate = collect(PublicationGates::all())->firstWhere('id', 'G-07');

        $this->assertFalse($gate['closed']);
        $this->assertStringContainsString('enquiries address', $gate['detail']);
        $this->assertStringNotContainsString('registered office', $gate['detail']);
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
