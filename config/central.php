<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Quotation Limits
    |--------------------------------------------------------------------------
    |
    | These limits control how many offer groups and offers can be created
    | for each quotation itinerary.
    |
    */
    'quotation' => [
        'max_offer_groups' => env('MAX_OFFER_GROUPS', 4),
        'max_offers_per_group' => env('MAX_OFFERS_PER_GROUP', 6),
        'max_companions_per_group' => env('MAX_COMPANIONS_PER_GROUP', 2),
    ],
];

