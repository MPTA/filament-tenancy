<?php

return [
    'resource_name' => 'Companion Type',
    'resource_name_plural' => 'Companion Types',
    'navigation_label' => 'Companion Types',
    'navigation_group' => 'Data Types',
    
    'sections' => [
        'basic_information' => [
            'title' => 'Basic Information',
            'description' => 'Enter the basic companion type details',
        ],
        'language_requirements' => [
            'title' => 'Language Requirements',
            'description' => 'Specify language requirements for this companion type',
        ],
        'pricing_configuration' => [
            'title' => 'Pricing Configuration',
            'description' => 'Set pricing for different service durations',
        ],
        'pricing_information' => [
            'title' => 'Pricing Information',
        ],
        'service_limits' => [
            'title' => 'Service Limits',
            'description' => 'Define maximum service hours and time limits',
        ],
        'system_information' => [
            'title' => 'System Information',
        ],
    ],
    
    'fields' => [
        'companion_type_name' => 'Companion Type Name',
    ],
    
    'columns' => [
        'companion_type' => 'Companion Type',
    ],
    
    'placeholders' => [
        'name' => 'e.g., Professional Guide, Cultural Companion',
        'slug' => 'e.g., professional-guide, cultural-companion',
        'companion_category' => 'Select a companion category',
        'native_language' => 'Select native language',
        'speaking_language' => 'Select speaking language',
        'no_category' => 'No category',
        'not_specified' => 'Not specified',
        'not_set' => 'Not set',
        'no_slug' => 'No slug',
        'price_placeholder' => '0.00',
        'max_hours_day' => '8',
        'max_hours_half' => '4',
    ],
    
    'helpers' => [
        'name' => 'Full name of the companion type',
        'slug' => 'URL-friendly identifier (auto-generated from name)',
        'category' => 'Category this companion type belongs to',
        'native_language' => 'Primary language of the companion',
        'speaking_language' => 'Language the companion can speak',
        'per_day_price' => 'Price for full day service',
        'half_day_price' => 'Price for half day service',
        'per_hour_price' => 'Price per hour of service',
        'extra_hour_price' => 'Price for additional hours beyond limit',
        'max_hours_per_day' => 'Maximum hours allowed per day',
        'max_hours_half_day' => 'Maximum hours allowed for half day service',
    ],
    
    'suffixes' => [
        'hours' => 'hours',
    ],
    
    'messages' => [
        'slug_copied' => 'Slug copied',
    ],
    
    'validations' => [
        'duplicate_combination' => 'A companion type with this category, native language, and speaking language combination already exists for this tenant. Please choose different values.',
    ],
    
    'bulk_actions' => [
        'export' => 'Export Selected',
        'duplicate' => 'Duplicate Selected',
    ],
    
    'notifications' => [
        'export_started_title' => 'Export Started',
        'export_started_body' => 'Selected companion types will be exported.',
        'duplication_complete_title' => 'Duplication Complete',
        'duplication_complete_body' => 'Selected companion types have been duplicated.',
    ],
    
    'global_search' => [
        'no_category' => 'No Category',
        'not_specified' => 'Not specified',
        'not_set' => 'Not set',
        'category_label' => 'Category',
        'native_language_label' => 'Native Language',
        'speaking_language_label' => 'Speaking Language',
        'per_day_price_label' => 'Per Day Price',
    ],
    
    'copy_suffix' => ' (Copy)',
    'copy_slug_suffix' => '-copy',
    
    'empty_state' => [
        'heading' => 'No companion types yet',
        'description' => 'Create your first companion type to get started.',
    ],
];

