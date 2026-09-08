<?php

namespace App\Erp\Casts;

use App\Erp\Enums\ErpRole;
use App\Erp\Support\LegacyRoles;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Reads erp_role without ever throwing on it.
 *
 * Laravel's built-in enum cast calls ErpRole::from(), which raises a ValueError
 * on anything it does not recognise — and that error surfaces as a 500 on every
 * page the account touches, including the sign-in page, so there is no way back
 * in to fix it. A column that can be left holding an old value by a migration
 * that could not be run is exactly the column that must not do that.
 *
 * @implements CastsAttributes<ErpRole|null, ErpRole|string|null>
 */
class ErpRoleCast implements CastsAttributes
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?ErpRole
    {
        return LegacyRoles::resolve(is_string($value) ? $value : null);
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, string|null>
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        if ($value === null) {
            return [$key => null];
        }

        // Anything written goes in as a current value, so the old ones can only
        // ever leave the database, never enter it.
        $role = $value instanceof ErpRole ? $value : LegacyRoles::resolve((string) $value);

        return [$key => $role?->value];
    }
}
