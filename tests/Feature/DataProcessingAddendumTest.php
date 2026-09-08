<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Support\PublicationGates;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The addendum, and the things about it that must not quietly stop being true.
 *
 * Two failure modes are worth a test rather than a proofread. A published
 * instrument can drift out of step with the register it points at, so that a
 * controller reads an annex that no longer describes anybody. And a draft can
 * lose the notice saying it is a draft, which turns a working document into a
 * representation that we are already bound by terms nobody has signed.
 */
class DataProcessingAddendumTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The readable text of a page, with markup and line wrapping taken out.
     *
     * These assertions are about what the document says, not how its source is
     * wrapped. Matching raw HTML would make a test fail when somebody reflows a
     * paragraph, which trains people to ignore it.
     */
    private function prose(string $route): string
    {
        $html = $this->get($route)->assertOk()->getContent();

        return (string) preg_replace('/\s+/', ' ', strip_tags($html));
    }

    private function assertSays(string $route, string ...$phrases): void
    {
        $prose = $this->prose($route);

        foreach ($phrases as $phrase) {
            $this->assertStringContainsString($phrase, $prose);
        }
    }

    public function test_the_addendum_publishes(): void
    {
        $this->assertSays(route('legal.data-processing'), 'Data processing addendum');
    }

    public function test_it_covers_every_term_article_28_prescribes(): void
    {
        // Article 28(3) lists what a processor contract must contain. Each of
        // these is the operative language for one of them; losing any of them
        // makes the instrument non-compliant while still reading as complete.
        $this->assertSays(
            route('legal.data-processing'),
            'documented instructions',          // 28(3)(a)
            'confidentiality obligation',       // 28(3)(b)
            'technical and organisational',     // 28(3)(c), Art 32
            'Sub-processor',                    // 28(3)(d), Art 28(2)
            'Data Subject rights',              // 28(3)(e)
            'Personal Data Breach',             // 28(3)(f), Art 33(2)
            'impact assessment',                // 28(3)(f), Arts 35-36
            'deletes or returns',               // 28(3)(g)
            'audits, including inspections',    // 28(3)(h)
        );
    }

    public function test_it_states_the_processing_details_article_28_requires(): void
    {
        // The chapeau to 28(3): subject matter, duration, nature and purpose,
        // the types of data and the categories of data subject.
        $this->assertSays(
            route('legal.data-processing'),
            'Subject matter',
            'Duration',
            'Nature and purpose',
            'Categories of personal data',
            'Categories of data subjects',
        );
    }

    public function test_it_names_both_regimes_and_the_transfer_mechanism(): void
    {
        $this->assertSays(
            route('legal.data-processing'),
            'Kenya Data Protection Act 2019',
            'GDPR',
            'standard contractual clauses',
            'Chapter V',
        );
    }

    public function test_it_contracts_in_the_name_of_a_legal_person(): void
    {
        // A division cannot contract. If the addendum ever starts naming the
        // division as the processor, it is unexecutable.
        $this->assertSays(
            route('legal.data-processing'),
            config('company.parent.name'),
            'not a separate legal person',
        );
    }

    public function test_it_says_when_it_takes_effect(): void
    {
        // Published standard terms have to say what makes them bite, or a reader
        // cannot tell whether they are already bound by them.
        $this->assertSays(
            route('legal.data-processing'),
            'takes effect on signature by both parties',
        );
    }

    public function test_it_offers_somewhere_to_sign(): void
    {
        $this->assertSays(route('legal.data-processing'), 'For the Processor', 'For the Controller');
    }

    // --- the register --------------------------------------------------------

    public function test_the_register_publishes_and_lists_the_hosting_provider(): void
    {
        $this->assertSays(
            route('legal.sub-processors'),
            'HostPinnacle Cloud Limited',
            'Transfer safeguard',
        );
    }

    public function test_every_register_entry_carries_its_location_and_basis(): void
    {
        // The G-01 discipline applied to the register: a location claim is
        // published with how it is known, or it is not published.
        $entries = array_merge(
            config('sub_processors.engaged'),
            config('sub_processors.conditional'),
        );

        $this->assertNotEmpty($entries);

        foreach ($entries as $entry) {
            foreach (['name', 'role', 'service', 'entity', 'location', 'basis', 'transfer'] as $field) {
                $this->assertNotEmpty(
                    $entry[$field] ?? null,
                    "Sub-processor \"{$entry['name']}\" publishes no {$field}.",
                );
            }
        }
    }

    public function test_a_conditional_sub_processor_is_never_presented_as_authorised(): void
    {
        foreach (config('sub_processors.conditional') as $entry) {
            $this->assertNull(
                $entry['engaged_on'],
                "\"{$entry['name']}\" is listed as conditional but carries an engagement date.",
            );
        }

        $this->assertSays(route('legal.sub-processors'), 'disclosure, not authorisation');
    }

    public function test_the_register_and_the_addendum_agree_on_the_notice_period(): void
    {
        // The addendum promises a period; the register is what it is honoured
        // against. If they disagree, one of them is a broken promise.
        $notice = config('sub_processors.notice_days');

        $this->assertIsInt($notice);
        $this->assertGreaterThan(0, $notice);

        $this->assertSays(route('legal.data-processing'), "{$notice} days'");
        $this->assertSays(route('legal.sub-processors'), "{$notice} days'");
    }

    public function test_the_register_carries_a_change_log_entry_for_its_own_publication(): void
    {
        $this->assertNotEmpty(
            config('sub_processors.changes'),
            'A register with no change log cannot support a notice period.',
        );

        $this->assertSays(route('legal.sub-processors'), 'Change log');
    }

    public function test_each_document_points_at_the_other(): void
    {
        $this->get(route('legal.data-processing'))->assertSee(route('legal.sub-processors'), false);
        $this->get(route('legal.sub-processors'))->assertSee(route('legal.data-processing'), false);
    }

    // --- the gate ------------------------------------------------------------

    public function test_gate_g06_stays_open_until_a_person_signs_it_off(): void
    {
        // Drafting the instrument does not close the gate. Only counsel and the
        // DPO can, and they record it themselves.
        $gate = collect(PublicationGates::all())->firstWhere('id', 'G-06');

        $this->assertFalse($gate['closed']);
        $this->assertStringContainsString('drafted', $gate['detail']);

        Setting::put('gate_g06_counsel_signed_off', '1');
        Setting::flush();

        $gate = collect(PublicationGates::all())->firstWhere('id', 'G-06');
        $this->assertTrue($gate['closed']);
    }

    public function test_the_footer_links_the_register_from_every_page(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('legal.sub-processors'), false);
    }
}
