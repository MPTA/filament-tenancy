<?php

return [
    'resource_name' => '用户',
    'resource_name_plural' => '用户',
    'navigation_label' => '用户',
    'navigation_group' => '用户管理',
    
    'sections' => [
        'essential_information' => [
            'title' => '基本信息',
            'description' => '创建用户帐户所需的信息',
        ],
        'contact_information' => [
            'title' => '联系信息',
            'description' => '此用户的联系方式',
        ],
    ],
    
    'filters' => [
        'email_verified' => [
            'label' => '邮箱验证状态',
            'placeholder' => '所有用户',
            'true_label' => '已验证',
            'false_label' => '未验证',
        ],
    ],
    
    'messages' => [
        'email_copied' => '邮箱已复制！',
        'password_helper' => '留空以保留当前密码',
    ],
    
    'placeholders' => [
        'no_data' => '—',
    ],
    
    'empty_state' => [
        'heading' => '暂无用户',
        'description' => '开始创建新用户。',
    ],
];

