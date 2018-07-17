<?php

$truckUUID   = cons('uuid.truck');
$trailerUUID = cons('uuid.trailer');

return [
    'columns'  => ['code', 'name', 'type'],

    // 长度类型需与 constant.php 中 uuid.truck 和 uuid.trailer 对应
    'comments' => ['车辆编码', '编码名称', '车辆类型'],

    'records' => [
        ['H01', '普通货车', $truckUUID],
        ['H02', '厢式货车', $truckUUID],
        ['H05', '平板货车', $truckUUID],
        ['H09', '仓栅式货车', $truckUUID],
        ['G02', '厢式挂车', $trailerUUID],
        ['G07', '仓栅式挂车', $trailerUUID],
        ['G04', '平板挂车', $trailerUUID],
        ['G01', '普通挂车', $trailerUUID],
        ['G05', '集装箱挂车', $trailerUUID],
    ],
];