<?php

/*
| The service level tiers.
|
| These were constants in the SlaTier enum, which meant the only way to correct
| a commercial commitment was a code change and a deploy — on a host with no
| shell. Every figure here is something a client can hold the firm to, so the
| people who negotiate them have to be able to edit them.
|
| The values below are the defaults. Anything an administrator saves in the
| console is written to the settings table and overlaid on top of these by
| SiteSettings, the same way the corporate identity works.
|
| Publication gate G-05 governs whether these may be presented as commitments at
| all: until somebody ratifies them against the composite service levels of the
| infrastructure underneath, the site describes the framework rather than quoting
| a binding number. Publishing a 99.95% target on infrastructure whose own
| composite SLA is lower creates an obligation that cannot be met.
*/

return [

    'tiers' => [

        'platinum' => [
            'label' => 'Platinum',
            'availability_target' => '99.95%',
            'p1_response' => '15 minutes',
            'p1_resolution' => '4 hours',
            'coverage' => '24 × 7 × 365',
            'service_credits' => true,
        ],

        'gold' => [
            'label' => 'Gold',
            'availability_target' => '99.9%',
            'p1_response' => '1 hour',
            'p1_resolution' => '8 hours',
            'coverage' => '24 × 5 plus on-call',
            'service_credits' => true,
        ],

        'silver' => [
            'label' => 'Silver',
            'availability_target' => '99.5%',
            'p1_response' => '4 hours',
            'p1_resolution' => '2 business days',
            'coverage' => '09:00–18:00 EAT',
            'service_credits' => false,
        ],

    ],

];
