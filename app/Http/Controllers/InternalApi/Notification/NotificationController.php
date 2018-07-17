<?php

namespace App\Http\Controllers\InternalApi\Notification;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Requests\InternalApi\Notification\NotificationRequest;
use App\Http\Resources\InternalApi\Notification\NotificationResource;
use App\Services\Notification\NotificationService;

class NotificationController extends BaseController
{
    /**
     *创建通知
     *
     * @param \App\Http\Requests\InternalApi\Notification\NotificationRequest $request
     *
     * @return \App\Http\Resources\InternalApi\Notification\NotificationResource
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function store(NotificationRequest $request)
    {
        $notification = (new NotificationService())->create($request->validated());

        return new NotificationResource($notification);
    }
}
