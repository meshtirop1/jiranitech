<?php

namespace App\Erp\Enums;

/**
 * The posts on /company/leadership, and what each one may do here.
 *
 * The roles are the published org chart rather than a generic admin/user
 * hierarchy, so the authority a person holds in this system is the authority the
 * company has already stated in public that they hold. When the leadership page
 * says the Data Protection Officer is independent of delivery, that is not a
 * sentence — it is why the DPO cannot be assigned work or review it.
 *
 * The three practice leads differ only in discipline, not in what they may do,
 * so they share one case and carry their discipline in job_title. Multiplying
 * the enum for identical permissions would only invite them to drift apart.
 */
enum ErpRole: string
{
    case ManagingDirector = 'managing_director';
    case ChiefTechnologyOfficer = 'chief_technology_officer';
    case DirectorOfDelivery = 'director_of_delivery';
    case HeadOfInformationSecurity = 'head_of_information_security';
    case DataProtectionOfficer = 'data_protection_officer';
    case PracticeLead = 'practice_lead';
    case Engineer = 'engineer';

    public function label(): string
    {
        return match ($this) {
            self::ManagingDirector => 'Managing Director',
            self::ChiefTechnologyOfficer => 'Chief Technology Officer',
            self::DirectorOfDelivery => 'Director of Delivery',
            self::HeadOfInformationSecurity => 'Head of Information Security',
            self::DataProtectionOfficer => 'Data Protection Officer',
            self::PracticeLead => 'Practice Lead',
            self::Engineer => 'Engineer',
        };
    }

    public function descriptor(): string
    {
        return match ($this) {
            self::ManagingDirector => 'Answerable for the division as a whole. Sees and may do everything.',
            self::ChiefTechnologyOfficer => 'Owns technical direction and the architecture standard. Sees every engagement and may review any of them.',
            self::DirectorOfDelivery => 'Accountable for every engagement meeting its baseline. Opens projects, staffs them and may review anywhere.',
            self::HeadOfInformationSecurity => 'Owns the security management system. Sees every engagement and may halt any release by refusing a review.',
            self::DataProtectionOfficer => 'Statutory role, independent of delivery. Reads every engagement; takes no work and casts no review.',
            self::PracticeLead => 'Technical quality across a discipline. Leads projects, reviews work on them, and enrols engineers.',
            self::Engineer => 'Works the tasks assigned to them on the projects they are on.',
        };
    }

    /**
     * Whether this post is part of the leadership published at
     * /company/leadership. Used only for grouping people in the interface.
     */
    public function isLeadership(): bool
    {
        return $this !== self::Engineer;
    }

    /** Reads every engagement without being enrolled on it. */
    public function seesEverything(): bool
    {
        return $this !== self::Engineer && $this !== self::PracticeLead;
    }

    /** Opens engagements and appoints their lead. */
    public function opensProjects(): bool
    {
        return in_array($this, [
            self::ManagingDirector,
            self::ChiefTechnologyOfficer,
            self::DirectorOfDelivery,
        ], true);
    }

    /**
     * Creates accounts. Leadership does this, not only the directors — a practice
     * lead staffing their own discipline should not have to queue behind the
     * Managing Director to add an engineer.
     */
    public function enrolsPeople(): bool
    {
        return in_array($this, [
            self::ManagingDirector,
            self::ChiefTechnologyOfficer,
            self::DirectorOfDelivery,
            self::PracticeLead,
        ], true);
    }

    /**
     * Which posts this one may create.
     *
     * A practice lead may enrol engineers and nothing else. Without this, being
     * able to create accounts would be a route to granting yourself — or a
     * colleague — authority nobody appointed.
     *
     * @return array<int, self>
     */
    public function mayCreate(): array
    {
        if ($this === self::PracticeLead) {
            return [self::Engineer];
        }

        return $this->enrolsPeople() ? self::cases() : [];
    }

    public function mayCreateRole(self $role): bool
    {
        return in_array($role, $this->mayCreate(), true);
    }

    /**
     * May accept or reject work on any engagement, without being a member.
     *
     * A practice lead reviews too, but only on projects they are on — that is a
     * project-level right and lives in ProjectRole.
     */
    public function reviewsAnywhere(): bool
    {
        return in_array($this, [
            self::ManagingDirector,
            self::ChiefTechnologyOfficer,
            self::DirectorOfDelivery,
            self::HeadOfInformationSecurity,
        ], true);
    }

    /**
     * May be assigned a task.
     *
     * The Data Protection Officer may not. The leadership page states the post is
     * independent of delivery, which is a requirement of the Data Protection Act
     * 2019 rather than a preference, and independence that the system quietly
     * allows to be broken is not independence.
     */
    public function mayHoldWork(): bool
    {
        return $this !== self::DataProtectionOfficer;
    }

    /** Kept for the middleware alias and older call sites. */
    public function administersDelivery(): bool
    {
        return $this->opensProjects();
    }

    /**
     * The discipline titles offered when enrolling a practice lead, so the three
     * posts on the leadership page can be filled exactly.
     *
     * @return array<int, string>
     */
    public static function practiceDisciplines(): array
    {
        return [
            'Practice Lead — Artificial Intelligence & Automation',
            'Practice Lead — Financial Technology & Payments',
            'Practice Lead — Cloud Infrastructure & DevOps',
        ];
    }
}
