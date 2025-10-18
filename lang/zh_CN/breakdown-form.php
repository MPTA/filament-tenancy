<?php

return [
    // Wizard Steps
    'steps' => [
        'vehicle_types' => '车辆类型',
        'tickets' => '门票',
        'meals' => '餐饮',
        'hotels' => '酒店',
        'attractions' => '景点',
        'companions' => '陪同人员',
        'expenses' => '费用',
    ],

    // Vehicle Types Section
    'vehicle_types' => [
        'section_title' => '车辆类型',
        'quantities_section' => '车辆数量',
        'vehicle_days' => '车辆天数',
        'vehicle_half_days' => '车辆半天数',
        'vehicle_hours' => '车辆小时数',
        'airport_transfers' => '机场接送',
        'driver_meal_budget' => '司机餐饮预算',
        'driver_accommodation_budget' => '司机住宿预算',
        'vehicle_type' => '车辆类型',
        'per_day_price' => '每日价格',
        'half_day_price' => '半天价格',
        'per_hour_price' => '每小时价格',
        'airport_transfer_price' => '机场接送价格',
        'cannot_delete_in_use' => '无法删除：此车辆类型正在 :count 个报价组中使用',
    ],

    // Tickets Section
    'tickets' => [
        'transport_mode' => '交通方式',
        'class' => '等级',
        'from_city' => '出发城市',
        'to_city' => '目的地城市',
        'price' => '价格',
    ],

    // Meals Section
    'meals' => [
        'meal_type' => '餐饮类型',
        'price' => '价格',
    ],

    // Hotels Section
    'hotels' => [
        'accommodation' => '住宿',
        'city' => '城市',
        'nights' => '晚数',
        'has_breakfast' => '含早餐',
        'room_categories' => '房间类别',
        'room_category' => '房间类别',
        'price' => '价格',
    ],

    // Attractions Section
    'attractions' => [
        'section_title' => '景点',
        'experiences_section' => '体验项目',
        'experience' => '体验项目',
        'charge_mode' => '收费模式',
        'free_for_guide' => '导游免费',
        'free_for_companions' => '陪同人员免费',
        'price' => '价格',
        'attraction' => '景点',
        'city' => '城市',
        'entry_price' => '门票价格',
        'outview' => '外观',
        'sub_attractions' => '子景点',
        'sub_attraction' => '子景点',
    ],

    // Companions Section
    'companions' => [
        'section_title' => '陪同人员',
        'budgets_section' => '陪同人员预算',
        'companion_meal_budget' => '陪同人员餐饮预算',
        'companion_accommodation_budget' => '陪同人员住宿预算',
        'companion_type' => '陪同人员类型',
        'companion_category' => '陪同人员类别',
        'per_day_price' => '每日价格',
        'half_day_price' => '半天价格',
        'per_hour_price' => '每小时价格',
        'cannot_delete_in_use' => '无法删除：此陪同人员类型正在 :count 个报价组中使用',
    ],

    // Expenses Section
    'expenses' => [
        'section_title' => '费用',
        'expense' => '费用项目',
        'charge_mode' => '收费模式',
        'price' => '价格',
    ],

    // Validation Messages
    'validation' => [
        'numeric' => '值必须是有效数字',
        'min' => '值不能为负数',
        'required' => ':field 是必填项',
        'required_field' => [
            'driver_meal_budget' => '司机餐饮预算是必填项',
            'driver_accommodation_budget' => '司机住宿预算是必填项',
            'entry_price' => '门票价格是必填项',
            'companion_meal_budget' => '陪同人员餐饮预算是必填项',
            'companion_accommodation_budget' => '陪同人员住宿预算是必填项',
            'price' => '价格是必填项',
        ],
    ],

    // Placeholders
    'placeholders' => [
        'enter_price' => '请输入价格',
        'select_value' => '请选择',
    ],

    // Page Titles and Actions
    'page' => [
        'edit_breakdown_title' => '编辑明细 - 报价单 :number',
        'store' => '存储',
        'save_and_close' => '保存并关闭',
        'save_and_complete' => '保存并完成',
        'view_quotation' => '查看报价单',
        'complete_breakdown_heading' => '完成明细',
        'complete_breakdown_description' => '您确定要完成此明细吗？这将保存、标记为完成，并跳转到报价组页面。',
        'complete_breakdown_submit' => '是的，完成',
        'cannot_edit_title' => '无法编辑明细',
        'cannot_edit_body' => '请在编辑明细之前先完成行程。',
        'saved_successfully' => '明细保存成功！',
        'error_saving' => '保存明细时出错',
        'completed_successfully' => '明细完成成功！',
        'can_create_offers' => '您现在可以创建报价了。',
        'error_completing' => '完成明细时出错',
        'cannot_complete_title' => '无法完成明细',
        'cannot_complete_body' => '请先完成行程。',
        'cannot_save_title' => '无法保存明细',
        'cannot_save_body' => '行程未完成。请先完成行程。',
    ],
];

