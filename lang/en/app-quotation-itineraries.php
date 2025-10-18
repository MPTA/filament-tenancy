<?php

return [
    'resource_name' => 'Quotation',
    'resource_name_plural' => 'Quotations',
    'navigation_label' => 'Quotations',
    'navigation_group' => 'Quotations',
    
    // Form Fields
    'fields' => [
        'inquiry_title' => 'Inquiry Title',
        'title' => 'Title',
        'attachments' => 'Attachments',
        'reference' => 'Reference',
        'contact' => 'Contact',
        'requested_currency' => 'Requested Currency',
        'inquiry_number' => 'Inquiry Number',
        'quotation_number' => 'Quotation Number',
        'currency' => 'Currency',
        'date_type' => 'Date Type',
        'accommodation_stars' => 'Accommodation Stars',
        'arrival' => 'Arrival',
        'departure' => 'Departure',
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
        'from_date' => 'From Date',
        'to_date' => 'To Date',
        'exchange_rate' => 'Exchange Rate',
        'expire_date' => 'Expire Date',
        'expiry_date' => 'Expiry Date',
        'room_categories' => 'Room Categories',
        'foreigner_passengers' => 'Foreigner Passengers',
        'description' => 'Description',
        'internal_note' => 'Internal Note',
        'first_name' => 'First name',
        'last_name' => 'Last name',
        'mobile' => 'Mobile',
        'postal_address' => 'Postal Address',
        'is_customer' => 'Is Customer',
        'entry_date_arrival' => 'Entry Date (Arrival)',
        'travel_mode' => 'Travel Mode',
        'total_days' => 'Total Days',
        'total_activities' => 'Total Activities',
        'breakfast' => 'Breakfast',
        'lunch' => 'Lunch',
        'dinner' => 'Dinner',
        'tickets' => 'Tickets',
        'attractions' => 'Attractions',
        'experiences' => 'Experiences',
        'vehicle' => 'Vehicle',
        'companion' => 'Companion',
        'enter_transportation_details' => 'Enter Transportation Details',
        'vehicle_days' => 'Vehicle Days',
        'half_days' => 'Half Days',
        'vehicle_hours' => 'Vehicle Hours',
        'driver_meal_budget' => 'Driver Meal Budget',
        'driver_accommodation_budget' => 'Driver Accommodation Budget',
        'companion_meal_budget' => 'Companion Meal Budget',
        'companion_accommodation_budget' => 'Companion Accommodation Budget',
        'vehicle_type' => 'Vehicle Type',
        'per_day_price' => 'Per Day Price',
        'half_day_price' => 'Half Day Price',
        'per_hour_price' => 'Per Hour Price',
        'transport_mode' => 'Transport Mode',
        'from_city' => 'From City',
        'to_city' => 'To City',
        'class' => 'Class',
        'price' => 'Price',
        'meal_type' => 'Meal Type',
        'charge_mode' => 'Charge Mode',
        'hotel' => 'Hotel',
        'nights' => 'Nights',
        'room_prices' => 'Room Prices',
        'attraction' => 'Attraction',
        'outview' => 'Outview',
        'entry_price' => 'Entry Price',
        'sub_attractions' => 'Sub-Attractions',
        'companion_type' => 'Companion Type',
        'offer_group_number' => 'Offer Group Number',
        'created_at' => 'Created At',
        'include_driver_meal' => 'Include Driver Meal',
        'driver_same_meal' => 'Driver Same Meal',
        'include_driver_hotel' => 'Include Driver Hotel',
        'driver_stays_same_hotel' => 'Driver Stays Same Hotel',
        'driver_room_type' => 'Driver Room Type',
        'same_meal' => 'Same Meal',
        'stay_same_hotel' => 'Stay Same Hotel',
        'room_type' => 'Room Type',
        'living_city' => 'Living City',
        'driver_meal_cost' => 'Driver Meal Cost',
        'driver_hotel_cost' => 'Driver Hotel Cost',
        'same_hotel' => 'Same Hotel',
        'driver_room' => 'Driver Room',
        'car' => 'Car',
        'pax' => 'PAX',
        'drivers' => 'Drivers',
        'markup' => 'Markup',
        'leaders_quantity' => 'Leaders Quantity',
        'leader_room_category' => 'Leader Room Category',
        'pax_quantity' => 'PAX Quantity',
        'drivers_quantity' => 'Drivers Quantity',
        'markup_percent' => 'Markup (%)',
        'full_days' => 'Full Days',
        'hours' => 'Hours',
        'day_price' => 'Day Price',
        'total_companion_salary' => 'Total Companion Salary',
        'meal_records' => 'Meal Records',
        'quantity' => 'Quantity',
        'unit_price' => 'Unit Price',
        'total_price' => 'Total Price',
        'ticket_records' => 'Ticket Records',
        'experience_records' => 'Experience Records',
        'attraction_records' => 'Attraction Records',
        'per_night' => 'Per Night',
        'total_cost' => 'Total Cost',
        'expense_records' => 'Expense Records',
        'accommodation_records' => 'Accommodation Records',
        'total_companion_cost' => 'Total Companion Cost',
        'salary' => 'Salary',
        'accommodation' => 'Accommodation',
    ],
    
    'helpers' => [
        'foreigner_passengers' => 'Check if this quotation is for foreign passengers',
        'foreigner_passengers_affects' => 'Enable if passengers are foreigners (affects attraction pricing)',
        'attachments' => 'You can upload up to 10 files. Max size: 10MB per file.',
        'room_categories' => 'Select up to 3 room types (required).',
        'room_categories_regenerate' => 'Select up to 3 room types (required). Changing this will regenerate the breakdown.',
        'from_date_required' => 'Please select From Date first',
        'to_date_after_from' => 'Must be same or after From Date',
        'exchange_rate_format' => 'Enter a valid number with up to 4 decimal places (e.g., 42500.5000)',
        'exchange_rate_display' => '1 :from = :rate :to',
        'description_visible' => 'This description will be visible to the customer in the quotation view.',
        'internal_note_private' => 'Internal note - NOT visible to the customer. Use this for team notes and reminders.',
        'currency_sync' => 'Changing currency will update both Inquiry and Quotation',
        'currency_sync_quotation' => 'Changing currency will update both Quotation and Inquiry',
        'entry_date_arrival' => 'Entry date for the group arrival',
        'entry_date_controlled' => 'Entry date is controlled by transportation. Remove transportation to edit manually.',
        'enter_transportation_details' => 'Enable to add entry and exit transportation information',
        'include_driver_meal_helper' => 'Should driver meal cost be included in calculations?',
        'driver_same_meal_helper' => 'Does the driver have the same meals as passengers?',
        'include_driver_hotel_helper' => 'Should driver hotel cost be included in calculations?',
        'driver_stays_same_hotel_helper' => 'Does the driver stay in the same hotel as passengers?',
        'select_driver_room_type' => 'Select driver room type',
        'companion_same_meal_helper' => 'Does this companion have the same meals?',
        'companion_stay_same_hotel_helper' => 'Does this companion stay in the same hotel?',
        'select_room_type' => 'Select room type',
    ],
    
    'placeholders' => [
        'exchange_rate' => 'e.g., 1.0000',
        'exchange_rate_modal' => 'e.g., 42500.5000',
        'no_title' => 'No title',
        'no_number' => 'No number',
        'no_contact' => 'No contact',
        'not_specified' => 'Not specified',
        'no_reference' => 'No reference',
        'no_attachments' => 'No attachments',
        'no_description' => 'No description',
        'no_internal_note' => 'No internal note',
        'default_room_categories' => 'Default (Twin, Single)',
        'no_contact_info' => 'No contact information available',
        'select_travel_mode' => 'Select travel mode',
        'no_transportations' => 'No transportation details available. Click "Create Transportation" to add entry and exit transportation.',
        'unknown_city' => 'Unknown City',
        'not_specified' => 'Not specified',
        'na' => 'N/A',
        'unknown' => 'Unknown',
        'no_prices' => 'No prices',
    ],
    
    // Repeater Labels
    'repeater_labels' => [
        'add_companion' => 'Add Companion',
        'new_companion' => 'New Companion',
    ],
    
    'validations' => [
        'exchange_rate_regex' => 'Please enter a valid number with up to 4 decimal places.',
        'exchange_rate_numeric' => 'Exchange rate must be a number.',
        'exchange_rate_gt' => 'Exchange rate must be greater than 0.',
        'to_date_after_from_date' => 'The end date must be same or after the start date.',
    ],
    
    // Table Columns
    'columns' => [
        'number' => 'Number',
        'inquiry_title' => 'Inquiry Title',
        'contact' => 'Contact',
        'offers' => 'Offers',
        'expires' => 'Expires',
        'status' => 'Status',
        'created' => 'Created',
        'updated' => 'Updated',
    ],
    
    // Filters
    'filters' => [
        'status' => 'Status',
        'active' => 'Active',
        'expired' => 'Expired',
    ],
    
    // Tabs
    'tabs' => [
        'information' => 'Information',
        'itinerary' => 'Itinerary',
        'breakdown' => 'Breakdown',
        'offers' => 'Offers',
    ],
    
    // Sections
    'sections' => [
        'inquiry_information' => [
            'title' => 'Inquiry Information',
            'description' => 'Basic inquiry details and information',
        ],
        'inquiry_itinerary_details' => [
            'title' => 'Inquiry Itinerary Details',
            'description' => 'Travel itinerary information',
        ],
        'quotation_information' => [
            'title' => 'Quotation Information',
            'description' => 'Quotation and pricing details',
        ],
        'personal_information' => [
            'title' => 'Personal Information',
        ],
        'contact_information' => [
            'title' => 'Contact Information',
        ],
        'customer_status' => [
            'title' => 'Customer Status',
        ],
        'group_transportations' => [
            'title' => 'Group Transportations',
            'description' => 'Entry and exit transportation details for the group',
        ],
        'itinerary_summary' => [
            'title' => 'Itinerary Summary',
            'description' => 'Quick overview of your travel plan',
        ],
        'create_itinerary' => [
            'title' => 'Create Itinerary',
            'description' => 'Start building your travel plan',
        ],
        'itinerary_days' => [
            'title' => 'Itinerary Days',
            'description' => 'Your travel plan day by day',
        ],
        'create_breakdown' => [
            'title' => 'Create Breakdown',
            'description' => 'Start building your cost breakdown',
        ],
        'breakdown_overview' => [
            'title' => 'Breakdown Overview',
            'description' => 'Cost breakdown summary and details',
        ],
        'vehicle_types' => [
            'title' => 'Vehicle Types',
            'description' => 'Vehicle pricing and details',
        ],
        'tickets' => [
            'title' => 'Tickets',
            'description' => 'Transportation tickets and pricing',
        ],
        'meals' => [
            'title' => 'Meals',
            'description' => 'Meal types and quantities',
        ],
        'experiences' => [
            'title' => 'Experiences',
            'description' => 'Experience activities and pricing',
        ],
        'accommodations' => [
            'title' => 'Accommodations',
            'description' => 'Hotel accommodations and room pricing',
        ],
        'attractions' => [
            'title' => 'Attractions',
            'description' => 'Tourist attractions and entry fees',
        ],
        'companions' => [
            'title' => 'Companions',
            'description' => 'Tour guides and companion services',
        ],
        'additional_expenses' => [
            'title' => 'Additional Expenses',
            'description' => 'Miscellaneous expenses and costs',
        ],
        'offer_groups' => [
            'title' => 'Offer Groups',
            'description' => 'Manage offer groups and create new ones',
        ],
        'driver_settings' => [
            'title' => 'Driver Settings',
            'description' => 'Configure driver-related costs and accommodations',
        ],
        'companions_settings' => [
            'title' => 'Companions',
            'description' => 'Add and manage travel companions',
        ],
        'offers' => [
            'title' => 'Offers',
            'description' => 'Manage offers for this offer group',
        ],
        'offer_details' => [
            'title' => 'Offer Details',
            'description_create' => 'Create a new offer for this offer group',
            'description_edit' => 'Edit offer settings',
        ],
        'driver_settings_infolist' => [
            'title' => 'Driver Settings',
            'description' => 'Driver cost configuration',
        ],
        'companions_details' => [
            'title' => 'Companions Details',
        ],
        'meal_details' => [
            'title' => 'Meal Details',
            'description' => 'Total: :total CNY - Detailed meal cost breakdown',
        ],
        'ticket_details' => [
            'title' => 'Ticket Details',
            'description' => 'Total: :total CNY - Transportation ticket cost breakdown',
        ],
        'experience_details' => [
            'title' => 'Experience Details',
            'description' => 'Total: :total CNY - Experience cost breakdown',
        ],
        'attraction_details' => [
            'title' => 'Attraction Details',
            'description' => 'Total: :total CNY - Attraction cost breakdown',
        ],
        'expense_details' => [
            'title' => 'Expense Details',
            'description' => 'Total: :total CNY - Additional expense breakdown',
        ],
        'accommodation_details' => [
            'title' => 'Accommodation Details',
            'description' => 'Total: :total CNY - Accommodation cost breakdown',
        ],
        'cost_summary' => [
            'title' => 'Cost Summary',
            'description' => 'Total cost breakdown for this companion',
        ],
    ],
    
    // Actions
    'actions' => [
        'customer_view' => 'Customer View',
        'view' => 'View',
        'delete' => 'Delete',
        'delete_transportation' => 'Delete Transportation',
        'quick_add' => 'Quick add',
        'quick_add_contact' => 'Quick Add Contact',
        'edit_inquiry' => 'Edit Inquiry',
        'edit_quotation' => 'Edit Quotation',
        'view_contact_details' => 'View Contact Details',
        'close' => 'Close',
        'save_changes' => 'Save Changes',
        'create_transportation' => 'Create Transportation',
        'edit_transportation' => 'Edit Transportation',
        'create_itinerary' => 'Create Itinerary',
        'edit_itinerary_days' => 'Edit Itinerary Days',
        'complete' => 'Complete',
        'save' => 'Save',
        'create' => 'Create',
        'create_breakdown' => 'Create Breakdown',
        'complete_itinerary_first' => 'Complete Itinerary First',
        'regenerate' => 'Regenerate',
        'edit' => 'Edit',
        'add_new_offer_group' => 'Add new Offer group',
        'create_breakdown_first' => 'Create Breakdown First',
        'complete_breakdown_to_create_offer' => 'Complete Breakdown to Create Offer',
        'maximum_offer_groups_reached' => 'Maximum Offer Groups Reached',
        'view_report' => 'View Report',
        'details' => 'Details',
        'create_offer' => 'Create Offer',
        'update_offer' => 'Update Offer',
        'yes_delete' => 'Yes, Delete',
    ],
    
    // Tooltips
    'tooltips' => [
        'offers_count' => ':count offer(s) created',
        'no_offers_yet' => 'No offers yet',
        'edit_tooltip' => 'Edit',
        'delete_tooltip' => 'Delete',
        'report_tooltip' => 'Report',
    ],
    
    // Tooltips - Offers
    'tooltips_offers' => [
        'max_offer_groups' => 'Maximum :max offer groups allowed per quotation.',
        'create_breakdown_before_offer' => 'Please create a breakdown before creating an offer group.',
        'complete_breakdown_before_offer' => 'Please complete the breakdown before creating an offer group.',
        'max_offers_per_group' => '⚠️ Maximum :max offers per group reached',
        'complete_breakdown_before_create_offer' => '⚠️ Complete the breakdown first to create offer',
    ],
    
    // Delete Modal
    'modals' => [
        'delete_heading' => 'Delete Quotation :number',
        'delete_description' => 'Are you sure you want to delete quotation itinerary ":number"?',
        'delete_description_only_quotation' => 'Are you sure you want to delete quotation itinerary ":number"?' . "\n\n" . 'Note: This is the only quotation for inquiry #:inquiry_number. You can choose to delete the inquiry as well.',
        'delete_inquiry_checkbox' => 'Also delete the related inquiry (#:inquiry_number) and all its data',
        'delete_inquiry_helper' => 'Warning: This will permanently delete the inquiry, inquiry itinerary, and all related data.',
        'delete_submit' => 'Delete',
    ],
    
    // Infolist Labels
    'infolist' => [
        'active_customer' => 'Active Customer',
        'lead_contact' => 'Lead Contact',
    ],
    
    // Messages
    'messages' => [
        'email_copied' => 'Email copied!',
        'phone_copied' => 'Phone copied!',
        'mobile_copied' => 'Mobile copied!',
        'breakdown_regenerated' => ':reason Breakdown has been regenerated.',
        'quotation_updated' => 'Quotation information has been updated.',
    ],
    
    // Table Headers
    'table_headers' => [
        'type' => 'Type',
        'from' => 'From',
        'to' => 'To',
        'departure' => 'Departure',
        'arrival' => 'Arrival',
        'number' => 'Number',
    ],
    
    // Status Labels
    'status_labels' => [
        'completed' => 'Completed',
        'in_progress' => 'In Progress',
        'locked' => 'Locked',
        'active' => 'Active',
    ],
    
    // Modals - Itinerary
    'modals_itinerary' => [
        'create_transportation_heading' => 'Create Group Transportation',
        'create_transportation_description' => 'Enter entry and exit transportation details',
        'edit_transportation_heading' => 'Edit Group Transportation',
        'edit_transportation_description' => 'Update entry and exit transportation details',
        'delete_transportation_heading' => 'Delete Transportation',
        'delete_transportation_description' => 'Are you sure you want to delete all transportation records? This action cannot be undone.',
        'create_itinerary_heading' => 'Create New Itinerary',
        'create_itinerary_description' => 'Enter transportation details and choose travel mode',
        'complete_itinerary_heading' => 'Complete Itinerary',
        'complete_itinerary_description' => 'Are you sure you want to mark this itinerary as complete?',
    ],
    
    // Modals - Breakdown
    'modals_breakdown' => [
        'create_breakdown_heading' => 'Create New Breakdown',
        'create_breakdown_description' => 'Create a detailed cost breakdown for this quotation itinerary',
        'regenerate_breakdown_heading' => 'Regenerate Breakdown',
        'regenerate_breakdown_description' => 'This will update the breakdown based on the current itinerary. Are you sure?',
        'complete_breakdown_heading' => 'Complete Breakdown',
        'complete_breakdown_description_incomplete' => 'Please complete the itinerary first before marking the breakdown as complete.',
        'complete_breakdown_description' => 'Are you sure you want to mark this breakdown as complete?',
        'delete_breakdown_heading' => 'Delete Breakdown',
        'delete_breakdown_description' => 'Are you sure you want to delete this breakdown? This action cannot be undone.',
    ],
    
    // Tooltips - Breakdown
    'tooltips_breakdown' => [
        'complete_itinerary_before_breakdown' => 'Please complete the itinerary before creating breakdown',
        'complete_itinerary_before_regenerate' => 'Please complete the itinerary before regenerating breakdown',
        'complete_itinerary_before_complete' => 'Please complete the itinerary before marking breakdown as complete',
        'complete_itinerary_before_edit' => 'Please complete the itinerary before editing breakdown',
    ],
    
    // Page Titles
    'page_titles' => [
        'quotation_view' => 'Quotation View - :number',
        'quotation' => 'Quotation :number',
    ],
    
    // Customer View Actions
    'customer_view' => [
        'label' => 'Customer View',
        'view_quotation' => 'View Quotation',
        'refresh' => 'Refresh',
        'print' => 'Print',
        'export_pdf' => 'Export PDF',
        'switch_language' => 'Language',
        'select_language' => 'Select Language',
        'tooltip_print' => 'Print quotation',
        'tooltip_download' => 'Download as PDF',
        'tooltip_switch_language' => 'Change the display language for this customer view only',
    ],
    
    // Customer View Tooltips
    'tooltips_customer_view' => [
        'itinerary_must_be_created' => 'Itinerary must be created first',
        'itinerary_must_be_completed' => 'Itinerary must be completed first',
        'breakdown_must_be_created' => 'Breakdown must be created first',
        'breakdown_must_be_completed' => 'Breakdown must be completed first',
    ],
    
    // Customer View Notifications
    'notifications_customer_view' => [
        'itinerary_not_found_title' => 'Itinerary Not Found',
        'itinerary_not_found_body' => 'The itinerary must be created before viewing the customer quotation.',
        'itinerary_incomplete_title' => 'Itinerary Incomplete',
        'itinerary_incomplete_body' => 'The itinerary must be completed before viewing the customer quotation.',
        'breakdown_not_found_title' => 'Breakdown Not Found',
        'breakdown_not_found_body' => 'The breakdown must be created before viewing the customer quotation.',
        'breakdown_incomplete_title' => 'Breakdown Incomplete',
        'breakdown_incomplete_body' => 'The breakdown must be completed before viewing the customer quotation.',
        'refreshed_title' => 'Refreshed',
        'refreshed_body' => 'Page data has been refreshed successfully.',
        'data_changed_title' => 'Data Changed',
        'data_changed_body' => 'The itinerary or breakdown is no longer complete. Redirecting...',
        'cannot_print_title' => 'Cannot Print',
        'cannot_print_itinerary_missing' => 'The itinerary must be created first.',
        'cannot_print_itinerary_incomplete' => 'The itinerary must be completed before printing.',
        'cannot_print_breakdown_missing' => 'The breakdown must be created first.',
        'cannot_print_breakdown_incomplete' => 'The breakdown must be completed before printing.',
        'cannot_export_title' => 'Cannot Export PDF',
        'cannot_export_itinerary_missing' => 'The itinerary must be created first.',
        'cannot_export_itinerary_incomplete' => 'The itinerary must be completed before exporting.',
        'cannot_export_breakdown_missing' => 'The breakdown must be created first.',
        'cannot_export_breakdown_incomplete' => 'The breakdown must be completed before exporting.',
    ],
    
    // Notifications
    'notifications' => [
        'deleted_title' => 'Deleted successfully',
        'deleted_quotation_only' => 'Quotation has been deleted.',
        'deleted_quotation_and_inquiry' => 'Quotation and inquiry have been deleted.',
        'contact_required_fields' => 'First name and email are required',
        'contact_added_title' => 'Contact added',
        'contact_added_body' => 'The contact has been created and selected.',
        'inquiry_updated_title' => 'Inquiry updated successfully!',
        'quotation_updated_title' => 'Quotation updated successfully!',
        'transportation_created_title' => 'Transportation created successfully!',
        'transportation_updated_title' => 'Transportation updated successfully!',
        'transportation_deleted_title' => 'Transportation deleted successfully!',
        'transportation_deleted_body' => 'All transportation records have been removed.',
        'cannot_complete_title' => 'Cannot Complete Itinerary!',
        'cannot_complete_body' => 'The last day (Day :day) cannot have accommodation because it is the checkout day. Please edit the itinerary and remove the accommodation from the last day.',
        'itinerary_completed_title' => 'Itinerary completed successfully!',
        'itinerary_completed_body' => 'Breakdown has been automatically generated. Redirecting to breakdown form...',
        'incomplete_itinerary_title' => 'Incomplete Itinerary',
        'incomplete_itinerary_body' => 'Please complete the itinerary first before generating breakdown.',
        'breakdown_generated_title' => 'Breakdown Generated',
        'breakdown_generated_body' => 'Cost breakdown has been successfully generated. Redirecting to breakdown form...',
        'breakdown_regenerated_title' => 'Breakdown regenerated successfully!',
        'breakdown_regenerated_body' => 'The breakdown has been updated. Redirecting to breakdown editor...',
        'cannot_regenerate_title' => 'Cannot Regenerate',
        'cannot_regenerate_body' => 'Please complete the itinerary first before regenerating breakdown.',
        'cannot_complete_breakdown_title' => 'Cannot Complete Breakdown',
        'cannot_complete_breakdown_body' => 'Please complete the itinerary first before marking the breakdown as complete.',
        'breakdown_completed_title' => 'Breakdown completed successfully!',
        'breakdown_deleted_title' => 'Breakdown deleted successfully!',
    ],
    
    // Breakdown Placeholders
    'breakdown_placeholders' => [
        'no_rooms' => 'No rooms',
        'no_sub_attractions' => 'No sub-attractions',
    ],
    
    // Offer Group Infolist Placeholders
    'infolist_placeholders' => [
        'base_budget' => 'Base Budget',
        'unknown_companion' => 'Unknown Companion',
        'same_meal_as_passengers' => 'Same meal as passengers',
        'stays_in_same_hotel' => 'Stays in same hotel',
        'lives_in' => 'Lives in: :city',
    ],
    
    // Offer Group Infolist Messages
    'infolist_messages' => [
        'salary_days' => 'Salary (:full full days + :half half days)',
    ],
    
    // Offers Messages
    'offers_messages' => [
        'no_offers_registered' => 'No offers have been registered yet.',
        'no_offers_created' => 'No offers have been created yet.',
        'offer_group_title' => 'Offer Group :number - :status',
        'offer_group_driver_meal' => 'Driver meal included',
        'offer_group_driver_hotel' => 'Driver hotel included',
        'offer_group_companions_count' => ':count companion(s)',
        'offer_group_last_sync' => 'Last sync: :time',
    ],
    
    // Offers Modals
    'modals_offers' => [
        'view_offer_group_heading' => 'Offer Group :number',
        'edit_offer_group_heading' => 'Edit Offer Group',
        'delete_offer_heading' => 'Delete Offer',
        'delete_offer_description' => 'Are you sure you want to delete this offer? This action cannot be undone.',
        'create_offer_heading' => 'Create New Offer',
        'edit_offer_heading' => 'Edit Offer',
        'offer_details_report_heading' => 'Offer Details Report',
    ],
    
    // Offers Notifications
    'notifications_offers' => [
        'max_offer_groups_title' => 'Maximum offer groups reached',
        'max_offer_groups_body' => 'You can create a maximum of :max offer groups per quotation.',
        'offer_group_created_title' => 'Offer Group Created Successfully!',
        'offer_group_created_body' => 'The offer group and companions have been saved.',
        'error_creating_offer_group_title' => 'Error Creating Offer Group',
        'error_creating_offer_group_body' => 'An error occurred while saving the offer group: :error',
        'offer_group_updated_title' => 'Offer Group Updated!',
        'offer_group_updated_body' => 'Offer group settings updated and :count offer(s) recalculated successfully.',
        'update_failed_title' => 'Update Failed',
        'update_failed_body' => 'An error occurred: :error',
        'deleted_title' => 'Deleted!',
        'max_offers_title' => 'Maximum offers reached',
        'max_offers_body' => 'You can create a maximum of :max offers per offer group.',
        'validation_error_title' => 'Validation Error',
        'validation_error_vehicle_required' => 'Vehicle Type is required.',
        'offer_created_title' => 'Offer Created Successfully!',
        'offer_created_body' => 'The offer has been created successfully. All costs including driver, leader, companions, and final prices for all room categories have been calculated.',
        'error_creating_offer_title' => 'Error Creating Offer',
        'error_creating_offer_body' => 'An error occurred while creating the offer: :error',
        'offer_updated_title' => 'Offer Updated!',
        'offer_updated_body' => 'All prices and costs have been recalculated successfully.',
        'offer_deleted_title' => 'Offer Deleted Successfully!',
        'offer_deleted_body' => 'The offer and all related records have been automatically deleted.',
        'error_deleting_offer_title' => 'Error Deleting Offer',
        'error_deleting_offer_body' => 'An error occurred while deleting the offer: :error',
    ],
];

