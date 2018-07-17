<?php

namespace App\Services\Notification;


use App\Models\Notification\Notification;
use App\Services\BaseService;
use Urland\Exceptions\Server;

class NotificationService extends BaseService
{
    /**
     * 创建通知
     *
     * @param $attributes
     *
     * @return $this|\Illuminate\Database\Eloquent\Model
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function create($attributes)
    {
        // TODO
        // 补充 to_name、from_name、type
        $attributes['to_name']   = '';
        $attributes['from_name'] = '';
        $attributes['type']      = 0;

        $attributes['status']   = 1;
        $attributes['contents'] = $attributes;

        $notification = Notification::create($attributes);
        if (!$notification->exists) {
            throw new Server\InternalServerException('通知记录失败');
        }

        return $notification;
    }


}