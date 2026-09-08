<?php

namespace App\Erp\Enums;

/**
 * What a person may do across the whole system.
 *
 * Project-level authority is separate and lives in ProjectRole: somebody can be
 * an engineer everywhere and the lead on one engagement, which is how a small
 * division actually staffs work.
 */
enum ErpRole: string
{
    case Engineer = 'engineer';
    case Lead = 'lead';
    case Director = 'director';

    public function label(): string
    {
        return match ($this) {
            self::Engineer => 'Engineer',
            self::Lead => 'Lead engineer',
            self::Director => 'Delivery director',
        };
    }

    public function descriptor(): string
    {
        return match ($this) {
            self::Engineer => 'Works tasks on the projects they are assigned to.',
            self::Lead => 'Reviews and accepts work on projects they lead.',
            self::Director => 'Sees every project. May review anywhere, and is the escalation for a lead reviewing their own work.',
        };
    }

    /** Sees every project without being a member of it. */
    public function seesEverything(): bool
    {
        return $this === self::Director;
    }

    /** May create projects and enrol people. */
    public function administersDelivery(): bool
    {
        return $this === self::Director;
    }
}
