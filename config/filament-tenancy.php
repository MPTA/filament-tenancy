<?php

return [
    "central_domain" => env('CENTRAL_DOMAIN', 'localhost'),
    "single_database" => env('SINGLE_DATABASE', true),
    "tenant_user_model" => \App\Models\Tenants\TenantUser::class,

    /**
     * Tenant identification method
     * Options: 'domain', 'subdomain', 'path'
     */
    "identification_method" => env('TENANT_IDENTIFICATION', 'path'),

    "features" => [
        "homepage" => true,
        "auth" => true,
        "impersonation" => true,
    ]
];
