<?php

return [
    // UUID前缀类型
    'uuid'   => [
        'admin'          => '管理员',
        'driver'         => '司机',
        'truck'          => '车辆',
        'trailer'        => '挂车',
        'order'          => '订单',
        'place'          => '装卸货地',
        'order_log'      => '订单日志',
        'truck_log'      => '车辆日志',
        'gas_card_order' => '油卡充值',
    ],

    // 通用状态
    'status' => [
        'success' => '已完成',  // 99
        'cancel'  => '已取消',  // 88
    ],

    // 车辆相关常量
    'truck'  => [
        // 车辆状态相关常量
        'status'   => [
            'available'      => '车辆可用',      // 1
            'driver_confirm' => '等待司机确认',   // 10
            'in_transit'     => '车辆在途',      // 20
        ],
        // 车辆油卡相关常量
        'gas_card' => [
            'status'  => [
                'normal' => '正常',   // 1
                'lose'   => '已挂失', // 2
                'cancel' => '已注销', // 88
            ],
            'channel' => [
                'sinopec'    => '中石化',  // 101
                'petrochina' => '中石油',  // 102
            ],
            'order'   => [
                'waiting_appoval'  => '等待审核',
                'waiting_recharge' => '等待充值',
                'cancel'           => '审核失败',
                'recharge_success' => '充值成功',
            ],
        ],
        // 检查证件
        'approval' => [
            'type'   => [
                'truck_certificates'   => '车辆证件检查', // 1
                'trailer_certificates' => '挂车证件检查', // 1
            ],
            'status' => [
                'waiting' => '等待审核',
                'cancel'  => '审核不通过',
                'success' => '审核通过',
            ],
        ],
        // 车辆费用日志相关常量
        'log'      => [
            // 车辆费用日志状态常量
            'status'          => [
                'normal' => '正常', // 1
            ],

            // 日志类型
            'type'            => [
                'refuel'    => '加油',     // 1
                'coolant'   => '加水',     // 2
                'park'      => '停车',     // 3
                'weight'    => '过磅',     // 4
                'repair'    => '维修',     // 5
                'adblue'    => '尿素',     // 6
                'penalty'   => '罚款',     // 7
                'toll_road' => '路桥',     // 8
                'other'     => '其他费用', // 50
            ],
            // 加油支付类型
            'refuel_pay_type' => [
                'fixed'    => '定点加油', // 1
                'gas_card' => '油卡加油', // 2
                'cash'     => '现金加油', // 3
            ],
            // 维修类型
            'repair_type'     => [
                'mechanical' => '机械维修', // 1
                'circuit'    => '电路维修', // 2
                'trailer'    => '车厢维修', // 3
                'tire'       => '轮胎',     // 4
                'care'       => '保养',     // 5
                'other'      => '其他',     // 50
            ],
        ],
    ],

    // 订单相关常量
    'order'  => [
        // 专线订单
        'mainline' => [
            // 专线订单状态常量
            'status' => [
                'uncreated'        => '未创建完成的订单',  // 0
                'created'          => '新创建的订单',     // 1
                'driver_confirm'   => '等待司机确认',     // 10
                'driver_prepare'   => '司机正在准备',     // 15
                'in_transit'       => '车辆在途',         // 20
                'arrive_loading'   => '到达装货地点',     // 30
                'arrive_unloading' => '到达卸货地点',     // 40
                'finish_unloading' => '已完成卸货',       // 50
                'success'          => '运输完成',        // 99
                'cancel'           => '订单取消',        // 88
            ],
            // 订单司机相关
            'driver' => [
                'type'   => [
                    'confirm' => '接单者',   // 1
                    'follow'  => '随车司机', // 2
                ],
                'status' => [
                    'normal'  => '正常',    // 1
                    'success' => '运输完成', // 99
                    'cancel'  => '订单取消', // 88
                ],
            ],
            // 装卸货地址类型常量
            'place'  => [
                'type' => [
                    'loading'   => '装货地', // 1
                    'unloading' => '卸货地', // 2
                ],
            ],
            // 日志相关常量
            'log'    => [
                'status' => [
                    'normal' => '正常', // 1
                ],
                'type'   => [
                    'appoint_truck'         => '指派车辆',      // 2
                    'appoint_trailer'       => '指派挂车',      // 3
                    'driver_confirm'        => '确认接单',      // 10
                    'swap_driving'          => '司机换班',      // 11
                    'check_truck_certs'     => '检查证件',      // 12
                    'check_truck'           => '检查车辆',      // 13
                    'check_trailer_certs'   => '检查挂车证件',  // 14
                    'check_trailer'         => '检查挂车',      // 15
                    'traffic_jam'           => '记录堵车',      // 21
                    'high_way_enter'        => '进入高速',      // 22
                    'high_way_leave'        => '离开高速',      // 23
                    'arrive_loading'        => '到达装货地',    // 30
                    'receive_receipt'       => '交接单据',      // 31
                    'count_loading_begin'   => '装货计时',      // 32
                    'count_loading_end'     => '装货计时',      // 33
                    'record_seals'          => '录封签号',      // 34
                    'record_weight'         => '录过磅单',      // 35
                    'add_loading'           => '添加多点装货地', // 38
                    'complete_loading'      => '装货完成',      // 39
                    'arrive_unloading'      => '到达卸货地',    // 40
                    'send_receipt'          => '交接单据',      // 41
                    'count_unloading_begin' => '卸货计时',      // 42
                    'count_unloading_end'   => '卸货计时',      // 43
                    'unloading_abnormal'    => '卸货异常',      // 44
                    'add_unloading'         => '添加多点卸货地', // 48
                    'complete_unloading'    => '卸货完成',      // 49
                    'cancel'                => '订单取消',      // 88
                    'success'               => '运输完成',      // 99
                ],
            ],
        ],
    ],
];