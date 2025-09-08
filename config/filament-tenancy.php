<?php

return [
    "central_domain" => env('CENTRAL_DOMAIN', 'localhost'),
    "single_database" => env('SINGLE_DATABASE', true),

    "features" => [
        "homepage" => true,
        "auth" => true,
        "impersonation" => true,
    ]
];
