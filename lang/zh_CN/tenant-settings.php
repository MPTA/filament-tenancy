<?php

return [
    'title' => '租户设置',
    'navigation_label' => '设置',
    
    'sections' => [
        'company_information' => [
            'title' => '公司信息',
            'description' => '基本公司详情和信息',
        ],
        'contact_information' => [
            'title' => '联系信息',
            'description' => '联系方式和沟通信息',
        ],
        'location_preferences' => [
            'title' => '位置与偏好',
            'description' => '地理位置和系统偏好设置',
        ],
        'base_budget_configuration' => [
            'title' => '基础预算配置',
            'description' => '设置司机和陪同的基础预算',
        ],
    ],
    
    'actions' => [
        'save' => '保存',
    ],
    
    'helpers' => [
        'logo_upload' => '上传公司标志。最大大小：2MB。',
        'signature_upload' => '上传公司签名。最大大小：2MB。',
        'currency_auto' => '货币由所选国家自动确定',
        'driver_meal_per_day' => '司机每日餐费基础预算',
        'driver_accommodation_per_night' => '司机每晚住宿基础预算',
        'companion_meal_per_day' => '陪同每日餐费基础预算',
        'companion_accommodation_per_night' => '陪同每晚住宿基础预算',
    ],
    
    'placeholders' => [
        'driver_meal_budget' => '50.00',
        'driver_accommodation_budget' => '100.00',
        'companion_meal_budget' => '50.00',
        'companion_accommodation_budget' => '100.00',
        'no_currency' => '—',
    ],
    
    'notifications' => [
        'updated_success' => '设置更新成功！',
        'created_success' => '设置创建成功！',
        'error_title' => '保存设置时出错！',
    ],
];

