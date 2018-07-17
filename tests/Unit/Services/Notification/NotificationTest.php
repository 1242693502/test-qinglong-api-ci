<?php

namespace Tests\Unit\Services\Notification;


use App\Models\Notification\Notification;
use App\Services\Notification\NotificationService;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    /**
     * 测试创建通知
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testCreate()
    {
        $attributes = [
            'to_uuid'     => '333222111555667791',
            'to_type'     => '1',
            'to_name'     => '接收人',
            'from_uuid'   => '333222111555667758',
            'from_type'   => '2',
            'from_name'   => '来源用户名称',
            'type'        => '0',
            'status'      => '1',
            'title'       => '司机通知',
            'description' => '通知功能测试',
        ];

        $notification = (new NotificationService())->create($attributes);

        $this->assertTrue($notification instanceof Notification);

        $notificationData = [
            'to_uuid'     => $attributes['to_uuid'],
            'to_type'     => $attributes['to_type'],
            'from_uuid'   => $attributes['from_uuid'],
            'from_type'   => $attributes['from_type'],
            'type'        => $attributes['type'],
            'status'      => $attributes['status'],
            'title'       => $attributes['title'],
            'description' => $attributes['description'],
        ];
        $this->assertDatabaseHas('notifications', $notificationData);

        $notification = Notification::where('to_uuid', $attributes['to_uuid'])
            ->where('to_type', $attributes['to_type'])
            ->where('from_uuid', $attributes['from_uuid'])
            ->where('type', $attributes['type'])
            ->where('status', $attributes['status'])
            ->first();

        $contents = [
            'to_uuid'     => $attributes['to_uuid'],
            'to_type'     => $attributes['to_type'],
            'to_name'     => '',
            'from_uuid'   => $attributes['from_uuid'],
            'from_type'   => $attributes['from_type'],
            'from_name'   => '',
            'type'        => $attributes['type'],
            'status'      => $attributes['status'],
            'title'       => $attributes['title'],
            'description' => $attributes['description'],
        ];

        $this->assertArraySubset($contents, $notification->contents);

        return $notification;
    }
}