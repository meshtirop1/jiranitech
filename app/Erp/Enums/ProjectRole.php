<?php

namespace App\Erp\Enums;

/**
 * A person's standing on one project.
 *
 * Kept separate from ErpRole because leadership is per engagement: the reviewer
 * of a payments task should be the lead on that payments project, not whoever
 * happens to hold a senior title.
 */
enum ProjectRole: string
{
    case Lead = 'lead';
    case Engineer = 'engineer';
    case Reviewer = 'reviewer';
    case Observer = 'observer';

    public function label(): string
    {
        return match ($this) {
            self::Lead => 'Lead engineer',
            self::Engineer => 'Engineer',
            self::Reviewer => 'Reviewer',
            self::Observer => 'Observer',
        };
    }

    /** May accept or reject work submitted for review. */
    public function mayReview(): bool
    {
        return in_array($this, [self::Lead, self::Reviewer], true);
    }

    /** May create tasks, assign them and enrol members. */
    public function mayDirect(): bool
    {
        return $this === self::Lead;
    }

    public function mayBeAssignedWork(): bool
    {
        return in_array($this, [self::Lead, self::Engineer], true);
    }
}
