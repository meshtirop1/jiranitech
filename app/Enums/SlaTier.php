<?php

namespace App\Enums;

use App\Models\Setting;

/**
 * The service level tiers.
 *
 * The cases are the identity of a tier and stay in code, because a service row
 * points at one by name and renaming a case would orphan those rows. What each
 * tier promises does not stay in code: those are commercial commitments a client
 * can hold the firm to, and the people who negotiate them edit them in the
 * console. See config/sla.php.
 *
 * Publication gate G-05 governs whether they may be quoted as commitments at
 * all — until they are ratified against the composite service levels of the
 * infrastructure underneath, the site publishes the framework and the statement
 * of work carries the binding numbers.
 */
enum SlaTier: string
{
    case Platinum = 'platinum';
    case Gold = 'gold';
    case Silver = 'silver';

    public function label(): string
    {
        return $this->setting('label') ?? ucfirst($this->value);
    }

    public function availabilityTarget(): string
    {
        return $this->setting('availability_target') ?? '—';
    }

    public function priorityOneResponse(): string
    {
        return $this->setting('p1_response') ?? '—';
    }

    public function priorityOneResolution(): string
    {
        return $this->setting('p1_resolution') ?? '—';
    }

    public function coverage(): string
    {
        return $this->setting('coverage') ?? '—';
    }

    public function hasServiceCredits(): bool
    {
        return (bool) config('sla.tiers.'.$this->value.'.service_credits', false);
    }

    /** Whether the published targets have been confirmed as achievable and bound. */
    public static function ratified(): bool
    {
        return (bool) Setting::get('gate_g05_ratified');
    }

    /**
     * The editable fields on a tier, and what each one is for.
     *
     * Declared once so the console form, its validation and the settings map all
     * come from the same place — a field that can be edited but not validated is
     * an unchecked write, and one that is stored but never shown is a promise
     * nobody can see they made.
     *
     * @return array<string, string>
     */
    public static function fields(): array
    {
        return [
            'label' => 'Tier name',
            'availability_target' => 'Availability target',
            'p1_response' => 'P1 response',
            'p1_resolution' => 'P1 resolution target',
            'coverage' => 'Coverage',
        ];
    }

    private function setting(string $field): ?string
    {
        $value = config('sla.tiers.'.$this->value.'.'.$field);

        return filled($value) ? (string) $value : null;
    }
}
