<?php

$truckUUID   = cons('uuid.truck');
$trailerUUID = cons('uuid.trailer');

// 车辆证件照类型
return [
    'columns' => ['code', 'name', 'type'],

    'comments' => ['证件照编码', '证件照名称', '应用类型'],

    'records' => [
        [300, '行驶证', $truckUUID],
        [310, '营运证', $truckUUID],
        [320, '保险卡', $truckUUID],
        [400, '行驶证', $trailerUUID],
        [410, '营运证', $trailerUUID],
        [420, '保险卡', $trailerUUID],
    ],
];