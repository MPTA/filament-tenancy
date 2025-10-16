<?php

return [
    'resource_name' => '住宿',
    'resource_name_plural' => '住宿',
    'navigation_label' => '住宿',
    
    'sections' => [
        'basic_information' => [
            'title' => '基本信息',
            'description' => '住宿基本详情',
        ],
        'location_information' => [
            'title' => '位置信息',
            'description' => '住宿位置详情',
        ],
    ],
    
    'columns' => [
        'tenant_prices' => '租户价格',
    ],
    
    'filters' => [
        'star_rating' => [
            '1' => '1星',
            '2' => '2星',
            '3' => '3星',
            '4' => '4星',
            '5' => '5星',
        ],
    ],
    
    'messages' => [
        'no_rating' => '无评分',
        'prices_count' => ':count 个价格',
    ],
];

