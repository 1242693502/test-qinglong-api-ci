<?php

namespace App\Services\Order\MainLine;

use App\Models\Order\MainLine\Status;
use App\Models\Order\OrderMainLine;
use App\Services\BaseService;
use Urland\Exceptions\Client;
use Urland\Exceptions\Server;

class ActionService extends BaseService
{
    /**
     * @var \QingLong\Action\ActionManager
     */
    protected $stageManager;

    /**
     * @var string
     */
    protected $orderUUID;

    /**
     * @var \App\Services\Order\MainLine\ActionAllowPolicyService
     */
    protected $allowPolicyService;

    /**
     * @var \App\Services\Order\MainLine\ActionHiddenPolicyService
     */
    protected $hiddenPolicyService;

    /**
     * ActionService constructor.
     *
     * @param $orderUUID
     *
     * @throws \Urland\Exceptions\Client\NotFoundException
     */
    public function __construct($orderUUID)
    {
        if (empty($orderUUID) || !OrderMainLine::where('order_uuid', $orderUUID)->exists()) {
            throw new Client\NotFoundException('订单不存在');
        }

        $this->orderUUID = $orderUUID;

        $this->stageManager = app('ql.action')->make();
        $this->stageManager->setActionAllowHandler(function ($stage, $actionCode, $allow) {
            $methodName = 'allow' . studly_case($actionCode);
            if (method_exists($this->allowPolicyService(), $methodName)) {
                return $this->allowPolicyService()->{$methodName}($stage, $actionCode, $allow);
            }

            return $allow;
        });

        $this->stageManager->setActionHiddenHandler(function ($stage, $actionCode) {
            $methodName = 'hide' . studly_case($actionCode);
            if (method_exists($this->hiddenPolicyService(), $methodName)) {
                return $this->hiddenPolicyService()->{$methodName}($stage, $actionCode);
            }

            return false;
        });
    }

    /**
     * 获取orderUUID对应的actionService
     *
     * @param string $orderUUID
     *
     * @return \App\Services\Order\MainLine\ActionService
     * @throws \Urland\Exceptions\Client\NotFoundException
     */
    public static function serviceForOrderUUID($orderUUID)
    {
        static $actionServices = [];
        if (isset($actionServices[$orderUUID])) {
            return $actionServices[$orderUUID];
        }

        return $actionServices[$orderUUID] = new static($orderUUID);
    }

    /**
     * 获取当前stage
     *
     * @return null|\QingLong\Action\Stage
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function stage()
    {
        $orderMainLineStatus = $this->fetchOrderMainLineStatus();
        $orderStatusKey      = cons()->key('order.mainline.status', $orderMainLineStatus->order_status);

        return $this->stageManager->stage($orderStatusKey, $orderMainLineStatus->action_flag);
    }

    /**
     * 获取订单可操作列表
     *
     * @return \Illuminate\Support\Collection|\QingLong\Action\Action[]
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function actions()
    {
        $stage = $this->stage();

        return $stage ? $stage->actions() : collect();
    }

    /**
     * 设置action是否已完成
     *
     * @param string|array $actionCodes
     * @param bool         $done
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function setActionsDone($actionCodes, $done = true)
    {
        $orderMainLineStatus = $this->fetchOrderMainLineStatus();
        $orderStatusKey      = cons()->key('order.mainline.status', $orderMainLineStatus->order_status);
        $stage               = $this->stageManager->stage($orderStatusKey, $orderMainLineStatus->action_flag);

        if (empty($stage)) {
            throw new Client\NotFoundException('订单对应状态不存在');
        }

        // 设置多个值
        foreach ((array)$actionCodes as $actionCode) {
            $stage->setActionDone($actionCode, $done);
        }

        if (!$orderMainLineStatus->setAttribute('action_flag', $stage->getFlag())->save()) {
            throw new Server\InternalServerException('订单状态保存失败');
        }
    }

    /**
     * 获取订单对应状态
     *
     * @return \App\Models\Order\MainLine\Status
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    protected function fetchOrderMainLineStatus()
    {
        return Status::where('order_uuid', $this->orderUUID)
            ->orderByDesc('id')
            ->firstOrFail(['id', 'order_status', 'action_flag']);
    }

    /**
     * 操作是否可用规则
     *
     * @return \App\Services\Order\MainLine\ActionAllowPolicyService
     */
    protected function allowPolicyService()
    {
        if ($this->allowPolicyService) {
            return $this->allowPolicyService;
        }

        return $this->allowPolicyService = new ActionAllowPolicyService($this->orderUUID);
    }

    /**
     * 操作是否隐藏规则
     *
     * @return \App\Services\Order\MainLine\ActionHiddenPolicyService
     */
    protected function hiddenPolicyService()
    {
        if ($this->hiddenPolicyService) {
            return $this->hiddenPolicyService;
        }

        return $this->hiddenPolicyService = new ActionHiddenPolicyService($this->orderUUID);
    }
}
