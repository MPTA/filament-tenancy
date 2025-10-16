<?php

return [
    'resource_name' => '报价单',
    'resource_name_plural' => '报价单',
    'navigation_label' => '报价单',
    'navigation_group' => '报价管理',
    
    // Form Fields
    'fields' => [
        'inquiry_title' => '询价标题',
        'title' => '标题',
        'attachments' => '附件',
        'reference' => '参考',
        'contact' => '联系人',
        'requested_currency' => '请求货币',
        'inquiry_number' => '询价编号',
        'quotation_number' => '报价单编号',
        'currency' => '货币',
        'date_type' => '日期类型',
        'accommodation_stars' => '住宿星级',
        'arrival' => '到达',
        'departure' => '离开',
        'start_date' => '开始日期',
        'end_date' => '结束日期',
        'from_date' => '开始日期',
        'to_date' => '结束日期',
        'exchange_rate' => '汇率',
        'expire_date' => '过期日期',
        'expiry_date' => '到期日期',
        'room_categories' => '房间类别',
        'foreigner_passengers' => '外国乘客',
        'description' => '描述',
        'internal_note' => '内部备注',
        'first_name' => '名字',
        'last_name' => '姓氏',
        'mobile' => '手机',
        'postal_address' => '邮政地址',
        'is_customer' => '是客户',
        'entry_date_arrival' => '入境日期（到达）',
        'travel_mode' => '旅行方式',
        'total_days' => '总天数',
        'total_activities' => '总活动数',
        'breakfast' => '早餐',
        'lunch' => '午餐',
        'dinner' => '晚餐',
        'tickets' => '票务',
        'attractions' => '景点',
        'experiences' => '体验',
        'vehicle' => '车辆',
        'companion' => '陪同',
        'enter_transportation_details' => '输入交通详情',
        'vehicle_days' => '车辆天数',
        'half_days' => '半天',
        'vehicle_hours' => '车辆小时',
        'driver_meal_budget' => '司机餐费预算',
        'driver_accommodation_budget' => '司机住宿预算',
        'companion_meal_budget' => '陪同餐费预算',
        'companion_accommodation_budget' => '陪同住宿预算',
        'vehicle_type' => '车辆类型',
        'per_day_price' => '每天价格',
        'half_day_price' => '半天价格',
        'per_hour_price' => '每小时价格',
        'transport_mode' => '交通方式',
        'from_city' => '出发城市',
        'to_city' => '到达城市',
        'class' => '等级',
        'price' => '价格',
        'meal_type' => '餐食类型',
        'charge_mode' => '计费方式',
        'hotel' => '酒店',
        'nights' => '晚数',
        'room_prices' => '房间价格',
        'attraction' => '景点',
        'outview' => '外观',
        'entry_price' => '门票价格',
        'sub_attractions' => '子景点',
        'companion_type' => '陪同类型',
        'offer_group_number' => '优惠组编号',
        'created_at' => '创建时间',
        'include_driver_meal' => '包含司机餐费',
        'driver_same_meal' => '司机同餐',
        'include_driver_hotel' => '包含司机住宿',
        'driver_stays_same_hotel' => '司机同住',
        'driver_room_type' => '司机房型',
        'same_meal' => '同餐',
        'stay_same_hotel' => '同住',
        'room_type' => '房型',
        'living_city' => '居住城市',
        'driver_meal_cost' => '司机餐费',
        'driver_hotel_cost' => '司机住宿费',
        'same_hotel' => '同住',
        'driver_room' => '司机房间',
        'car' => '车辆',
        'pax' => '人数',
        'drivers' => '司机',
        'markup' => '加价',
        'leaders_quantity' => '领队数量',
        'leader_room_category' => '领队房型',
        'pax_quantity' => '旅客数量',
        'drivers_quantity' => '司机数量',
        'markup_percent' => '加价（%）',
        'full_days' => '全天',
        'hours' => '小时',
        'day_price' => '每天价格',
        'total_companion_salary' => '陪同工资总计',
        'meal_records' => '餐食记录',
        'quantity' => '数量',
        'unit_price' => '单价',
        'total_price' => '总价',
        'ticket_records' => '票务记录',
        'experience_records' => '体验记录',
        'attraction_records' => '景点记录',
        'per_night' => '每晚',
        'total_cost' => '总费用',
        'expense_records' => '费用记录',
        'accommodation_records' => '住宿记录',
        'total_companion_cost' => '陪同总费用',
        'salary' => '工资',
        'accommodation' => '住宿',
    ],
    
    'helpers' => [
        'foreigner_passengers' => '如果此报价单适用于外国乘客，请选中此项',
        'foreigner_passengers_affects' => '如果乘客是外国人请启用（影响景点定价）',
        'attachments' => '您可以上传最多10个文件。每个文件最大10MB。',
        'room_categories' => '选择最多3种房型（必填）。',
        'room_categories_regenerate' => '选择最多3种房型（必填）。更改此项将重新生成费用明细。',
        'from_date_required' => '请先选择开始日期',
        'to_date_after_from' => '必须等于或晚于开始日期',
        'exchange_rate_format' => '输入最多4位小数的有效数字（例如：42500.5000）',
        'exchange_rate_display' => '1 :from = :rate :to',
        'description_visible' => '此描述将在报价单视图中对客户可见。',
        'internal_note_private' => '内部备注 - 对客户不可见。用于团队备注和提醒。',
        'currency_sync' => '更改货币将同时更新询价和报价单',
        'currency_sync_quotation' => '更改货币将同时更新报价单和询价',
        'entry_date_arrival' => '团队到达的入境日期',
        'entry_date_controlled' => '入境日期由交通控制。删除交通后可手动编辑。',
        'enter_transportation_details' => '启用以添加入境和出境交通信息',
        'include_driver_meal_helper' => '司机餐费是否应包含在计算中？',
        'driver_same_meal_helper' => '司机是否与乘客享用相同的餐食？',
        'include_driver_hotel_helper' => '司机住宿费是否应包含在计算中？',
        'driver_stays_same_hotel_helper' => '司机是否与乘客住在同一酒店？',
        'select_driver_room_type' => '选择司机房型',
        'companion_same_meal_helper' => '此陪同是否享用相同的餐食？',
        'companion_stay_same_hotel_helper' => '此陪同是否住在同一酒店？',
        'select_room_type' => '选择房型',
    ],
    
    'placeholders' => [
        'exchange_rate' => '例如：1.0000',
        'exchange_rate_modal' => '例如：42500.5000',
        'no_title' => '无标题',
        'no_number' => '无编号',
        'no_contact' => '无联系人',
        'not_specified' => '未指定',
        'no_reference' => '无参考',
        'no_attachments' => '无附件',
        'no_description' => '无描述',
        'no_internal_note' => '无内部备注',
        'default_room_categories' => '默认（双床房，单人房）',
        'no_contact_info' => '无联系人信息',
        'select_travel_mode' => '选择旅行方式',
        'no_transportations' => '无交通详情。点击"创建交通"添加入境和出境交通。',
        'unknown_city' => '未知城市',
        'not_specified' => '未指定',
        'na' => '不适用',
        'unknown' => '未知',
        'no_prices' => '无价格',
    ],
    
    // Repeater Labels
    'repeater_labels' => [
        'add_companion' => '添加陪同',
        'new_companion' => '新陪同',
    ],
    
    'validations' => [
        'exchange_rate_regex' => '请输入最多4位小数的有效数字。',
        'exchange_rate_numeric' => '汇率必须是数字。',
        'exchange_rate_gt' => '汇率必须大于0。',
        'to_date_after_from_date' => '结束日期必须等于或晚于开始日期。',
    ],
    
    // Table Columns
    'columns' => [
        'number' => '编号',
        'inquiry_title' => '询价标题',
        'contact' => '联系人',
        'offers' => '优惠',
        'expires' => '过期日期',
        'status' => '状态',
        'created' => '创建',
        'updated' => '更新',
    ],
    
    // Filters
    'filters' => [
        'status' => '状态',
        'active' => '有效',
        'expired' => '已过期',
    ],
    
    // Tabs
    'tabs' => [
        'information' => '信息',
        'itinerary' => '行程',
        'breakdown' => '费用明细',
        'offers' => '优惠',
    ],
    
    // Sections
    'sections' => [
        'inquiry_information' => [
            'title' => '询价信息',
            'description' => '基本询价详情和信息',
        ],
        'inquiry_itinerary_details' => [
            'title' => '询价行程详情',
            'description' => '旅行行程信息',
        ],
        'quotation_information' => [
            'title' => '报价单信息',
            'description' => '报价单和价格详情',
        ],
        'personal_information' => [
            'title' => '个人信息',
        ],
        'contact_information' => [
            'title' => '联系信息',
        ],
        'customer_status' => [
            'title' => '客户状态',
        ],
        'group_transportations' => [
            'title' => '团队交通',
            'description' => '团队的入境和出境交通详情',
        ],
        'itinerary_summary' => [
            'title' => '行程概览',
            'description' => '您的旅行计划快速概览',
        ],
        'create_itinerary' => [
            'title' => '创建行程',
            'description' => '开始构建您的旅行计划',
        ],
        'itinerary_days' => [
            'title' => '行程天数',
            'description' => '您的每日旅行计划',
        ],
        'create_breakdown' => [
            'title' => '创建费用明细',
            'description' => '开始构建您的费用明细',
        ],
        'breakdown_overview' => [
            'title' => '费用明细概览',
            'description' => '费用明细汇总和详情',
        ],
        'vehicle_types' => [
            'title' => '车辆类型',
            'description' => '车辆价格和详情',
        ],
        'tickets' => [
            'title' => '票务',
            'description' => '交通票务和价格',
        ],
        'meals' => [
            'title' => '餐食',
            'description' => '餐食类型和数量',
        ],
        'experiences' => [
            'title' => '体验',
            'description' => '体验活动和价格',
        ],
        'accommodations' => [
            'title' => '住宿',
            'description' => '酒店住宿和房间价格',
        ],
        'attractions' => [
            'title' => '景点',
            'description' => '旅游景点和门票',
        ],
        'companions' => [
            'title' => '陪同',
            'description' => '导游和陪同服务',
        ],
        'additional_expenses' => [
            'title' => '其他费用',
            'description' => '杂项费用和成本',
        ],
        'offer_groups' => [
            'title' => '优惠组',
            'description' => '管理优惠组并创建新的',
        ],
        'driver_settings' => [
            'title' => '司机设置',
            'description' => '配置司机相关费用和住宿',
        ],
        'companions_settings' => [
            'title' => '陪同',
            'description' => '添加和管理旅行陪同',
        ],
        'offers' => [
            'title' => '优惠',
            'description' => '管理此优惠组的优惠',
        ],
        'offer_details' => [
            'title' => '优惠详情',
            'description_create' => '为此优惠组创建新优惠',
            'description_edit' => '编辑优惠设置',
        ],
        'driver_settings_infolist' => [
            'title' => '司机设置',
            'description' => '司机费用配置',
        ],
        'companions_details' => [
            'title' => '陪同详情',
        ],
        'meal_details' => [
            'title' => '餐食详情',
            'description' => '总计：:total CNY - 详细餐费明细',
        ],
        'ticket_details' => [
            'title' => '票务详情',
            'description' => '总计：:total CNY - 交通票务费用明细',
        ],
        'experience_details' => [
            'title' => '体验详情',
            'description' => '总计：:total CNY - 体验费用明细',
        ],
        'attraction_details' => [
            'title' => '景点详情',
            'description' => '总计：:total CNY - 景点费用明细',
        ],
        'expense_details' => [
            'title' => '费用详情',
            'description' => '总计：:total CNY - 其他费用明细',
        ],
        'accommodation_details' => [
            'title' => '住宿详情',
            'description' => '总计：:total CNY - 住宿费用明细',
        ],
        'cost_summary' => [
            'title' => '费用汇总',
            'description' => '此陪同的总费用明细',
        ],
    ],
    
    // Actions
    'actions' => [
        'customer_view' => '客户视图',
        'view' => '查看',
        'delete' => '删除',
        'quick_add' => '快速添加',
        'quick_add_contact' => '快速添加联系人',
        'edit_inquiry' => '编辑询价',
        'edit_quotation' => '编辑报价单',
        'view_contact_details' => '查看联系人详情',
        'close' => '关闭',
        'save_changes' => '保存更改',
        'create_transportation' => '创建交通',
        'edit_transportation' => '编辑交通',
        'create_itinerary' => '创建行程',
        'edit_itinerary_days' => '编辑行程天数',
        'complete' => '完成',
        'save' => '保存',
        'create' => '创建',
        'create_breakdown' => '创建费用明细',
        'complete_itinerary_first' => '先完成行程',
        'regenerate' => '重新生成',
        'edit' => '编辑',
        'add_new_offer_group' => '添加新优惠组',
        'create_breakdown_first' => '先创建费用明细',
        'complete_breakdown_to_create_offer' => '完成费用明细以创建优惠',
        'maximum_offer_groups_reached' => '已达优惠组上限',
        'view_report' => '查看报告',
        'details' => '详情',
        'create_offer' => '创建优惠',
        'update_offer' => '更新优惠',
        'yes_delete' => '是的，删除',
    ],
    
    // Tooltips
    'tooltips' => [
        'offers_count' => '已创建 :count 个优惠',
        'no_offers_yet' => '尚无优惠',
        'edit_tooltip' => '编辑',
        'delete_tooltip' => '删除',
        'report_tooltip' => '报告',
    ],
    
    // Tooltips - Offers
    'tooltips_offers' => [
        'max_offer_groups' => '每个报价单最多允许 :max 个优惠组。',
        'create_breakdown_before_offer' => '请先创建费用明细，然后再创建优惠组。',
        'complete_breakdown_before_offer' => '请先完成费用明细，然后再创建优惠组。',
        'max_offers_per_group' => '⚠️ 已达每组 :max 个优惠上限',
        'complete_breakdown_before_create_offer' => '⚠️ 请先完成费用明细，然后再创建优惠',
    ],
    
    // Delete Modal
    'modals' => [
        'delete_heading' => '删除报价单 :number',
        'delete_description' => '确定要删除报价单行程":number"吗？',
        'delete_description_only_quotation' => '确定要删除报价单行程":number"吗？' . "\n\n" . '注意：这是询价 #:inquiry_number 的唯一报价单。您可以选择同时删除询价。',
        'delete_inquiry_checkbox' => '同时删除相关询价（#:inquiry_number）及其所有数据',
        'delete_inquiry_helper' => '警告：这将永久删除询价、询价行程及所有相关数据。',
        'delete_submit' => '删除',
    ],
    
    // Infolist Labels
    'infolist' => [
        'active_customer' => '活跃客户',
        'lead_contact' => '潜在客户',
    ],
    
    // Messages
    'messages' => [
        'email_copied' => '邮箱已复制！',
        'phone_copied' => '电话已复制！',
        'mobile_copied' => '手机已复制！',
        'breakdown_regenerated' => ':reason 费用明细已重新生成。',
        'quotation_updated' => '报价单信息已更新。',
    ],
    
    // Table Headers
    'table_headers' => [
        'type' => '类型',
        'from' => '出发',
        'to' => '到达',
        'departure' => '离开',
        'arrival' => '到达',
        'number' => '编号',
    ],
    
    // Status Labels
    'status_labels' => [
        'completed' => '已完成',
        'in_progress' => '进行中',
        'locked' => '已锁定',
        'active' => '活跃',
    ],
    
    // Modals - Itinerary
    'modals_itinerary' => [
        'create_transportation_heading' => '创建团队交通',
        'create_transportation_description' => '输入入境和出境交通详情',
        'edit_transportation_heading' => '编辑团队交通',
        'edit_transportation_description' => '更新入境和出境交通详情',
        'create_itinerary_heading' => '创建新行程',
        'create_itinerary_description' => '输入交通详情并选择旅行方式',
        'complete_itinerary_heading' => '完成行程',
        'complete_itinerary_description' => '确定要将此行程标记为完成吗？',
    ],
    
    // Modals - Breakdown
    'modals_breakdown' => [
        'create_breakdown_heading' => '创建新费用明细',
        'create_breakdown_description' => '为此报价单行程创建详细的费用明细',
        'regenerate_breakdown_heading' => '重新生成费用明细',
        'regenerate_breakdown_description' => '这将根据当前行程更新费用明细。确定继续吗？',
        'complete_breakdown_heading' => '完成费用明细',
        'complete_breakdown_description_incomplete' => '请先完成行程，然后再将费用明细标记为完成。',
        'complete_breakdown_description' => '确定要将此费用明细标记为完成吗？',
        'delete_breakdown_heading' => '删除费用明细',
        'delete_breakdown_description' => '确定要删除此费用明细吗？此操作无法撤销。',
    ],
    
    // Tooltips - Breakdown
    'tooltips_breakdown' => [
        'complete_itinerary_before_breakdown' => '请先完成行程，然后再创建费用明细',
        'complete_itinerary_before_regenerate' => '请先完成行程，然后再重新生成费用明细',
        'complete_itinerary_before_complete' => '请先完成行程，然后再将费用明细标记为完成',
        'complete_itinerary_before_edit' => '请先完成行程，然后再编辑费用明细',
    ],
    
    // Page Titles
    'page_titles' => [
        'quotation_view' => '报价单视图 - :number',
        'quotation' => '报价单 :number',
    ],
    
    // Customer View Actions
    'customer_view' => [
        'label' => '客户视图',
        'view_quotation' => '查看报价单',
        'refresh' => '刷新',
        'print' => '打印',
        'export_pdf' => '导出PDF',
        'switch_language' => '语言',
        'select_language' => '选择语言',
        'tooltip_print' => '打印报价单',
        'tooltip_download' => '下载为PDF',
        'tooltip_switch_language' => '仅更改此客户视图的显示语言',
    ],
    
    // Customer View Tooltips
    'tooltips_customer_view' => [
        'itinerary_must_be_created' => '必须先创建行程',
        'itinerary_must_be_completed' => '必须先完成行程',
        'breakdown_must_be_created' => '必须先创建费用明细',
        'breakdown_must_be_completed' => '必须先完成费用明细',
    ],
    
    // Customer View Notifications
    'notifications_customer_view' => [
        'itinerary_not_found_title' => '未找到行程',
        'itinerary_not_found_body' => '查看客户报价单前必须先创建行程。',
        'itinerary_incomplete_title' => '行程未完成',
        'itinerary_incomplete_body' => '查看客户报价单前必须先完成行程。',
        'breakdown_not_found_title' => '未找到费用明细',
        'breakdown_not_found_body' => '查看客户报价单前必须先创建费用明细。',
        'breakdown_incomplete_title' => '费用明细未完成',
        'breakdown_incomplete_body' => '查看客户报价单前必须先完成费用明细。',
        'refreshed_title' => '已刷新',
        'refreshed_body' => '页面数据已成功刷新。',
        'data_changed_title' => '数据已更改',
        'data_changed_body' => '行程或费用明细不再完整。正在重定向...',
        'cannot_print_title' => '无法打印',
        'cannot_print_itinerary_missing' => '必须先创建行程。',
        'cannot_print_itinerary_incomplete' => '打印前必须先完成行程。',
        'cannot_print_breakdown_missing' => '必须先创建费用明细。',
        'cannot_print_breakdown_incomplete' => '打印前必须先完成费用明细。',
        'cannot_export_title' => '无法导出PDF',
        'cannot_export_itinerary_missing' => '必须先创建行程。',
        'cannot_export_itinerary_incomplete' => '导出前必须先完成行程。',
        'cannot_export_breakdown_missing' => '必须先创建费用明细。',
        'cannot_export_breakdown_incomplete' => '导出前必须先完成费用明细。',
    ],
    
    // Notifications
    'notifications' => [
        'deleted_title' => '删除成功',
        'deleted_quotation_only' => '报价单已删除。',
        'deleted_quotation_and_inquiry' => '报价单和询价已删除。',
        'contact_required_fields' => '名字和邮箱为必填项',
        'contact_added_title' => '联系人已添加',
        'contact_added_body' => '联系人已创建并选中。',
        'inquiry_updated_title' => '询价更新成功！',
        'quotation_updated_title' => '报价单更新成功！',
        'transportation_created_title' => '交通创建成功！',
        'transportation_updated_title' => '交通更新成功！',
        'cannot_complete_title' => '无法完成行程！',
        'cannot_complete_body' => '最后一天（第 :day 天）不能有住宿，因为这是退房日。请编辑行程并从最后一天移除住宿。',
        'itinerary_completed_title' => '行程完成成功！',
        'itinerary_completed_body' => '费用明细已自动生成。正在跳转到费用明细表单...',
        'incomplete_itinerary_title' => '行程未完成',
        'incomplete_itinerary_body' => '请先完成行程，然后再生成费用明细。',
        'breakdown_generated_title' => '费用明细已生成',
        'breakdown_generated_body' => '费用明细已成功生成。正在跳转到费用明细表单...',
        'breakdown_regenerated_title' => '费用明细重新生成成功！',
        'breakdown_regenerated_body' => '费用明细已更新。正在跳转到费用明细编辑器...',
        'cannot_regenerate_title' => '无法重新生成',
        'cannot_regenerate_body' => '请先完成行程，然后再重新生成费用明细。',
        'cannot_complete_breakdown_title' => '无法完成费用明细',
        'cannot_complete_breakdown_body' => '请先完成行程，然后再将费用明细标记为完成。',
        'breakdown_completed_title' => '费用明细完成成功！',
        'breakdown_deleted_title' => '费用明细删除成功！',
    ],
    
    // Breakdown Placeholders
    'breakdown_placeholders' => [
        'no_rooms' => '无房间',
        'no_sub_attractions' => '无子景点',
    ],
    
    // Offer Group Infolist Placeholders
    'infolist_placeholders' => [
        'base_budget' => '基础预算',
        'unknown_companion' => '未知陪同',
        'same_meal_as_passengers' => '与乘客同餐',
        'stays_in_same_hotel' => '住在同一酒店',
        'lives_in' => '居住地：:city',
    ],
    
    // Offer Group Infolist Messages
    'infolist_messages' => [
        'salary_days' => '工资（:full 全天 + :half 半天）',
    ],
    
    // Offers Messages
    'offers_messages' => [
        'no_offers_registered' => '尚未注册任何优惠。',
        'no_offers_created' => '尚未创建任何优惠。',
        'offer_group_title' => '优惠组 :number - :status',
        'offer_group_driver_meal' => '包含司机餐费',
        'offer_group_driver_hotel' => '包含司机住宿',
        'offer_group_companions_count' => ':count 位陪同',
        'offer_group_last_sync' => '最后同步：:time',
    ],
    
    // Offers Modals
    'modals_offers' => [
        'view_offer_group_heading' => '优惠组 :number',
        'edit_offer_group_heading' => '编辑优惠组',
        'delete_offer_heading' => '删除优惠',
        'delete_offer_description' => '确定要删除此优惠吗？此操作无法撤销。',
        'create_offer_heading' => '创建新优惠',
        'edit_offer_heading' => '编辑优惠',
        'offer_details_report_heading' => '优惠详情报告',
    ],
    
    // Offers Notifications
    'notifications_offers' => [
        'max_offer_groups_title' => '已达优惠组上限',
        'max_offer_groups_body' => '每个报价单最多可创建 :max 个优惠组。',
        'offer_group_created_title' => '优惠组创建成功！',
        'offer_group_created_body' => '优惠组和陪同已保存。',
        'error_creating_offer_group_title' => '创建优惠组错误',
        'error_creating_offer_group_body' => '保存优惠组时发生错误：:error',
        'offer_group_updated_title' => '优惠组已更新！',
        'offer_group_updated_body' => '优惠组设置已更新，:count 个优惠已成功重新计算。',
        'update_failed_title' => '更新失败',
        'update_failed_body' => '发生错误：:error',
        'deleted_title' => '已删除！',
        'max_offers_title' => '已达优惠上限',
        'max_offers_body' => '每个优惠组最多可创建 :max 个优惠。',
        'validation_error_title' => '验证错误',
        'validation_error_vehicle_required' => '车辆类型为必填项。',
        'offer_created_title' => '优惠创建成功！',
        'offer_created_body' => '优惠已成功创建。包括司机、领队、陪同和所有房型的最终价格在内的所有费用已计算完成。',
        'error_creating_offer_title' => '创建优惠错误',
        'error_creating_offer_body' => '创建优惠时发生错误：:error',
        'offer_updated_title' => '优惠已更新！',
        'offer_updated_body' => '所有价格和费用已成功重新计算。',
        'offer_deleted_title' => '优惠删除成功！',
        'offer_deleted_body' => '优惠及所有相关记录已自动删除。',
        'error_deleting_offer_title' => '删除优惠错误',
        'error_deleting_offer_body' => '删除优惠时发生错误：:error',
    ],
];

