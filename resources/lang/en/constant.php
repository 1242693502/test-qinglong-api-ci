<?php

return [
    // UUID前缀类型
    'uuid'   => [
        'admin'          => 'admin',     // 管理员
        'driver'         => 'driver',    // 司机
        'truck'          => 'truck',     // 车辆
        'trailer'        => 'trailer',   // 挂车
        'order'          => 'order',     // 订单
        'place'          => 'place',     // 装卸货地
        'order_log'      => 'order_log', // 订单日志
        'truck_log'      => 'truck_log', // 车辆日志
        'gas_card_order' => 'gas_card_order',    // 油卡充值UUID
    ],

    // 通用状态
    'status' => [
        'success' => 'success', // 已完成
        'cancel'  => 'cancel',  // 已取消
    ],

    // 车辆相关常量
    'truck'  => [
        // 车辆状态相关常量
        'status'   => [
            'available'      => 'available',      // 车辆可用
            'driver_confirm' => 'driver_confirm', // 已经指派订单，等待司机确认
            'in_transit'     => 'in_transit',     // 车辆在途
        ],
        // 车辆油卡相关常量
        'gas_card' => [
            'status'  => [
                'normal' => 'normal', // 正常
                'lose'   => 'lose',   // 已挂失
                'cancel' => 'cancel', // 已注销
            ],
            'channel' => [
                'sinopec'    => 'sinopec',      // 中石化
                'petrochina' => 'petrochina',  // 中石油
            ],
            'order'   => [
                'waiting_appoval'  => 'waiting_appoval',
                'waiting_recharge' => 'waiting_recharge',
                'cancel'           => 'cancel',
                'recharge_success' => 'recharge_success',
            ],
        ],
        // 检查证件
        'approval' => [
            'type'   => [
                'truck_certificates'   => 'truck_certificates', // 车辆证件
                'trailer_certificates' => 'trailer_certificates', // 挂车证件
            ],
            'status' => [
                'waiting' => 'waiting',
                'cancel'  => 'cancel',
                'success' => 'success',
            ],
        ],
        // 车辆费用日志相关常量
        'log'      => [
            // 车辆费用日志状态常量
            'status'          => [
                'normal' => 'normal', // 正常
            ],
            // 日志类型
            'type'            => [
                'refuel'    => 'refuel',    // 加油
                'coolant'   => 'coolant',   // 加水
                'park'      => 'park',      // 停车
                'weight'    => 'weight',    // 过磅
                'repair'    => 'repair',    // 维修
                'adblue'    => 'adblue',    // 尿素
                'penalty'   => 'penalty',   // 罚款
                'toll_road' => 'toll_road', // 路桥费
                'other'     => 'other',     // 其他费用
            ],
            // 加油支付类型
            'refuel_pay_type' => [
                'fixed'    => 'fixed',    // 定点加油
                'gas_card' => 'gas_card', // 油卡加油
                'cash'     => 'cash',     // 现金加油
            ],
            // 维修类型
            'repair_type'     => [
                'mechanical' => 'mechanical', // 机械维修
                'circuit'    => 'circuit',    // 电路维修
                'trailer'    => 'trailer',    // 车厢维修
                'tire'       => 'tire',       // 轮胎
                'care'       => 'care',       // 保养
                'other'      => 'other',      // 其他
            ],
        ],
    ],

    // 订单相关常量
    'order'  => [
        // 专线订单
        'mainline' => [
            // 专线订单状态常量
            'status' => [
                'uncreated'        => 'uncreated',        // 未创建完成的订单
                'created'          => 'created',          // 新创建的订单(未指派司机车辆）
                'driver_confirm'   => 'driver_confirm',   // 等待司机确认
                'driver_prepare'   => 'driver_prepare',   // 司机正在准备
                'in_transit'       => 'in_transit',       // 车辆在途
                'arrive_loading'   => 'arrive_loading',   // 到达装货地点
                'arrive_unloading' => 'arrive_unloading', // 到达卸货地点
                'finish_unloading' => 'finish_unloading', // 已完成卸货
                'success'          => 'success',          // 运输完成
                'cancel'           => 'cancel',           // 订单取消
            ],
            // 订单司机相关
            'driver' => [
                'type'   => [
                    'confirm' => 'confirm', // 接单者
                    'follow'  => 'follow',  // 随车司机
                ],
                'status' => [
                    'normal'  => 'normal',  // 正常
                    'success' => 'success', // 已完成
                    'cancel'  => 'cancel',  // 已取消
                ],
            ],
            // 装卸货地址类型常量
            'place'  => [
                'type' => [
                    'loading'   => 'loading',   // 装货地
                    'unloading' => 'unloading', // 卸货地
                ],
            ],
            // 日志相关常量
            'log'    => [
                'status' => [
                    'normal' => 'normal', // 正常
                ],
                'type'   => [
                    'appoint_truck'         => 'appoint_truck',         // 指派车辆
                    'appoint_trailer'       => 'appoint_trailer',       // 指派挂车
                    'driver_confirm'        => 'driver_confirm',        // 确认接单
                    'swap_driving'          => 'swap_driving',          // 司机换班
                    'check_truck_certs'     => 'check_truck_certs',     // 检查证件
                    'check_truck'           => 'check_truck',           // 检查车辆
                    'check_trailer_certs'   => 'check_trailer_certs',   // 检查挂车证件
                    'check_trailer'         => 'check_trailer',         // 检查挂车
                    'traffic_jam'           => 'traffic_jam',           // 记录堵车
                    'high_way_enter'        => 'high_way_enter',        // 进入高速
                    'high_way_leave'        => 'high_way_leave',        // 离开高速
                    'arrive_loading'        => 'arrive_loading',        // 到达装货地
                    'receive_receipt'       => 'receive_receipt',       // 交接单据
                    'count_loading_begin'   => 'count_loading_begin',   // 装货计时开始
                    'count_loading_end'     => 'count_loading_end',     // 装货计时结束
                    'record_seals'          => 'record_seals',          // 录封签号
                    'record_weight'         => 'record_weight',         // 录过磅单
                    'add_loading'           => 'add_loading',           // 添加多点装货地
                    'complete_loading'      => 'complete_loading',      // 装货完成
                    'arrive_unloading'      => 'arrive_unloading',      // 到达卸货地
                    'send_receipt'          => 'send_receipt',          // 交接单据
                    'count_unloading_begin' => 'count_unloading_begin', // 卸货计时开始
                    'count_unloading_end'   => 'count_unloading_end',   // 卸货计时结束
                    'unloading_abnormal'    => 'unloading_abnormal',    // 卸货异常
                    'add_unloading'         => 'add_unloading',         // 添加多点卸货地
                    'complete_unloading'    => 'complete_unloading',    // 卸货完成
                    'cancel'                => 'cancel',                // 订单取消
                    'success'               => 'success',               // 运输完成
                ],
            ],
        ],
    ],
];