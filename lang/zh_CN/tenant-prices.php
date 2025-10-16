<?php

return [
    'title' => '租户价格',
    
    'sections' => [
        'meal_inclusion' => [
            'title' => '餐食包含',
            'description' => '选择此价格包含的餐食',
        ],
    ],
    
    'columns' => [
        'meals_included' => '包含餐食',
    ],
    
    'filters' => [
        'active_prices' => '有效价格',
        'expired_prices' => '过期价格',
        'with_breakfast' => '含早餐',
        'with_lunch' => '含午餐',
        'with_dinner' => '含晚餐',
    ],
    
    'actions' => [
        'add_price' => '添加价格',
    ],
    
    'messages' => [
        'included' => '包含',
        'not_included' => '不包含',
        'active' => '有效',
        'expired' => '已过期',
        'no_meals' => '不含餐',
        'indefinite' => '无限期',
        'indefinite_validity' => '无限期有效',
    ],
    
    'placeholders' => [
        'valid_to' => '留空表示无限期有效',
    ],
    
    'validations' => [
        'room_category_required' => '房间类别为必填项。',
        'price_required' => '价格为必填项',
        'price_numeric' => '价格必须为数字',
        'price_min' => '价格不能为负数',
        'valid_from_required' => '生效日期为必填项。',
        'valid_from_date' => '请输入有效的日期。',
    ],
    
    'notifications' => [
        'duplicate_price_title' => '重复价格',
        'duplicate_price_body' => '已存在具有相同房间类别、日期和餐食包含的价格。',
    ],
    
    'empty_state' => [
        'heading' => '暂无价格',
        'description' => '为此住宿添加价格以开始使用。',
    ],
];

