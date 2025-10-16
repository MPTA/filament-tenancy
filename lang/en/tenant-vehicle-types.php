<?php

return [
    'resource_name' => 'Vehicle Type',
    'resource_name_plural' => 'Vehicle Types',
    'navigation_label' => 'Vehicle Types',
    'navigation_group' => 'Data Types',
    
    'sections' => [
        'basic_information' => [
            'title' => 'Basic Information',
            'description' => 'Enter the vehicle type details',
        ],
        'capacity_specifications' => [
            'title' => 'Capacity & Specifications',
            'description' => 'Set passenger capacity and specifications',
        ],
        'pricing_configuration' => [
            'title' => 'Pricing Configuration',
            'description' => 'Set pricing for different service types',
        ],
        'service_options' => [
            'title' => 'Service Options',
            'description' => 'Configure service options and features',
        ],
        'vehicle_type_details' => [
            'title' => 'Vehicle Type Details',
        ],
        'pricing_information' => [
            'title' => 'Pricing Information',
        ],
        'media_description' => [
            'title' => 'Media & Description',
        ],
        'system_information' => [
            'title' => 'System Information',
        ],
    ],
    
    'fields' => [
        'vehicle_type_name' => 'Vehicle Type Name',
        'vehicle_category' => 'Vehicle Category',
        'cover_image_url' => 'Cover Image URL',
        'minimum_capacity' => 'Minimum Capacity',
        'maximum_capacity' => 'Maximum Capacity',
        'min_capacity' => 'Min Capacity',
        'max_capacity' => 'Max Capacity',
        'max_hours_per_day' => 'Max Hours Per Day',
        'max_hours_half_day' => 'Max Hours Half Day',
        'max_hours_day' => 'Max Hours/Day',
        'max_hours_half_day_short' => 'Max Hours/Half Day',
        'per_day_price' => 'Per Day Price',
        'half_day_price' => 'Half Day Price',
        'extra_hour_price' => 'Extra Hour Price',
        'airport_transfer_price' => 'Airport Transfer Price',
        'vip_service' => 'VIP Service',
        'cover_image' => 'Cover Image',
    ],
    
    'columns' => [
        'vehicle_type' => 'Vehicle Type',
        'airport_transfer' => 'Airport Transfer',
    ],
    
    'placeholders' => [
        'name' => 'e.g., Sedan, SUV, Minivan, Bus',
        'slug' => 'e.g., sedan, suv, minivan, bus',
        'vehicle_category' => 'Select a vehicle category',
        'cover_url' => 'https://example.com/image.jpg',
        'description' => 'Brief description of the vehicle type',
        'capacity_min' => '1',
        'capacity_max' => '4',
        'max_hours_day' => '8',
        'max_hours_half' => '4',
        'price' => '0.00',
        'no_slug' => 'No slug',
        'no_category' => 'No category',
        'not_set' => 'Not set',
        'not_specified' => 'Not specified',
        'not_available' => 'Not available',
        'no_cover_image' => 'No cover image',
        'no_description_provided' => 'No description provided',
    ],
    
    'helpers' => [
        'name' => 'Full name of the vehicle type',
        'slug' => 'URL-friendly identifier (auto-generated from name)',
        'vehicle_category' => 'Category this vehicle type belongs to',
        'cover_url' => 'URL to the vehicle cover image',
        'description' => 'Short description for listings and previews',
        'capacity_min' => 'Minimum number of passengers',
        'capacity_max' => 'Maximum number of passengers',
        'max_hours_day' => 'Maximum hours allowed per day',
        'max_hours_half' => 'Maximum hours for half day service',
        'per_day_price' => 'Price for full day service',
        'half_day_price' => 'Price for half day service',
        'extra_hour_price' => 'Price for each extra hour',
        'airport_transfer_price' => 'Price for airport transfer service',
        'vip_service' => 'Enable VIP service features',
    ],
    
    'suffixes' => [
        'passengers' => 'passengers',
        'pax' => 'pax',
        'hours' => 'hours',
    ],
    
    'filters' => [
        'vehicle_category' => 'Vehicle Category',
        'vip_service' => 'VIP Service',
        'all_vehicles' => 'All vehicles',
        'vip_only' => 'VIP only',
        'non_vip_only' => 'Non-VIP only',
        'capacity_range' => 'Capacity Range',
        'capacity_1_4' => '1-4 passengers',
        'capacity_5_8' => '5-8 passengers',
        'capacity_9_16' => '9-16 passengers',
        'capacity_17_30' => '17-30 passengers',
        'capacity_30_plus' => '30+ passengers',
        'has_pricing' => 'Has Pricing',
        'has_pricing_yes' => 'Has Pricing',
        'has_pricing_no' => 'No Pricing',
    ],
    
    'bulk_actions' => [
        'export' => 'Export Selected',
        'duplicate' => 'Duplicate Selected',
        'toggle_vip' => 'Toggle VIP Status',
    ],
    
    'notifications' => [
        'export_started_title' => 'Export Started',
        'export_started_body' => 'Selected vehicle types will be exported.',
        'duplication_complete_title' => 'Duplication Complete',
        'duplication_complete_body' => 'Selected vehicle types have been duplicated.',
        'vip_status_updated_title' => 'VIP Status Updated',
        'vip_status_updated_body' => 'Selected vehicle types VIP status has been toggled.',
    ],
    
    'messages' => [
        'slug_copied' => 'Slug copied',
        'name_copy_suffix' => ' (Copy)',
        'slug_copy_suffix' => '-copy',
    ],
    
    'global_search' => [
        'category' => 'Category',
        'capacity' => 'Capacity',
        'vip' => 'VIP',
        'per_day_price' => 'Per Day Price',
        'vip_service' => 'VIP Service',
        'standard_service' => 'Standard Service',
    ],
];

