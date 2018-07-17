<?php

namespace QingLong\Platform\Order;


use Urland\Exceptions\Client\ValidationException;
use Urland\Exceptions\Server\InternalServerException;

class Order
{
    protected $client = null;

    public function __construct()
    {
        $this->client = app('api-client')->service('order56-api');
    }

    /**
     * 更新专线订单状态
     *
     * @param string $orderUUID 订单系统的orderUUID
     * @param int    $status
     *
     * @return bool
     */
    public function updateOrderMainLineStatus(string $orderUUID, int $status)
    {
        //TODO:转换订单状态(目前与订单系统状态一致)
        $param = [
            'order_status' => $status
        ];
        try {
            $this->client->patch('mainline-orders/' . $orderUUID . '/status', $param);
        } catch (\Exception $e) {
            if ($e instanceof ValidationException) {
                $message = head(head($e->errors()));
            } else {
                $message = $e->getMessage();
            }
            \Log::error('请求失败：' . $message, ['exception' => $e]);
        }

        return true;
    }
}