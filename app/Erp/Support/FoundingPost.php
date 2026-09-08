<?php

namespace App\Erp\Support;

use App\Erp\Enums\ErpRole;
use App\Models\User;

/**
 * The way the first leader gets in.
 *
 * Every account in this system is created by somebody who already holds a post,
 * which is the property that makes delegated enrolment safe — and also means a
 * division with nobody in it can never be started. Seeding a founding account
 * from a migration would mean shipping a password, and this host has no shell to
 * run a one-off command from.
 *
 * So the site administrator holds the Managing Director's post, but only while
 * nobody is in charge of delivery. The moment somebody is appointed to a
 * directing post the loan lapses on the next request, and the administrator goes
 * back to having no delivery role at all. It cannot be used to escalate later:
 * it is only ever available when the alternative is a system nobody can enter.
 *
 * This does not weaken the separation EnsureUserWorksInDelivery keeps between
 * administering the website and working on delivery. The administrator already
 * owns the console and the database that back both; the loan grants nothing that
 * was not already reachable, and it is never enough to be given work — the
 * founding holder stays out of User::inDelivery(), so they are never offered as
 * an assignee.
 */
final class FoundingPost
{
    private const MEMO = 'erp.founding-post.vacant';

    /**
     * True while no active account holds a post that can open an engagement.
     *
     * erpRole() is asked several times a page, so the answer is memoised on the
     * container — which lives exactly one request — and thrown away whenever any
     * account is written, since that is the only thing the answer depends on.
     * Appointing the first director therefore ends the loan immediately rather
     * than at the end of the request that made the appointment.
     */
    public static function isVacant(): bool
    {
        if (app()->bound(self::MEMO)) {
            return app()->make(self::MEMO);
        }

        $vacant = User::query()
            ->where('is_active', true)
            ->whereIn('erp_role', collect(ErpRole::cases())
                ->filter->opensProjects()
                ->map->value
                ->all())
            ->doesntExist();

        app()->instance(self::MEMO, $vacant);

        return $vacant;
    }

    /** Called from User's model events; see the note on isVacant(). */
    public static function forget(): void
    {
        app()->forgetInstance(self::MEMO);
    }

    /** Whether this account is currently standing in as the founding leader. */
    public static function heldBy(User $user): bool
    {
        return $user->is_admin
            && $user->is_active
            && $user->erp_role === null
            && self::isVacant();
    }
}
