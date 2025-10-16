<?php

return [
    'resource_name' => 'Accommodation',
    'resource_name_plural' => 'Accommodations',
    'navigation_label' => 'Accommodations',
    
    'sections' => [
        'basic_information' => [
            'title' => 'Basic Information',
            'description' => 'Accommodation basic details',
        ],
        'location_information' => [
            'title' => 'Location Information',
            'description' => 'Accommodation location details',
        ],
    ],
    
    'columns' => [
        'tenant_prices' => 'Tenant Prices',
    ],
    
    'filters' => [
        'star_rating' => [
            '1' => '1 Star',
            '2' => '2 Stars',
            '3' => '3 Stars',
            '4' => '4 Stars',
            '5' => '5 Stars',
        ],
    ],
    
    'messages' => [
        'no_rating' => 'No rating',
        'prices_count' => ':count prices',
    ],
    
    'empty_state' => [
        'heading' => 'No accommodations available',
        'description' => 'Contact admin to add accommodations to your tenant.',
    ],
];

