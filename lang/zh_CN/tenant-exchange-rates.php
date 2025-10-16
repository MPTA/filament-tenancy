<?php

return [
    'resource_name' => '汇率',
    'resource_name_plural' => '汇率',
    'navigation_label' => '汇率',
    'navigation_group' => '财务管理',
    
    'sections' => [
        'exchange_rate_information' => [
            'title' => '汇率信息',
            'description' => '输入汇率详情',
        ],
    ],
    
    'fields' => [
        'to_currency_tenant_default' => '目标货币（租户默认）',
    ],
    
    'placeholders' => [
        'from_currency' => '选择源货币',
        'rate' => '例如：1.25',
    ],
    
    'helpers' => [
        'to_currency_auto' => '自动设置为您租户的默认货币',
        'rate' => '输入汇率（1 源货币 = X 目标货币）',
    ],
    
    'validations' => [
        'same_currency' => '源货币和目标货币不能相同。',
        'duplicate_combination' => '此汇率组合已存在。',
    ],
    
    'bulk_actions' => [
        'export' => '导出所选',
    ],
    
    'notifications' => [
        'export_started_title' => '导出已开始',
        'export_started_body' => '所选汇率将被导出。',
    ],
    
    'global_search' => [
        'to' => '至',
        'from' => '源',
        'to_label' => '至',
        'rate_label' => '汇率',
        'unknown' => '未知',
    ],
    
    'empty_state' => [
        'heading' => '暂无汇率',
        'description' => '创建您的第一个汇率以开始使用。',
    ],
];

