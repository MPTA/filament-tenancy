<?php

return [
    'resource_name' => '询价',
    'resource_name_plural' => '询价',
    'navigation_label' => '询价',
    'navigation_group' => '报价管理',
    
    'columns' => [
        'number' => '编号',
        'title' => '标题',
        'reference' => '参考',
        'contact' => '联系人',
        'quotations' => '报价单',
        'quotation_numbers' => '报价单编号',
        'created' => '创建',
        'updated' => '更新',
    ],
    
    'actions' => [
        'delete' => '删除',
    ],
    
    'modals' => [
        'delete_heading' => '删除询价 #:number',
        'delete_description_with_quotations' => '无法删除此询价，因为它有 :count 个报价单。请先删除所有报价单。',
        'delete_description' => '确定要删除此询价行程吗？此操作无法撤消。',
        'delete_submit' => '删除',
    ],
    
    'tooltips' => [
        'quotations_count' => ':count 个报价单',
        'no_quotations' => '无报价单',
        'cannot_delete_has_quotations' => '此询价有 :count 个报价单。请先删除所有报价单。',
    ],
    
    'notifications' => [
        'cannot_delete_title' => '无法删除询价',
        'cannot_delete_body' => '此询价有 :count 个报价单。请先删除所有报价单。',
    ],
    
    'widgets' => [
        'total_inquiries' => '询价总数',
    ],
];

