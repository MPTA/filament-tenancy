<?php

return [
    'resource_name' => 'Meal Type',
    'resource_name_plural' => 'Meal Types',
    'navigation_label' => 'Meal Types',
    'navigation_group' => 'Data Types',
    
    'sections' => [
        'meal_type_information' => [
            'title' => 'Meal Type Information',
            'description' => 'Enter the meal type details',
        ],
        'pricing_information' => [
            'title' => 'Pricing Information',
            'description' => 'Set pricing for this meal type',
        ],
        'meal_type_details' => [
            'title' => 'Meal Type Details',
        ],
        'description_pricing' => [
            'title' => 'Description & Pricing',
        ],
        'system_information' => [
            'title' => 'System Information',
        ],
    ],
    
    'fields' => [
        'meal_type_name' => 'Meal Type Name',
        'meal_category' => 'Meal Category',
    ],
    
    'columns' => [
        'meal_type' => 'Meal Type',
    ],
    
    'placeholders' => [
        'name' => 'e.g., Breakfast, Lunch, Dinner, Snack',
        'slug' => 'e.g., breakfast, lunch, dinner, snack',
        'description' => 'Brief description of the meal type',
        'meal_category' => 'Select a meal category',
        'price' => '0.00',
        'no_slug' => 'No slug',
        'no_category' => 'No category',
        'not_set' => 'Not set',
        'no_description' => 'No description',
        'no_description_provided' => 'No description provided',
        'not_specified' => 'Not specified',
        'not_available' => 'Not available',
    ],
    
    'helpers' => [
        'name' => 'Full name of the meal type',
        'slug' => 'URL-friendly identifier (auto-generated from name)',
        'meal_category' => 'Category this meal type belongs to',
        'description' => 'Short description for listings and previews',
        'price' => 'Price for this meal type',
    ],
    
    'filters' => [
        'meal_category' => 'Meal Category',
        'has_price' => 'Has Price',
        'has_price_yes' => 'Has Price',
        'has_price_no' => 'No Price',
    ],
    
    'bulk_actions' => [
        'export' => 'Export Selected',
        'duplicate' => 'Duplicate Selected',
    ],
    
    'notifications' => [
        'export_started_title' => 'Export Started',
        'export_started_body' => 'Selected meal types will be exported.',
        'duplication_complete_title' => 'Duplication Complete',
        'duplication_complete_body' => 'Selected meal types have been duplicated.',
    ],
    
    'messages' => [
        'slug_copied' => 'Slug copied',
        'name_copy_suffix' => ' (Copy)',
        'slug_copy_suffix' => '-copy',
    ],
    
    'global_search' => [
        'category' => 'Category',
        'price' => 'Price',
        'description' => 'Description',
    ],
];

