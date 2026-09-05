<?php

namespace App\Enums;

enum InsightFormat: string
{
    case CaseStudy = 'case-studies';
    case Whitepaper = 'whitepapers';
    case EngineeringNote = 'engineering-notes';

    public function label(): string
    {
        return match ($this) {
            self::CaseStudy => 'Case study',
            self::Whitepaper => 'Whitepaper',
            self::EngineeringNote => 'Engineering note',
        };
    }

    public function isGated(): bool
    {
        return $this === self::Whitepaper;
    }
}
