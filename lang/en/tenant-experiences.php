<?php

return [
    'resource_name' => 'Experience',
    'resource_name_plural' => 'Experiences',
    'navigation_label' => 'Experiences',
    'navigation_group' => 'Content',
    
    'sections' => [
        'basic_information' => [
            'title' => 'Basic Information',
            'description' => 'Enter the basic experience details',
        ],
        'pricing_location' => [
            'title' => 'Pricing & Location',
            'description' => 'Set pricing and location information',
        ],
        'location_details' => [
            'title' => 'Location Details',
            'description' => 'Specify the location of the experience',
        ],
        'experience_details' => [
            'title' => 'Experience Details',
        ],
        'description_content' => [
            'title' => 'Description & Content',
        ],
        'pricing_information' => [
            'title' => 'Pricing Information',
        ],
        'location_information' => [
            'title' => 'Location Information',
        ],
        'system_information' => [
            'title' => 'System Information',
        ],
    ],
    
    'fields' => [
        'experience_name' => 'Experience Name',
        'content' => 'Content',
        'price_default_currency' => 'Price (Default Currency)',
        'free_for_companions' => 'Free for Companions',
    ],
    
    'columns' => [
        'experience_name' => 'Experience Name',
    ],
    
    'placeholders' => [
        'name' => 'e.g., City Walking Tour, Cultural Experience',
        'slug' => 'e.g., city-walking-tour, cultural-experience',
        'description' => 'Brief description of the experience',
        'content' => 'Detailed content about the experience',
        'price' => '0.00',
        'charge_mode' => 'Select charge mode',
        'city' => 'Select a city',
        'district' => 'Select a district',
        'address' => 'Full address of the experience location',
        'no_slug' => 'No slug',
        'not_specified' => 'Not specified',
        'free' => 'Free',
        'unknown' => 'Unknown',
        'no_description' => 'No description provided',
        'no_content' => 'No content provided',
        'no_address' => 'No address provided',
        'not_available' => 'Not available',
    ],
    
    'helpers' => [
        'name' => 'Full name of the experience',
        'slug' => 'URL-friendly identifier (auto-generated from name). Must enter name in English for automatic slug generation',
        'description' => 'Short description for listings and previews',
        'content' => 'Full content with all details about the experience',
        'price' => 'Price for the experience (in default currency)',
        'charge_mode' => 'How the experience is charged',
        'is_free_for_guide' => 'Whether this experience is free for tour guides',
        'is_free_for_other_companions' => 'Whether this experience is free for other companions (automatically enables free for guide)',
        'address' => 'Complete address including street, building, etc.',
        'city' => 'City where the experience takes place',
        'district' => 'District within the city',
    ],
    
    'filters' => [
        'charge_mode' => [
            'per_person' => 'Per Person',
            'per_group' => 'Per Group',
            'per_hour' => 'Per Hour',
            'fixed' => 'Fixed Price',
        ],
        'status' => [
            'placeholder' => 'All experiences',
            'true_label' => 'Active only',
            'false_label' => 'Inactive only',
        ],
    ],
    
    'messages' => [
        'slug_copied' => 'Slug copied',
    ],
    
    'bulk_actions' => [
        'activate' => 'Activate Selected',
        'deactivate' => 'Deactivate Selected',
        'export' => 'Export Selected',
    ],
    
    'notifications' => [
        'activated_title' => 'Experiences Activated',
        'activated_body' => 'Selected experiences have been activated.',
        'deactivated_title' => 'Experiences Deactivated',
        'deactivated_body' => 'Selected experiences have been deactivated.',
        'export_started_title' => 'Export Started',
        'export_started_body' => 'Selected experiences will be exported.',
    ],
    
    'global_search' => [
        'no_location' => 'No Location',
        'free' => 'Free',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'price_label' => 'Price',
        'city_label' => 'City',
        'status_label' => 'Status',
        'charge_mode_label' => 'Charge Mode',
        'not_specified' => 'Not specified',
    ],
    
    'empty_state' => [
        'heading' => 'No experiences yet',
        'description' => 'Create your first experience to get started.',
    ],
];

