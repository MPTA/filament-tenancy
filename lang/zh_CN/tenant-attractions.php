<?php

return [
    'resource_name' => '景点',
    'resource_name_plural' => '景点',
    'navigation_label' => '景点',
    
    'sections' => [
        'basic_information' => [
            'title' => '基本信息',
            'description' => '景点基本详情和信息',
        ],
        'tenant_pricing' => [
            'title' => '租户定价',
            'description' => '管理此景点的自定义价格',
        ],
        'sub_attractions_pricing' => [
            'title' => '子景点定价',
            'description' => '管理子景点的价格',
        ],
    ],
    
    'columns' => [
        'tenant_prices' => '租户价格',
    ],
    
    'filters' => [
        'type' => [
            'historical' => '历史',
            'natural' => '自然',
            'cultural' => '文化',
            'religious' => '宗教',
            'entertainment' => '娱乐',
        ],
    ],
    
    'actions' => [
        'edit_pricing' => '编辑价格',
    ],
    
    'modals' => [
        'edit_pricing_heading' => '编辑景点价格（租户默认货币）',
    ],
    
    'repeater' => [
        'sub_attractions' => '子景点',
    ],
    
    'messages' => [
        'not_set' => '未设置',
        'no_additional_content' => '无附加内容',
        'prices_count' => ':count 个价格',
    ],
    
    'notifications' => [
        'pricing_updated' => '价格更新成功！',
    ],
    
    'empty_state' => [
        'heading' => '暂无景点',
        'description' => '请联系管理员为您的租户添加景点。',
    ],
];

