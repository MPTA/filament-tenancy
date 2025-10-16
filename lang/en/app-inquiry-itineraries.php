<?php

return [
    'resource_name' => 'Inquiry',
    'resource_name_plural' => 'Inquiries',
    'navigation_label' => 'Inquiries',
    'navigation_group' => 'Quotations',
    
    'columns' => [
        'number' => 'Number',
        'title' => 'Title',
        'reference' => 'Reference',
        'contact' => 'Contact',
        'quotations' => 'Quotations',
        'quotation_numbers' => 'Quotation Numbers',
        'created' => 'Created',
        'updated' => 'Updated',
    ],
    
    'actions' => [
        'delete' => 'Delete',
    ],
    
    'modals' => [
        'delete_heading' => 'Delete Inquiry #:number',
        'delete_description_with_quotations' => 'Cannot delete this inquiry because it has :count quotation(s). Please delete all quotations first.',
        'delete_description' => 'Are you sure you want to delete this inquiry itinerary? This action cannot be undone.',
        'delete_submit' => 'Delete',
    ],
    
    'tooltips' => [
        'quotations_count' => ':count quotation(s)',
        'no_quotations' => 'No quotations',
        'cannot_delete_has_quotations' => 'This inquiry has :count quotation(s). Delete all quotations first.',
    ],
    
    'notifications' => [
        'cannot_delete_title' => 'Cannot delete inquiry',
        'cannot_delete_body' => 'This inquiry has :count quotation(s). Please delete all quotations first.',
    ],
];

