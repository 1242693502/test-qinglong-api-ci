<?php


Route::middleware(['auth:internal_api'])->group(function ()
{

    // 车辆相关
    Route::prefix('trucks')->namespace('Truck')->group(function ()
    {
        Route::post('', 'TruckController@store');
        Route::put('{truckUUID}', 'TruckController@update');

        // 车辆关联司机相关
        Route::get('{truckUUID}/drivers', 'TruckController@showAppointedDrivers');
        Route::patch('{truckUUID}/drivers', 'TruckController@appointDrivers');
        Route::patch('{truckUUID}/swap-driving', 'TruckController@swapDriving');
        Route::delete('{truckUUID}/drivers', 'TruckController@removeDrivers');
        // 当前车辆正在驾驶的司机信息
        Route::get('{truckUUID}/driving-driver', 'TruckController@drivingDriver');

        // 车辆油卡
        Route::post('{truckUUID}/gas-cards', 'TruckGasCardController@store');
    });
    Route::namespace('Truck')->group(function ()
    {
        // 解绑车辆油卡
        Route::delete('truck-gas-card/{gasCardNo}', 'TruckGasCardController@unbind');
        // 挂失车辆油卡
        Route::patch('truck-gas-card/{gasCardNo}', 'TruckGasCardController@loss');

        // 审核订单
        Route::patch('truck-approvals/{truck_approval}', 'TruckApprovalController@approval');
    });

    // 油卡系统
    Route::namespace('GasCard')->group(function ()
    {
        // 获取库存列表
        Route::get('/gas-cards', 'GasCardController@index');
        // 申请油卡充值
        Route::post('/gas-card-orders/{gasCardNo}', 'GasCardOrderController@store');
        // 审核油卡充值
        Route::patch('/gas-card-orders/{gasCardOrderUUID}/approval', 'GasCardOrderController@approval');
        // 油卡充值成功回调
        Route::patch('/gas-card-orders/{gasCardOrderUUID}/success', 'GasCardOrderController@rechargeSuccess');
    });

    // 司机相关
    Route::namespace('Driver')->group(function ()
    {
        Route::post('drivers', 'DriverController@store');
        Route::put('drivers/{driverUUID}', 'DriverController@update');
        // 获取正在驾驶的车辆信息
        Route::get('drivers/{driverUUID}/driving-truck', 'DriverController@drivingTruck');
        // 获取正在驾驶的车辆里程
        Route::get('drivers/{driverUUID}/driving-truck-mileage', 'DriverController@drivingTruckMileage');
    });

    // 挂车相关
    Route::namespace('Trailer')->group(function ()
    {
        Route::post('trailers', 'TrailerController@store');
        Route::put('trailers/{trailerUUID}', 'TrailerController@update');
    });

    // Initial 路由
    Route::namespace('Initial')->group(function ()
    {
        Route::get('driver-license-types', 'Driver\LicenseTypeController@index');
        Route::get('driver-certificate-types', 'Driver\CertificateTypeController@index');

        Route::get('truck-colors', 'Truck\ColorController@index');
        Route::get('truck-belong-types', 'Truck\BelongTypeController@index');
        Route::get('truck-lengths/{type?}', 'Truck\LengthController@index');
        Route::get('truck-types/{type?}', 'Truck\TypeController@index');
        Route::get('truck-certificate-types/{type?}', 'Truck\CertificateTypeController@index');
        Route::get('truck-plates', 'Truck\PlateController@index');
        Route::get('truck-checks', 'Truck\CheckController@index');
        Route::get('truck-check-certificates', 'Truck\CheckCertificateController@index');

        Route::get('trailer-checks', 'Trailer\CheckController@index');
        Route::get('trailer-check-certificates', 'Trailer\CheckCertificateController@index');

    });

    // 车辆记录
    Route::namespace('Truck\Log')->group(function ()
    {
        Route::post('truck-adblue-logs', 'AdBlueController@store');
        Route::post('truck-park-logs', 'ParkController@store');
        Route::post('truck-refuel-logs', 'RefuelController@store');
        Route::post('truck-repair-logs', 'RepairController@store');
        Route::post('truck-other-logs', 'OtherController@store');
        Route::post('truck-penalty-logs', 'PenaltyController@store');
        Route::post('truck-toll-road-logs', 'TollRoadController@store');
        Route::post('truck-coolant-logs', 'CoolantController@store');
        Route::post('truck-weight-logs', 'WeightController@store');
    });

    // 订单记录
    Route::prefix('mainline-orders')->namespace('Order')->group(function ()
    {
        Route::post('/', 'OrderMainLineController@store');
        Route::get('{orderUUID}/actions', 'MainLine\ActionController@getOrderActions');
        Route::patch('{orderUUID}/truck', 'OrderMainLineController@appointTruck');
        Route::patch('{orderUUID}/driver-confirm', 'OrderMainLineController@driverConfirm');

        // 订单日志
        Route::post('{orderUUID}/record-seals-logs', 'MainLine\Log\RecordSealsController@store');
        Route::post('{orderUUID}/record-weight-logs', 'MainLine\Log\RecordWeightController@store');
        Route::post('{orderUUID}/receive-receipt-logs', 'MainLine\Log\ReceiveReceiptController@store');
        Route::post('{orderUUID}/send-receipt-logs', 'MainLine\Log\SendReceiptController@store');
        Route::post('{orderUUID}/count-loading-begin-logs', 'MainLine\Log\CountLoadingBeginController@store');
        Route::post('{orderUUID}/count-loading-end-logs', 'MainLine\Log\CountLoadingEndController@store');
        Route::post('{orderUUID}/count-unloading-begin-logs', 'MainLine\Log\CountUnloadingBeginController@store');
        Route::post('{orderUUID}/count-unloading-end-logs', 'MainLine\Log\CountUnloadingEndController@store');
        Route::post('{orderUUID}/highway-enter-logs', 'MainLine\Log\HighWayEnterController@store');
        Route::post('{orderUUID}/highway-leave-logs', 'MainLine\Log\HighWayLeaveController@store');
        Route::post('{orderUUID}/traffic-jam-logs', 'MainLine\Log\TrafficJamController@store');
        Route::post('{orderUUID}/arrive-loading-logs', 'MainLine\Log\ArriveLoadingController@store');
        Route::post('{orderUUID}/arrive-unloading-logs', 'MainLine\Log\ArriveUnloadingController@store');
        Route::post('{orderUUID}/add-loading-logs', 'MainLine\Log\AddLoadingController@store');
        Route::post('{orderUUID}/add-unloading-logs', 'MainLine\Log\AddUnloadingController@store');
        Route::post('{orderUUID}/complete-loading-logs', 'MainLine\Log\CompleteLoadingController@store');
        Route::post('{orderUUID}/complete-unloading-logs', 'MainLine\Log\CompleteUnloadingController@store');
        Route::post('{orderUUID}/unloading-abnormal-logs', 'MainLine\Log\UnloadingAbnormalController@store');
        Route::post('{orderUUID}/complete', 'MainLine\Log\CompleteController@store');
        Route::post('{orderUUID}/no-count-loading-logs', 'MainLine\Log\NoCountLoadingController@store');
        Route::post('{orderUUID}/no-count-unloading-logs', 'MainLine\Log\NoCountUnloadingController@store');

        //证件检查
        Route::post('{orderUUID}/check-truck-certificates', 'MainLine\Log\CheckTruckController@checkTruckCertificates');
        //车辆检查
        Route::post('{orderUUID}/check-truck', 'MainLine\Log\CheckTruckController@checkTruck');
        //挂车证件检查
        Route::post('{orderUUID}/check-trailer-certificates', 'MainLine\Log\CheckTrailerController@store');
        //挂车检查
        Route::post('{orderUUID}/check-trailer', 'MainLine\Log\CheckTrailerController@checkTrailer');
    });

    // 油卡相关
    Route::namespace('GasCard')->group(function ()
    {
        Route::post('filling-stations', 'FillingStationController@store');
        Route::put('filling-stations/{stationID}', 'FillingStationController@update');
    });

    // 通知
    Route::namespace('Notification')->group(function ()
    {
        Route::post('notifications', 'NotificationController@store');
    });

});

Route::apiFallback();