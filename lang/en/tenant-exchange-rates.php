<?php

return [
    'resource_name' => 'Exchange Rate',
    'resource_name_plural' => 'Exchange Rates',
    'navigation_label' => 'Exchange Rates',
    'navigation_group' => 'Financial Management',
    
    'sections' => [
        'exchange_rate_information' => [
            'title' => 'Exchange Rate Information',
            'description' => 'Enter the exchange rate details',
        ],
    ],
    
    'fields' => [
        'to_currency_tenant_default' => 'To Currency (Tenant Default)',
    ],
    
    'placeholders' => [
        'from_currency' => 'Select source currency',
        'rate' => 'e.g., 1.25',
    ],
    
    'helpers' => [
        'to_currency_auto' => 'This is automatically set to your tenant\'s default currency',
        'rate' => 'Enter the exchange rate (1 from currency = X to currency)',
    ],
    
    'validations' => [
        'same_currency' => 'From Currency and To Currency cannot be the same.',
        'duplicate_combination' => 'This exchange rate combination already exists.',
    ],
    
    'bulk_actions' => [
        'export' => 'Export Selected',
    ],
    
    'notifications' => [
        'export_started_title' => 'Export Started',
        'export_started_body' => 'Selected exchange rates will be exported.',
    ],
    
    'global_search' => [
        'to' => 'to',
        'from' => 'From',
        'to_label' => 'To',
        'rate_label' => 'Rate',
        'unknown' => 'Unknown',
    ],
    
    'empty_state' => [
        'heading' => 'No exchange rates yet',
        'description' => 'Create your first exchange rate to get started.',
    ],
];

