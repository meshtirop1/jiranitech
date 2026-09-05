<?php

namespace App\Enums;

/**
 * Publication gate G-05. These targets are illustrative until ratified against the
 * composite SLA of the underlying cloud providers.
 */
enum SlaTier: string
{
    case Platinum = 'platinum';
    case Gold = 'gold';
    case Silver = 'silver';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function availabilityTarget(): string
    {
        return match ($this) {
            self::Platinum => '99.95%',
            self::Gold => '99.9%',
            self::Silver => '99.5%',
        };
    }

    public function priorityOneResponse(): string
    {
        return match ($this) {
            self::Platinum => '15 minutes',
            self::Gold => '1 hour',
            self::Silver => '4 hours',
        };
    }

    public function priorityOneResolution(): string
    {
        return match ($this) {
            self::Platinum => '4 hours',
            self::Gold => '8 hours',
            self::Silver => '2 business days',
        };
    }

    public function coverage(): string
    {
        return match ($this) {
            self::Platinum => '24 × 7 × 365',
            self::Gold => '24 × 5 plus on-call',
            self::Silver => '09:00–18:00 EAT',
        };
    }

    public function hasServiceCredits(): bool
    {
        return $this !== self::Silver;
    }
}
