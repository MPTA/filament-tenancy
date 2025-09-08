<?php

return [
    "central_domain" => env('CENTRAL_DOMAIN', 'localhost'),
    "single_database" => env('SINGLE_DATABASE', true),
    "tenant_user_model" => \App\Models\Tenants\TenantUser::class,

    "features" => [
        "homepage" => true,
        "auth" => true,
        "impersonation" => true,
    ]
];
