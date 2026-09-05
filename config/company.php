<?php

/*
| Corporate identity used across the site chrome.
|
| Publication gate G-07 (JTS-WEB-IA-001 section 4): the registered office, company
| registration number and a named contact route are mandatory before launch. Their
| absence is a documented disqualifier in institutional and public-sector vendor
| screening. Values resolve from the environment so they can differ per deployment.
*/

return [

    'legal_name' => env('COMPANY_LEGAL_NAME', 'Jiranisoko Tech Solutions'),
    'parent_name' => env('COMPANY_PARENT_NAME', 'Jiranisoko Market Ltd'),
    'parent_url' => env('COMPANY_PARENT_URL', 'https://jiranisoko.com'),
    'division_line' => 'A division of '.env('COMPANY_PARENT_NAME', 'Jiranisoko Market Ltd'),

    'registered_address' => env('COMPANY_REGISTERED_ADDRESS'),
    'registration_number' => env('COMPANY_REGISTRATION_NUMBER'),

    'city' => env('COMPANY_CITY', 'Eldoret'),
    'country' => env('COMPANY_COUNTRY', 'Kenya'),
    'timezone_label' => 'EAT (UTC+3)',
    'office_hours' => '08:30–17:30 EAT, Monday to Friday',

    'email' => [
        'enquiries' => env('COMPANY_EMAIL_ENQUIRIES'),
        'rfp' => env('COMPANY_EMAIL_RFP'),
        'security' => env('COMPANY_EMAIL_SECURITY'),
    ],

    'telephone' => env('COMPANY_TELEPHONE'),

    'client_portal_url' => env('COMPANY_CLIENT_PORTAL_URL'),

    /*
    | Response commitments quoted in H-02 and H-11 microcopy. These are commercial
    | promises: change them here rather than in the templates so one edit governs
    | every place the site states them.
    */
    'response' => [
        'acknowledgement' => 'one business day',
        'substantive' => 'two business days',
    ],

];
