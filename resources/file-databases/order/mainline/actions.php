<?php

// 订单操作控制
return [
    'type' => 'object',

    'columns'  => ['stage', 'code', 'name', 'repeat', 'singleton', 'position'],

    // 单例操作指整个订单流程共享是否完成状态
    'comments' => ['阶段', '操作代码', '操作名称', '是否可重复', '是否单例操作', '所在位置'],

    'defaults' => ['', '', '', true, false, 0],


    'records' => [
        // 派单阶段
        [
            'stage'    => 'created',
            'code'     => 'appoint_truck',
            'name'     => '指派车辆',
            'repeat'   => false,
            'position' => 1,
        ],
        [
            'stage'    => 'created',
            'code'     => 'appoint_trailer',
            'name'     => '指派挂车',
            'repeat'   => false,
            'position' => 2,
        ],

        // 接单阶段
        [
            'stage'    => 'driver_confirm',
            'code'     => 'driver_confirm',
            'name'     => '确认接单',
            'repeat'   => false,
            'position' => 1,
        ],

        // 准备阶段
        [
            'stage'    => 'driver_prepare',
            'code'     => 'check_truck_certs',
            'name'     => '检查车辆证件',
            'repeat'   => false,
            'position' => 1,
        ],
        [
            'stage'    => 'driver_prepare',
            'code'     => 'check_truck',
            'name'     => '检查车辆',
            'repeat'   => false,
            'position' => 2,
        ],

        // 在途阶段
        [
            'stage'    => 'in_transit',
            'code'     => 'traffic_jam',
            'name'     => '记录堵车',
            'position' => 1,
        ],
        [
            'stage'    => 'in_transit',
            'code'     => 'high_way_enter',
            'name'     => '进入高速',
            'position' => 2,
        ],
        [
            'stage'    => 'in_transit',
            'code'     => 'high_way_leave',
            'name'     => '离开高速',
            'position' => 3,
        ],
        [
            'stage'    => 'in_transit',
            'code'     => 'arrive_loading',
            'name'     => '到达装货地',
            'repeat'   => false,
            'position' => 4,
        ],
        [
            'stage'    => 'in_transit',
            'code'     => 'arrive_unloading',
            'name'     => '到达卸货地',
            'repeat'   => false,
            'position' => 5,

        ],

        // 到达装货地阶段
        [
            'stage'     => 'arrive_loading',
            'code'      => 'check_trailer_certs',
            'name'      => '检查挂车证件',
            'repeat'    => false,
            'singleton' => true,
            'position'  => 1,
        ],
        [
            'stage'     => 'arrive_loading',
            'code'      => 'check_trailer',
            'name'      => '检查挂车',
            'repeat'    => false,
            'singleton' => true,
            'position'  => 2,

        ],
        [
            'stage'     => 'arrive_loading',
            'code'      => 'receive_receipt',
            'name'      => '单据拍照',
            'singleton' => true,
            'position'  => 3,
        ],
        [
            'stage'     => 'arrive_loading',
            'code'      => 'record_weight',
            'name'      => '录过磅单',
            'singleton' => true,
            'position'  => 4,
        ],
        [
            'stage'     => 'arrive_loading',
            'code'      => 'record_seals',
            'name'      => '录封签号',
            'singleton' => true,
            'position'  => 5,
        ],
        [
            'stage'    => 'arrive_loading',
            'code'     => 'count_loading_begin',
            'name'     => '装货计时',
            'position' => 6,
        ],
        [
            'stage'    => 'arrive_loading',
            'code'     => 'count_loading_end',
            'name'     => '装货计时',
            'position' => 7,
        ],
        [
            'stage'    => 'arrive_loading',
            'code'     => 'add_loading',
            'name'     => '添加新的装货地',
            'repeat'   => false,
            'position' => 14,
        ],
        [
            'stage'    => 'arrive_loading',
            'code'     => 'complete_loading',
            'name'     => '装货完成',
            'repeat'   => false,
            'position' => 15,
        ],

        // 到达卸货阶段
        [
            'stage'     => 'arrive_unloading',
            'code'      => 'check_truck',
            'name'      => '检查车辆',
            'repeat'    => false,
            'position'  => 5,
        ],
        [
            'stage'     => 'arrive_unloading',
            'code'      => 'send_receipt',
            'name'      => '交接单据',
            'singleton' => true,
            'position'  => 1,
        ],
        [
            'stage'    => 'arrive_unloading',
            'code'     => 'count_unloading_begin',
            'name'     => '卸货计时',
            'position' => 2,
        ],
        [
            'stage'    => 'arrive_unloading',
            'code'     => 'count_unloading_end',
            'name'     => '卸货计时',
            'position' => 3,
        ],
        [
            'stage'    => 'arrive_unloading',
            'code'     => 'unloading_abnormal',
            'name'     => '卸货异常',
            'position' => 4,
        ],
        [
            'stage'    => 'arrive_unloading',
            'code'     => 'add_unloading',
            'name'     => '添加新的卸货地',
            'repeat'   => false,
            'position' => 14,
        ],
        [
            'stage'    => 'arrive_unloading',
            'code'     => 'complete_unloading',
            'name'     => '卸货完成',
            'repeat'   => false,
            'position' => 15,
        ],
    ],
];