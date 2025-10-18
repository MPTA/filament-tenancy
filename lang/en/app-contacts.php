<?php

return [
    'resource_name' => 'Contact',
    'resource_name_plural' => 'Contacts',
    'navigation_label' => 'Contacts',
    'navigation_group' => 'CRM',
    
    'sections' => [
        'contact_information' => [
            'title' => 'Contact Information',
            'description' => 'Enter the contact details',
        ],
        'additional_information' => [
            'title' => 'Additional Information',
        ],
        'contact_details' => [
            'title' => 'Contact Details',
        ],
        'address' => [
            'title' => 'Address',
        ],
        'system_information' => [
            'title' => 'System Information',
        ],
    ],
    
    'fields' => [
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'email_address' => 'Email Address',
        'country_code' => 'Country Code',
        'mark_as_customer' => 'Mark as Customer',
        'is_customer' => 'Is Customer',
        'postal_address' => 'Postal Address',
    ],
    
    'columns' => [
        'type' => 'Type',
    ],
    
    'placeholders' => [
        'not_provided' => 'Not provided',
        'no_email' => 'No email',
        'no_phone' => 'No phone',
        'no_company' => 'No company',
        'not_specified' => 'Not specified',
        'no_address_provided' => 'No address provided',
        'not_available' => 'Not available',
    ],
    
    'helpers' => [
        'mark_as_customer' => 'Enable to save this contact as a customer (otherwise saved as lead)',
    ],
    
    'messages' => [
        'email_copied' => 'Email copied',
        'phone_copied' => 'Phone copied',
        'mobile_copied' => 'Mobile copied',
    ],
    
    'filters' => [
        'customer_status' => 'Customer Status',
        'all_contacts' => 'All contacts',
        'customers_only' => 'Customers only',
        'leads_only' => 'Leads only',
    ],
    
    'bulk_actions' => [
        'export' => 'Export Selected',
    ],
    
    'notifications' => [
        'export_started_title' => 'Export Started',
        'export_started_body' => 'Selected contacts will be exported.',
    ],
    
    'global_search' => [
        'email' => 'Email',
        'phone' => 'Phone',
        'company' => 'Company',
    ],
    
    'widgets' => [
        'total_contacts' => 'Total Contacts',
    ],
];

