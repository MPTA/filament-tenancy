<?php

return [
    'resource_name' => '车辆类型',
    'resource_name_plural' => '车辆类型',
    'navigation_label' => '车辆类型',
    'navigation_group' => '数据类型',
    
    'sections' => [
        'basic_information' => [
            'title' => '基本信息',
            'description' => '输入车辆类型详情',
        ],
        'capacity_specifications' => [
            'title' => '容量与规格',
            'description' => '设置乘客容量和规格',
        ],
        'pricing_configuration' => [
            'title' => '价格配置',
            'description' => '设置不同服务类型的价格',
        ],
        'service_options' => [
            'title' => '服务选项',
            'description' => '配置服务选项和功能',
        ],
        'vehicle_type_details' => [
            'title' => '车辆类型详情',
        ],
        'pricing_information' => [
            'title' => '价格信息',
        ],
        'media_description' => [
            'title' => '媒体与描述',
        ],
        'system_information' => [
            'title' => '系统信息',
        ],
    ],
    
    'fields' => [
        'vehicle_type_name' => '车辆类型名称',
        'vehicle_category' => '车辆类别',
        'cover_image_url' => '封面图片URL',
        'minimum_capacity' => '最小容量',
        'maximum_capacity' => '最大容量',
        'min_capacity' => '最小容量',
        'max_capacity' => '最大容量',
        'max_hours_per_day' => '每日最大小时数',
        'max_hours_half_day' => '半日最大小时数',
        'max_hours_day' => '最大小时数/日',
        'max_hours_half_day_short' => '最大小时数/半日',
        'per_day_price' => '全日价格',
        'half_day_price' => '半日价格',
        'extra_hour_price' => '额外小时价格',
        'airport_transfer_price' => '机场接送价格',
        'vip_service' => 'VIP服务',
        'cover_image' => '封面图片',
    ],
    
    'columns' => [
        'vehicle_type' => '车辆类型',
        'airport_transfer' => '机场接送',
    ],
    
    'placeholders' => [
        'name' => '例如：轿车、SUV、面包车、巴士',
        'slug' => '例如：sedan、suv、minivan、bus',
        'vehicle_category' => '选择车辆类别',
        'cover_url' => 'https://example.com/image.jpg',
        'description' => '车辆类型的简要描述',
        'capacity_min' => '1',
        'capacity_max' => '4',
        'max_hours_day' => '8',
        'max_hours_half' => '4',
        'price' => '0.00',
        'no_slug' => '无别名',
        'no_category' => '无类别',
        'not_set' => '未设置',
        'not_specified' => '未指定',
        'not_available' => '不可用',
        'no_cover_image' => '无封面图片',
        'no_description_provided' => '未提供描述',
    ],
    
    'helpers' => [
        'name' => '车辆类型的完整名称',
        'slug' => 'URL友好标识符（从名称自动生成）',
        'vehicle_category' => '此车辆类型所属的类别',
        'cover_url' => '车辆封面图片的URL',
        'description' => '用于列表和预览的简短描述',
        'capacity_min' => '最小乘客数量',
        'capacity_max' => '最大乘客数量',
        'max_hours_day' => '每日允许的最大小时数',
        'max_hours_half' => '半日服务的最大小时数',
        'per_day_price' => '全日服务的价格',
        'half_day_price' => '半日服务的价格',
        'extra_hour_price' => '每额外小时的价格',
        'airport_transfer_price' => '机场接送服务的价格',
        'vip_service' => '启用VIP服务功能',
    ],
    
    'suffixes' => [
        'passengers' => '位乘客',
        'pax' => '人',
        'hours' => '小时',
    ],
    
    'filters' => [
        'vehicle_category' => '车辆类别',
        'vip_service' => 'VIP服务',
        'all_vehicles' => '所有车辆',
        'vip_only' => '仅VIP',
        'non_vip_only' => '仅非VIP',
        'capacity_range' => '容量范围',
        'capacity_1_4' => '1-4位乘客',
        'capacity_5_8' => '5-8位乘客',
        'capacity_9_16' => '9-16位乘客',
        'capacity_17_30' => '17-30位乘客',
        'capacity_30_plus' => '30+位乘客',
        'has_pricing' => '有价格',
        'has_pricing_yes' => '有价格',
        'has_pricing_no' => '无价格',
    ],
    
    'bulk_actions' => [
        'export' => '导出所选',
        'duplicate' => '复制所选',
        'toggle_vip' => '切换VIP状态',
    ],
    
    'notifications' => [
        'export_started_title' => '导出已开始',
        'export_started_body' => '所选车辆类型将被导出。',
        'duplication_complete_title' => '复制完成',
        'duplication_complete_body' => '所选车辆类型已复制。',
        'vip_status_updated_title' => 'VIP状态已更新',
        'vip_status_updated_body' => '所选车辆类型的VIP状态已切换。',
    ],
    
    'messages' => [
        'slug_copied' => '别名已复制',
        'name_copy_suffix' => '（副本）',
        'slug_copy_suffix' => '-copy',
    ],
    
    'global_search' => [
        'category' => '类别',
        'capacity' => '容量',
        'vip' => 'VIP',
        'per_day_price' => '全日价格',
        'vip_service' => 'VIP服务',
        'standard_service' => '标准服务',
    ],
];

