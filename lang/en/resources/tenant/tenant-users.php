<?php

return [
    'resource_name' => 'User',
    'resource_name_plural' => 'Users',
    'navigation_label' => 'Users',
    'navigation_group' => 'User Management',
    
    'sections' => [
        'essential_information' => [
            'title' => 'Essential Information',
            'description' => 'Required information to create a user account',
        ],
        'contact_information' => [
            'title' => 'Contact Information',
            'description' => 'Contact details for this user',
        ],
    ],
    
    'fields' => [
        // Uses common.fields for standard fields
    ],
    
    'columns' => [
        // Uses common.fields for standard columns
    ],
    
    'filters' => [
        'email_verified' => [
            'label' => 'Email Verified',
            'placeholder' => 'All Users',
            'true_label' => 'Verified',
            'false_label' => 'Not Verified',
        ],
    ],
    
    'messages' => [
        'email_copied' => 'Email copied!',
        'password_helper' => 'Leave blank to keep current password',
    ],
    
    'placeholders' => [
        'no_data' => '—',
    ],
];

