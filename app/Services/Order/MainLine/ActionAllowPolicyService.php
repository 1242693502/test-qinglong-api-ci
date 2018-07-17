<?php

namespace App\Services\Order\MainLine;

use App\Services\BaseService;

class ActionAllowPolicyService extends BaseService
{
    use ActionPolicyTrait;

    /*
    |--------------------------------------------------------------------------
    | 车辆在途
    |--------------------------------------------------------------------------
    |
    |
    */

    /**
     * 是否允许检查车辆
     *
     * @param \QingLong\Action\Stage $stage
     * @param string                 $actionCode
     * @param bool                   $allow
     *
     * @return bool
     */
    public function allowCheckTruck($stage, $actionCode, $allow)
    {
        if (!$allow) {
            return false;
        }

        switch ($stage->getCode()) {
            case 'driver_prepare':
                return $stage->isActionDone('check_truck_certs');

            case 'arrive_unloading':
                return true;
        }

        return false;
    }

    /**
     * 是否允许进入高速
     *
     * @param \QingLong\Action\Stage $stage
     * @param string                 $actionCode
     * @param bool                   $allow
     *
     * @return bool
     */
    public function allowHighWayEnter($stage, $actionCode, $allow)
    {
        return $allow && !$stage->isActionDone($actionCode);
    }

    /**
     * 是否允许离开高速
     *
     * @param \QingLong\Action\Stage $stage
     * @param string                 $actionCode
     * @param bool                   $allow
     *
     * @return bool
     */
    public function allowHighWayLeave($stage, $actionCode, $allow)
    {
        return $allow && $stage->isActionDone('high_way_enter');
    }

    /**
     * 是否允许到达装货地操作
     *
     * @param \QingLong\Action\Stage $stage
     * @param string                 $actionCode
     * @param bool                   $allow
     *
     * @return bool
     */
    public function allowArriveLoading($stage, $actionCode, $allow)
    {
        if (!$allow || $this->isFinishAllLoadingPlaces()) {
            return false;
        }
        return !$stage->isActionDone('high_way_enter') || $stage->isActionDone('high_way_leave');
    }

    /**
     * 是否允许到达卸货地操作
     *
     * @param \QingLong\Action\Stage $stage
     * @param string                 $actionCode
     * @param bool                   $allow
     *
     * @return bool
     */
    public function allowArriveUnloading($stage, $actionCode, $allow)
    {
        if (!$allow || !$this->isFinishAllLoadingPlaces()) {
            return false;
        }
        return !$stage->isActionDone('high_way_enter') || $stage->isActionDone('high_way_leave');
    }

    /*
    |--------------------------------------------------------------------------
    | 车辆到达装货地
    |--------------------------------------------------------------------------
    |
    |
    */

    /**
     * 是否允许检查挂车
     *
     * @param \QingLong\Action\Stage $stage
     * @param string                 $actionCode
     * @param bool                   $allow
     *
     * @return bool
     */
    public function allowCheckTrailer($stage, $actionCode, $allow)
    {
        return $allow && $stage->isActionDone('check_trailer_certs');
    }

    /**
     * 是否允许接收单据
     *
     * @param \QingLong\Action\Stage $stage
     * @param string                 $actionCode
     * @param bool                   $allow
     *
     * @return bool
     */
    public function allowReceiveReceipt($stage, $actionCode, $allow)
    {
        return $allow && $stage->isActionDone('check_trailer');
    }

    /**
     * 是否允许记录过磅单
     *
     * @param \QingLong\Action\Stage $stage
     * @param string                 $actionCode
     * @param bool                   $allow
     *
     * @return bool
     */
    public function allowRecordWeight($stage, $actionCode, $allow)
    {
        return $allow && $stage->isActionDone('check_trailer');
    }

    /**
     * 是否允许录封签号
     *
     * @param \QingLong\Action\Stage $stage
     * @param string                 $actionCode
     * @param bool                   $allow
     *
     * @return bool
     */
    public function allowRecordSeals($stage, $actionCode, $allow)
    {
        return $allow && $stage->isActionDone('check_trailer');
    }

    /**
     * 是否允许装货计时开始
     *
     * @param \QingLong\Action\Stage $stage
     * @param string                 $actionCode
     * @param bool                   $allow
     *
     * @return bool
     */
    public function allowCountLoadingBegin($stage, $actionCode, $allow)
    {
        return $allow && $stage->isActionDone('check_trailer')
            && !$stage->isActionDone($actionCode);
    }

    /**
     * 是否允许装货计时结束
     *
     * @param \QingLong\Action\Stage $stage
     * @param string                 $actionCode
     * @param bool                   $allow
     *
     * @return bool
     */
    public function allowCountLoadingEnd($stage, $actionCode, $allow)
    {
        return $allow && $stage->isActionDone('check_trailer')
            && $stage->isActionDone('count_loading_begin');
    }

    /**
     * 是否允许多个装货点
     *
     * @param \QingLong\Action\Stage $stage
     * @param string                 $actionCode
     * @param bool                   $allow
     *
     * @return bool
     */
    public function allowAddLoading($stage, $actionCode, $allow)
    {
        return $this->allowCompleteLoading($stage, $actionCode, $allow);
    }

    /**
     * 是否允许完成装货
     *
     * @param \QingLong\Action\Stage $stage
     * @param string                 $actionCode
     * @param bool                   $allow
     *
     * @return bool
     */
    public function allowCompleteLoading($stage, $actionCode, $allow)
    {
        if (!$allow || !$stage->isActionDone('check_trailer')) {
            return false;
        }

        return !$stage->isActionDone('count_loading_begin') || $stage->isActionDone('count_loading_end');
    }

    /*
    |--------------------------------------------------------------------------
    | 到达卸货地
    |--------------------------------------------------------------------------
    |
    |
    */
    /**
     * 是否允许卸货计时开始
     *
     * @param \QingLong\Action\Stage $stage
     * @param string                 $actionCode
     * @param bool                   $allow
     *
     * @return bool
     */
    public function allowCountUnloadingBegin($stage, $actionCode, $allow)
    {
        return $allow && !$stage->isActionDone($actionCode);
    }

    /**
     * 是否允许卸货计时结束
     *
     * @param \QingLong\Action\Stage $stage
     * @param string                 $actionCode
     * @param bool                   $allow
     *
     * @return bool
     */
    public function allowCountUnloadingEnd($stage, $actionCode, $allow)
    {
        return $allow && $stage->isActionDone('count_unloading_begin');
    }

    /**
     * 是否允许多个卸货点
     *
     * @param \QingLong\Action\Stage $stage
     * @param string                 $actionCode
     * @param bool                   $allow
     *
     * @return bool
     */
    public function allowAddUnloading($stage, $actionCode, $allow)
    {
        return $this->allowCompleteUnloading($stage, $actionCode, $allow);
    }

    /**
     * 是否允许完成卸货
     *
     * @param \QingLong\Action\Stage $stage
     * @param string                 $actionCode
     * @param bool                   $allow
     *
     * @return bool
     */
    public function allowCompleteUnloading($stage, $actionCode, $allow)
    {
        if (!$allow) {
            return false;
        }

        return !$stage->isActionDone('count_unloading_begin') || $stage->isActionDone('count_unloading_end');
    }

    /**
     * 是否允许交接单据
     *
     * @param \QingLong\Action\Stage $stage
     * @param string                 $actionCode
     * @param bool                   $allow
     *
     * @return bool
     */
    public function allowSendReceipt($stage, $actionCode, $allow)
    {
        return $allow && !$this->isFinishSendReceipt();
    }
}
