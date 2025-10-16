<?php

return [
    'resource_name' => '陪同类型',
    'resource_name_plural' => '陪同类型',
    'navigation_label' => '陪同类型',
    'navigation_group' => '数据类型',
    
    'sections' => [
        'basic_information' => [
            'title' => '基本信息',
            'description' => '输入陪同类型的基本详情',
        ],
        'language_requirements' => [
            'title' => '语言要求',
            'description' => '指定此陪同类型的语言要求',
        ],
        'pricing_configuration' => [
            'title' => '价格配置',
            'description' => '设置不同服务时长的价格',
        ],
        'pricing_information' => [
            'title' => '价格信息',
        ],
        'service_limits' => [
            'title' => '服务限制',
            'description' => '定义最大服务时间和时限',
        ],
        'system_information' => [
            'title' => '系统信息',
        ],
    ],
    
    'fields' => [
        'companion_type_name' => '陪同类型名称',
    ],
    
    'columns' => [
        'companion_type' => '陪同类型',
    ],
    
    'placeholders' => [
        'name' => '例如：专业导游、文化陪同',
        'slug' => '例如：professional-guide、cultural-companion',
        'companion_category' => '选择陪同类别',
        'native_language' => '选择母语',
        'speaking_language' => '选择会说语言',
        'no_category' => '无类别',
        'not_specified' => '未指定',
        'not_set' => '未设置',
        'no_slug' => '无别名',
        'price_placeholder' => '0.00',
        'max_hours_day' => '8',
        'max_hours_half' => '4',
    ],
    
    'helpers' => [
        'name' => '陪同类型的完整名称',
        'slug' => 'URL友好标识符（从名称自动生成）。必须用英文输入名称以自动生成slug',
        'category' => '此陪同类型所属的类别',
        'native_language' => '陪同的主要语言',
        'speaking_language' => '陪同会说的语言',
        'per_day_price' => '全天服务价格',
        'half_day_price' => '半天服务价格',
        'per_hour_price' => '每小时服务价格',
        'extra_hour_price' => '超时额外小时价格',
        'max_hours_per_day' => '每日允许的最大小时数',
        'max_hours_half_day' => '半天服务允许的最大小时数',
    ],
    
    'suffixes' => [
        'hours' => '小时',
    ],
    
    'messages' => [
        'slug_copied' => '别名已复制',
    ],
    
    'validations' => [
        'duplicate_combination' => '此租户已存在具有相同类别、母语和会说语言组合的陪同类型。请选择不同的值。',
    ],
    
    'bulk_actions' => [
        'export' => '导出所选',
        'duplicate' => '复制所选',
    ],
    
    'notifications' => [
        'export_started_title' => '导出已开始',
        'export_started_body' => '所选陪同类型将被导出。',
        'duplication_complete_title' => '复制完成',
        'duplication_complete_body' => '所选陪同类型已复制。',
    ],
    
    'global_search' => [
        'no_category' => '无类别',
        'not_specified' => '未指定',
        'not_set' => '未设置',
        'category_label' => '类别',
        'native_language_label' => '母语',
        'speaking_language_label' => '会说语言',
        'per_day_price_label' => '全天价格',
    ],
    
    'copy_suffix' => '（副本）',
    'copy_slug_suffix' => '-copy',
    
    'empty_state' => [
        'heading' => '暂无陪同类型',
        'description' => '创建您的第一个陪同类型以开始使用。',
    ],
];

