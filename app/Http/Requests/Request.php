<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Urland\Api\Http\Requests\ApiRequest;

abstract class Request extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * 通用状态描述
     *
     * @return array
     */
    protected function commonAttributes()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function createDefaultValidator(ValidationFactory $factory)
    {
        return $factory->make(
            $this->validationData(), $this->container->call([$this, 'rules']),
            $this->messages(), array_merge($this->commonAttributes(), $this->attributes())
        );
    }
}
