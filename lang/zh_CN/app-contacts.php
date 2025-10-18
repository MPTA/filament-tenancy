<?php

return [
    'resource_name' => '联系人',
    'resource_name_plural' => '联系人',
    'navigation_label' => '联系人',
    'navigation_group' => 'CRM',
    
    'sections' => [
        'contact_information' => [
            'title' => '联系人信息',
            'description' => '输入联系人详情',
        ],
        'additional_information' => [
            'title' => '附加信息',
        ],
        'contact_details' => [
            'title' => '联系人详情',
        ],
        'address' => [
            'title' => '地址',
        ],
        'system_information' => [
            'title' => '系统信息',
        ],
    ],
    
    'fields' => [
        'first_name' => '名字',
        'last_name' => '姓氏',
        'email_address' => '电子邮箱',
        'country_code' => '国家代码',
        'mark_as_customer' => '标记为客户',
        'is_customer' => '是客户',
        'postal_address' => '邮政地址',
    ],
    
    'columns' => [
        'type' => '类型',
    ],
    
    'placeholders' => [
        'not_provided' => '未提供',
        'no_email' => '无邮箱',
        'no_phone' => '无电话',
        'no_company' => '无公司',
        'not_specified' => '未指定',
        'no_address_provided' => '未提供地址',
        'not_available' => '不可用',
    ],
    
    'helpers' => [
        'mark_as_customer' => '启用以将此联系人保存为客户（否则保存为潜在客户）',
    ],
    
    'messages' => [
        'email_copied' => '邮箱已复制',
        'phone_copied' => '电话已复制',
        'mobile_copied' => '手机已复制',
    ],
    
    'filters' => [
        'customer_status' => '客户状态',
        'all_contacts' => '所有联系人',
        'customers_only' => '仅客户',
        'leads_only' => '仅潜在客户',
    ],
    
    'bulk_actions' => [
        'export' => '导出所选',
    ],
    
    'notifications' => [
        'export_started_title' => '导出已开始',
        'export_started_body' => '所选联系人将被导出。',
    ],
    
    'global_search' => [
        'email' => '邮箱',
        'phone' => '电话',
        'company' => '公司',
    ],
    
    'widgets' => [
        'total_contacts' => '联系人总数',
    ],
];

