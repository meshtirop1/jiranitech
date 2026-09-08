<?php

/*
| The sub-processor register.
|
| A published register is a separate obligation from the Data Processing
| Addendum that promises it. The addendum agrees the change-notification terms;
| this file is the thing those terms are honoured against.
|
| It lives in configuration rather than in a table for two reasons. A register is
| a legal representation, so every change should arrive as a reviewed, dated
| commit rather than as a form submission with no reviewer — the git history is
| the change log a controller is entitled to see. And the host this runs on has
| no shell, so a new table could not be migrated onto it in any case.
|
| Adding, removing or changing an entry starts the notice period in
| `notice_days`. Record the change in `changes` at the same time: an entry that
| appears without a dated notice is a breach of the addendum, not a typo.
|
| `basis` carries how each location claim is known, the same discipline gate G-01
| applies to every figure on the site. A claim with a weak basis is published with
| a weak basis showing, not quietly rounded up.
*/

return [

    /*
    | Days of notice before a new sub-processor begins processing. Clause 7.4 of
    | the addendum gives the controller this long to object.
    */
    'notice_days' => 30,

    'register_version' => '1.0',
    'register_effective_on' => '2026-09-09',

    /*
    | Engaged on every engagement. A controller signing the addendum is
    | authorising these by name.
    */
    'engaged' => [
        [
            'name' => 'HostPinnacle Cloud Limited',
            'role' => 'Infrastructure and communications provider',
            'service' => 'Application hosting, database storage, electronic mail, DNS and release backups '
                .'for this website, the administration console and the delivery system.',
            'entity' => 'Incorporated in the Republic of Kenya. Registered office at 1st Floor, Studio House, '
                .'Marcus Garvey Road, off Argwings Kodhek Road, Kilimani, Nairobi.',
            'location' => 'Kenya — written confirmation of the serving facility outstanding',
            'basis' => 'Jurisdiction of incorporation and registered office taken from the provider\'s own '
                .'published corporate details. The physical location of the facility serving this account '
                .'has been requested from the provider in writing and is not yet confirmed, so it is not '
                .'asserted here as fact.',
            'transfer' => 'None relied upon. If the provider confirms that the serving facility sits outside '
                .'Kenya, clause 8 applies and the controller is notified under clause 7.4 before any '
                .'further processing.',
            'engaged_on' => '2026-09-06',
        ],
    ],

    /*
    | Not engaged by default. Each is used only where a specific engagement calls
    | for it, and only with that controller's written authorisation recorded in
    | Annex 1 of their addendum. Listing them here is not authorisation; it is
    | disclosure of what may be proposed.
    */
    'conditional' => [
        [
            'name' => 'GitHub, Inc.',
            'role' => 'Source control, where the engagement places its repository there',
            'service' => 'Hosting of the engagement\'s source repository, issues and build history. Engaged '
                .'only where the controller asks for the repository to be held on GitHub rather than on '
                .'infrastructure we or they operate.',
            'entity' => 'Incorporated in the United States of America.',
            'location' => 'United States and other regions operated by the provider',
            'basis' => 'The provider publishes a data protection agreement incorporating standard '
                .'contractual clauses. Its current terms are read and the transfer mechanism confirmed at '
                .'the point of engagement rather than assumed from this entry.',
            'transfer' => 'Standard contractual clauses under the provider\'s data protection agreement, plus '
                .'the controller\'s written authorisation under clause 7.2. A repository holding personal '
                .'data is not placed here without both.',
            'engaged_on' => null,
        ],
    ],

    /*
    | Every change to the two lists above, newest first. This is the record the
    | notice period is measured against.
    */
    'changes' => [
        [
            'on' => '2026-09-09',
            'note' => 'Register first published at version 1.0. HostPinnacle Cloud Limited recorded as the '
                .'sole engaged sub-processor. No change notice arises: no engagement predates this entry.',
        ],
    ],

];
