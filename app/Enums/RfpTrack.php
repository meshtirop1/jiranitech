<?php

namespace App\Enums;

enum RfpTrack: string
{
    case EnterpriseModernisation = 'enterprise-modernisation';
    case NewProductBuild = 'new-product-build';
    case TeamAugmentation = 'team-augmentation';
    case StrategicAdvisory = 'strategic-advisory';

    public function label(): string
    {
        return match ($this) {
            self::EnterpriseModernisation => 'Enterprise modernisation',
            self::NewProductBuild => 'New product build',
            self::TeamAugmentation => 'Team augmentation',
            self::StrategicAdvisory => 'Strategic advisory',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::EnterpriseModernisation => 'An existing system — core, legacy or on-premises — that must be migrated, re-architected or integrated.',
            self::NewProductBuild => 'A platform to be taken from concept or specification to production under a fixed scope.',
            self::TeamAugmentation => 'Senior engineering capacity embedded into an existing team and roadmap.',
            self::StrategicAdvisory => 'An architectural, security or transformation decision that must be made before any build begins.',
        };
    }
}
