<?php

namespace QingLong\Action;

class Stage
{
    /**
     * @var string
     */
    protected $code;

    /**
     * @var \Illuminate\Support\Collection|\QingLong\Action\Action[]
     */
    protected $actions;

    /**
     * @var \Illuminate\Support\Collection|\QingLong\Action\Action[]
     */
    protected $codeActions;

    /**
     * @var null|callable
     */
    public $actionAllowHandler = null;

    /**
     * @var null|callable
     */
    public $actionHiddenHandler = null;

    /**
     * Stage constructor.
     *
     * @param string                               $code
     * @param array|\Illuminate\Support\Collection $actions
     * @param null|callable                        $actionAllowHandler
     * @param null|callable                        $actionHiddenHandler
     */
    public function __construct($code, $actions, $actionAllowHandler = null, $actionHiddenHandler = null)
    {
        $this->code                = $code;
        $this->actionAllowHandler  = $actionAllowHandler;
        $this->actionHiddenHandler = $actionHiddenHandler;

        $this->actions = collect($actions)->keyBy(function ($action)
        {
            // 添加可回调方法
            $action->allowHandler = function ($actionCode, $allow)
            {
                return is_callable($this->actionAllowHandler) ?
                    ($this->actionAllowHandler)($this, $actionCode, $allow) : $allow;
            };

            $action->hiddenHandler = function ($actionCode)
            {
                return is_callable($this->actionHiddenHandler) ?
                    ($this->actionHiddenHandler)($this, $actionCode) : false;
            };

            return $action->getPosition();
        });

        $this->codeActions = $this->actions->keyBy(function ($action)
        {
            return $action->getCode();
        });
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * 获取操作列表
     *
     * @return \Illuminate\Support\Collection|\QingLong\Action\Action[]
     */
    public function actions()
    {
        return $this->actions;
    }

    /**
     * 通过操作代码获取action
     *
     * @param string $code
     * @param mixed  $default
     *
     * @return \QingLong\Action\Action|mixed
     */
    public function action($code, $default = null)
    {
        return $this->codeActions->get($code, $default);
    }

    /**
     * 通过位置获取action
     *
     * @param int   $position
     * @param mixed $default
     *
     * @return \QingLong\Action\Action|mixed
     */
    public function actionAtPosition($position, $default = null)
    {
        return $this->actions->get($position, $default);
    }

    /**
     * 复制一个新的对象
     *
     * @return \QingLong\Action\Stage
     */
    public function copy()
    {
        $stage = new static(
            $this->getCode(),
            $this->actions->map(function ($action)
            {
                return $action->copy();
            }),
            $this->actionAllowHandler,
            $this->actionHiddenHandler
        );

        return $stage;
    }

    /**
     * 设置action已完成
     *
     * @param string $actionCode
     * @param bool   $isDone
     *
     * @return $this
     */
    public function setActionDone($actionCode, $isDone = true)
    {
        /* @var \QingLong\Action\Action $action */
        $action = $this->codeActions->get($actionCode);
        if ($action) {
            $action->setIsDone($isDone);
        }

        return $this;
    }

    /**
     * 判断某个操作是否已完成
     *
     * @param string $actionCode
     * @param bool   $default
     *
     * @return bool
     */
    public function isActionDone($actionCode, $default = false)
    {
        /* @var \QingLong\Action\Action $action */
        $action = $this->codeActions->get($actionCode);

        return $action ? $action->isDone() : $default;
    }

    /**
     * 获取当前stage的flag
     *
     * @return int
     */
    public function getFlag()
    {
        $flag = 0;
        foreach ($this->actions as $index => $action) {
            if ($action->isDone()) {
                $flag |= 1 << $index;
            }
        }

        return $flag;
    }

    /**
     * 获取当前stage的单例flag
     *
     * @return int
     */
    public function getSingletonFlag()
    {
        $flag = 0;
        foreach ($this->actions as $index => $action) {
            if ($action->isSingleton()) {
                $flag |= 1 << $index;
            }
        }

        return $flag;
    }
}