<?php

namespace App\Services\Order\MainLine;

use App\Services\BaseService;

class ActionHiddenPolicyService extends BaseService
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
     * 是否隐藏进入高速
     *
     * @param \QingLong\Action\Stage $stage
     * @param string                 $actionCode
     *
     * @return bool
     */
    public function hideHighWayEnter($stage, $actionCode)
    {
        return $stage->isActionDone($actionCode);
    }

    /**
     * 是否隐藏离开高速
     *
     * @param \QingLong\Action\Stage $stage
     * @param string                 $actionCode
     *
     * @return bool
     */
    public function hideHighWayLeave($stage, $actionCode)
    {
        return !$stage->isActionDone('high_way_enter');
    }

    /**
     * 是否隐藏到达装货地
     *
     * @param \QingLong\Action\Stage $stage
     * @param string                 $actionCode
     *
     * @return bool
     */
    public function hideArriveLoading($stage, $actionCode)
    {
        return $this->isFinishAllLoadingPlaces();
    }

    /**
     * 是否隐藏到达卸货地
     *
     * @param \QingLong\Action\Stage $stage
     * @param string                 $actionCode
     *
     * @return bool
     */
    public function hideArriveUnloading($stage, $actionCode)
    {
        return !$this->isFinishAllLoadingPlaces();
    }

    /*
    |--------------------------------------------------------------------------
    | 到达装货地
    |--------------------------------------------------------------------------
    |
    |
    */

    /**
     * 是否隐藏装货计时开始
     *
     * @param \QingLong\Action\Stage $stage
     * @param string                 $actionCode
     *
     * @return bool
     */
    public function hideCountLoadingBegin($stage, $actionCode)
    {
        return $stage->isActionDone($actionCode);
    }

    /**
     * 是否显示装货计时结束
     *
     * @param \QingLong\Action\Stage $stage
     * @param string                 $actionCode
     *
     * @return bool
     */
    public function hideCountLoadingEnd($stage, $actionCode)
    {
        return !$stage->isActionDone('count_loading_begin');
    }

    /**
     * 是否隐藏添加新的装货地
     *
     * @param \QingLong\Action\Stage $stage
     * @param string                 $actionCode
     *
     * @return bool
     */
    public function hideAddLoading($stage, $actionCode)
    {
        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | 到达卸货地
    |--------------------------------------------------------------------------
    |
    |
    */
    /**
     * 是否隐藏卸货计时开始
     *
     * @param \QingLong\Action\Stage $stage
     * @param string                 $actionCode
     *
     * @return bool
     */
    public function hideCountUnloadingBegin($stage, $actionCode)
    {
        return $stage->isActionDone($actionCode);
    }

    /**
     * 是否显示卸货计时结束
     *
     * @param \QingLong\Action\Stage $stage
     * @param string                 $actionCode
     *
     * @return bool
     */
    public function hideCountUnloadingEnd($stage, $actionCode)
    {
        return !$stage->isActionDone('count_unloading_begin');
    }

    /**
     * 是否隐藏添加新的卸货地
     *
     * @param \QingLong\Action\Stage $stage
     * @param string                 $actionCode
     *
     * @return bool
     */
    public function hideAddUnloading($stage, $actionCode)
    {
        return true;
    }
}
