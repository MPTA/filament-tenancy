<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Enum Translations (Chinese Simplified)
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for translating enum labels and descriptions.
    |
    */

    'activity_category_type' => [
        'meal' => '餐饮',
        'meal_description' => '食品和餐饮相关活动',
        'attraction' => '景点',
        'attraction_description' => '旅游景点和地标',
        'ticket' => '门票',
        'ticket_description' => '需要门票的活动和演出',
        'experience' => '体验',
        'experience_description' => '独特的体验和活动',
        'custom' => '自定义',
        'custom_description' => '用户自定义活动',
    ],

    'attraction_type' => [
        'natural' => '自然',
        'natural_description' => '海滩、山脉、国家公园、森林、湖泊、瀑布、岛屿、野生动物保护区、峡谷、沙漠、河流、火山、洞穴',
        'man_made' => '人造',
        'man_made_description' => '历史遗址、纪念碑、博物馆、艺术画廊、城堡/堡垒、古代寺庙、摩天大楼、桥梁、动物园/水族馆、植物园、主题公园、游乐园、工厂',
        'cultural' => '文化',
        'cultural_description' => '遗产地、民族飞地、生活历史博物馆、宗教寺庙、节日、公共艺术、图书馆、剧院、民族社区、工业遗产',
        'sport' => '运动',
        'sport_description' => '体育场、滑雪胜地、高尔夫球场、冒险公园、极限运动场地、帆船赛、F1赛道、攀岩墙、冲浪点',
        'events' => '活动',
        'events_description' => '嘉年华、音乐会、展览、文化节、宗教活动、体育赛事、工业旅游、贸易展览',
        'leisure' => '休闲',
        'leisure_description' => '度假村、购物中心、水疗中心、夜生活场所、蜜月景点、健康中心、游轮港口、赌场',
    ],

    'charge_mode' => [
        'per_person' => '按人收费',
        'per_person_description' => '按每个人收费',
        'per_group' => '按组收费',
        'per_group_description' => '按组收费，不考虑人数',
    ],

    'companion_category' => [
        'tour_guide' => '导游',
        'tour_guide_description' => '专业导游，提供观光和文化体验',
        'staff' => '工作人员',
        'staff_description' => '一般工作人员，提供支持和协助',
        'translator' => '翻译',
        'translator_description' => '语言翻译，提供沟通支持',
        'driver' => '司机',
        'driver_description' => '专业司机，提供交通服务',
    ],

    'contact_type' => [
        'user' => '用户',
        'lead' => '潜在客户',
        'customer' => '客户',
    ],

    'gender' => [
        'male' => '男',
        'female' => '女',
    ],

    'hire_mode' => [
        'daily' => '按天',
        'daily_description' => '全天租用（24小时）',
        'half_day' => '半天',
        'half_day_description' => '半天租用（最多8小时）',
        'hourly' => '按小时',
        'hourly_description' => '按小时租用',
    ],

    'inquiry_date_type' => [
        'fixed_date' => '固定日期',
        'fixed_date_description' => '特定的固定旅行日期',
        'flexible_date' => '灵活日期',
        'flexible_date_description' => '灵活的旅行日期范围',
        'series' => '系列',
        'series_description' => '定期旅行系列',
    ],

    'inquiry_type' => [
        'itinerary' => '行程',
        'itinerary_description' => '旅行行程规划咨询',
    ],

    'meal_part' => [
        'breakfast' => '早餐',
        'breakfast_description' => '早晨用餐',
        'lunch' => '午餐',
        'lunch_description' => '中午用餐',
        'dinner' => '晚餐',
        'dinner_description' => '晚上用餐',
    ],

    'offer_group_link_status' => [
        'linked' => '已关联',
        'linked_description' => '自动与明细变化同步',
        'decoupled' => '已解耦',
        'decoupled_description' => '独立快照 - 不受明细变化影响',
        'outdated' => '已过期',
        'outdated_description' => '明细已更改 - 需要审查',
    ],

    'quotation_type' => [
        'general' => '常规',
        'itinerary' => '行程',
    ],

    'room_category' => [
        'twin' => '双床房',
        'single' => '单人间',
        'double_for_two' => '双人大床房（两人）',
        'double_for_one' => '双人大床房（一人）',
        'triple' => '三人间',
        'suite_for_one' => '套房（一人）',
        'suite_for_two' => '套房（两人）',
    ],

    'star_rating' => [
        '1' => '一星级',
        '1_description' => '基础住宿，设施简单',
        '2' => '二星级',
        '2_description' => '经济型住宿，基础设施',
        '3' => '三星级',
        '3_description' => '中档住宿，设施和服务良好',
        '4' => '四星级',
        '4_description' => '高品质住宿，设施和服务优秀',
        '5' => '五星级',
        '5_description' => '豪华住宿，设施和服务卓越',
    ],

    'ticket_class' => [
        'economy' => '经济舱',
        'economy_description' => '标准经济舱机票',
        'business' => '商务舱',
        'business_description' => '商务舱机票，增强服务',
        'first' => '头等舱',
        'first_description' => '头等舱机票，高端服务',
    ],

    'transport_mode' => [
        'air' => '航空',
        'air_description' => '航空运输（航班）',
        'train' => '火车',
        'train_description' => '火车运输',
        'land' => '陆路',
        'land_description' => '陆路运输（公共汽车、汽车等）',
    ],

    'travel_mode' => [
        'air' => '航空旅行',
        'air_description' => '仅乘飞机旅行',
        'self_driving' => '自驾',
        'self_driving_description' => '仅开车/车辆旅行',
        'air_self_driving' => '航空 + 自驾',
        'air_self_driving_description' => '先航空旅行，然后自驾',
        'self_driving_air' => '自驾 + 航空',
        'self_driving_air_description' => '先自驾，然后航空旅行',
    ],

    'vehicle_usage_mode' => [
        'hour' => '小时',
        'half_day' => '半天',
        'full_day' => '全天',
    ],
];

