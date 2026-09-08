<?php

/*
| The delivery system.
|
| It runs inside this application rather than as a second one: the host has no
| shell, so a separate deployment would double the operational surface for no
| benefit, and both need the same people, the same session table and the same
| design system. What separates them is the hostname, not the codebase.
*/

return [

    /*
    | Requests on this host reach the delivery system; everything else reaches
    | the public site. Left null in local development so the routes answer on
    | whatever host the dev server is using.
    */
    'domain' => env('ERP_DOMAIN'),

    'name' => env('ERP_NAME', 'Jiranisoko Delivery'),

    /*
    | Reference formats. Both are quoted in correspondence and in commit
    | messages, so they are short and unambiguous rather than sequential ids.
    */
    'project_prefix' => env('ERP_PROJECT_PREFIX', 'JTS-P'),

];
