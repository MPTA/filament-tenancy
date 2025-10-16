<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Enum Translations
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for translating enum labels and descriptions.
    |
    */

    'activity_category_type' => [
        'meal' => 'Meal',
        'meal_description' => 'Food and dining related activities',
        'attraction' => 'Attraction',
        'attraction_description' => 'Tourist attractions and landmarks',
        'ticket' => 'Ticket',
        'ticket_description' => 'Ticketed events and shows',
        'experience' => 'Experience',
        'experience_description' => 'Unique experiences and activities',
        'custom' => 'Custom',
        'custom_description' => 'Custom user-defined activities',
    ],

    'attraction_type' => [
        'natural' => 'Natural',
        'natural_description' => 'Beaches, Mountains, National Parks, Forests, Lakes, Waterfalls, Islands, Wildlife Reserves, Canyons, Deserts, Rivers, Volcanoes, Caves',
        'man_made' => 'Man-made',
        'man_made_description' => 'Historical Sites, Monuments, Museums, Art Galleries, Castles/Forts, Ancient Temples, Skyscrapers, Bridges, Zoos/Aquaria, Botanical Gardens, Theme Parks, Amusement Parks, Factories',
        'cultural' => 'Cultural',
        'cultural_description' => 'Heritage Sites, Ethnic Enclaves, Living History Museums, Religious Temples, Festivals, Public Art, Libraries, Theaters, Ethnic Communities, Industrial Heritage',
        'sport' => 'Sport',
        'sport_description' => 'Stadiums, Ski Resorts, Golf Courses, Adventure Parks, Extreme Sports Sites, Sailing Regattas, Formula 1 Tracks, Climbing Walls, Surf Spots',
        'events' => 'Events',
        'events_description' => 'Carnivals, Concerts, Exhibitions, Cultural Festivals, Religious Events, Sports Events, Industrial Tours, Trade Shows',
        'leisure' => 'Leisure',
        'leisure_description' => 'Resorts, Shopping Malls, Spas, Nightlife Venues, Honeymoon Spots, Wellness Centers, Cruise Ports, Gambling Casinos',
    ],

    'charge_mode' => [
        'per_person' => 'Per Person',
        'per_person_description' => 'Charged per individual person',
        'per_group' => 'Per Group',
        'per_group_description' => 'Charged per group regardless of size',
    ],

    'companion_category' => [
        'tour_guide' => 'Tour Guide',
        'tour_guide_description' => 'Professional tour guide for sightseeing and cultural experiences',
        'staff' => 'Staff',
        'staff_description' => 'General staff member for support and assistance',
        'translator' => 'Translator',
        'translator_description' => 'Language translator for communication support',
        'driver' => 'Driver',
        'driver_description' => 'Professional driver for transportation services',
    ],

    'contact_type' => [
        'user' => 'User',
        'lead' => 'Lead',
        'customer' => 'Customer',
    ],

    'gender' => [
        'male' => 'Male',
        'female' => 'Female',
    ],

    'hire_mode' => [
        'daily' => 'Daily',
        'daily_description' => 'Full day hire (24 hours)',
        'half_day' => 'Half Day',
        'half_day_description' => 'Half day hire (up to 8 hours)',
        'hourly' => 'Hourly',
        'hourly_description' => 'Hourly hire',
    ],

    'inquiry_date_type' => [
        'fixed_date' => 'Fixed Date',
        'fixed_date_description' => 'Specific fixed dates for travel',
        'flexible_date' => 'Flexible Date',
        'flexible_date_description' => 'Flexible date range for travel',
        'series' => 'Series',
        'series_description' => 'Recurring travel series',
    ],

    'inquiry_type' => [
        'itinerary' => 'Itinerary',
        'itinerary_description' => 'Travel itinerary planning inquiry',
    ],

    'meal_part' => [
        'breakfast' => 'Breakfast',
        'breakfast_description' => 'Morning meal',
        'lunch' => 'Lunch',
        'lunch_description' => 'Midday meal',
        'dinner' => 'Dinner',
        'dinner_description' => 'Evening meal',
    ],

    'offer_group_link_status' => [
        'linked' => 'Linked',
        'linked_description' => 'Automatically syncs with breakdown changes',
        'decoupled' => 'Decoupled',
        'decoupled_description' => 'Independent snapshot - not affected by breakdown changes',
        'outdated' => 'Outdated',
        'outdated_description' => 'Breakdown changed - needs review',
    ],

    'quotation_type' => [
        'general' => 'General',
        'itinerary' => 'Itinerary',
    ],

    'room_category' => [
        'twin' => 'Twin',
        'single' => 'Single',
        'double_for_two' => 'Double For Two',
        'double_for_one' => 'Double For One',
        'triple' => 'Triple',
        'suite_for_one' => 'Suite For One',
        'suite_for_two' => 'Suite For Two',
    ],

    'star_rating' => [
        '1' => '1 Star',
        '1_description' => 'Basic accommodation with minimal amenities',
        '2' => '2 Stars',
        '2_description' => 'Budget-friendly accommodation with basic facilities',
        '3' => '3 Stars',
        '3_description' => 'Mid-range accommodation with good facilities and services',
        '4' => '4 Stars',
        '4_description' => 'High-quality accommodation with excellent facilities and services',
        '5' => '5 Stars',
        '5_description' => 'Luxury accommodation with exceptional facilities and services',
    ],

    'ticket_class' => [
        'economy' => 'Economy',
        'economy_description' => 'Standard economy class ticket',
        'business' => 'Business',
        'business_description' => 'Business class ticket with enhanced services',
        'first' => 'First Class',
        'first_description' => 'First class ticket with premium services',
    ],

    'transport_mode' => [
        'air' => 'Air',
        'air_description' => 'Air transportation (flights)',
        'train' => 'Train',
        'train_description' => 'Train transportation',
        'land' => 'Land',
        'land_description' => 'Land transportation (bus, car, etc.)',
    ],

    'travel_mode' => [
        'air' => 'Air Travel',
        'air_description' => 'Travel by airplane only',
        'self_driving' => 'Self Driving',
        'self_driving_description' => 'Travel by car/vehicle only',
        'air_self_driving' => 'Air + Self Driving',
        'air_self_driving_description' => 'Air travel followed by self driving',
        'self_driving_air' => 'Self Driving + Air',
        'self_driving_air_description' => 'Self driving followed by air travel',
    ],

    'vehicle_usage_mode' => [
        'hour' => 'Hour',
        'half_day' => 'Half Day',
        'full_day' => 'Full Day',
    ],
];

