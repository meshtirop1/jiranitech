<?php

namespace App\Erp\Support;

use App\Erp\Enums\ErpRole;
use App\Models\User;

/**
 * Accounts written before the published posts became the roles.
 *
 * The first cut of the delivery system used director / lead / engineer, and a
 * migration maps those onto the posts at /company/leadership. On this host that
 * migration cannot be run: there is no shell, no cron and the store is SQLite,
 * so there is nothing to run `artisan migrate` from. An account still holding an
 * old value therefore threw on every request that read its role, which took the
 * whole delivery system down for that account.
 *
 * So the translation lives here as well as in the migration. resolve() means a
 * stale value can never be fatal, and heal() writes the corrected value back the
 * first time the account is used — which is the migration, arriving one row at a
 * time through the front door because that is the only door there is.
 */
final class LegacyRoles
{
    /** @var array<string, string> */
    public const MAP = [
        'director' => 'managing_director',
        'lead' => 'practice_lead',
    ];

    /**
     * The role a stored value means, whatever vintage it is.
     *
     * An unrecognised value resolves to null rather than throwing: an account
     * whose role cannot be read should lose access, not take the site down.
     */
    public static function resolve(?string $stored): ?ErpRole
    {
        if ($stored === null || $stored === '') {
            return null;
        }

        return ErpRole::tryFrom(self::MAP[$stored] ?? $stored);
    }

    /** True if this is a value from before the rename. */
    public static function isStale(?string $stored): bool
    {
        return $stored !== null && isset(self::MAP[$stored]);
    }

    /**
     * Write the current value back, once, for one account.
     *
     * Saved through the model rather than quietly, so the founding post is told
     * that who is in charge may have changed.
     */
    public static function heal(User $user): void
    {
        $stored = $user->getRawOriginal('erp_role');

        if (! self::isStale($stored)) {
            return;
        }

        $user->forceFill(['erp_role' => self::MAP[$stored]])->save();
    }
}
