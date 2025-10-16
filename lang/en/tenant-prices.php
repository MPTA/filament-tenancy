<?php

return [
    'title' => 'Tenant Prices',
    
    'sections' => [
        'meal_inclusion' => [
            'title' => 'Meal Inclusion',
            'description' => 'Select which meals are included in this price',
        ],
    ],
    
    'columns' => [
        'meals_included' => 'Meals Included',
    ],
    
    'filters' => [
        'active_prices' => 'Active Prices',
        'expired_prices' => 'Expired Prices',
        'with_breakfast' => 'With Breakfast',
        'with_lunch' => 'With Lunch',
        'with_dinner' => 'With Dinner',
    ],
    
    'actions' => [
        'add_price' => 'Add Price',
    ],
    
    'messages' => [
        'included' => 'Included',
        'not_included' => 'Not Included',
        'active' => 'Active',
        'expired' => 'Expired',
        'no_meals' => 'No meals',
        'indefinite' => 'Indefinite',
        'indefinite_validity' => 'Indefinite validity',
    ],
    
    'placeholders' => [
        'valid_to' => 'Leave empty for indefinite validity',
    ],
    
    'validations' => [
        'room_category_required' => 'Room category is required.',
        'price_required' => 'Price is required',
        'price_numeric' => 'Price must be a number',
        'price_min' => 'Price cannot be negative',
        'valid_from_required' => 'Valid from date is required.',
        'valid_from_date' => 'Please enter a valid date.',
    ],
    
    'notifications' => [
        'duplicate_price_title' => 'Duplicate Price',
        'duplicate_price_body' => 'A price with the same room category, date, and meal inclusion already exists.',
    ],
    
    'empty_state' => [
        'heading' => 'No prices yet',
        'description' => 'Add a price for this accommodation to get started.',
    ],
];

