<?php

namespace App\Http\Resources\InternalApi;

class EmptyResource extends BaseResource
{
    /**
     * Options for encoding data to JSON.
     *
     * @var int
     */
    protected $encodingOptions = 0;

    /**
     * EmptyResource constructor.
     *
     * @param bool $isArray
     */
    public function __construct($isArray = false)
    {
        parent::__construct(null);

        $this->encodingOptions = $isArray ? 0 : JSON_FORCE_OBJECT;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [];
    }
}
