<?php

return [
    'resource_name' => 'Attraction',
    'resource_name_plural' => 'Attractions',
    'navigation_label' => 'Attractions',
    
    'sections' => [
        'basic_information' => [
            'title' => 'Basic Information',
            'description' => 'Basic attraction details and information',
        ],
        'tenant_pricing' => [
            'title' => 'Tenant Pricing',
            'description' => 'Manage your custom prices for this attraction',
        ],
        'sub_attractions_pricing' => [
            'title' => 'Sub Attractions Pricing',
            'description' => 'Manage prices for sub attractions',
        ],
    ],
    
    'columns' => [
        'tenant_prices' => 'Tenant Prices',
    ],
    
    'filters' => [
        'type' => [
            'historical' => 'Historical',
            'natural' => 'Natural',
            'cultural' => 'Cultural',
            'religious' => 'Religious',
            'entertainment' => 'Entertainment',
        ],
    ],
    
    'actions' => [
        'edit_pricing' => 'Edit Pricing',
    ],
    
    'modals' => [
        'edit_pricing_heading' => 'Edit Attraction Pricing (Tenant Default Currency)',
    ],
    
    'repeater' => [
        'sub_attractions' => 'Sub Attractions',
    ],
    
    'messages' => [
        'not_set' => 'Not set',
        'no_additional_content' => 'No additional content',
        'prices_count' => ':count prices',
    ],
    
    'notifications' => [
        'pricing_updated' => 'Pricing updated successfully!',
    ],
    
    'empty_state' => [
        'heading' => 'No attractions available',
        'description' => 'Contact admin to add attractions to your tenant.',
    ],
];

