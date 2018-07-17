<?php

namespace QingLong\Action;

class Factory
{
    /**
     * @var array
     */
    protected $resolved = [];

    /**
     * @var array
     */
    protected $resolvers = [];

    /**
     * Factory constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param string $name
     *
     * @return \QingLong\Action\ActionManager
     * @throws \InvalidArgumentException
     */
    public function make($name = 'default')
    {
        return $this->resolve($name);
    }

    /**
     * @param string $name
     *
     * @return \QingLong\Action\ActionManager
     * @throws \InvalidArgumentException
     */
    public function resolve($name = 'default')
    {
        if (isset($this->resolved[$name])) {
            return $this->resolved[$name];
        }

        if (!isset($this->resolvers[$name])) {
            throw new \InvalidArgumentException('Could not resolve actions for name ' . $name);
        }

        return $this->resolved[$name] = new ActionManager(value($this->resolvers[$name]));
    }

    /**
     * 添加解析器
     *
     * @param mixed  $callable
     * @param string $name
     */
    public function setResolver($callable, $name = 'default')
    {
        $this->resolvers[$name] = $callable;
    }
}