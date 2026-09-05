<?php

namespace App\Enums;

/**
 * Publication gate G-02. A standard we follow but have not certified must never
 * render as a certification; the label carries the qualification explicitly.
 */
enum ComplianceStatus: string
{
    case Certified = 'certified';
    case Aligned = 'aligned';
    case InProgress = 'in_progress';

    public function label(): string
    {
        return match ($this) {
            self::Certified => 'Certified',
            self::Aligned => 'Aligned — not certified',
            self::InProgress => 'Certification in progress',
        };
    }

    public function isAssertable(): bool
    {
        return $this === self::Certified;
    }
}
