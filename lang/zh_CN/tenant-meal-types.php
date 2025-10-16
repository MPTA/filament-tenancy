<?php

return [
    'resource_name' => '餐食类型',
    'resource_name_plural' => '餐食类型',
    'navigation_label' => '餐食类型',
    'navigation_group' => '数据类型',
    
    'sections' => [
        'meal_type_information' => [
            'title' => '餐食类型信息',
            'description' => '输入餐食类型详情',
        ],
        'pricing_information' => [
            'title' => '价格信息',
            'description' => '设置此餐食类型的价格',
        ],
        'meal_type_details' => [
            'title' => '餐食类型详情',
        ],
        'description_pricing' => [
            'title' => '描述与价格',
        ],
        'system_information' => [
            'title' => '系统信息',
        ],
    ],
    
    'fields' => [
        'meal_type_name' => '餐食类型名称',
        'meal_category' => '餐食类别',
    ],
    
    'columns' => [
        'meal_type' => '餐食类型',
    ],
    
    'placeholders' => [
        'name' => '例如：早餐、午餐、晚餐、小吃',
        'slug' => '例如：breakfast、lunch、dinner、snack',
        'description' => '餐食类型的简要描述',
        'meal_category' => '选择餐食类别',
        'price' => '0.00',
        'no_slug' => '无别名',
        'no_category' => '无类别',
        'not_set' => '未设置',
        'no_description' => '无描述',
        'no_description_provided' => '未提供描述',
        'not_specified' => '未指定',
        'not_available' => '不可用',
    ],
    
    'helpers' => [
        'name' => '餐食类型的完整名称',
        'slug' => 'URL友好标识符（从名称自动生成）。必须用英文输入名称以自动生成slug',
        'meal_category' => '此餐食类型所属的类别',
        'description' => '用于列表和预览的简短描述',
        'price' => '此餐食类型的价格',
    ],
    
    'filters' => [
        'meal_category' => '餐食类别',
        'has_price' => '有价格',
        'has_price_yes' => '有价格',
        'has_price_no' => '无价格',
    ],
    
    'bulk_actions' => [
        'export' => '导出所选',
        'duplicate' => '复制所选',
    ],
    
    'notifications' => [
        'export_started_title' => '导出已开始',
        'export_started_body' => '所选餐食类型将被导出。',
        'duplication_complete_title' => '复制完成',
        'duplication_complete_body' => '所选餐食类型已复制。',
    ],
    
    'messages' => [
        'slug_copied' => '别名已复制',
        'name_copy_suffix' => '（副本）',
        'slug_copy_suffix' => '-copy',
    ],
    
    'global_search' => [
        'category' => '类别',
        'price' => '价格',
        'description' => '描述',
    ],
];

