<?php

/*
| Version and status of the published legal instruments.
|
| An instrument that changes without its version changing is one nobody can cite
| in a contract, so the version and the effective date live here and are quoted
| on the page rather than typed into it.
|
| `counsel_reviewed` is deliberately not something this file can assert. Whether
| counsel and the Data Protection Officer have signed an instrument off is a fact
| about people, recorded in the settings table by the person who holds that
| authority, and publication gate G-06 reads it from there. A drafter marking
| their own draft as reviewed is the exact failure the gate exists to prevent.
*/

return [

    /*
    | Commercial variables quoted in the terms of engagement.
    |
    | They live here rather than in the prose so that one edit governs every
    | place the site states them, and so that changing a commercial position is
    | a reviewed, dated commit rather than a search-and-replace through a legal
    | document. Every figure here is a promise to a client: change it knowing
    | that the published terms change with it.
    */
    'terms' => [
        'version' => '1.0',
        'effective_on' => '2026-09-09',

        'payment_days' => 30,
        'late_interest_percent' => 2,          // per month, on overdue sums
        'warranty_days' => 90,                 // defect correction after acceptance
        'minimum_term_days' => 90,             // dedicated team engagements
        'scale_down_notice_days' => 30,
        'termination_notice_days' => 60,
        'service_credit_cap_percent' => 10,    // of the monthly service charge
        'liability_cap_months' => 12,          // charges paid in the preceding period
        'non_solicitation_months' => 12,
    ],

    'privacy' => [
        'version' => '1.0',
        'effective_on' => '2026-09-09',
    ],

    'accessibility' => [
        'standard' => 'WCAG 2.2 Level AA',
        'reviewed_on' => '2026-09-09',
        'independently_audited' => false,
    ],

    'dpa' => [
        'version' => '1.0',
        'effective_on' => '2026-09-09',

        /*
        | Instruments the addendum leans on that are not yet drafted. Named here
        | so the dependency is visible rather than discovered at signature.
        */
        'depends_on' => [
            'Terms of engagement — liability cap referenced by clause 12.2',
        ],
    ],

];
