<?php

namespace Tests\Feature;

use App\Admin\ContentType;
use App\Models\Industry;
use App\Models\Pillar;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The content that describes what the firm sells, edited by the firm.
 *
 * Everything on a service page was seeded at build time and reachable only
 * through the database, so the person responsible for the copy could not change
 * a word of it. These assert that the console now reaches all of it, and that
 * editing it moves what the public page says.
 */
class ContentEditorTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true, 'is_active' => true]);
    }

    public function test_every_declared_type_opens(): void
    {
        $this->seed();
        $admin = $this->admin();

        foreach (ContentType::all() as $type) {
            $this->actingAs($admin)
                ->get(route('admin.content.index', $type->key))
                ->assertOk()
                ->assertSee($type->label);
        }
    }

    public function test_an_unknown_type_is_not_a_five_hundred(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.content.index', 'wishful-thinking'))
            ->assertNotFound();
    }

    public function test_the_console_is_closed_to_everyone_else(): void
    {
        $this->get(route('admin.content.index', 'services'))->assertRedirect();

        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->get(route('admin.content.index', 'services'))
            ->assertForbidden();
    }

    // --- editing reaches the public page -------------------------------------

    public function test_editing_a_service_changes_what_the_public_page_says(): void
    {
        $this->seed();
        $service = Service::query()->with('pillar')->firstOrFail();

        $this->actingAs($this->admin())
            ->put(route('admin.content.update', ['services', $service->id]), [
                'pillar_id' => $service->pillar_id,
                'title' => $service->title,
                'slug' => $service->slug,
                'executive_summary' => 'A sentence the business actually wrote itself.',
                'outcomes' => "A reconciled ledger\nAn audit trail that survives examination",
                'capabilities' => 'Gateway integration',
                'stack' => "PHP\nPostgreSQL",
                'architecture_note' => null,
                'sla_tier' => $service->sla_tier?->value,
                'compliance_tags' => '',
                'meta_title' => null,
                'meta_description' => null,
                'sort_order' => $service->sort_order,
            ])
            ->assertSessionHasNoErrors();

        $this->get(route('services.show', [$service->pillar, $service->fresh()]))
            ->assertOk()
            ->assertSee('A sentence the business actually wrote itself.')
            ->assertSee('An audit trail that survives examination');
    }

    public function test_a_list_field_is_stored_as_a_list_not_a_blob(): void
    {
        // The lists render as bullets. If the textarea were stored verbatim the
        // page would show one bullet containing newlines.
        $this->seed();
        $industry = Industry::query()->firstOrFail();

        $this->actingAs($this->admin())
            ->put(route('admin.content.update', ['industries', $industry->id]), [
                'title' => $industry->title,
                'slug' => $industry->slug,
                'constraint_statement' => $industry->constraint_statement,
                'pressures' => "Settlement windows\n\n  Deposit flight  \nRegulatory reporting",
                'regulatory_notes' => $industry->regulatory_notes,
                'sort_order' => $industry->sort_order,
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(
            ['Settlement windows', 'Deposit flight', 'Regulatory reporting'],
            $industry->fresh()->pressures,
            'Blank lines should be dropped and each line trimmed.',
        );
    }

    // --- creating and deleting ------------------------------------------------

    public function test_a_new_industry_can_be_created_and_appears_publicly(): void
    {
        $this->seed();

        $this->actingAs($this->admin())
            ->post(route('admin.content.store', 'industries'), [
                'title' => 'Agricultural cooperatives',
                'slug' => 'agricultural-cooperatives',
                'constraint_statement' => 'Payment cycles follow harvests, not months.',
                'pressures' => 'Seasonal liquidity',
                'regulatory_notes' => null,
                'sort_order' => 90,
            ])
            ->assertSessionHasNoErrors();

        $this->get(route('industries.index'))->assertOk()->assertSee('Agricultural cooperatives');
    }

    public function test_a_duplicate_slug_is_refused(): void
    {
        // Two records on one address means one of them silently shadows the
        // other, which is a page that vanishes with no error anywhere.
        $this->seed();
        $existing = Industry::query()->firstOrFail();

        $this->actingAs($this->admin())
            ->post(route('admin.content.store', 'industries'), [
                'title' => 'Something else',
                'slug' => $existing->slug,
                'constraint_statement' => 'Anything.',
                'pressures' => '',
                'regulatory_notes' => null,
                'sort_order' => 91,
            ])
            ->assertSessionHasErrors('slug');
    }

    public function test_a_record_can_be_deleted(): void
    {
        $this->seed();

        $industry = Industry::create([
            'title' => 'Temporary', 'slug' => 'temporary',
            'constraint_statement' => 'Here briefly.', 'pressures' => [], 'sort_order' => 99,
        ]);

        $this->actingAs($this->admin())
            ->delete(route('admin.content.destroy', ['industries', $industry->id]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('industries', ['id' => $industry->id]);
    }

    public function test_a_pillar_carrying_services_refuses_to_be_deleted(): void
    {
        // Cascading would leave every service under it pointing at a discipline
        // that no longer exists, which is a set of broken pages rather than a
        // tidy-up.
        $this->seed();

        $pillar = Pillar::query()->has('services')->firstOrFail();

        $this->actingAs($this->admin())
            ->delete(route('admin.content.destroy', ['pillars', $pillar->id]))
            ->assertSessionHasErrors('delete');

        $this->assertDatabaseHas('pillars', ['id' => $pillar->id]);
    }

    public function test_a_required_field_cannot_be_emptied(): void
    {
        $this->seed();
        $pillar = Pillar::query()->firstOrFail();

        $this->actingAs($this->admin())
            ->put(route('admin.content.update', ['pillars', $pillar->id]), [
                'title' => '',
                'nav_title' => $pillar->nav_title,
                'slug' => $pillar->slug,
                'number' => $pillar->number,
                'descriptor' => $pillar->descriptor,
                'thesis' => $pillar->thesis,
                'sort_order' => $pillar->sort_order,
            ])
            ->assertSessionHasErrors('title');

        $this->assertNotEmpty($pillar->fresh()->title);
    }

    public function test_every_field_the_form_renders_is_validated(): void
    {
        // The form and the rules come from one declaration. This asserts that
        // stays true: a field on screen with no rules behind it is an unchecked
        // write straight into the database.
        foreach (ContentType::all() as $type) {
            $rules = $type->rules();

            foreach ($type->fields as $field) {
                $this->assertArrayHasKey(
                    $field->name,
                    $rules,
                    "{$type->label}: the field “{$field->label}” is rendered but never validated.",
                );
                $this->assertNotEmpty($rules[$field->name]);
            }
        }
    }
}
