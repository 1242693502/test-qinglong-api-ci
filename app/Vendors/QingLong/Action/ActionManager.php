<?php

namespace QingLong\Action;

class ActionManager
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $stages;

    /**
     * @var \QingLong\Action\Stage[]
     */
    protected $stageFlags = [];

    /**
     * 是否允许操作处理器
     *
     * @var null|callable
     */
    protected $actionAllowHandler = null;

    /**
     * 是否隐藏处理器
     *
     * @var null|callable
     */
    protected $actionHiddenHandler = null;

    /**
     * ActionManager constructor.
     *
     * @param array|\Illuminate\Support\Collection $plainActions
     */
    public function __construct($plainActions)
    {
        $this->stages = $this->parse($plainActions);
    }

    /**
     * 根据key获取stage
     *
     * @param string $stageKey
     * @param int    $flag
     *
     * @return \QingLong\Action\Stage|null
     */
    public function stage($stageKey, $flag = 0)
    {
        $stage = $this->stages->get($stageKey);
        if (empty($flag)) {
            return $stage ? $stage->copy() : null;
        }

        if (empty($stage)) {
            return null;
        }

        // TODO: 没有缓存
        return $this->mapStageWithFlag($stage, $flag);
    }

    /**
     * @param null|callable $actionAllowHandler
     */
    public function setActionAllowHandler($actionAllowHandler)
    {
        $this->actionAllowHandler = $actionAllowHandler;
        $this->stages->each(function ($stage)
        {
            $stage->actionAllowHandler = $this->actionAllowHandler;
        });
    }

    /**
     * @param null|callable $actionHiddenHandler
     */
    public function setActionHiddenHandler($actionHiddenHandler)
    {
        $this->actionHiddenHandler = $actionHiddenHandler;
        $this->stages->each(function ($stage)
        {
            $stage->actionHiddenHandler = $this->actionHiddenHandler;
        });
    }

    /**
     * 根据flag获取对应的操作属性
     *
     * @param \QingLong\Action\Stage $stage
     * @param int                    $flag
     *
     * @return \QingLong\Action\Stage
     */
    protected function mapStageWithFlag($stage, $flag)
    {
        $newActions = collect();
        foreach ($stage->actions() as $action) {
            $newAction = $action->copy();
            $newAction->setIsDone((bool)(1 << $action->getPosition() & $flag));

            $newActions->push($newAction);
        }

        return new Stage($stage->getCode(), $newActions, $this->actionAllowHandler, $this->actionHiddenHandler);
    }

    /**
     * 此方法应该由业务系统提供
     *
     * @param array|\Illuminate\Support\Collection $plainActions
     *
     * @return \Illuminate\Support\Collection
     */
    protected function parse($plainActions)
    {
        $stages = collect();
        foreach ($plainActions as $plainAction) {
            $action   = new Action(
                $plainAction['stage'],
                $plainAction['code'],
                $plainAction['name'],
                $plainAction['repeat'],
                $plainAction['singleton'],
                $plainAction['position']
            );
            $stageKey = $action->getStage() ?: 'default';

            if ($stages->has($stageKey)) {
                $stages->get($stageKey)->push($action);
            } else {
                $stages->offsetSet($stageKey, collect([$action]));
            }
        }

        return $stages->map(function ($actions, $stageCode)
        {
            return new Stage($stageCode, $actions, $this->actionAllowHandler, $this->actionHiddenHandler);
        });
    }
}