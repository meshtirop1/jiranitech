<?php

/*
| Corporate identity used across the site chrome.
|
| Three distinct entities, deliberately kept apart:
|
|   Jiranisoko Market Ltd          the holding company (PVT-YQ195JQY)
|   ├── Jiranisoko Tech Solutions  this site, the enterprise technology division
|   └── JiraniSoko Marketplace     the consumer marketplace at jiranisoko.com
|
| The marketplace is a sibling platform, not the parent. Linking the holding
| company's name to the marketplace sends a procurement reviewer who clicked
| "Investor & Group" to a consumer classifieds app, which is why `parent_url`
| and `marketplace_url` are separate settings and neither falls back to the other.
*/

return [

    'legal_name' => env('COMPANY_LEGAL_NAME', 'Jiranisoko Tech Solutions'),
    'division_line' => 'A division of '.env('COMPANY_PARENT_NAME', 'Jiranisoko Market Ltd'),

    /*
    | The holding company. `parent_url` is the holding company's own corporate site.
    | Leave it unset until one exists — the group link then resolves to this site's
    | own group page rather than pointing somewhere that is not the holding company.
    */
    'parent' => [
        'name' => env('COMPANY_PARENT_NAME', 'Jiranisoko Market Ltd'),
        'url' => env('COMPANY_PARENT_URL'),
        'registration_number' => env('COMPANY_PARENT_REGISTRATION_NUMBER', 'PVT-YQ195JQY'),

        /*
        | KRA personal identification number, from the company's PIN certificate.
        | It belongs to the holding company for the same reason the registration
        | number does: this division is not a separate taxpayer. Kenyan procurement
        | asks for it, so publishing it saves a round trip — but it is a setting,
        | not a constant, and clearing it removes it from the footer.
        */
        'tax_pin' => env('COMPANY_PARENT_TAX_PIN'),

        'incorporated_on' => env('COMPANY_PARENT_INCORPORATED_ON', '2026-03-30'),
        'jurisdiction' => 'Republic of Kenya, Companies Act 2015',
    ],

    /*
    | Sibling platform operated by the group. Referenced as evidence that we operate
    | systems rather than only build them. It is not the parent and must never be
    | linked as such.
    */
    'marketplace' => [
        'name' => env('COMPANY_MARKETPLACE_NAME', 'JiraniSoko Marketplace'),
        'url' => env('COMPANY_MARKETPLACE_URL', 'https://jiranisoko.com'),
    ],

    // Kept for templates that only need the parent's display name.
    'parent_name' => env('COMPANY_PARENT_NAME', 'Jiranisoko Market Ltd'),

    /*
    | Street-level line only. The footer and the schema.org graph add the city and
    | country from the two keys below, so putting them here as well would repeat
    | them on the page and inside PostalAddress.
    */
    'registered_address' => env('COMPANY_REGISTERED_ADDRESS'),

    // Correspondence goes to the box, not the building.
    'postal_address' => env('COMPANY_POSTAL_ADDRESS'),

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
