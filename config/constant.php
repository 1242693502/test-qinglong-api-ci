<?php

return [
    // UUID前缀类型
    'uuid'         => [
        'admin'   => 11, // 管理员
        'driver'  => 12, // 司机
        'truck'   => 13, // 车辆，更改此值时，请同时更改 file-databases/truck/lengths 与 types
        'trailer' => 14, // 挂车，更改此值时，请同时更改 file-databases/truck/lengths 与 types

        'order' => 35, // 订单
        'place' => 36, // 装卸货地

        'order_log' => 57, // 订单日志
        'truck_log' => 58, // 车辆日志

        'gas_card_order' => 20,    // 油卡充值UUID
    ],

    // 通用状态
    'status'       => [
        'success' => 99, // 已完成
        'cancel'  => 88, // 已取消
    ],

    // 车辆相关常量
    'truck'        => [
        // 车辆状态相关常量
        'status'   => [
            'available'      => 1,  // 车辆可用
            'driver_confirm' => 10, // 已经指派订单，等待司机确认
            'in_transit'     => 20, // 车辆在途
        ],
        // 车辆油卡相关常量
        'gas_card' => [
            'status'  => [
                'normal' => 1,  // 正常
                'lose'   => 2,  // 已挂失
                'cancel' => 88, // 已注销
            ],
            'channel' => [
                'sinopec'    => 101,    // 中石化
                'petrochina' => 102,    // 中石油
            ],
            'order'   => [
                'waiting_appoval'  => 1,
                'waiting_recharge' => 10,
                'cancel'           => 88,
                'recharge_success' => 99,
            ],
        ],
        // 检查证件
        'approval' => [
            'type'   => [
                'truck_certificates'   => 1, // 车辆证件检查
                'trailer_certificates' => 2, // 挂车证件检查
            ],
            'status' => [
                'waiting' => 1,
                'cancel'  => 88,
                'success' => 99,
            ],
        ],
        // 车辆费用日志相关常量
        'log'      => [
            // 车辆费用日志状态常量
            'status'          => [
                'normal' => 1, // 正常
            ],
            // 日志类型
            'type'            => [
                'refuel'    => 1,  // 加油
                'coolant'   => 2,  // 加水
                'park'      => 3,  // 停车
                'weight'    => 4,  // 过磅
                'repair'    => 5,  // 维修
                'adblue'    => 6,  // 尿素
                'penalty'   => 7,  // 罚款
                'toll_road' => 8,  // 路桥费
                'other'     => 50, // 其他费用
            ],
            // 加油支付类型
            'refuel_pay_type' => [
                'fixed'    => 1, // 定点加油
                'gas_card' => 2, // 油卡加油
                'cash'     => 3, // 现金加油
            ],
            // 维修类型
            'repair_type'     => [
                'mechanical' => 1,  // 机械维修
                'circuit'    => 2,  // 电路维修
                'trailer'    => 3,  // 车厢维修
                'tire'       => 4,  // 轮胎
                'care'       => 5,  // 保养
                'other'      => 50, // 其他
            ],
        ],
    ],

    // 订单相关常量
    'order'        => [
        // 专线订单
        'mainline' => [
            // 专线订单状态常量
            'status' => [
                'uncreated'        => 0,  // 未创建完成的订单
                'created'          => 1,  // 新创建的订单(未指派司机车辆）
                'driver_confirm'   => 10, // 等待司机确认
                'driver_prepare'   => 15, // 司机正在准备
                'in_transit'       => 20, // 车辆在途
                'arrive_loading'   => 30, // 到达装货地点
                'arrive_unloading' => 40, // 到达卸货地点
                'finish_unloading' => 50, // 已完成卸货
                'success'          => 99, // 运输完成
                'cancel'           => 88, // 订单取消
            ],
            // 订单司机相关
            'driver' => [
                'type'   => [
                    'confirm' => 1, // 接单者
                    'follow'  => 2, // 随车司机
                ],
                'status' => [
                    'normal'  => 1,  // 正常
                    'success' => 99, // 已完成
                    'cancel'  => 88, // 已取消
                ],
            ],
            // 装卸货地址类型常量
            'place'  => [
                'type' => [
                    'loading'   => 1, // 装货地
                    'unloading' => 2, // 卸货地
                ],
            ],
            // 日志相关常量
            'log'    => [
                'status' => [
                    'normal' => 1, // 正常
                ],
                'type'   => [
                    'appoint_truck'         => 2,  // 指派车辆
                    'appoint_trailer'       => 3,  // 指派挂车
                    'driver_confirm'        => 10, // 确认接单
                    'swap_driving'          => 11, // 司机换班
                    'check_truck_certs'     => 12, // 检查证件
                    'check_truck'           => 13, // 检查车辆
                    'check_trailer_certs'   => 14, // 检查挂车证件
                    'check_trailer'         => 15, // 检查挂车
                    'traffic_jam'           => 21, // 记录堵车
                    'high_way_enter'        => 22, // 进入高速
                    'high_way_leave'        => 23, // 离开高速
                    'arrive_loading'        => 30, // 到达装货地
                    'receive_receipt'       => 31, // 交接单据
                    'count_loading_begin'   => 32, // 装货计时开始
                    'count_loading_end'     => 33, // 装货计时结束
                    'record_seals'          => 34, // 录封签号
                    'record_weight'         => 35, // 录过磅单
                    'add_loading'           => 38, // 添加多点装货地
                    'complete_loading'      => 39, // 装货完成
                    'arrive_unloading'      => 40, // 到达卸货地
                    'send_receipt'          => 41, // 交接单据
                    'count_unloading_begin' => 42, // 卸货计时开始
                    'count_unloading_end'   => 43, // 卸货计时结束
                    'unloading_abnormal'    => 44, // 卸货异常
                    'add_unloading'         => 48, // 添加多点卸货地
                    'complete_unloading'    => 49, // 卸货完成
                    'cancel'                => 88, // 订单取消
                    'success'               => 99, // 运输完成
                ],
            ],
        ],
    ],

    // 通知
    'notification' => [
        'type' => [
            'system' => 1, // 系统
            'admin'  => 2, // 管理员
            'driver' => 3, // 司机
        ],

        'status' => [
            'normal' => 1, // 新消息
            'read'   => 2, // 已读
        ],
    ],
];