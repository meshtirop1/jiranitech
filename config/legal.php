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

    'dpa' => [
        'version' => 'Draft 1.0',
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
