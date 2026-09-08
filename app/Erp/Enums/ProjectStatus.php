<?php

namespace App\Erp\Enums;

enum ProjectStatus: string
{
    case Discovery = 'discovery';
    case Active = 'active';
    case OnHold = 'on_hold';
    case Delivered = 'delivered';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Discovery => 'Discovery',
            self::Active => 'In delivery',
            self::OnHold => 'On hold',
            self::Delivered => 'Delivered',
            self::Closed => 'Closed',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::Active => 'attention',
            self::Delivered => 'positive',
            self::OnHold => 'negative',
            self::Closed => 'muted',
            default => 'neutral',
        };
    }

    public function isRunning(): bool
    {
        return in_array($this, [self::Discovery, self::Active], true);
    }
}
