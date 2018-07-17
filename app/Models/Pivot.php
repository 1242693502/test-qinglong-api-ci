<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot as BasePivot;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Pivot
 *
 * @package App\Models
 */
class Pivot extends BasePivot
{
    use SoftDeletes;

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'create_time';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'update_time';

    /**
     * The name of the "deleted at" column.
     *
     * @var string
     */
    const DELETED_AT = 'delete_time';

    /**
     * model名称
     *
     * @var string
     */
    const MODEL_NAME = '资源';

    /**
     * Get the attributes that should be converted to dates.
     *
     * @return array
     */
    public function getDates()
    {
        $defaults = [static::CREATED_AT, static::UPDATED_AT, self::DELETED_AT];

        return $this->usesTimestamps() ? array_unique(array_merge($this->dates, $defaults)) : $this->dates;
    }
}
