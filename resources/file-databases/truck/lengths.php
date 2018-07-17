<?php

$truckUUID   = cons('uuid.truck');
$trailerUUID = cons('uuid.trailer');

return [
    'columns'  => ['code', 'name', 'type'],

    // 长度类型需与 constant.php 中 uuid.truck 和 uuid.trailer 对应
    'comments' => ['车辆长度编码', '长度编码名称', '长度类型'],

    'records' => [
        ['38', '3.8米', $truckUUID],
        ['40', '4米', $truckUUID],
        ['42', '4.2米', $truckUUID],
        ['43', '4.3米', $truckUUID],
        ['45', '4.5米', $truckUUID],
        ['48', '4.8米', $truckUUID],
        ['50', '5米', $truckUUID],
        ['53', '5.3米', $truckUUID],
        ['57', '5.7米', $truckUUID],
        ['58', '5.8米', $truckUUID],
        ['60', '6米', $truckUUID],
        ['62', '6.2米', $truckUUID],
        ['63', '6.3米', $truckUUID],
        ['68', '6.8米', $truckUUID],
        ['70', '7米', $truckUUID],
        ['72', '7.2米', $truckUUID],
        ['74', '7.4米', $truckUUID],
        ['75', '7.5米', $truckUUID],
        ['76', '7.6米', $truckUUID],
        ['77', '7.7米', $truckUUID],
        ['78', '7.8米', $truckUUID],
        ['80', '8米', $truckUUID],
        ['82', '8.2米', $truckUUID],
        ['85', '8.5米', $truckUUID],
        ['86', '8.6米', $truckUUID],
        ['87', '8.7米', $truckUUID],
        ['88', '8.8米', $truckUUID],
        ['96', '9.6米', $truckUUID],
        ['20', '2米', $trailerUUID],
        ['130', '13米', $trailerUUID],
        ['150', '15米', $trailerUUID],
        ['160', '16米', $trailerUUID],
        ['175', '17.5米', $trailerUUID],
        ['185', '18.5米', $trailerUUID],
        ['200', '20米', $trailerUUID],
        ['220', '22米', $trailerUUID],
    ],
];