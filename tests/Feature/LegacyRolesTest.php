<?php

namespace Tests\Feature;

use App\Erp\Enums\ErpRole;
use App\Erp\Support\LegacyRoles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * An account left holding a value from before the rename.
 *
 * This is not hypothetical: it happened in production, and because the enum cast
 * threw on read it took out every page that account touched — including the
 * sign-in page, so there was no way back in to correct it. A column whose
 * migration cannot be guaranteed to have run is a column that must never be able
 * to do that.
 */
class LegacyRolesTest extends TestCase
{
    use RefreshDatabase;

    private function withStoredRole(string $stored): User
    {
        $user = User::factory()->create(['erp_role' => ErpRole::Engineer, 'is_active' => true]);

        // Straight past the cast, the way the old rows actually sit on disk.
        DB::table('users')->where('id', $user->id)->update(['erp_role' => $stored]);

        return $user->fresh();
    }

    public function test_an_old_value_reads_as_the_post_it_became(): void
    {
        $this->assertSame(ErpRole::ManagingDirector, $this->withStoredRole('director')->erpRole());
        $this->assertSame(ErpRole::PracticeLead, $this->withStoredRole('lead')->erpRole());
    }

    public function test_an_unreadable_value_denies_access_instead_of_throwing(): void
    {
        $user = $this->withStoredRole('archduke');

        $this->assertNull($user->erpRole());
        $this->assertFalse($user->worksInDelivery());
    }

    public function test_the_sign_in_page_survives_an_account_with_an_old_value(): void
    {
        // The failure that mattered: this 500'd, so the account could not even
        // reach the form to sign in as somebody else.
        $this->actingAs($this->withStoredRole('director'))
            ->get(route('erp.login'))
            ->assertRedirect(route('erp.dashboard'));
    }

    public function test_using_the_system_writes_the_current_value_back(): void
    {
        $user = $this->withStoredRole('director');

        $this->actingAs($user)->get(route('erp.dashboard'))->assertOk();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'erp_role' => 'managing_director']);
    }

    public function test_healing_is_a_no_op_for_a_current_value(): void
    {
        $user = User::factory()->create(['erp_role' => ErpRole::PracticeLead, 'is_active' => true]);

        LegacyRoles::heal($user);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'erp_role' => 'practice_lead']);
    }

    public function test_an_old_value_can_never_be_written_back_in(): void
    {
        $user = User::factory()->create(['erp_role' => ErpRole::Engineer, 'is_active' => true]);

        $user->update(['erp_role' => 'director']);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'erp_role' => 'managing_director']);
    }

    public function test_an_old_value_still_counts_as_somebody_being_in_charge(): void
    {
        // Otherwise the founding post would be handed out over the head of an
        // account that already holds a directing role.
        $admin = User::factory()->create(['is_admin' => true, 'erp_role' => null, 'is_active' => true]);
        $legacy = $this->withStoredRole('director');

        $this->actingAs($legacy)->get(route('erp.dashboard'))->assertOk();

        $this->assertNull($admin->fresh()->erpRole(), 'The founding post was loaned out anyway.');
    }
}
