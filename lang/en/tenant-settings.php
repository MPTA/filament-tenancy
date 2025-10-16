<?php

return [
    'title' => 'Tenant Settings',
    'navigation_label' => 'Settings',
    
    'sections' => [
        'company_information' => [
            'title' => 'Company Information',
            'description' => 'Basic company details and information',
        ],
        'contact_information' => [
            'title' => 'Contact Information',
            'description' => 'Contact details and communication information',
        ],
        'location_preferences' => [
            'title' => 'Location & Preferences',
            'description' => 'Geographic location and system preferences',
        ],
        'base_budget_configuration' => [
            'title' => 'Base Budget Configuration',
            'description' => 'Set base budgets for drivers and companions',
        ],
    ],
    
    'actions' => [
        'save' => 'Save',
    ],
    
    'helpers' => [
        'logo_upload' => 'Upload your company logo. Maximum size: 2MB.',
        'signature_upload' => 'Upload your company signature. Maximum size: 2MB.',
        'currency_auto' => 'Currency is automatically determined by the selected country',
        'driver_meal_per_day' => 'Base budget for driver meals per day',
        'driver_accommodation_per_night' => 'Base budget for driver accommodation per night',
        'companion_meal_per_day' => 'Base budget for companion meals per day',
        'companion_accommodation_per_night' => 'Base budget for companion accommodation per night',
    ],
    
    'placeholders' => [
        'driver_meal_budget' => '50.00',
        'driver_accommodation_budget' => '100.00',
        'companion_meal_budget' => '50.00',
        'companion_accommodation_budget' => '100.00',
        'no_currency' => '—',
    ],
    
    'notifications' => [
        'updated_success' => 'Settings updated successfully!',
        'created_success' => 'Settings created successfully!',
        'error_title' => 'Error saving settings!',
    ],
];

