<?php

namespace App\Http\Resources;

use Urland\Api\Http\Resources\ApiResource;

abstract class Resource extends ApiResource
{
    /**
     * 通用日期格式
     *
     * @var string
     */
    protected $dateFormat = \DateTime::RFC3339;

    /**
     * 日期格式化
     *
     * @param \DateTime $datetime
     * @param mixed $default
     *
     * @return string
     */
    protected function formatDate($datetime, $default = null)
    {
        return $datetime ? $datetime->format($this->dateFormat) : $default;
    }
}
