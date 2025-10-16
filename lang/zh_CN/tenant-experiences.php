<?php

return [
    'resource_name' => '体验',
    'resource_name_plural' => '体验',
    'navigation_label' => '体验',
    'navigation_group' => '内容管理',
    
    'sections' => [
        'basic_information' => [
            'title' => '基本信息',
            'description' => '输入体验的基本详情',
        ],
        'pricing_location' => [
            'title' => '价格与位置',
            'description' => '设置价格和位置信息',
        ],
        'location_details' => [
            'title' => '位置详情',
            'description' => '指定体验的位置',
        ],
        'experience_details' => [
            'title' => '体验详情',
        ],
        'description_content' => [
            'title' => '描述与内容',
        ],
        'pricing_information' => [
            'title' => '价格信息',
        ],
        'location_information' => [
            'title' => '位置信息',
        ],
        'system_information' => [
            'title' => '系统信息',
        ],
    ],
    
    'fields' => [
        'experience_name' => '体验名称',
        'content' => '内容',
        'price_default_currency' => '价格（默认货币）',
        'free_for_companions' => '陪同免费',
    ],
    
    'columns' => [
        'experience_name' => '体验名称',
    ],
    
    'placeholders' => [
        'name' => '例如：城市徒步游、文化体验',
        'slug' => '例如：city-walking-tour、cultural-experience',
        'description' => '体验的简要描述',
        'content' => '关于体验的详细内容',
        'price' => '0.00',
        'charge_mode' => '选择收费方式',
        'city' => '选择城市',
        'district' => '选择地区',
        'address' => '体验地点的完整地址',
        'no_slug' => '无别名',
        'not_specified' => '未指定',
        'free' => '免费',
        'unknown' => '未知',
        'no_description' => '未提供描述',
        'no_content' => '未提供内容',
        'no_address' => '未提供地址',
        'not_available' => '不可用',
    ],
    
    'helpers' => [
        'name' => '体验的完整名称',
        'slug' => 'URL友好标识符（从名称自动生成）。必须用英文输入名称以自动生成slug',
        'description' => '用于列表和预览的简短描述',
        'content' => '包含所有详情的完整内容',
        'price' => '体验价格（默认货币）',
        'charge_mode' => '体验的收费方式',
        'is_free_for_guide' => '此体验是否对导游免费',
        'is_free_for_other_companions' => '此体验是否对其他陪同免费（自动启用导游免费）',
        'address' => '包括街道、建筑等的完整地址',
        'city' => '体验发生的城市',
        'district' => '城市内的地区',
    ],
    
    'filters' => [
        'charge_mode' => [
            'per_person' => '按人收费',
            'per_group' => '按团收费',
            'per_hour' => '按小时收费',
            'fixed' => '固定价格',
        ],
        'status' => [
            'placeholder' => '所有体验',
            'true_label' => '仅启用',
            'false_label' => '仅禁用',
        ],
    ],
    
    'messages' => [
        'slug_copied' => '别名已复制',
    ],
    
    'bulk_actions' => [
        'activate' => '启用所选',
        'deactivate' => '禁用所选',
        'export' => '导出所选',
    ],
    
    'notifications' => [
        'activated_title' => '体验已启用',
        'activated_body' => '所选体验已启用。',
        'deactivated_title' => '体验已禁用',
        'deactivated_body' => '所选体验已禁用。',
        'export_started_title' => '导出已开始',
        'export_started_body' => '所选体验将被导出。',
    ],
    
    'global_search' => [
        'no_location' => '无位置',
        'free' => '免费',
        'active' => '启用',
        'inactive' => '禁用',
        'price_label' => '价格',
        'city_label' => '城市',
        'status_label' => '状态',
        'charge_mode_label' => '收费方式',
        'not_specified' => '未指定',
    ],
    
    'empty_state' => [
        'heading' => '暂无体验',
        'description' => '创建您的第一个体验以开始使用。',
    ],
];

