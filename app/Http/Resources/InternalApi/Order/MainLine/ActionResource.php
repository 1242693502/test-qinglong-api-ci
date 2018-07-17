<?php

namespace App\Http\Resources\InternalApi\Order\MainLine;

use App\Http\Resources\InternalApi\BaseResource;

/**
 * Class ActionResource
 *
 * @package App\Http\Resources
 *
 * @mixin \QingLong\Action\Action
 */
class ActionResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'stage'  => $this->getStage(),
            'code'   => $this->getCode(),
            'name'   => $this->getName(),
            'done'   => $this->isDone(),
            'allow'  => $this->computedAllow(),
            'hidden' => $this->computedHidden(),
        ];
    }
}
