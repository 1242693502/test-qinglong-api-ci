<?php

namespace QingLong\Action;

class Action
{
    /**
     * @var string
     */
    protected $stage;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var boolean
     */
    protected $repeat = true;

    /**
     * @var bool
     */
    protected $singleton = false;

    /**
     * @var integer
     */
    protected $position = 0;

    /**
     * @var bool
     */
    protected $isDone = false;

    /**
     * @var null|callable
     */
    public $allowHandler = null;

    /**
     * @var null|callable
     */
    public $hiddenHandler = null;

    /**
     * Action constructor.
     *
     * @param string $stage
     * @param string $code
     * @param string $name
     * @param bool   $repeat
     * @param bool   $singleton
     * @param int    $position
     */
    public function __construct($stage, $code, $name, $repeat, $singleton, $position)
    {
        $this->stage     = $stage;
        $this->code      = $code;
        $this->name      = $name;
        $this->repeat    = (bool)$repeat;
        $this->singleton = (bool)$singleton;
        $this->position  = $position;
    }

    /**
     * @return string
     */
    public function getStage()
    {
        return $this->stage;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isSingleton()
    {
        return $this->singleton;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param bool $isDone
     *
     * @return $this
     */
    public function setIsDone($isDone)
    {
        $this->isDone = $isDone;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDone()
    {
        return $this->isDone;
    }

    /**
     * 简单判断是否允许操作
     *
     * @return bool
     */
    public function allow()
    {
        return $this->repeat || !$this->isDone();
    }

    /**
     * 结合业务后判断是否允许操作
     *
     * @return bool
     */
    public function computedAllow()
    {
        if (is_callable($this->allowHandler)) {
            return ($this->allowHandler)($this->code, $this->allow());
        }

        return $this->allow();
    }

    /**
     * 结合业务后判断是否隐藏
     *
     * @return bool
     */
    public function computedHidden()
    {
        if (is_callable($this->hiddenHandler)) {
            return ($this->hiddenHandler)($this->code);
        }

        return false;
    }

    /**
     * 复制一个action
     *
     * @return \QingLong\Action\Action
     */
    public function copy()
    {
        return new static(
            $this->stage,
            $this->code,
            $this->name,
            $this->repeat,
            $this->singleton,
            $this->position
        );
    }
}