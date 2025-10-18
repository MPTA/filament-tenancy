<?php

return [
    // Wizard Steps
    'steps' => [
        'vehicle_types' => 'Vehicle Types',
        'tickets' => 'Tickets',
        'meals' => 'Meals',
        'hotels' => 'Hotels',
        'attractions' => 'Attractions',
        'companions' => 'Companions',
        'expenses' => 'Expenses',
    ],

    // Vehicle Types Section
    'vehicle_types' => [
        'section_title' => 'Vehicle Types',
        'quantities_section' => 'Vehicle Quantities',
        'vehicle_days' => 'Vehicle Days',
        'vehicle_half_days' => 'Vehicle Half Days',
        'vehicle_hours' => 'Vehicle Hours',
        'airport_transfers' => 'Airport Transfers',
        'driver_meal_budget' => 'Driver Meal Budget',
        'driver_accommodation_budget' => 'Driver Accommodation Budget',
        'vehicle_type' => 'Vehicle Type',
        'per_day_price' => 'Per Day Price',
        'half_day_price' => 'Half Day Price',
        'per_hour_price' => 'Per Hour Price',
        'airport_transfer_price' => 'Airport Transfer Price',
        'cannot_delete_in_use' => 'Cannot delete: This vehicle type is being used in :count offer group(s)',
    ],

    // Tickets Section
    'tickets' => [
        'transport_mode' => 'Transport Mode',
        'class' => 'Class',
        'from_city' => 'From City',
        'to_city' => 'To City',
        'price' => 'Price',
    ],

    // Meals Section
    'meals' => [
        'meal_type' => 'Meal Type',
        'price' => 'Price',
    ],

    // Hotels Section
    'hotels' => [
        'accommodation' => 'Accommodation',
        'city' => 'City',
        'nights' => 'Nights',
        'has_breakfast' => 'Has Breakfast',
        'room_categories' => 'Room Categories',
        'room_category' => 'Room Category',
        'price' => 'Price',
    ],

    // Attractions Section
    'attractions' => [
        'section_title' => 'Attractions',
        'experiences_section' => 'Experiences',
        'experience' => 'Experience',
        'charge_mode' => 'Charge Mode',
        'free_for_guide' => 'Free for Guide',
        'free_for_companions' => 'Free for Companions',
        'price' => 'Price',
        'attraction' => 'Attraction',
        'city' => 'City',
        'entry_price' => 'Entry Price',
        'outview' => 'Outview',
        'sub_attractions' => 'Sub Attractions',
        'sub_attraction' => 'Sub Attraction',
    ],

    // Companions Section
    'companions' => [
        'section_title' => 'Companions',
        'budgets_section' => 'Companion Budgets',
        'companion_meal_budget' => 'Companion Meal Budget',
        'companion_accommodation_budget' => 'Companion Accommodation Budget',
        'companion_type' => 'Companion Type',
        'companion_category' => 'Companion Category',
        'per_day_price' => 'Per Day Price',
        'half_day_price' => 'Half Day Price',
        'per_hour_price' => 'Per Hour Price',
        'cannot_delete_in_use' => 'Cannot delete: This companion type is being used in :count offer group(s)',
    ],

    // Expenses Section
    'expenses' => [
        'section_title' => 'Expenses',
        'expense' => 'Expense',
        'charge_mode' => 'Charge Mode',
        'price' => 'Price',
    ],

    // Validation Messages
    'validation' => [
        'numeric' => 'Value must be a valid number',
        'min' => 'Value cannot be negative',
        'required' => ':field is required',
        'required_field' => [
            'driver_meal_budget' => 'Driver meal budget is required',
            'driver_accommodation_budget' => 'Driver accommodation budget is required',
            'entry_price' => 'Entry price is required',
            'companion_meal_budget' => 'Companion meal budget is required',
            'companion_accommodation_budget' => 'Companion accommodation budget is required',
            'price' => 'Price is required',
        ],
    ],

    // Placeholders
    'placeholders' => [
        'enter_price' => 'Enter price',
        'select_value' => 'Select value',
    ],

    // Page Titles and Actions
    'page' => [
        'edit_breakdown_title' => 'Edit Breakdown - Quotation :number',
        'store' => 'Store',
        'save_and_close' => 'Save and Close',
        'save_and_complete' => 'Save and Complete',
        'view_quotation' => 'View Quotation',
        'complete_breakdown_heading' => 'Complete Breakdown',
        'complete_breakdown_description' => 'Are you sure you want to complete this breakdown? This will save, mark it as complete, and redirect you to the Offers tab.',
        'complete_breakdown_submit' => 'Yes, Complete',
        'cannot_edit_title' => 'Cannot Edit Breakdown',
        'cannot_edit_body' => 'Please complete the itinerary before editing the breakdown.',
        'saved_successfully' => 'Breakdown saved successfully!',
        'error_saving' => 'Error saving breakdown',
        'completed_successfully' => 'Breakdown completed successfully!',
        'can_create_offers' => 'You can now create offers.',
        'error_completing' => 'Error completing breakdown',
        'cannot_complete_title' => 'Cannot Complete Breakdown',
        'cannot_complete_body' => 'Please complete the itinerary first.',
        'cannot_save_title' => 'Cannot Save Breakdown',
        'cannot_save_body' => 'The itinerary is not complete. Please complete the itinerary first.',
    ],
];

